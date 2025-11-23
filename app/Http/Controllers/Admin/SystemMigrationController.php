<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SystemMigrationController extends Controller
{
    /**
     * Show the migration form
     */
    public function showForm()
    {
        try {
            // Attempt to load the view normally
            return view('admin.system.migrate');
        } catch (\Exception $e) {
            // If there's a database error (tables don't exist), still show the form
            // This allows the migration page to work even when database is completely empty
            return response()->view('admin.system.migrate', [], 200);
        }
    }

    /**
     * Run database migrations with system key verification
     */
    public function migrate(Request $request)
    {
        $request->validate([
            'system_key' => 'required|string',
            'run_composer' => 'nullable|boolean',
            'migration_type' => 'required|in:migrate,refresh',
            'run_seeders' => 'nullable|boolean',
            'create_admin' => 'nullable|boolean',
            'admin_email' => 'required_if:create_admin,1|email|nullable',
            'admin_password' => 'required_if:create_admin,1|min:8|nullable',
        ]);

        $providedKey = $request->input('system_key');
        $systemKey = env('SYSTEM_KEY');

        // Check if system key is configured
        if (empty($systemKey)) {
            return back()->with('error', 'System key is not configured in .env file.');
        }

        // Verify the system key
        if ($providedKey !== $systemKey) {
            Log::warning('Failed migration attempt with invalid system key', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            return back()->with('error', 'Invalid system key provided.');
        }

        try {
            $migrationType = $request->input('migration_type');
            $output = '';

            // Run composer install if requested
            if ($request->input('run_composer')) {
                $output .= "=== Running Composer Install ===\n";
                
                Log::info('Running composer install via system migration', [
                    'ip' => $request->ip(),
                ]);

                // Execute composer install
                $composerOutput = shell_exec('cd ' . base_path() . ' && composer install --no-interaction --prefer-dist --optimize-autoloader 2>&1');
                
                if ($composerOutput) {
                    $output .= $composerOutput . "\n";
                    $output .= "✓ Composer install completed\n\n";
                } else {
                    $output .= "⚠ Composer install may have failed or produced no output\n\n";
                }
            }

            // Run migrations based on type
            Log::info('Running database migrations via system key', [
                'ip' => $request->ip(),
                'type' => $migrationType,
            ]);

            if ($migrationType === 'refresh') {
                Artisan::call('migrate:fresh', [
                    '--force' => true,
                ]);
                $output .= "=== Running migrate:fresh ===\n";
            } else {
                Artisan::call('migrate', [
                    '--force' => true,
                ]);
                $output .= "=== Running migrate ===\n";
            }

            $output .= Artisan::output();

            // Create admin user if requested
            if ($request->input('create_admin')) {
                $output .= "\n=== Creating Admin User ===\n";
                
                $adminEmail = $request->input('admin_email');
                $adminPassword = $request->input('admin_password');

                // Create or update user
                $user = \App\Models\User::updateOrCreate(
                    ['email' => $adminEmail],
                    [
                        'name' => 'Admin User',
                        'password' => \Illuminate\Support\Facades\Hash::make($adminPassword),
                        'email_verified_at' => now(),
                    ]
                );

                // Ensure admin role exists
                $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
                
                // Assign all permissions to admin role
                $adminRole->syncPermissions(\Spatie\Permission\Models\Permission::all());
                
                // Assign admin role to user
                $user->syncRoles(['admin']);

                $output .= "✓ Admin user created/updated: {$adminEmail}\n";
                $output .= "✓ Admin role assigned with all permissions\n";

                Log::info('Admin user created via system migration', [
                    'email' => $adminEmail,
                ]);
            }

            // Run seeders if requested
            if ($request->input('run_seeders')) {
                $output .= "\n=== Running Database Seeders ===\n";
                
                Artisan::call('db:seed', [
                    '--force' => true,
                ]);
                
                $output .= Artisan::output();
                $output .= "✓ Database seeders completed\n";

                Log::info('Database seeders executed via system migration');
            }

            Log::info('Database migrations completed successfully', [
                'output' => $output,
            ]);

            return back()->with('success', 'Database migrations completed successfully!')
                        ->with('output', $output);

        } catch (\Exception $e) {
            Log::error('Migration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Migration failed: ' . $e->getMessage());
        }
    }
}

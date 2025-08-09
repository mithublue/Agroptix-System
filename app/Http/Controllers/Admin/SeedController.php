<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SeedController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage_options']);
    }

    protected function ensureLocal()
    {
        if (!app()->environment('local')) {
            abort(403, 'Seeding UI is only available in local development environment.');
        }
    }

    public function index()
    {
        $this->ensureLocal();
        $seeders = $this->discoverSeeders();
        return view('admin.dev-seeder.index', [
            'seeders' => $seeders,
        ]);
    }

    public function list(Request $request)
    {
        $this->ensureLocal();
        return response()->json([
            'seeders' => $this->discoverSeeders(),
        ]);
    }

    public function runOne(Request $request)
    {
        $this->ensureLocal();
        $request->validate([
            'class' => 'required|string',
        ]);

        $class = $request->input('class');
        if (!class_exists($class)) {
            return response()->json([
                'success' => false,
                'message' => "Seeder class not found: {$class}",
            ], 422);
        }

        try {
            // Run the given seeder class
            $exitCode = Artisan::call('db:seed', [
                '--class' => $class,
                '--force' => true,
            ]);
            $output = Artisan::output();

            return response()->json([
                'success' => $exitCode === 0,
                'message' => trim($output) ?: "Seeded: {$class}",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function discoverSeeders(): array
    {
        $seeders = [];
        $path = database_path('seeders');
        if (!File::isDirectory($path)) {
            return $seeders;
        }

        // Scan recursively for any *Seeder.php files
        $files = File::allFiles($path);
        $seen = [];
        foreach ($files as $file) {
            $filename = $file->getFilename();
            if (!Str::endsWith($filename, '.php')) {
                continue;
            }
            if (!Str::endsWith($filename, 'Seeder.php')) {
                continue; // only list seeder classes
            }

            $relative = str_replace($path . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $classBase = str_replace(['/', '\\'], '\\', substr($relative, 0, -4)); // strip .php
            $fqcn = 'Database\\Seeders\\' . $classBase;

            if (class_exists($fqcn) && !isset($seen[$fqcn])) {
                $seen[$fqcn] = true;
                $pretty = preg_replace('/([a-z])([A-Z])/', '$1 $2', basename($classBase));
                // Show subnamespace as prefix if present
                $nsPrefix = trim(str_replace('\\' . basename($classBase), '', $classBase), '\\');
                if ($nsPrefix !== '') {
                    $pretty = str_replace('\\', ' / ', $nsPrefix) . ' / ' . $pretty;
                }
                $seeders[] = [
                    'name' => $pretty,
                    'class' => $fqcn,
                ];
            }
        }

        // Sort by name for stable UI
        usort($seeders, fn($a, $b) => strcmp($a['name'], $b['name']));
        return $seeders;
    }
}

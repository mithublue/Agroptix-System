<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EcoProcessController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

// Test route for debugging
Route::get('/test-eco-process-create/{batch}', [EcoProcessController::class, 'create'])
    ->name('test.eco-process.create');

// Test route - basic route test
Route::get('/test-route', function () {
    return 'Test route is working!';
});

// Debug route - remove after checking
Route::get('/debug-permissions', function () {
    $user = auth()->user();

    if (!$user) {
        return response()->json([
            'authenticated' => false,
            'message' => 'No authenticated user',
            'session' => session()->all(),
            'auth_check' => auth()->check()
        ]);
    }

    return response()->json([
        'authenticated' => true,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified' => (bool) $user->email_verified_at,
        ],
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getAllPermissions()->pluck('name'),
        'can_create_source' => $user->can('create_source'),
        'session' => [
            'auth' => session('auth'),
            'permissions' => session('permissions')
        ]
    ]);
})->middleware('auth');

Route::get('/debug/permissions', function () {
    $user = auth()->check() ? auth()->user() : User::first();

    if (!$user) {
        return 'No user found';
    }

    return [
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getAllPermissions()->pluck('name'),
        'can_create_delivery' => $user->can('create', \App\Models\Delivery::class),
        'policies' => [
            'create' => $user->can('create', \App\Models\Delivery::class),
            'view' => $user->can('view', \App\Models\Delivery::class),
        ]
    ];
})->middleware('web');

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// Source status update route
Route::patch('/sources/{source}/status', [\App\Http\Controllers\SourceController::class, 'updateStatus'])
    ->name('sources.update.status')
    ->middleware('can:manage_source');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/products', [ProfileController::class, 'updateProducts'])->name('profile.products.update');

    // Profile Setup Wizard
    Route::get('/profile/setup', [\App\Http\Controllers\ProfileController::class, 'showSetupWizard'])->name('profile.setup');
    Route::post('/profile/setup', [\App\Http\Controllers\ProfileController::class, 'saveSetupWizard'])->name('profile.setup.save');

    Route::middleware(['can:create_source'])->group(function () {
        Route::post('sources', [App\Http\Controllers\SourceController::class, 'store'])->name('sources.store');
        Route::get('sources/create', [App\Http\Controllers\SourceController::class, 'create'])->name('sources.create');
    });
    // Protected routes with permission middleware
    Route::middleware(['can:view_source'])->group(function () {
        Route::get('sources', [App\Http\Controllers\SourceController::class, 'index'])->name('sources.index');
        Route::get('sources/{source}', [App\Http\Controllers\SourceController::class, 'show'])->name('sources.show');
    });

    // Bulk destroy for sources (place before parameterized delete route)
    Route::delete('sources/bulk-destroy', [App\Http\Controllers\SourceController::class, 'bulkDestroy'])
        ->name('sources.bulk-destroy')
        ->middleware('can:delete_source');

    Route::middleware(['can:edit_source'])->group(function () {
        Route::get('sources/{source}/edit', [\App\Http\Controllers\SourceController::class, 'edit'])->name('sources.edit');
        Route::put('sources/{source}', [\App\Http\Controllers\SourceController::class, 'update'])->name('sources.update');
    });

    Route::middleware(['can:delete_source'])->delete('sources/{source}', [\App\Http\Controllers\SourceController::class, 'destroy'])->name('sources.destroy');

    // Certifications
    Route::post('certifications', [\App\Http\Controllers\CertificationController::class, 'store'])->name('certifications.store');
    Route::delete('certifications/{certification}', [\App\Http\Controllers\CertificationController::class, 'destroy'])->name('certifications.destroy');

    // Products
    Route::middleware(['can:create_product'])->group(function () {
        Route::get('products/create', [\App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
        Route::post('products', [\App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
    });
    Route::middleware(['can:view_product'])->group(function () {
        Route::get('products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
        Route::get('products/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
    });

    // Bulk destroy for products (place before parameterized delete route)
    Route::delete('products/bulk-destroy', [\App\Http\Controllers\ProductController::class, 'bulkDestroy'])
        ->name('products.bulk-destroy')
        ->middleware('can:delete_product');

    Route::middleware(['can:edit_product'])->group(function () {
        Route::get('products/{product}/edit', [\App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [\App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
    });

    Route::middleware(['can:delete_product'])->delete('products/{product}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');

    // Product status update route
    Route::patch('/products/{product}/status', [\App\Http\Controllers\ProductController::class, 'updateStatus'])
        ->name('products.update.status')
        ->middleware('can:edit_product');

    // AJAX endpoints for dynamic selects (TomSelect)
    Route::get('ajax/products/by-owner', [\App\Http\Controllers\ProductController::class, 'listByOwner'])
        ->name('ajax.products.by-owner');
    Route::get('ajax/sources/by-owner', [\App\Http\Controllers\SourceController::class, 'listByOwner'])
        ->name('ajax.sources.by-owner')
        ->middleware('can:view_source');
    Route::get('ajax/producers', [\App\Http\Controllers\ProducerController::class, 'list'])
        ->name('ajax.producers')
        ->middleware('can:create_batch');

    // Users AJAX (for admin selects)
    Route::get('ajax/users', [\App\Http\Controllers\UserAjaxController::class, 'list'])
        ->name('ajax.users')
        ->middleware('can:manage_users');

    // Batches
    Route::middleware(['can:create_batch'])->group(function () {
        Route::get('batches/create', [\App\Http\Controllers\BatchController::class, 'create'])->name('batches.create');
        Route::post('batches', [\App\Http\Controllers\BatchController::class, 'store'])->name('batches.store');
    });
    Route::middleware(['can:view_batch'])->group(function () {
        Route::get('batches', [\App\Http\Controllers\BatchController::class, 'index'])->name('batches.index');
        Route::get('batches/{batch}', [\App\Http\Controllers\BatchController::class, 'show'])->name('batches.show');
    });

    // Bulk destroy for batches (place before parameterized delete route)
    Route::delete('batches/bulk-destroy', [\App\Http\Controllers\BatchController::class, 'bulkDestroy'])
        ->name('batches.bulk-destroy')
        ->middleware('can:delete_batch');

    Route::middleware(['can:edit_batch'])->group(function () {
        Route::get('batches/{batch}/edit', [\App\Http\Controllers\BatchController::class, 'edit'])->name('batches.edit');
        Route::put('batches/{batch}', [\App\Http\Controllers\BatchController::class, 'update'])->name('batches.update');
    });

    Route::middleware(['can:delete_batch'])->delete('batches/{batch}', [\App\Http\Controllers\BatchController::class, 'destroy'])->name('batches.destroy');

    // Quality Tests
    Route::middleware(['can:view_quality_test'])->group(function () {
        Route::get('quality-tests/batches', [App\Http\Controllers\QualityTestController::class, 'batchList'])->name('quality-tests.batchList');
        Route::get('batches/{batch}/qualitytests', [App\Http\Controllers\QualityTestController::class, 'getTestsForBatch'])->name('batches.quality-tests.index');
    });
    Route::prefix('batches/{batch}')->group(function () {
        // Create routes
        Route::middleware(['can:create_quality_test'])->group(function () {
            Route::get('quality-tests/create', [\App\Http\Controllers\QualityTestController::class, 'create'])->name('quality-tests.create');
            Route::post('quality-tests/upload-certificate', [\App\Http\Controllers\QualityTestController::class, 'uploadCertificate'])->name('quality-tests.upload-certificate');
            Route::post('quality-tests', [\App\Http\Controllers\QualityTestController::class, 'store'])->name('quality-tests.store');
        });

        // View routes
        Route::middleware(['can:view_quality_test'])->group(function () {
            Route::get('quality-tests', [App\Http\Controllers\QualityTestController::class, 'index'])->name('quality-tests.index');
            Route::get('quality-tests/{qualityTest}', [App\Http\Controllers\QualityTestController::class, 'show'])->name('quality-tests.show');
        });

        // Edit/Update routes
        Route::middleware(['can:edit_quality_test'])->group(function () {
            Route::get('quality-tests/{qualityTest}/edit', [App\Http\Controllers\QualityTestController::class, 'edit'])->name('quality-tests.edit');
            Route::put('quality-tests/{qualityTest}', [App\Http\Controllers\QualityTestController::class, 'update'])->name('quality-tests.update');
        });

        Route::middleware(['can:delete_quality_test'])->group(function () {
            Route::middleware(['can:delete_quality_test'])->delete('quality-tests/{quality_test}', [App\Http\Controllers\QualityTestController::class, 'destroy'])->name('quality-tests.destroy');

            Route::post('quality-tests/delete-certificate', [App\Http\Controllers\QualityTestController::class, 'deleteCertificate'])->name('quality-tests.delete-certificate');
        });

        Route::post('quality-tests/ready', [\App\Http\Controllers\QualityTestController::class, 'markReadyForPackaging'])
            ->name('quality-tests.ready')
            ->middleware(['can:create_quality_test']);
    });

    // Shipments
    Route::middleware(['can:create_shipment'])->group(function () {
        Route::get('shipments/create', [App\Http\Controllers\ShipmentController::class, 'create'])->name('shipments.create');
        Route::post('shipments', [App\Http\Controllers\ShipmentController::class, 'store'])->name('shipments.store');
    });

    Route::middleware(['can:view_shipment'])->group(function () {
        Route::get('shipments', [App\Http\Controllers\ShipmentController::class, 'index'])->name('shipments.index');
        Route::get('shipments/{shipment}', [App\Http\Controllers\ShipmentController::class, 'show'])->name('shipments.show');
        Route::post('shipments/render-details', [App\Http\Controllers\ShipmentController::class, 'renderDetails'])->name('shipments.render-details');
    });

    Route::middleware(['can:edit_shipment'])->group(function () {
        Route::get('shipments/{shipment}/edit', [App\Http\Controllers\ShipmentController::class, 'edit'])->name('shipments.edit');
        Route::put('shipments/{shipment}', [App\Http\Controllers\ShipmentController::class, 'update'])->name('shipments.update');
    });

    Route::middleware(['can:delete_shipment'])->delete('shipments/{shipment}', [App\Http\Controllers\ShipmentController::class, 'destroy'])->name('shipments.destroy');

    // Batches
    Route::middleware(['can:create_batch'])->group(function () {
        Route::get('batches/create', [App\Http\Controllers\BatchController::class, 'create'])->name('batches.create');
        Route::post('batches', [App\Http\Controllers\BatchController::class, 'store'])->name('batches.store');
    });

    Route::middleware(['can:view_batch'])->group(function () {
        Route::get('batches', [App\Http\Controllers\BatchController::class, 'index'])->name('batches.index');
        Route::get('batches/{batch}', [App\Http\Controllers\BatchController::class, 'show'])->name('batches.show');
    });

    Route::middleware(['can:delete_batch'])->delete('batches/{batch}', [App\Http\Controllers\BatchController::class, 'destroy'])->name('batches.destroy');

    // Eco Processes
    Route::prefix('batches/{batch}')->middleware(['can:view_batch'])->group(function () {
        // Timeline routes
        Route::get('/timeline', [\App\Http\Controllers\BatchController::class, 'showTimeline'])
            ->name('batches.timeline');

        // QR Code route
        Route::get('/qrcode', [\App\Http\Controllers\BatchController::class, 'showQrCode'])
            ->name('batches.qr-code');

        Route::get('/eco-processes', [\App\Http\Controllers\EcoProcessController::class, 'index'])
            ->name('batches.eco-processes.index');

        Route::get('/eco-processes/{ecoProcess}', [\App\Http\Controllers\EcoProcessController::class, 'show'])
            ->name('batches.eco-processes.show')
            ->where('ecoProcess', '[0-9]+');

        Route::middleware(['can:create_batch'])->group(function () {
            Route::get('/eco-processes/create', [\App\Http\Controllers\EcoProcessController::class, 'create'])
                ->name('batches.eco-processes.create');

            Route::post('/eco-processes', [\App\Http\Controllers\EcoProcessController::class, 'store'])
                ->name('batches.eco-processes.store');

        });
        // Update status route
        Route::patch('/eco-processes/{ecoProcess}/status', [\App\Http\Controllers\EcoProcessController::class, 'updateStatus'])
            ->name('batches.eco-processes.status.update')
            ->middleware(['auth', 'can:manage_batch']);

        Route::delete('/eco-processes/{ecoProcess}', [\App\Http\Controllers\EcoProcessController::class, 'destroy'])
            ->name('batches.eco-processes.destroy')
            ->middleware(['auth', 'can:delete_batch']);

        Route::middleware(['can:edit_batch'])->group(function () {
            Route::get('/eco-processes/{ecoProcess}/edit', [\App\Http\Controllers\EcoProcessController::class, 'edit'])
                ->name('batches.eco-processes.edit');
            Route::put('/eco-processes/{ecoProcess}', [\App\Http\Controllers\EcoProcessController::class, 'update'])
                ->name('batches.eco-processes.update');
        });

    });
});

// Conversations & Messages
Route::middleware(['auth'])->group(function () {
    // Conversations
    Route::get('conversations', [\App\Http\Controllers\ConversationController::class, 'index'])
        ->name('conversations.index');
    Route::post('conversations', [\App\Http\Controllers\ConversationController::class, 'store'])
        ->name('conversations.store');
    Route::get('conversations/{conversation}', [\App\Http\Controllers\ConversationController::class, 'show'])
        ->name('conversations.show');

    // Messages
    Route::post('conversations/{conversation}/messages', [\App\Http\Controllers\MessageController::class, 'store'])
        ->name('conversations.messages.store');
});

// Phone OTP verification routes
Route::middleware(['auth'])->group(function () {
    Route::get('/verify-phone', [\App\Http\Controllers\Auth\PhoneVerificationController::class, 'show'])->name('auth.phone.verify.form');
    Route::post('/verify-phone', [\App\Http\Controllers\Auth\PhoneVerificationController::class, 'verify'])->name('auth.phone.verify');
});

require __DIR__ . '/auth.php';

// RPC Units
Route::middleware(['auth'])->group(function () {
    Route::resource('rpcunit', \App\Http\Controllers\RpcUnitController::class)->names('rpcunit');
});

// Farmers Registration
Route::get('/farmers/register', [\App\Http\Controllers\RegistrationController::class, 'create_farmer'])->name('farmers.create');
Route::post('/farmers', [\App\Http\Controllers\RegistrationController::class, 'store_farmer'])->name('farmers.store');

// Debug route to check permissions
Route::get('/debug-permissions', function () {
    $user = auth()->user();

    if (!$user) {
        return response()->json([
            'authenticated' => false,
            'message' => 'No authenticated user',
            'session' => session()->all(),
            'auth_check' => auth()->check(),
            'can_create_batch' => false,
            'permissions' => []
        ]);
    }

    return response()->json([
        'authenticated' => true,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'can_create_batch' => $user->can('create_batch')
        ]
    ]);

    return response()->json([
        'authenticated' => true,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified' => (bool) $user->email_verified_at,
        ],
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getAllPermissions()->pluck('name'),
        'can_manage_roles' => $user->can('manage_roles'),
        'can_manage_users' => $user->can('manage_users'),
        'can_manage_permissions' => $user->can('manage_permissions'),
        'session' => [
            'auth' => session('auth'),
            'permissions' => session('permissions')
        ]
    ]);
})->middleware('auth');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Live Monitoring
    // Live Monitoring (Legacy/Demo Dashboard)
    Route::get('/live-monitoring', [\App\Http\Controllers\Admin\LiveMonitoringController::class, 'index'])
        ->name('live-monitoring.index')
        ->middleware('can:view_monitoring');

    // Live Tracking (Real GPS Map)
    Route::get('/live-monitoring/map', [\App\Http\Controllers\Admin\LiveTrackingController::class, 'index'])
        ->name('live-monitoring.map')
        ->middleware('can:view_monitoring');

    Route::get('/live-monitoring/api', [\App\Http\Controllers\Admin\LiveTrackingController::class, 'apiLocations'])
        ->name('live-monitoring.api')
        ->middleware('can:view_monitoring');

    Route::post('/live-monitoring/update/{shipment}', [\App\Http\Controllers\Admin\LiveTrackingController::class, 'updateLocation'])
        ->name('live-monitoring.update')
        ->middleware('can:view_monitoring');

    // Users
    Route::middleware(['can:manage_users'])->group(function () {
        // Place static routes BEFORE resource to avoid collision with users/{user}
        Route::delete('users/bulk-destroy', [\App\Http\Controllers\Admin\UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);
        Route::post('users/{user}/status', [\App\Http\Controllers\Admin\UserController::class, 'updateStatus'])->name('users.status');
    });

    // Roles
    Route::middleware(['can:manage_roles'])->group(function () {
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    });

    // Batches
    Route::resource('batches', \App\Http\Controllers\BatchController::class);

    // Traceability Routes
    Route::middleware(['auth'])->group(function () {
        // Batch Traceability
        Route::prefix('batches/{batch}')->group(function () {
            // Batch Timeline
            Route::get('timeline', [\App\Http\Controllers\BatchController::class, 'showTimeline'])
                ->name('batches.timeline');

            // Batch QR Code
            Route::get('qrcode', [\App\Http\Controllers\BatchController::class, 'showQrCode'])
                ->name('batches.qr-code');

            // Batch Trace Events
            Route::get('trace-events', [\App\Http\Controllers\BatchController::class, 'listTraceEvents'])
                ->name('batches.trace-events');
        });

        // QR Code Scanner
        Route::get('/qr-scanner', [\App\Http\Controllers\QrCodeController::class, 'showScanner'])
            ->name('qr-scanner');

        // Handle QR Code Scan
        Route::post('/qr-scan', [\App\Http\Controllers\QrCodeController::class, 'handleScan'])
            ->name('qr-scan');
    });

    // Packaging
    Route::middleware(['can:view_packaging'])->group(function () {
        Route::resource('packaging', \App\Http\Controllers\PackagingController::class);

        // Explicitly define the update route to ensure it's included
        Route::put('packaging/{packaging}', [\App\Http\Controllers\PackagingController::class, 'update'])
            ->name('packaging.update')
            ->middleware('can:edit_packaging');

        // Additional routes for packaging
        Route::post('packaging/import', [\App\Http\Controllers\PackagingController::class, 'import'])
            ->name('packaging.import')
            ->middleware('can:import_packaging');

        Route::get('packaging/export', [\App\Http\Controllers\PackagingController::class, 'export'])
            ->name('packaging.export')
            ->middleware('can:export_packaging');
    });

    // Options
    Route::middleware(['can:manage_options'])->group(function () {
        Route::resource('options', App\Http\Controllers\Admin\OptionController::class)->only(['index', 'edit', 'update']);
        Route::post('options/save-user-options', [\App\Http\Controllers\Admin\OptionController::class, 'saveUserOptions'])->name('options.saveUserOptions')->middleware(['auth', 'can:manage_options']);
        Route::get('options/general', [\App\Http\Controllers\Admin\OptionController::class, 'generalSettings'])->name('options.general');
        Route::post('options/general', [\App\Http\Controllers\Admin\OptionController::class, 'saveGeneralSettings'])->name('options.saveGeneralSettings');

        // Compliance Standards
        Route::resource('compliance-standards', \App\Http\Controllers\ComplianceStandardController::class);
    });

    // Permissions
    Route::middleware(['can:manage_permissions'])->group(function () {
        Route::get('permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])
            ->name('permissions.index');
    });

    // Development: Interactive Seeder UI (local only)
    Route::prefix('dev')->middleware(['auth', 'can:manage_options'])->group(function () {
        Route::get('seeder', [\App\Http\Controllers\Admin\SeedController::class, 'index'])->name('dev.seeder.index');
        Route::get('seeder/list', [\App\Http\Controllers\Admin\SeedController::class, 'list'])->name('dev.seeder.list');
        Route::post('seeder/run', [\App\Http\Controllers\Admin\SeedController::class, 'runOne'])->name('dev.seeder.run');
    });
});

// System Migration (requires system key from .env) - Public route for fresh installations
// Outside web middleware to avoid session errors when database tables don't exist yet
Route::get('admin/system', function () {
    return redirect()->route('admin.system.migrate');
});
Route::prefix('admin/system')->name('admin.system.')->group(function () {
    Route::get('migrate', [\App\Http\Controllers\Admin\SystemMigrationController::class, 'showForm'])->name('migrate');
    Route::post('migrate', [\App\Http\Controllers\Admin\SystemMigrationController::class, 'migrate'])->name('migrate.run');
});

// Batch status update route - outside admin prefix but still protected by auth and permission
Route::patch('batches/{batch}/status', [\App\Http\Controllers\BatchController::class, 'updateStatus'])
    ->name('batches.status.update')
    ->middleware(['auth', 'can:manage_batch']);

// Delivery Routes
Route::middleware(['auth'])->group(function () {
    // Resource routes for Deliveries
    Route::resource('deliveries', \App\Http\Controllers\DeliveryController::class)->except(['show']);

    // AJAX route for rendering delivery details
    Route::post('deliveries/render-details', [\App\Http\Controllers\DeliveryController::class, 'renderDetails'])
        ->name('deliveries.render-details');

    // Custom delivery routes
    Route::prefix('deliveries')->group(function () {
        // Update delivery status
        Route::patch('{delivery}/status', [\App\Http\Controllers\DeliveryController::class, 'updateStatus'])
            ->name('deliveries.status.update');

        // Show delivery details (using GET for better readability in URLs)
        Route::get('show/{delivery}', [\App\Http\Controllers\DeliveryController::class, 'show'])
            ->name('deliveries.show');
    });
});

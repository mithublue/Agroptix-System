<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

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
            'email_verified' => (bool)$user->email_verified_at,
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['can:create_source'])->group(function () {
        Route::post('sources', [App\Http\Controllers\SourceController::class, 'store'])->name('sources.store');
        Route::get('sources/create', [App\Http\Controllers\SourceController::class, 'create'])->name('sources.create');
    });
    // Protected routes with permission middleware
    Route::middleware(['can:view_source'])->group(function () {
        Route::get('sources', [App\Http\Controllers\SourceController::class, 'index'])->name('sources.index');
        Route::get('sources/{source}', [App\Http\Controllers\SourceController::class, 'show'])->name('sources.show');
    });

    Route::middleware(['can:edit_source'])->group(function () {
        Route::get('sources/{source}/edit', [\App\Http\Controllers\SourceController::class, 'edit'])->name('sources.edit');
        Route::put('sources/{source}', [\App\Http\Controllers\SourceController::class, 'update'])->name('sources.update');
    });

    Route::middleware(['can:delete_source'])->delete('sources/{source}', [\App\Http\Controllers\SourceController::class, 'destroy'])->name('sources.destroy');

    // Products
    Route::middleware(['can:create_product'])->group(function () {
        Route::get('products/create', [\App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
        Route::post('products', [\App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
    });
    Route::middleware(['can:view_product'])->group(function () {
        Route::get('products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
        Route::get('products/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
    });

    Route::middleware(['can:edit_product'])->group(function () {
        Route::get('products/{product}/edit', [\App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [\App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
    });

    Route::middleware(['can:delete_product'])->delete('products/{product}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');

    // Batches
    Route::middleware(['can:create_batch'])->group(function () {
        Route::get('batches/create', [\App\Http\Controllers\BatchController::class, 'create'])->name('batches.create');
        Route::post('batches', [\App\Http\Controllers\BatchController::class, 'store'])->name('batches.store');
    });
    Route::middleware(['can:view_batch'])->group(function () {
        Route::get('batches', [\App\Http\Controllers\BatchController::class, 'index'])->name('batches.index');
        Route::get('batches/{batch}', [\App\Http\Controllers\BatchController::class, 'show'])->name('batches.show');
    });

    Route::middleware(['can:edit_batch'])->group(function () {
        Route::get('batches/{batch}/edit', [\App\Http\Controllers\BatchController::class, 'edit'])->name('batches.edit');
        Route::put('batches/{batch}', [\App\Http\Controllers\BatchController::class, 'update'])->name('batches.update');
    });

    Route::middleware(['can:delete_batch'])->delete('batches/{batch}', [\App\Http\Controllers\BatchController::class, 'destroy'])->name('batches.destroy');

    // Quality Tests
    Route::middleware(['can:create_quality_test'])->group(function () {
        Route::get('quality-tests/create', [\App\Http\Controllers\QualityTestController::class, 'create'])->name('quality-tests.create');
        Route::post('quality-tests', [\App\Http\Controllers\QualityTestController::class, 'store'])->name('quality-tests.store');
    });
    Route::middleware(['can:view_quality_test'])->group(function () {
        Route::get('quality-tests', [App\Http\Controllers\QualityTestController::class, 'index'])->name('quality-tests.index');
        Route::get('quality-tests/{quality_test}', [App\Http\Controllers\QualityTestController::class, 'show'])->name('quality-tests.show');
    });

    Route::middleware(['can:edit_quality_test'])->group(function () {
        Route::get('quality-tests/{quality_test}/edit', [App\Http\Controllers\QualityTestController::class, 'edit'])->name('quality-tests.edit');
        Route::put('quality-tests/{quality_test}', [App\Http\Controllers\QualityTestController::class, 'update'])->name('quality-tests.update');
    });

    Route::middleware(['can:delete_quality_test'])->delete('quality-tests/{quality_test}', [App\Http\Controllers\QualityTestController::class, 'destroy'])->name('quality-tests.destroy');

    // Shipments
    Route::middleware(['can:create_shipment'])->group(function () {
        Route::get('shipments/create', [App\Http\Controllers\ShipmentController::class, 'create'])->name('shipments.create');
        Route::post('shipments', [App\Http\Controllers\ShipmentController::class, 'store'])->name('shipments.store');
    });
    Route::middleware(['can:view_shipment'])->group(function () {
        Route::get('shipments', [App\Http\Controllers\ShipmentController::class, 'index'])->name('shipments.index');
        Route::get('shipments/{shipment}', [App\Http\Controllers\ShipmentController::class, 'show'])->name('shipments.show');
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

    Route::middleware(['can:edit_batch'])->group(function () {
        Route::get('batches/{batch}/edit', [App\Http\Controllers\BatchController::class, 'edit'])->name('batches.edit');
        Route::put('batches/{batch}', [App\Http\Controllers\BatchController::class, 'update'])->name('batches.update');
    });

    Route::middleware(['can:delete_batch'])->delete('batches/{batch}', [App\Http\Controllers\BatchController::class, 'destroy'])->name('batches.destroy');

    // Eco Processes
    Route::prefix('batches/{batch}')->middleware(['can:view_batch'])->group(function () {
        Route::get('/eco-processes', [\App\Http\Controllers\EcoProcessController::class, 'index'])
            ->name('batches.eco-processes.index');
            
        Route::middleware(['can:create_batch'])->group(function () {
            Route::get('/eco-processes/create', [\App\Http\Controllers\EcoProcessController::class, 'create'])
                ->name('batches.eco-processes.create');
                
            Route::post('/eco-processes', [\App\Http\Controllers\EcoProcessController::class, 'store'])
                ->name('batches.eco-processes.store');
                
            Route::get('/eco-processes/{ecoProcess}/edit', [\App\Http\Controllers\EcoProcessController::class, 'edit'])
                ->name('batches.eco-processes.edit');
                
            Route::put('/eco-processes/{ecoProcess}', [\App\Http\Controllers\EcoProcessController::class, 'update'])
                ->name('batches.eco-processes.update');
        });
    });
});

require __DIR__.'/auth.php';

// Debug route to check permissions
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
            'email_verified' => (bool)$user->email_verified_at,
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
    // Users
    Route::middleware(['can:manage_users'])->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);
    });
    
    // Roles
    Route::middleware(['can:manage_roles'])->group(function () {
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
        
        Route::resource('batches', \App\Http\Controllers\BatchController::class);

    });
    
    // Permissions
    Route::middleware(['can:manage_permissions'])->group(function () {
        Route::get('permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])
             ->name('permissions.index');
    });
});

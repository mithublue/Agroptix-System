<?php

use App\Http\Controllers\Api\TraceabilityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->group(function () {
    // Traceability routes
    Route::prefix('traceability')->group(function () {
        // Get batch timeline
        Route::get('batch/{traceCode}/timeline', [TraceabilityController::class, 'getBatchTimeline'])
            ->name('api.traceability.batch.timeline');
            
        // Log a new trace event
        Route::post('events', [TraceabilityController::class, 'logEvent'])
            ->name('api.traceability.events.store');
            
        // Get QR code for a batch
        Route::get('batch/{traceCode}/qrcode', [TraceabilityController::class, 'getQrCode'])
            ->name('api.traceability.batch.qrcode');
            
        // Verify batch integrity
        Route::get('batch/{traceCode}/verify', [TraceabilityController::class, 'verifyIntegrity'])
            ->name('api.traceability.batch.verify');
            
        // Handle QR/barcode scan and trigger event
        Route::post('scan', [TraceabilityController::class, 'handleScan'])
            ->name('api.traceability.scan');
    });
});

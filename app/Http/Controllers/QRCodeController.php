<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class QRCodeController extends Controller
{
    /**
     * Display the QR code scanner interface.
     *
     * @return \Inertia\Response
     */
    public function scanner(): Response
    {
        return Inertia::render('QRCode/Scanner', [
            'title' => 'Scan Batch QR Code',
            'description' => 'Scan a batch QR code to view its timeline and details.',
        ]);
    }

    /**
     * Process a scanned QR code and redirect to the batch timeline.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
        ]);

        // Try to find the batch by trace code or ID
        $batch = Batch::where('trace_code', $request->code)
            ->orWhere('id', $request->code)
            ->first();

        if (!$batch) {
            return redirect()->back()->with([
                'error' => 'Batch not found. Please check the QR code and try again.',
                'scannedCode' => $request->code,
            ]);
        }

        // Check if the user has permission to view this batch
        if (!auth()->user()->can('view', $batch)) {
            return redirect()->back()->with([
                'error' => 'You do not have permission to view this batch.',
                'scannedCode' => $request->code,
            ]);
        }

        // Redirect to the batch timeline
        return redirect()->route('batches.timeline', $batch->trace_code);
    }

    /**
     * Generate a QR code for a batch.
     *
     * @param  string  $traceCode
     * @return \Illuminate\Http\Response
     */
    public function generate(string $traceCode)
    {
        // Find the batch by trace code
        $batch = Batch::where('trace_code', $traceCode)->firstOrFail();

        // Authorize that the user can view this batch
        $this->authorize('view', $batch);

        // Generate the URL for this batch's timeline
        $url = URL::route('batches.timeline', $batch->trace_code, true);
        
        // Generate the QR code as SVG
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->generate($url);

        // Return the QR code as a response
        return response($qrCode, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'inline; filename="batch-' . $batch->trace_code . '-qrcode.svg"',
        ]);
    }

    /**
     * Display a batch's QR code in a printable format.
     *
     * @param  string  $traceCode
     * @return \Inertia\Response
     */
    public function show(string $traceCode): Response
    {
        // Find the batch by trace code
        $batch = Batch::where('trace_code', $traceCode)->firstOrFail();

        // Authorize that the user can view this batch
        $this->authorize('view', $batch);

        // Generate the QR code URL
        $qrCodeUrl = route('qrcode.generate', $batch->trace_code);
        
        // Generate a short URL for the batch
        $shortUrl = URL::route('batches.timeline', $batch->trace_code, false);
        
        return Inertia::render('QRCode/Show', [
            'batch' => [
                'id' => $batch->id,
                'trace_code' => $batch->trace_code,
                'name' => $batch->name,
                'status' => $batch->status,
                'created_at' => $batch->created_at->toDateTimeString(),
                'product' => $batch->product ? [
                    'name' => $batch->product->name,
                    'sku' => $batch->product->sku,
                ] : null,
            ],
            'qr_code_url' => $qrCodeUrl,
            'short_url' => $shortUrl,
            'download_url' => route('qrcode.download', $batch->trace_code),
        ]);
    }

    /**
     * Download a batch's QR code as an SVG file.
     *
     * @param  string  $traceCode
     * @return \Illuminate\Http\Response
     */
    public function download(string $traceCode)
    {
        // Find the batch by trace code
        $batch = Batch::where('trace_code', $traceCode)->firstOrFail();

        // Authorize that the user can view this batch
        $this->authorize('view', $batch);

        // Generate the URL for this batch's timeline
        $url = URL::route('batches.timeline', $batch->trace_code, true);
        
        // Generate the QR code as SVG
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->generate($url);

        // Generate a filename
        $filename = 'batch-' . $batch->trace_code . '-qrcode.svg';

        // Return the QR code as a downloadable file
        return response($qrCode, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generate a QR code for a batch and return it as a data URL.
     * This is useful for embedding the QR code directly in HTML.
     *
     * @param  string  $traceCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQrCodeDataUrl(string $traceCode)
    {
        // Find the batch by trace code
        $batch = Batch::where('trace_code', $traceCode)->firstOrFail();

        // Authorize that the user can view this batch
        $this->authorize('view', $batch);

        // Generate the URL for this batch's timeline
        $url = URL::route('batches.timeline', $batch->trace_code, true);
        
        // Generate the QR code as SVG
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->generate($url);

        // Convert to base64 data URL
        $dataUrl = 'data:image/svg+xml;base64,' . base64_encode($qrCode);

        return response()->json([
            'success' => true,
            'data_url' => $dataUrl,
            'batch' => [
                'id' => $batch->id,
                'trace_code' => $batch->trace_code,
                'name' => $batch->name,
            ],
        ]);
    }
}

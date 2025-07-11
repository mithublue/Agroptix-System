<?php

namespace App\Http\Controllers;

use App\Models\Source;
use App\Models\Product;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Source statistics
        $totalSources = Source::count();
        $perishableSources = Source::where('type', 'perishable')->count();
        $nonPerishableSources = Source::where('type', 'non_perishable')->count();

        // Product statistics
        $totalProducts = Product::count();
        // Handle both string and boolean values for is_perishable
        $perishableProducts = Product::where(function($query) {
            $query->where('is_perishable', '1')
                  ->orWhere('is_perishable', 'true')
                  ->orWhere('is_perishable', 'yes');
        })->count();
        $nonPerishableProducts = $totalProducts - $perishableProducts;

        // Batch statistics
        $totalBatches = Batch::count();
        $processingBatches = Batch::where('status', 'processing')->count();
        $completedBatches = Batch::where('status', 'completed')->count();
        $pendingBatches = Batch::where('status', 'pending')->count();

        return view('dashboard', [
            'totalSources' => $totalSources,
            'perishableSources' => $perishableSources,
            'nonPerishableSources' => $nonPerishableSources,
            'totalProducts' => $totalProducts,
            'perishableProducts' => $perishableProducts,
            'nonPerishableProducts' => $nonPerishableProducts,
            'totalBatches' => $totalBatches,
            'processingBatches' => $processingBatches,
            'completedBatches' => $completedBatches,
            'pendingBatches' => $pendingBatches
        ]);
    }
}

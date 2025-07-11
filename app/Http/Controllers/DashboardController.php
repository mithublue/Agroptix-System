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
        // Consider products with is_perishable = 1/true/yes as Active, others as Inactive
        $activeProducts = Product::where(function($query) {
            $query->where('is_perishable', '1')
                  ->orWhere('is_perishable', 'true')
                  ->orWhere('is_perishable', 'yes');
        })->count();
        $inactiveProducts = $totalProducts - $activeProducts;

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
            'activeProducts' => $activeProducts,
            'inactiveProducts' => $inactiveProducts,
            'totalBatches' => $totalBatches,
            'processingBatches' => $processingBatches,
            'completedBatches' => $completedBatches,
            'pendingBatches' => $pendingBatches
        ]);
    }
}

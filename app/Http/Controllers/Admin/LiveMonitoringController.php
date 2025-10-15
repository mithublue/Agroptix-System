<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LiveMonitoringController extends Controller
{
    public function index()
    {
        // In a real application, you would fetch this data from your models
        $metrics = [
            'activeBatches' => 24,
            'qualityTestsToday' => 156,
            'issuesDetected' => 3
        ];

        return view('admin.live-monitoring.index', compact('metrics'));
    }
}

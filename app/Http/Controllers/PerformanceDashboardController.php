<?php

namespace App\Http\Controllers;

use App\Services\DashboardDataService;
use Illuminate\Http\Request;

class PerformanceDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardDataService $dashboardService)
    {
        $this->middleware(['auth', 'permission:dashboard.performance.view']);

        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $dashboardData = $this->dashboardService->getPerformanceDashboardData();

        return view('dashboard.performance.index', compact('dashboardData'));
    }

    public function data(Request $request)
    {
        $dashboardData = $this->dashboardService->getPerformanceDashboardData();

        return response()->json($dashboardData);
    }
}

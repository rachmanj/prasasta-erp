<?php

namespace App\Http\Controllers;

use App\Services\DashboardDataService;
use Illuminate\Http\Request;

class ExecutiveDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardDataService $dashboardService)
    {
        $this->middleware(['auth', 'permission:dashboard.executive.view']);

        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $dashboardData = $this->dashboardService->getExecutiveDashboardData();

        return view('dashboard.executive.index', compact('dashboardData'));
    }

    public function data(Request $request)
    {
        $dashboardData = $this->dashboardService->getExecutiveDashboardData();

        return response()->json($dashboardData);
    }
}

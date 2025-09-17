<?php

namespace App\Http\Controllers;

use App\Services\DashboardDataService;
use Illuminate\Http\Request;

class OperationalDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardDataService $dashboardService)
    {
        $this->middleware(['auth', 'permission:dashboard.operational.view']);

        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $dashboardData = $this->dashboardService->getOperationalDashboardData();

        return view('dashboard.operational.index', compact('dashboardData'));
    }

    public function data(Request $request)
    {
        $dashboardData = $this->dashboardService->getOperationalDashboardData();

        return response()->json($dashboardData);
    }
}

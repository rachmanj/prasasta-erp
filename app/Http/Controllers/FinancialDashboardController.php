<?php

namespace App\Http\Controllers;

use App\Services\DashboardDataService;
use Illuminate\Http\Request;

class FinancialDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardDataService $dashboardService)
    {
        $this->middleware(['auth', 'permission:dashboard.financial.view']);

        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $dashboardData = $this->dashboardService->getFinancialDashboardData();

        return view('dashboard.financial.index', compact('dashboardData'));
    }

    public function data(Request $request)
    {
        $dashboardData = $this->dashboardService->getFinancialDashboardData();

        return response()->json($dashboardData);
    }
}

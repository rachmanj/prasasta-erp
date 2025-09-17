<?php

namespace App\Http\Controllers;

use App\Services\ReportGenerationService;
use App\Services\Export\PDFExportService;
use App\Services\Export\ExcelExportService;
use App\Services\Export\CSVExportService;
use App\Jobs\GeneratePDFReportJob;
use App\Jobs\GenerateExcelReportJob;
use App\Jobs\GenerateCSVReportJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RevenueReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportGenerationService $reportService)
    {
        $this->middleware(['auth', 'permission:reports.revenue.view']);

        $this->reportService = $reportService;
    }

    public function index()
    {
        return view('reports.revenue.index');
    }

    public function recognitionReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $revenueData = $this->reportService->generateRevenueRecognitionReport($startDate, $endDate);

        if ($request->wantsJson()) {
            return response()->json($revenueData);
        }

        return view('reports.revenue.recognition', compact('revenueData', 'startDate', 'endDate'));
    }

    public function deferredRevenueReport(Request $request)
    {
        $deferredRevenue = \App\Models\RevenueRecognition::where('type', 'deferred')
            ->where('is_posted', false)
            ->with(['enrollment.student', 'batch.course'])
            ->get();

        $summary = [
            'total_deferred' => $deferredRevenue->sum('amount'),
            'enrollment_count' => $deferredRevenue->count(),
            'average_amount' => $deferredRevenue->count() > 0 ? $deferredRevenue->avg('amount') : 0,
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'deferred_revenue' => $deferredRevenue,
                'summary' => $summary,
                'generated_at' => Carbon::now()
            ]);
        }

        return view('reports.revenue.deferred', compact('deferredRevenue', 'summary'));
    }

    public function exportRecognitionReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $format = $request->get('format', 'pdf');
        $async = $request->get('async', false);

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $revenueData = $this->reportService->generateRevenueRecognitionReport($startDate, $endDate);

        if ($async) {
            // Queue the job for background processing
            if ($format === 'pdf') {
                GeneratePDFReportJob::dispatch('revenue_recognition', $revenueData, Auth::user()->email);
            } elseif ($format === 'excel') {
                GenerateExcelReportJob::dispatch('revenue_recognition', $revenueData, Auth::user()->email);
            } else {
                GenerateCSVReportJob::dispatch('revenue_recognition', $revenueData, Auth::user()->email);
            }
            
            return response()->json([
                'message' => strtoupper($format) . ' report generation queued. You will receive an email when ready.',
                'status' => 'queued'
            ]);
        }

        if ($format === 'pdf') {
            $pdfService = new PDFExportService();
            $filepath = $pdfService->generateRevenueRecognitionReport($revenueData);
            $filename = 'revenue_recognition_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf';
        } elseif ($format === 'excel') {
            $excelService = new ExcelExportService();
            $filepath = $excelService->exportRevenueRecognitionReport($revenueData);
            $filename = 'revenue_recognition_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        } else {
            $csvService = new CSVExportService();
            $filepath = $csvService->exportRevenueRecognitionReport($revenueData);
            $filename = 'revenue_recognition_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        }

        return response()->download($filepath, $filename);
    }

    public function exportDeferredRevenueReport(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $async = $request->get('async', false);

        $deferredRevenue = \App\Models\RevenueRecognition::where('type', 'deferred')
            ->where('posted_status', 'pending')
            ->with(['enrollment.student', 'enrollment.batch.course'])
            ->get();

        $reportData = [
            'revenue_data' => $deferredRevenue,
            'summary' => [
                'total_deferred' => $deferredRevenue->sum('amount'),
                'enrollment_count' => $deferredRevenue->count(),
                'average_amount' => $deferredRevenue->count() > 0 ? $deferredRevenue->avg('amount') : 0,
            ],
            'generated_at' => Carbon::now()
        ];

        if ($async) {
            // Queue the job for background processing
            if ($format === 'pdf') {
                GeneratePDFReportJob::dispatch('deferred_revenue', $reportData, Auth::user()->email);
            } elseif ($format === 'excel') {
                GenerateExcelReportJob::dispatch('deferred_revenue', $reportData, Auth::user()->email);
            } else {
                GenerateCSVReportJob::dispatch('deferred_revenue', $reportData, Auth::user()->email);
            }
            
            return response()->json([
                'message' => strtoupper($format) . ' report generation queued. You will receive an email when ready.',
                'status' => 'queued'
            ]);
        }

        if ($format === 'pdf') {
            $pdfService = new PDFExportService();
            $filepath = $pdfService->generateFromView('exports.pdf.deferred-revenue', [
                'data' => $reportData,
                'generated_at' => Carbon::now(),
                'company_name' => 'Prasasta ERP',
                'title' => 'Deferred Revenue Report'
            ]);
            $filename = 'deferred_revenue_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf';
        } elseif ($format === 'excel') {
            $excelService = new ExcelExportService();
            $filepath = $excelService->exportRevenueRecognitionReport($reportData);
            $filename = 'deferred_revenue_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        } else {
            $csvService = new CSVExportService();
            $filepath = $csvService->exportRevenueRecognitionReport($reportData);
            $filename = 'deferred_revenue_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        }

        return response()->download($filepath, $filename);
    }
}

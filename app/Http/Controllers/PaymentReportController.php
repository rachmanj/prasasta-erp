<?php

namespace App\Http\Controllers;

use App\Services\ReportGenerationService;
use App\Services\PaymentProcessingService;
use App\Services\Export\PDFExportService;
use App\Services\Export\ExcelExportService;
use App\Services\Export\CSVExportService;
use App\Jobs\GeneratePDFReportJob;
use App\Jobs\GenerateExcelReportJob;
use App\Jobs\GenerateCSVReportJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentReportController extends Controller
{
    protected $reportService;
    protected $paymentService;

    public function __construct(ReportGenerationService $reportService, PaymentProcessingService $paymentService)
    {
        $this->middleware(['auth', 'permission:reports.payment.view']);

        $this->reportService = $reportService;
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        return view('reports.payment.index');
    }

    public function agingReport(Request $request)
    {
        $agingData = $this->reportService->generatePaymentAgingReport();

        if ($request->wantsJson()) {
            return response()->json($agingData);
        }

        return view('reports.payment.aging', compact('agingData'));
    }

    public function collectionReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $collectionData = $this->reportService->generatePaymentCollectionReport($startDate, $endDate);

        if ($request->wantsJson()) {
            return response()->json($collectionData);
        }

        return view('reports.payment.collection', compact('collectionData', 'startDate', 'endDate'));
    }

    public function overdueReport(Request $request)
    {
        $daysOverdue = $request->get('days_overdue', null);
        $overdueData = $this->paymentService->getOverduePaymentsReport($daysOverdue);

        if ($request->wantsJson()) {
            return response()->json([
                'overdue_payments' => $overdueData,
                'total_amount' => collect($overdueData)->sum('total_amount'),
                'total_count' => count($overdueData),
                'generated_at' => Carbon::now()
            ]);
        }

        return view('reports.payment.overdue', compact('overdueData', 'daysOverdue'));
    }

    public function exportAgingReport(Request $request)
    {
        $agingData = $this->reportService->generatePaymentAgingReport();
        $format = $request->get('format', 'pdf');
        $async = $request->get('async', false);

        if ($async) {
            // Queue the job for background processing
            GeneratePDFReportJob::dispatch('payment_aging', $agingData, Auth::user()->email);
            
            return response()->json([
                'message' => 'PDF report generation queued. You will receive an email when ready.',
                'status' => 'queued'
            ]);
        }

        $pdfService = new PDFExportService();
        $filepath = $pdfService->generatePaymentAgingReport($agingData);

        return response()->download($filepath, 'payment_aging_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf');
    }

    public function exportCollectionReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $format = $request->get('format', 'excel');
        $async = $request->get('async', false);

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $collectionData = $this->reportService->generatePaymentCollectionReport($startDate, $endDate);

        if ($async) {
            // Queue the job for background processing
            if ($format === 'excel') {
                GenerateExcelReportJob::dispatch('payment_collection', $collectionData, Auth::user()->email);
            } else {
                GenerateCSVReportJob::dispatch('payment_collection', $collectionData, Auth::user()->email);
            }
            
            return response()->json([
                'message' => strtoupper($format) . ' report generation queued. You will receive an email when ready.',
                'status' => 'queued'
            ]);
        }

        if ($format === 'excel') {
            $excelService = new ExcelExportService();
            $filepath = $excelService->exportPaymentCollectionReport($collectionData);
            $filename = 'payment_collection_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        } else {
            $csvService = new CSVExportService();
            $filepath = $csvService->exportPaymentCollectionReport($collectionData);
            $filename = 'payment_collection_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        }

        return response()->download($filepath, $filename);
    }

    public function exportOverdueReport(Request $request)
    {
        $daysOverdue = $request->get('days_overdue', null);
        $format = $request->get('format', 'pdf');
        $async = $request->get('async', false);

        $overdueData = $this->paymentService->getOverduePaymentsReport($daysOverdue);
        $reportData = [
            'overdue_payments' => $overdueData,
            'total_amount' => collect($overdueData)->sum('total_amount'),
            'total_count' => count($overdueData),
            'generated_at' => Carbon::now()
        ];

        if ($async) {
            // Queue the job for background processing
            if ($format === 'pdf') {
                GeneratePDFReportJob::dispatch('payment_overdue', $reportData, Auth::user()->email);
            } elseif ($format === 'excel') {
                GenerateExcelReportJob::dispatch('payment_overdue', $reportData, Auth::user()->email);
            } else {
                GenerateCSVReportJob::dispatch('payment_overdue', $reportData, Auth::user()->email);
            }
            
            return response()->json([
                'message' => strtoupper($format) . ' report generation queued. You will receive an email when ready.',
                'status' => 'queued'
            ]);
        }

        if ($format === 'pdf') {
            $pdfService = new PDFExportService();
            $filepath = $pdfService->generateFromView('exports.pdf.payment-overdue', [
                'data' => $reportData,
                'generated_at' => Carbon::now(),
                'company_name' => 'Prasasta ERP',
                'title' => 'Overdue Payments Report'
            ]);
            $filename = 'payment_overdue_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf';
        } elseif ($format === 'excel') {
            $excelService = new ExcelExportService();
            $filepath = $excelService->exportPaymentCollectionReport($reportData);
            $filename = 'payment_overdue_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        } else {
            $csvService = new CSVExportService();
            $filepath = $csvService->exportPaymentCollectionReport($reportData);
            $filename = 'payment_overdue_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        }

        return response()->download($filepath, $filename);
    }
}

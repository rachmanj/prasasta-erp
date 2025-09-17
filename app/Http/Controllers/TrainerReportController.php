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

class TrainerReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportGenerationService $reportService)
    {
        $this->middleware(['auth', 'permission:reports.trainer.view']);

        $this->reportService = $reportService;
    }

    public function index()
    {
        return view('reports.trainer.index');
    }

    public function performanceReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $performanceData = $this->reportService->generateTrainerPerformanceReport($startDate, $endDate);

        if ($request->wantsJson()) {
            return response()->json($performanceData);
        }

        return view('reports.trainer.performance', compact('performanceData', 'startDate', 'endDate'));
    }

    public function utilizationReport(Request $request)
    {
        $trainers = \App\Models\Trainer::with(['courseBatches.enrollments', 'courseBatches.course'])
            ->where('status', 'active')
            ->get();

        $utilizationData = $trainers->map(function ($trainer) {
            $batches = $trainer->courseBatches->where('status', '!=', 'cancelled');
            $totalHours = $batches->sum(function ($batch) {
                return $batch->course->duration_hours;
            });

            $totalEnrollments = $batches->sum(function ($batch) {
                return $batch->enrollments->where('status', 'enrolled')->count();
            });

            $totalRevenue = $batches->sum(function ($batch) {
                return $batch->enrollments->sum('total_amount');
            });

            return [
                'trainer_id' => $trainer->id,
                'trainer_name' => $trainer->name,
                'trainer_type' => $trainer->type,
                'batch_count' => $batches->count(),
                'total_hours' => $totalHours,
                'total_enrollments' => $totalEnrollments,
                'total_revenue' => $totalRevenue,
                'hourly_rate' => $trainer->hourly_rate,
                'batch_rate' => $trainer->batch_rate,
                'revenue_share_percentage' => $trainer->revenue_share_percentage,
                'estimated_earnings' => $trainer->type === 'internal'
                    ? ($totalHours * $trainer->hourly_rate) + ($batches->count() * $trainer->batch_rate)
                    : $totalRevenue * ($trainer->revenue_share_percentage / 100),
            ];
        });

        $summary = [
            'total_trainers' => $trainers->count(),
            'internal_trainers' => $trainers->where('type', 'internal')->count(),
            'external_trainers' => $trainers->where('type', 'external')->count(),
            'total_batches' => $utilizationData->sum('batch_count'),
            'total_hours' => $utilizationData->sum('total_hours'),
            'total_enrollments' => $utilizationData->sum('total_enrollments'),
            'total_revenue' => $utilizationData->sum('total_revenue'),
            'total_estimated_earnings' => $utilizationData->sum('estimated_earnings'),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'utilization_data' => $utilizationData,
                'summary' => $summary,
                'generated_at' => Carbon::now()
            ]);
        }

        return view('reports.trainer.utilization', compact('utilizationData', 'summary'));
    }

    public function revenueReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $trainers = \App\Models\Trainer::with(['courseBatches.enrollments'])
            ->whereHas('courseBatches', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate]);
            })
            ->get();

        $revenueData = $trainers->map(function ($trainer) use ($startDate, $endDate) {
            $batches = $trainer->courseBatches->whereBetween('start_date', [$startDate, $endDate]);
            $totalRevenue = $batches->sum(function ($batch) {
                return $batch->enrollments->sum('total_amount');
            });

            $trainerType = $trainer->type;
            $trainerHourlyRate = $trainer->hourly_rate;
            $trainerBatchRate = $trainer->batch_rate;
            $trainerRevenueShare = $trainer->revenue_share_percentage;
            
            $trainerRevenue = $trainerType === 'internal'
                ? ($batches->sum(function ($batch) use ($trainerHourlyRate) {
                    return $batch->course->duration_hours * $trainerHourlyRate;
                }) + ($batches->count() * $trainerBatchRate))
                : $totalRevenue * ($trainerRevenueShare / 100);

            return [
                'trainer_id' => $trainer->id,
                'trainer_name' => $trainer->name,
                'trainer_type' => $trainer->type,
                'batch_count' => $batches->count(),
                'total_revenue' => $totalRevenue,
                'trainer_revenue' => $trainerRevenue,
                'revenue_share_percentage' => $trainer->revenue_share_percentage,
                'hourly_rate' => $trainer->hourly_rate,
                'batch_rate' => $trainer->batch_rate,
            ];
        });

        $summary = [
            'total_trainers' => $trainers->count(),
            'total_revenue' => $revenueData->sum('total_revenue'),
            'total_trainer_revenue' => $revenueData->sum('trainer_revenue'),
            'average_trainer_revenue' => $revenueData->count() > 0 ? $revenueData->avg('trainer_revenue') : 0,
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'revenue_data' => $revenueData,
                'summary' => $summary,
                'period' => [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d')
                ],
                'generated_at' => Carbon::now()
            ]);
        }

        return view('reports.trainer.revenue', compact('revenueData', 'summary', 'startDate', 'endDate'));
    }

    public function exportPerformanceReport(Request $request)
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

        $performanceData = $this->reportService->generateTrainerPerformanceReport($startDate, $endDate);

        if ($async) {
            // Queue the job for background processing
            if ($format === 'pdf') {
                GeneratePDFReportJob::dispatch('trainer_performance', $performanceData, Auth::user()->email);
            } elseif ($format === 'excel') {
                GenerateExcelReportJob::dispatch('trainer_performance', $performanceData, Auth::user()->email);
            } else {
                GenerateCSVReportJob::dispatch('trainer_performance', $performanceData, Auth::user()->email);
            }
            
            return response()->json([
                'message' => strtoupper($format) . ' report generation queued. You will receive an email when ready.',
                'status' => 'queued'
            ]);
        }

        if ($format === 'pdf') {
            $pdfService = new PDFExportService();
            $filepath = $pdfService->generateTrainerPerformanceReport($performanceData);
            $filename = 'trainer_performance_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf';
        } elseif ($format === 'excel') {
            $excelService = new ExcelExportService();
            $filepath = $excelService->exportTrainerPerformanceReport($performanceData);
            $filename = 'trainer_performance_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        } else {
            $csvService = new CSVExportService();
            $filepath = $csvService->exportTrainerPerformanceReport($performanceData);
            $filename = 'trainer_performance_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        }

        return response()->download($filepath, $filename);
    }

    public function exportUtilizationReport(Request $request)
    {
        $format = $request->get('format', 'excel');
        $async = $request->get('async', false);

        $trainers = \App\Models\Trainer::with(['courseBatches.enrollments', 'courseBatches.course'])
            ->where('status', 'active')
            ->get();

        $utilizationData = $trainers->map(function ($trainer) {
            $batches = $trainer->courseBatches->where('status', '!=', 'cancelled');
            $totalHours = $batches->sum(function ($batch) {
                return $batch->course->duration_hours;
            });

            $totalEnrollments = $batches->sum(function ($batch) {
                return $batch->enrollments->where('status', 'enrolled')->count();
            });

            $totalRevenue = $batches->sum(function ($batch) {
                return $batch->enrollments->sum('total_amount');
            });

            return [
                'trainer_id' => $trainer->id,
                'trainer_name' => $trainer->name,
                'trainer_type' => $trainer->type,
                'batch_count' => $batches->count(),
                'total_hours' => $totalHours,
                'total_enrollments' => $totalEnrollments,
                'total_revenue' => $totalRevenue,
                'hourly_rate' => $trainer->hourly_rate,
                'batch_rate' => $trainer->batch_rate,
                'revenue_share_percentage' => $trainer->revenue_share_percentage,
                'estimated_earnings' => $trainer->type === 'internal'
                    ? ($totalHours * $trainer->hourly_rate) + ($batches->count() * $trainer->batch_rate)
                    : $totalRevenue * ($trainer->revenue_share_percentage / 100),
            ];
        });

        $reportData = [
            'performance_data' => $utilizationData,
            'summary' => [
                'total_trainers' => $trainers->count(),
                'internal_trainers' => $trainers->where('type', 'internal')->count(),
                'external_trainers' => $trainers->where('type', 'external')->count(),
                'total_batches' => $utilizationData->sum('batch_count'),
                'total_hours' => $utilizationData->sum('total_hours'),
                'total_enrollments' => $utilizationData->sum('total_enrollments'),
                'total_revenue' => $utilizationData->sum('total_revenue'),
                'total_estimated_earnings' => $utilizationData->sum('estimated_earnings'),
            ],
            'generated_at' => Carbon::now()
        ];

        if ($async) {
            // Queue the job for background processing
            if ($format === 'pdf') {
                GeneratePDFReportJob::dispatch('trainer_utilization', $reportData, Auth::user()->email);
            } elseif ($format === 'excel') {
                GenerateExcelReportJob::dispatch('trainer_utilization', $reportData, Auth::user()->email);
            } else {
                GenerateCSVReportJob::dispatch('trainer_utilization', $reportData, Auth::user()->email);
            }
            
            return response()->json([
                'message' => strtoupper($format) . ' report generation queued. You will receive an email when ready.',
                'status' => 'queued'
            ]);
        }

        if ($format === 'pdf') {
            $pdfService = new PDFExportService();
            $filepath = $pdfService->generateFromView('exports.pdf.trainer-utilization', [
                'data' => $reportData,
                'generated_at' => Carbon::now(),
                'company_name' => 'Prasasta ERP',
                'title' => 'Trainer Utilization Report'
            ]);
            $filename = 'trainer_utilization_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf';
        } elseif ($format === 'excel') {
            $excelService = new ExcelExportService();
            $filepath = $excelService->exportTrainerPerformanceReport($reportData);
            $filename = 'trainer_utilization_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        } else {
            $csvService = new CSVExportService();
            $filepath = $csvService->exportTrainerPerformanceReport($reportData);
            $filename = 'trainer_utilization_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        }

        return response()->download($filepath, $filename);
    }

    public function exportRevenueReport(Request $request)
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

        $trainers = \App\Models\Trainer::with(['courseBatches.enrollments'])
            ->whereHas('courseBatches', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate]);
            })
            ->get();

        $revenueData = $trainers->map(function ($trainer) use ($startDate, $endDate) {
            $batches = $trainer->courseBatches->whereBetween('start_date', [$startDate, $endDate]);
            $totalRevenue = $batches->sum(function ($batch) {
                return $batch->enrollments->sum('total_amount');
            });

            $trainerType = $trainer->type;
            $trainerHourlyRate = $trainer->hourly_rate;
            $trainerBatchRate = $trainer->batch_rate;
            $trainerRevenueShare = $trainer->revenue_share_percentage;
            
            $trainerRevenue = $trainerType === 'internal'
                ? ($batches->sum(function ($batch) use ($trainerHourlyRate) {
                    return $batch->course->duration_hours * $trainerHourlyRate;
                }) + ($batches->count() * $trainerBatchRate))
                : $totalRevenue * ($trainerRevenueShare / 100);

            return [
                'trainer_id' => $trainer->id,
                'trainer_name' => $trainer->name,
                'trainer_type' => $trainer->type,
                'batch_count' => $batches->count(),
                'total_revenue' => $totalRevenue,
                'trainer_revenue' => $trainerRevenue,
                'revenue_share_percentage' => $trainer->revenue_share_percentage,
                'hourly_rate' => $trainer->hourly_rate,
                'batch_rate' => $trainer->batch_rate,
            ];
        });

        $reportData = [
            'performance_data' => $revenueData,
            'summary' => [
                'total_trainers' => $trainers->count(),
                'total_revenue' => $revenueData->sum('total_revenue'),
                'total_trainer_revenue' => $revenueData->sum('trainer_revenue'),
                'average_trainer_revenue' => $revenueData->count() > 0 ? $revenueData->avg('trainer_revenue') : 0,
            ],
            'generated_at' => Carbon::now()
        ];

        if ($async) {
            // Queue the job for background processing
            if ($format === 'pdf') {
                GeneratePDFReportJob::dispatch('trainer_revenue', $reportData, Auth::user()->email);
            } elseif ($format === 'excel') {
                GenerateExcelReportJob::dispatch('trainer_revenue', $reportData, Auth::user()->email);
            } else {
                GenerateCSVReportJob::dispatch('trainer_revenue', $reportData, Auth::user()->email);
            }
            
            return response()->json([
                'message' => strtoupper($format) . ' report generation queued. You will receive an email when ready.',
                'status' => 'queued'
            ]);
        }

        if ($format === 'pdf') {
            $pdfService = new PDFExportService();
            $filepath = $pdfService->generateFromView('exports.pdf.trainer-revenue', [
                'data' => $reportData,
                'generated_at' => Carbon::now(),
                'company_name' => 'Prasasta ERP',
                'title' => 'Trainer Revenue Report'
            ]);
            $filename = 'trainer_revenue_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf';
        } elseif ($format === 'excel') {
            $excelService = new ExcelExportService();
            $filepath = $excelService->exportTrainerPerformanceReport($reportData);
            $filename = 'trainer_revenue_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        } else {
            $csvService = new CSVExportService();
            $filepath = $csvService->exportTrainerPerformanceReport($reportData);
            $filename = 'trainer_revenue_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        }

        return response()->download($filepath, $filename);
    }
}

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

class CourseReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportGenerationService $reportService)
    {
        $this->middleware(['auth', 'permission:reports.course.view']);

        $this->reportService = $reportService;
    }

    public function index()
    {
        return view('reports.course.index');
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

        $performanceData = $this->reportService->generateCoursePerformanceReport($startDate, $endDate);

        if ($request->wantsJson()) {
            return response()->json($performanceData);
        }

        return view('reports.course.performance', compact('performanceData', 'startDate', 'endDate'));
    }

    public function enrollmentReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $enrollments = \App\Models\Enrollment::whereBetween('enrollment_date', [$startDate, $endDate])
            ->with(['student', 'batch.course', 'paymentPlan'])
            ->get();

        $summary = [
            'total_enrollments' => $enrollments->count(),
            'total_revenue' => $enrollments->sum('total_amount'),
            'average_enrollment_amount' => $enrollments->count() > 0 ? $enrollments->avg('total_amount') : 0,
            'completed_enrollments' => $enrollments->where('status', 'completed')->count(),
            'active_enrollments' => $enrollments->where('status', 'enrolled')->count(),
        ];

        // Group by course
        $courseEnrollments = $enrollments->groupBy('batch.course.name')->map(function ($group) {
            return [
                'count' => $group->count(),
                'revenue' => $group->sum('total_amount'),
                'average_amount' => $group->avg('total_amount'),
            ];
        });

        if ($request->wantsJson()) {
            return response()->json([
                'enrollments' => $enrollments,
                'summary' => $summary,
                'course_enrollments' => $courseEnrollments,
                'period' => [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d')
                ],
                'generated_at' => Carbon::now()
            ]);
        }

        return view('reports.course.enrollment', compact('enrollments', 'summary', 'courseEnrollments', 'startDate', 'endDate'));
    }

    public function capacityReport(Request $request)
    {
        $batches = \App\Models\CourseBatch::with(['course', 'enrollments'])
            ->where('status', '!=', 'cancelled')
            ->get();

        $capacityData = $batches->map(function ($batch) {
            $enrollmentCount = $batch->enrollments->where('status', 'enrolled')->count();
            $utilization = $batch->capacity > 0 ? round(($enrollmentCount / $batch->capacity) * 100, 2) : 0;

            return [
                'batch_id' => $batch->id,
                'course_name' => $batch->course->name,
                'batch_code' => $batch->batch_code,
                'capacity' => $batch->capacity,
                'enrolled' => $enrollmentCount,
                'available' => $batch->capacity - $enrollmentCount,
                'utilization_percentage' => $utilization,
                'status' => $batch->status,
            ];
        });

        $summary = [
            'total_batches' => $batches->count(),
            'total_capacity' => $batches->sum('capacity'),
            'total_enrolled' => $capacityData->sum('enrolled'),
            'total_available' => $capacityData->sum('available'),
            'average_utilization' => $capacityData->avg('utilization_percentage'),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'capacity_data' => $capacityData,
                'summary' => $summary,
                'generated_at' => Carbon::now()
            ]);
        }

        return view('reports.course.capacity', compact('capacityData', 'summary'));
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

        $performanceData = $this->reportService->generateCoursePerformanceReport($startDate, $endDate);

        if ($async) {
            // Queue the job for background processing
            if ($format === 'pdf') {
                GeneratePDFReportJob::dispatch('course_performance', $performanceData, Auth::user()->email);
            } elseif ($format === 'excel') {
                GenerateExcelReportJob::dispatch('course_performance', $performanceData, Auth::user()->email);
            } else {
                GenerateCSVReportJob::dispatch('course_performance', $performanceData, Auth::user()->email);
            }
            
            return response()->json([
                'message' => strtoupper($format) . ' report generation queued. You will receive an email when ready.',
                'status' => 'queued'
            ]);
        }

        if ($format === 'pdf') {
            $pdfService = new PDFExportService();
            $filepath = $pdfService->generateCoursePerformanceReport($performanceData);
            $filename = 'course_performance_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf';
        } elseif ($format === 'excel') {
            $excelService = new ExcelExportService();
            $filepath = $excelService->exportCoursePerformanceReport($performanceData);
            $filename = 'course_performance_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        } else {
            $csvService = new CSVExportService();
            $filepath = $csvService->exportCoursePerformanceReport($performanceData);
            $filename = 'course_performance_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        }

        return response()->download($filepath, $filename);
    }

    public function exportEnrollmentReport(Request $request)
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

        $enrollments = \App\Models\Enrollment::whereBetween('enrollment_date', [$startDate, $endDate])
            ->with(['student', 'batch.course', 'paymentPlan'])
            ->get();

        $reportData = [
            'enrollments' => $enrollments,
            'summary' => [
                'total_enrollments' => $enrollments->count(),
                'total_revenue' => $enrollments->sum('total_amount'),
                'average_enrollment_amount' => $enrollments->count() > 0 ? $enrollments->avg('total_amount') : 0,
                'completed_enrollments' => $enrollments->where('status', 'completed')->count(),
                'active_enrollments' => $enrollments->where('status', 'enrolled')->count(),
            ],
            'generated_at' => Carbon::now()
        ];

        if ($async) {
            // Queue the job for background processing
            if ($format === 'pdf') {
                GeneratePDFReportJob::dispatch('enrollment_report', $reportData, Auth::user()->email);
            } elseif ($format === 'excel') {
                GenerateExcelReportJob::dispatch('enrollment_report', $reportData, Auth::user()->email);
            } else {
                GenerateCSVReportJob::dispatch('bulk_enrollment', $enrollments->toArray(), Auth::user()->email);
            }
            
            return response()->json([
                'message' => strtoupper($format) . ' report generation queued. You will receive an email when ready.',
                'status' => 'queued'
            ]);
        }

        if ($format === 'pdf') {
            $pdfService = new PDFExportService();
            $filepath = $pdfService->generateFromView('exports.pdf.enrollment-report', [
                'data' => $reportData,
                'generated_at' => Carbon::now(),
                'company_name' => 'Prasasta ERP',
                'title' => 'Enrollment Report'
            ]);
            $filename = 'enrollment_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf';
        } elseif ($format === 'excel') {
            $excelService = new ExcelExportService();
            $filepath = $excelService->exportCoursePerformanceReport($reportData);
            $filename = 'enrollment_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        } else {
            $csvService = new CSVExportService();
            $filepath = $csvService->exportBulkEnrollmentData($enrollments->toArray());
            $filename = 'enrollment_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        }

        return response()->download($filepath, $filename);
    }

    public function exportCapacityReport(Request $request)
    {
        $format = $request->get('format', 'excel');
        $async = $request->get('async', false);

        $batches = \App\Models\CourseBatch::with(['course', 'enrollments'])
            ->where('status', '!=', 'cancelled')
            ->get();

        $capacityData = $batches->map(function ($batch) {
            $enrollmentCount = $batch->enrollments->where('status', 'enrolled')->count();
            $utilization = $batch->capacity > 0 ? round(($enrollmentCount / $batch->capacity) * 100, 2) : 0;

            return [
                'batch_id' => $batch->id,
                'course_name' => $batch->course->name,
                'batch_code' => $batch->batch_code,
                'capacity' => $batch->capacity,
                'enrolled' => $enrollmentCount,
                'available' => $batch->capacity - $enrollmentCount,
                'utilization_percentage' => $utilization,
                'status' => $batch->status,
            ];
        });

        $reportData = [
            'capacity_data' => $capacityData,
            'summary' => [
                'total_batches' => $batches->count(),
                'total_capacity' => $batches->sum('capacity'),
                'total_enrolled' => $capacityData->sum('enrolled'),
                'total_available' => $capacityData->sum('available'),
                'average_utilization' => $capacityData->avg('utilization_percentage'),
            ],
            'generated_at' => Carbon::now()
        ];

        if ($async) {
            // Queue the job for background processing
            if ($format === 'pdf') {
                GeneratePDFReportJob::dispatch('capacity_report', $reportData, Auth::user()->email);
            } elseif ($format === 'excel') {
                GenerateExcelReportJob::dispatch('capacity_report', $reportData, Auth::user()->email);
            } else {
                GenerateCSVReportJob::dispatch('capacity_report', $reportData, Auth::user()->email);
            }
            
            return response()->json([
                'message' => strtoupper($format) . ' report generation queued. You will receive an email when ready.',
                'status' => 'queued'
            ]);
        }

        if ($format === 'pdf') {
            $pdfService = new PDFExportService();
            $filepath = $pdfService->generateFromView('exports.pdf.capacity-report', [
                'data' => $reportData,
                'generated_at' => Carbon::now(),
                'company_name' => 'Prasasta ERP',
                'title' => 'Capacity Utilization Report'
            ]);
            $filename = 'capacity_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf';
        } elseif ($format === 'excel') {
            $excelService = new ExcelExportService();
            $filepath = $excelService->exportCoursePerformanceReport($reportData);
            $filename = 'capacity_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        } else {
            $csvService = new CSVExportService();
            $filepath = $csvService->exportCoursePerformanceReport($reportData);
            $filename = 'capacity_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        }

        return response()->download($filepath, $filename);
    }
}

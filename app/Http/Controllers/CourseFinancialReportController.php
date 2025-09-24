<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CourseFinancialReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:courses.view']);
    }

    public function index()
    {
        return view('reports.course-financial.index');
    }

    public function courseProfitability()
    {
        return view('reports.course-financial.profitability');
    }

    public function revenueRecognition()
    {
        return view('reports.course-financial.revenue-recognition');
    }

    public function outstandingReceivables()
    {
        return view('reports.course-financial.outstanding-receivables');
    }

    public function paymentCollection()
    {
        return view('reports.course-financial.payment-collection');
    }

    public function getCourseProfitabilityData(Request $request)
    {
        $query = DB::table('courses as c')
            ->leftJoin('course_categories as cc', 'c.category_id', '=', 'cc.id')
            ->leftJoin('course_batches as cb', 'c.id', '=', 'cb.course_id')
            ->leftJoin('enrollments as e', 'cb.id', '=', 'e.batch_id')
            ->leftJoin('revenue_recognitions as rr', function ($join) {
                $join->on('e.id', '=', 'rr.enrollment_id')
                    ->where('rr.type', '=', 'recognized');
            })
            ->select([
                'c.id',
                'c.code',
                'c.name',
                'cc.name as category_name',
                'c.base_price',
                DB::raw('COUNT(DISTINCT cb.id) as total_batches'),
                DB::raw('COUNT(DISTINCT e.id) as total_enrollments'),
                DB::raw('SUM(e.total_amount) as total_revenue'),
                DB::raw('SUM(rr.amount) as recognized_revenue'),
                DB::raw('AVG(cb.capacity) as avg_capacity'),
                DB::raw('COUNT(DISTINCT e.id) / COUNT(DISTINCT cb.id) as avg_enrollments_per_batch')
            ])
            ->where('c.status', 'active')
            ->groupBy('c.id', 'c.code', 'c.name', 'cc.name', 'c.base_price');

        return DataTables::of($query)
            ->addColumn('revenue_per_enrollment', function ($row) {
                return $row->total_enrollments > 0 ?
                    'Rp ' . number_format($row->total_revenue / $row->total_enrollments, 0, ',', '.') :
                    'Rp 0';
            })
            ->addColumn('utilization_rate', function ($row) {
                return $row->avg_capacity > 0 ?
                    number_format(($row->total_enrollments / $row->total_batches) / $row->avg_capacity * 100, 1) . '%' :
                    '0%';
            })
            ->addColumn('deferred_revenue', function ($row) {
                $deferred = $row->total_revenue - $row->recognized_revenue;
                return 'Rp ' . number_format($deferred, 0, ',', '.');
            })
            ->addColumn('total_revenue_formatted', function ($row) {
                return 'Rp ' . number_format($row->total_revenue, 0, ',', '.');
            })
            ->addColumn('recognized_revenue_formatted', function ($row) {
                return 'Rp ' . number_format($row->recognized_revenue, 0, ',', '.');
            })
            ->addColumn('base_price_formatted', function ($row) {
                return 'Rp ' . number_format($row->base_price, 0, ',', '.');
            })
            ->make(true);
    }

    public function getOutstandingReceivablesData(Request $request)
    {
        $query = DB::table('enrollments as e')
            ->leftJoin('course_batches as cb', 'e.batch_id', '=', 'cb.id')
            ->leftJoin('courses as c', 'cb.course_id', '=', 'c.id')
            ->leftJoin('course_categories as cc', 'c.category_id', '=', 'cc.id')
            ->leftJoin('customers as cu', 'e.student_id', '=', 'cu.id')
            ->leftJoin('installment_payments as ip', 'e.id', '=', 'ip.enrollment_id')
            ->select([
                'e.id',
                'e.enrollment_date',
                'e.total_amount',
                'e.status as enrollment_status',
                'c.name as course_name',
                'cc.name as category_name',
                'cb.batch_code',
                'cb.start_date',
                'cb.end_date',
                'cu.name as student_name',
                'cu.phone',
                'cu.email',
                DB::raw('SUM(CASE WHEN ip.status = "paid" THEN ip.paid_amount ELSE 0 END) as paid_amount'),
                DB::raw('SUM(CASE WHEN ip.status = "pending" AND ip.due_date < CURDATE() THEN ip.amount + ip.late_fee ELSE 0 END) as overdue_amount'),
                DB::raw('SUM(CASE WHEN ip.status = "pending" THEN ip.amount + ip.late_fee ELSE 0 END) as outstanding_amount')
            ])
            ->where('e.status', 'enrolled')
            ->groupBy(
                'e.id',
                'e.enrollment_date',
                'e.total_amount',
                'e.status',
                'c.name',
                'cc.name',
                'cb.batch_code',
                'cb.start_date',
                'cb.end_date',
                'cu.name',
                'cu.phone',
                'cu.email'
            )
            ->having('outstanding_amount', '>', 0)
            ->orderBy('overdue_amount', 'desc');

        return DataTables::of($query)
            ->addColumn('total_amount_formatted', function ($row) {
                return 'Rp ' . number_format($row->total_amount, 0, ',', '.');
            })
            ->addColumn('paid_amount_formatted', function ($row) {
                return 'Rp ' . number_format($row->paid_amount, 0, ',', '.');
            })
            ->addColumn('outstanding_amount_formatted', function ($row) {
                return 'Rp ' . number_format($row->outstanding_amount, 0, ',', '.');
            })
            ->addColumn('overdue_amount_formatted', function ($row) {
                return $row->overdue_amount > 0 ?
                    'Rp ' . number_format($row->overdue_amount, 0, ',', '.') :
                    'Rp 0';
            })
            ->addColumn('overdue_badge', function ($row) {
                return $row->overdue_amount > 0 ?
                    '<span class="badge badge-danger">Overdue</span>' :
                    '<span class="badge badge-success">Current</span>';
            })
            ->rawColumns(['overdue_badge'])
            ->make(true);
    }
}

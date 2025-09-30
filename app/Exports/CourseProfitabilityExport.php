<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class CourseProfitabilityExport
{
    protected $startDate;
    protected $endDate;
    protected $categoryId;

    public function __construct($startDate = null, $endDate = null, $categoryId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->categoryId = $categoryId;
    }

    public function getData()
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
                DB::raw('COUNT(DISTINCT e.id) / COUNT(DISTINCT cb.id) as avg_enrollments_per_batch'),
                DB::raw('MAX(rr.recognition_date) as latest_recognition_date'),
                DB::raw('COUNT(DISTINCT CASE WHEN rr.id IS NOT NULL THEN e.id END) as enrollments_with_recognition'),
                DB::raw('COUNT(DISTINCT CASE WHEN cb.status = "ongoing" OR cb.status = "completed" THEN cb.id END) as active_batches')
            ])
            ->where('c.status', 'active')
            ->groupBy('c.id', 'c.code', 'c.name', 'cc.name', 'c.base_price');

        // Apply filters
        if ($this->startDate) {
            $query->where('cb.start_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->where('cb.start_date', '<=', $this->endDate);
        }
        if ($this->categoryId) {
            $query->where('c.category_id', $this->categoryId);
        }

        return $query->get();
    }

    public function getHeadings()
    {
        return [
            'Course Code',
            'Course Name',
            'Category',
            'Base Price',
            'Total Batches',
            'Total Enrollments',
            'Total Revenue',
            'Recognized Revenue',
            'Deferred Revenue',
            'Recognition Status',
            'Recognition Date',
            'Revenue per Enrollment',
            'Utilization Rate (%)',
            'Active Batches'
        ];
    }

    public function mapRow($row)
    {
        $deferredRevenue = $row->total_revenue - $row->recognized_revenue;

        // Calculate recognition status
        $recognitionStatus = 'Not Recognized';
        if ($row->total_enrollments > 0) {
            $recognitionPercentage = ($row->enrollments_with_recognition / $row->total_enrollments) * 100;
            if ($recognitionPercentage == 100) {
                $recognitionStatus = 'Fully Recognized';
            } elseif ($recognitionPercentage > 0) {
                $recognitionStatus = 'Partially Recognized';
            }
        } else {
            $recognitionStatus = 'No Enrollments';
        }

        // Calculate utilization rate
        $utilizationRate = 0;
        if ($row->total_batches > 0 && $row->avg_capacity > 0) {
            $utilizationRate = ($row->total_enrollments / $row->total_batches) / $row->avg_capacity * 100;
        }

        return [
            $row->code,
            $row->name,
            $row->category_name,
            $row->base_price,
            $row->total_batches,
            $row->total_enrollments,
            $row->total_revenue,
            $row->recognized_revenue,
            $deferredRevenue,
            $recognitionStatus,
            $row->latest_recognition_date ? date('d/m/Y', strtotime($row->latest_recognition_date)) : '-',
            $row->total_enrollments > 0 ? $row->total_revenue / $row->total_enrollments : 0,
            round($utilizationRate, 1),
            $row->active_batches
        ];
    }
}

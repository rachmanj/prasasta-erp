<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\InstallmentPayment;
use App\Models\RevenueRecognition;
use App\Models\CourseBatch;
use App\Models\Course;
use App\Models\Trainer;
use App\Models\PaymentPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportGenerationService
{
    /**
     * Generate payment aging report
     */
    public function generatePaymentAgingReport(): array
    {
        $agingRanges = [
            'current' => ['min' => 0, 'max' => 0],
            '1-30' => ['min' => 1, 'max' => 30],
            '31-60' => ['min' => 31, 'max' => 60],
            '61-90' => ['min' => 91, 'max' => 90],
            'over_90' => ['min' => 91, 'max' => 9999],
        ];

        $results = [];
        $totalAmount = 0;

        foreach ($agingRanges as $range => $days) {
            $query = InstallmentPayment::where('status', 'overdue')
                ->whereRaw('DATEDIFF(NOW(), due_date) BETWEEN ? AND ?', [$days['min'], $days['max']])
                ->with(['enrollment.student', 'enrollment.batch.course']);

            $amount = $query->sum('total_amount');
            $count = $query->count();

            $results[$range] = [
                'range' => $range,
                'amount' => $amount,
                'count' => $count,
                'percentage' => 0 // Will be calculated after total
            ];

            $totalAmount += $amount;
        }

        // Calculate percentages
        foreach ($results as $range => $data) {
            $results[$range]['percentage'] = $totalAmount > 0
                ? round(($data['amount'] / $totalAmount) * 100, 2)
                : 0;
        }

        return [
            'aging_data' => $results,
            'total_amount' => $totalAmount,
            'generated_at' => Carbon::now()
        ];
    }

    /**
     * Generate payment collection report
     */
    public function generatePaymentCollectionReport(Carbon $startDate, Carbon $endDate): array
    {
        $payments = InstallmentPayment::where('status', 'paid')
            ->whereBetween('paid_date', [$startDate, $endDate])
            ->with(['enrollment.student', 'enrollment.batch.course'])
            ->get();

        $summary = [
            'total_collected' => $payments->sum('paid_amount'),
            'total_late_fees' => $payments->sum('late_fee'),
            'payment_count' => $payments->count(),
            'average_payment' => $payments->count() > 0 ? $payments->avg('paid_amount') : 0,
        ];

        // Group by payment method
        $paymentMethods = $payments->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'amount' => $group->sum('paid_amount'),
                'percentage' => 0 // Will be calculated
            ];
        });

        // Calculate percentages
        $totalAmount = $summary['total_collected'];
        foreach ($paymentMethods as $method => $data) {
            $paymentMethods[$method]['percentage'] = $totalAmount > 0
                ? round(($data['amount'] / $totalAmount) * 100, 2)
                : 0;
        }

        return [
            'summary' => $summary,
            'payment_methods' => $paymentMethods,
            'payments' => $payments,
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d')
            ],
            'generated_at' => Carbon::now()
        ];
    }

    /**
     * Generate revenue recognition report
     */
    public function generateRevenueRecognitionReport(Carbon $startDate, Carbon $endDate): array
    {
        $revenue = RevenueRecognition::whereBetween('recognition_date', [$startDate, $endDate])
            ->with(['enrollment.student', 'batch.course'])
            ->get();

        $summary = [
            'total_deferred' => $revenue->where('type', 'deferred')->sum('amount'),
            'total_recognized' => $revenue->where('type', 'recognized')->sum('amount'),
            'total_reversed' => $revenue->where('type', 'reversed')->sum('amount'),
            'net_revenue' => $revenue->where('type', 'recognized')->sum('amount') - $revenue->where('type', 'reversed')->sum('amount'),
        ];

        // Group by course
        $courseRevenue = $revenue->groupBy('batch.course.name')->map(function ($group) {
            return [
                'deferred' => $group->where('type', 'deferred')->sum('amount'),
                'recognized' => $group->where('type', 'recognized')->sum('amount'),
                'reversed' => $group->where('type', 'reversed')->sum('amount'),
                'net' => $group->where('type', 'recognized')->sum('amount') - $group->where('type', 'reversed')->sum('amount'),
            ];
        });

        return [
            'summary' => $summary,
            'course_revenue' => $courseRevenue,
            'revenue_data' => $revenue,
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d')
            ],
            'generated_at' => Carbon::now()
        ];
    }

    /**
     * Generate course performance report
     */
    public function generateCoursePerformanceReport(Carbon $startDate, Carbon $endDate): array
    {
        $courses = Course::with(['batches.enrollments', 'batches.trainer'])
            ->whereHas('batches', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate]);
            })
            ->get();

        $performance = $courses->map(function ($course) use ($startDate, $endDate) {
            $batches = $course->batches->whereBetween('start_date', [$startDate, $endDate]);

            $totalEnrollments = $batches->sum(function ($batch) {
                return $batch->enrollments->count();
            });

            $totalRevenue = $batches->sum(function ($batch) {
                return $batch->enrollments->sum('total_amount');
            });

            $averageEnrollment = $batches->count() > 0 ? $totalEnrollments / $batches->count() : 0;

            return [
                'course_id' => $course->id,
                'course_name' => $course->name,
                'course_code' => $course->code,
                'batch_count' => $batches->count(),
                'total_enrollments' => $totalEnrollments,
                'total_revenue' => $totalRevenue,
                'average_enrollment_per_batch' => $averageEnrollment,
                'capacity_utilization' => $batches->sum('capacity') > 0
                    ? round(($totalEnrollments / $batches->sum('capacity')) * 100, 2)
                    : 0,
            ];
        });

        return [
            'performance_data' => $performance,
            'summary' => [
                'total_courses' => $courses->count(),
                'total_batches' => $courses->sum(function ($course) {
                    return $course->batches->whereBetween('start_date', [$startDate, $endDate])->count();
                }),
                'total_enrollments' => $performance->sum('total_enrollments'),
                'total_revenue' => $performance->sum('total_revenue'),
                'average_capacity_utilization' => $performance->avg('capacity_utilization'),
            ],
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d')
            ],
            'generated_at' => Carbon::now()
        ];
    }

    /**
     * Generate trainer performance report
     */
    public function generateTrainerPerformanceReport(Carbon $startDate, Carbon $endDate): array
    {
        $trainers = Trainer::with(['courseBatches.enrollments', 'courseBatches.course'])
            ->whereHas('courseBatches', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate]);
            })
            ->get();

        $performance = $trainers->map(function ($trainer) use ($startDate, $endDate) {
            $batches = $trainer->courseBatches->whereBetween('start_date', [$startDate, $endDate]);

            $totalEnrollments = $batches->sum(function ($batch) {
                return $batch->enrollments->count();
            });

            $totalRevenue = $batches->sum(function ($batch) {
                return $batch->enrollments->sum('total_amount');
            });

            $trainerRevenue = $totalRevenue * ($trainer->revenue_share_percentage / 100);

            return [
                'trainer_id' => $trainer->id,
                'trainer_name' => $trainer->name,
                'trainer_type' => $trainer->type,
                'batch_count' => $batches->count(),
                'total_enrollments' => $totalEnrollments,
                'total_revenue' => $totalRevenue,
                'trainer_revenue' => $trainerRevenue,
                'hourly_rate' => $trainer->hourly_rate,
                'batch_rate' => $trainer->batch_rate,
                'revenue_share_percentage' => $trainer->revenue_share_percentage,
            ];
        });

        return [
            'performance_data' => $performance,
            'summary' => [
                'total_trainers' => $trainers->count(),
                'total_batches' => $performance->sum('batch_count'),
                'total_enrollments' => $performance->sum('total_enrollments'),
                'total_revenue' => $performance->sum('total_revenue'),
                'total_trainer_revenue' => $performance->sum('trainer_revenue'),
            ],
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d')
            ],
            'generated_at' => Carbon::now()
        ];
    }

    /**
     * Generate financial summary report
     */
    public function generateFinancialSummaryReport(Carbon $startDate, Carbon $endDate): array
    {
        // Revenue data
        $revenue = RevenueRecognition::whereBetween('recognition_date', [$startDate, $endDate])
            ->where('type', 'recognized')
            ->sum('amount');

        // Payment data
        $payments = InstallmentPayment::whereBetween('paid_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('paid_amount');

        // Enrollment data
        $enrollments = Enrollment::whereBetween('enrollment_date', [$startDate, $endDate])
            ->sum('total_amount');

        // Overdue data
        $overdue = InstallmentPayment::where('status', 'overdue')
            ->sum('total_amount');

        return [
            'revenue' => [
                'recognized_revenue' => $revenue,
                'total_enrollments' => $enrollments,
                'deferred_revenue' => $enrollments - $revenue,
            ],
            'payments' => [
                'collected_payments' => $payments,
                'overdue_amount' => $overdue,
                'collection_rate' => $enrollments > 0 ? round(($payments / $enrollments) * 100, 2) : 0,
            ],
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d')
            ],
            'generated_at' => Carbon::now()
        ];
    }
}

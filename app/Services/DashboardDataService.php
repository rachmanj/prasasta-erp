<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\InstallmentPayment;
use App\Models\RevenueRecognition;
use App\Models\CourseBatch;
use App\Models\Course;
use App\Models\Trainer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardDataService
{
    /**
     * Get executive dashboard data
     */
    public function getExecutiveDashboardData(): array
    {
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();

        // Revenue metrics
        $currentRevenue = RevenueRecognition::where('type', 'recognized')
            ->whereYear('recognition_date', $currentMonth->year)
            ->whereMonth('recognition_date', $currentMonth->month)
            ->sum('amount');

        $previousRevenue = RevenueRecognition::where('type', 'recognized')
            ->whereYear('recognition_date', $previousMonth->year)
            ->whereMonth('recognition_date', $previousMonth->month)
            ->sum('amount');

        // Enrollment metrics
        $currentEnrollments = Enrollment::whereYear('enrollment_date', $currentMonth->year)
            ->whereMonth('enrollment_date', $currentMonth->month)
            ->count();

        $previousEnrollments = Enrollment::whereYear('enrollment_date', $previousMonth->year)
            ->whereMonth('enrollment_date', $previousMonth->month)
            ->count();

        // Payment metrics
        $currentPayments = InstallmentPayment::where('status', 'paid')
            ->whereYear('paid_date', $currentMonth->year)
            ->whereMonth('paid_date', $currentMonth->month)
            ->sum('paid_amount');

        $previousPayments = InstallmentPayment::where('status', 'paid')
            ->whereYear('paid_date', $previousMonth->year)
            ->whereMonth('paid_date', $previousMonth->month)
            ->sum('paid_amount');

        // Overdue metrics
        $overdueAmount = InstallmentPayment::where('status', 'overdue')->sum('amount');
        $overdueCount = InstallmentPayment::where('status', 'overdue')->count();

        return [
            'revenue' => [
                'current' => $currentRevenue,
                'previous' => $previousRevenue,
                'growth' => $previousRevenue > 0 ? round((($currentRevenue - $previousRevenue) / $previousRevenue) * 100, 2) : 0,
            ],
            'enrollments' => [
                'current' => $currentEnrollments,
                'previous' => $previousEnrollments,
                'growth' => $previousEnrollments > 0 ? round((($currentEnrollments - $previousEnrollments) / $previousEnrollments) * 100, 2) : 0,
            ],
            'payments' => [
                'current' => $currentPayments,
                'previous' => $previousPayments,
                'growth' => $previousPayments > 0 ? round((($currentPayments - $previousPayments) / $previousPayments) * 100, 2) : 0,
            ],
            'overdue' => [
                'amount' => $overdueAmount,
                'count' => $overdueCount,
            ],
            'generated_at' => Carbon::now()
        ];
    }

    /**
     * Get financial dashboard data
     */
    public function getFinancialDashboardData(): array
    {
        $currentMonth = Carbon::now();
        $last12Months = [];

        // Generate last 12 months data
        for ($i = 11; $i >= 0; $i--) {
            $month = $currentMonth->copy()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $revenue = RevenueRecognition::where('type', 'recognized')
                ->whereBetween('recognition_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $payments = InstallmentPayment::where('status', 'paid')
                ->whereBetween('paid_date', [$monthStart, $monthEnd])
                ->sum('paid_amount');

            $enrollments = Enrollment::whereBetween('enrollment_date', [$monthStart, $monthEnd])
                ->sum('total_amount');

            $last12Months[] = [
                'month' => $month->format('Y-m'),
                'month_name' => $month->format('M Y'),
                'revenue' => $revenue,
                'payments' => $payments,
                'enrollments' => $enrollments,
            ];
        }

        // Payment method distribution
        $paymentMethods = InstallmentPayment::where('status', 'paid')
            ->whereYear('paid_date', $currentMonth->year)
            ->whereMonth('paid_date', $currentMonth->month)
            ->select('payment_method', DB::raw('SUM(paid_amount) as total_amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        // Revenue vs Payments comparison
        $revenueVsPayments = [
            'total_revenue' => RevenueRecognition::where('type', 'recognized')->sum('amount'),
            'total_payments' => InstallmentPayment::where('status', 'paid')->sum('paid_amount'),
            'deferred_revenue' => RevenueRecognition::where('type', 'deferred')->sum('amount'),
        ];

        return [
            'monthly_trends' => $last12Months,
            'payment_methods' => $paymentMethods,
            'revenue_vs_payments' => $revenueVsPayments,
            'generated_at' => Carbon::now()
        ];
    }

    /**
     * Get operational dashboard data
     */
    public function getOperationalDashboardData(): array
    {
        // Course capacity utilization
        $batches = CourseBatch::with(['course', 'enrollments'])
            ->where('status', '!=', 'cancelled')
            ->get();

        $capacityData = $batches->map(function ($batch) {
            $enrolled = $batch->enrollments->where('status', 'enrolled')->count();
            $utilization = $batch->capacity > 0 ? round(($enrolled / $batch->capacity) * 100, 2) : 0;

            return [
                'course_name' => $batch->course->name,
                'batch_code' => $batch->batch_code,
                'capacity' => $batch->capacity,
                'enrolled' => $enrolled,
                'utilization' => $utilization,
                'status' => $batch->status,
            ];
        });

        // Upcoming batches
        $upcomingBatches = CourseBatch::with(['course', 'trainer'])
            ->where('status', 'planned')
            ->where('start_date', '>=', Carbon::now())
            ->orderBy('start_date')
            ->limit(10)
            ->get();

        // Enrollment trends
        $enrollmentTrends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = Enrollment::whereDate('enrollment_date', $date)->count();
            $enrollmentTrends[] = [
                'date' => $date->format('Y-m-d'),
                'count' => $count,
            ];
        }

        // Course performance summary
        $coursePerformance = Course::with(['batches.enrollments'])
            ->get()
            ->map(function ($course) {
                $totalEnrollments = $course->batches->sum(function ($batch) {
                    return $batch->enrollments->count();
                });
                $totalRevenue = $course->batches->sum(function ($batch) {
                    return $batch->enrollments->sum('total_amount');
                });

                return [
                    'course_name' => $course->name,
                    'enrollments' => $totalEnrollments,
                    'revenue' => $totalRevenue,
                ];
            })
            ->sortByDesc('revenue')
            ->take(5);

        return [
            'capacity_utilization' => $capacityData,
            'upcoming_batches' => $upcomingBatches,
            'enrollment_trends' => $enrollmentTrends,
            'top_courses' => $coursePerformance,
            'generated_at' => Carbon::now()
        ];
    }

    /**
     * Get performance dashboard data
     */
    public function getPerformanceDashboardData(): array
    {
        $currentMonth = Carbon::now();

        // Trainer performance
        $trainerPerformance = Trainer::with(['courseBatches.enrollments'])
            ->whereHas('courseBatches', function ($query) use ($currentMonth) {
                $query->whereYear('start_date', $currentMonth->year)
                    ->whereMonth('start_date', $currentMonth->month);
            })
            ->get()
            ->map(function ($trainer) use ($currentMonth) {
                $batches = $trainer->courseBatches->filter(function ($batch) use ($currentMonth) {
                    return $batch->start_date->year === $currentMonth->year &&
                        $batch->start_date->month === $currentMonth->month;
                });

                $totalEnrollments = $batches->sum(function ($batch) {
                    return $batch->enrollments->count();
                });

                $totalRevenue = $batches->sum(function ($batch) {
                    return $batch->enrollments->sum('total_amount');
                });

                return [
                    'trainer_name' => $trainer->name,
                    'batch_count' => $batches->count(),
                    'enrollments' => $totalEnrollments,
                    'revenue' => $totalRevenue,
                ];
            })
            ->sortByDesc('revenue')
            ->take(10);

        // Course completion rates
        $courseCompletion = Course::with(['batches.enrollments'])
            ->get()
            ->map(function ($course) {
                $totalEnrollments = $course->batches->sum(function ($batch) {
                    return $batch->enrollments->count();
                });
                $completedEnrollments = $course->batches->sum(function ($batch) {
                    return $batch->enrollments->where('status', 'completed')->count();
                });

                $completionRate = $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100, 2) : 0;

                return [
                    'course_name' => $course->name,
                    'total_enrollments' => $totalEnrollments,
                    'completed_enrollments' => $completedEnrollments,
                    'completion_rate' => $completionRate,
                ];
            })
            ->sortByDesc('completion_rate')
            ->take(10);

        // Payment collection performance
        $collectionPerformance = [
            'total_due' => InstallmentPayment::where('status', 'pending')->sum('amount'),
            'total_collected' => InstallmentPayment::where('status', 'paid')->sum('paid_amount'),
            'total_overdue' => InstallmentPayment::where('status', 'overdue')->sum('amount'),
            'collection_rate' => 0, // Will be calculated
        ];

        $totalDue = $collectionPerformance['total_due'] + $collectionPerformance['total_collected'];
        $collectionPerformance['collection_rate'] = $totalDue > 0
            ? round(($collectionPerformance['total_collected'] / $totalDue) * 100, 2)
            : 0;

        return [
            'trainer_performance' => $trainerPerformance,
            'course_completion' => $courseCompletion,
            'collection_performance' => $collectionPerformance,
            'generated_at' => Carbon::now()
        ];
    }
}

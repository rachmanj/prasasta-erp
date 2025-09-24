<?php

namespace App\Services;

use App\Models\CourseBatch;
use App\Models\Trainer;
use App\Services\Accounting\PostingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseCostManagementService
{
    public function __construct(
        private PostingService $postingService
    ) {}

    /**
     * Record trainer costs for a batch
     */
    public function recordTrainerCosts(CourseBatch $batch, float $totalCost): int
    {
        if (!$batch->trainer_id) {
            throw new \Exception('Batch must have a trainer assigned');
        }

        $trainer = $batch->trainer;
        $course = $batch->course;

        // Get account IDs
        $trainerCostAccountId = $this->getTrainerCostAccountId();
        $cashAccountId = $this->getCashAccountId();

        $lines = [];

        // Debit: Trainer Costs
        $lines[] = [
            'account_id' => $trainerCostAccountId,
            'debit' => $totalCost,
            'credit' => 0,
            'project_id' => null,
            'fund_id' => null,
            'dept_id' => null,
            'memo' => "Trainer costs - {$trainer->name} - {$course->name} - Batch: {$batch->batch_code}",
        ];

        // Credit: Cash/Bank
        $lines[] = [
            'account_id' => $cashAccountId,
            'debit' => 0,
            'credit' => $totalCost,
            'project_id' => null,
            'fund_id' => null,
            'dept_id' => null,
            'memo' => "Payment to trainer - {$trainer->name}",
        ];

        try {
            $journalId = $this->postingService->postJournal([
                'date' => $batch->end_date->toDateString(),
                'description' => "Trainer Payment - {$course->name} - {$trainer->name} - Batch: {$batch->batch_code}",
                'source_type' => 'course_batch_cost',
                'source_id' => $batch->id,
                'lines' => $lines,
                'posted_by' => auth()->id(),
                'status' => 'posted',
            ]);

            Log::info("Created trainer cost journal entry {$journalId} for batch {$batch->id}");

            return $journalId;
        } catch (\Exception $e) {
            Log::error("Failed to create trainer cost journal entry for batch {$batch->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Record course delivery costs (materials, venue, etc.)
     */
    public function recordDeliveryCosts(CourseBatch $batch, array $costItems): int
    {
        $course = $batch->course;
        $totalCost = array_sum(array_column($costItems, 'amount'));

        // Get account IDs
        $deliveryCostAccountId = $this->getDeliveryCostAccountId();
        $cashAccountId = $this->getCashAccountId();

        $lines = [];

        // Debit: Course Delivery Costs
        $lines[] = [
            'account_id' => $deliveryCostAccountId,
            'debit' => $totalCost,
            'credit' => 0,
            'project_id' => null,
            'fund_id' => null,
            'dept_id' => null,
            'memo' => "Course delivery costs - {$course->name} - Batch: {$batch->batch_code}",
        ];

        // Credit: Cash/Bank
        $lines[] = [
            'account_id' => $cashAccountId,
            'debit' => 0,
            'credit' => $totalCost,
            'project_id' => null,
            'fund_id' => null,
            'dept_id' => null,
            'memo' => "Payment for course delivery costs - {$course->name}",
        ];

        try {
            $journalId = $this->postingService->postJournal([
                'date' => now()->toDateString(),
                'description' => "Course Delivery Costs - {$course->name} - Batch: {$batch->batch_code}",
                'source_type' => 'course_delivery_cost',
                'source_id' => $batch->id,
                'lines' => $lines,
                'posted_by' => auth()->id(),
                'status' => 'posted',
            ]);

            Log::info("Created delivery cost journal entry {$journalId} for batch {$batch->id}");

            return $journalId;
        } catch (\Exception $e) {
            Log::error("Failed to create delivery cost journal entry for batch {$batch->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculate course profitability
     */
    public function calculateCourseProfitability(int $courseId, string $startDate = null, string $endDate = null): array
    {
        $query = DB::table('courses as c')
            ->leftJoin('course_batches as cb', 'c.id', '=', 'cb.course_id')
            ->leftJoin('enrollments as e', 'cb.id', '=', 'e.batch_id')
            ->leftJoin('journals as j', function ($join) {
                $join->where('j.source_type', '=', 'course_batch')
                    ->whereColumn('j.source_id', 'cb.id');
            })
            ->leftJoin('journal_lines as jl', 'j.id', '=', 'jl.journal_id')
            ->leftJoin('accounts as a', 'jl.account_id', '=', 'a.id')
            ->where('c.id', $courseId);

        if ($startDate && $endDate) {
            $query->whereBetween('cb.start_date', [$startDate, $endDate]);
        }

        $results = $query->select([
            'c.id',
            'c.name',
            'c.base_price',
            DB::raw('COUNT(DISTINCT cb.id) as total_batches'),
            DB::raw('COUNT(DISTINCT e.id) as total_enrollments'),
            DB::raw('SUM(e.total_amount) as total_revenue'),
            DB::raw('SUM(CASE WHEN a.code LIKE "4.1%" THEN jl.credit ELSE 0 END) as recognized_revenue'),
            DB::raw('SUM(CASE WHEN a.code LIKE "5.1%" THEN jl.debit ELSE 0 END) as total_costs')
        ])
            ->groupBy('c.id', 'c.name', 'c.base_price')
            ->first();

        if (!$results) {
            return [
                'course_id' => $courseId,
                'course_name' => 'Unknown Course',
                'total_revenue' => 0,
                'total_costs' => 0,
                'gross_profit' => 0,
                'profit_margin' => 0,
                'enrollments' => 0,
                'batches' => 0,
                'revenue_per_enrollment' => 0,
                'cost_per_enrollment' => 0
            ];
        }

        $grossProfit = $results->recognized_revenue - $results->total_costs;
        $profitMargin = $results->recognized_revenue > 0 ?
            ($grossProfit / $results->recognized_revenue) * 100 : 0;

        return [
            'course_id' => $results->id,
            'course_name' => $results->name,
            'total_revenue' => $results->total_revenue,
            'recognized_revenue' => $results->recognized_revenue,
            'total_costs' => $results->total_costs,
            'gross_profit' => $grossProfit,
            'profit_margin' => $profitMargin,
            'enrollments' => $results->total_enrollments,
            'batches' => $results->total_batches,
            'revenue_per_enrollment' => $results->total_enrollments > 0 ?
                $results->recognized_revenue / $results->total_enrollments : 0,
            'cost_per_enrollment' => $results->total_enrollments > 0 ?
                $results->total_costs / $results->total_enrollments : 0
        ];
    }

    /**
     * Get trainer utilization report
     */
    public function getTrainerUtilizationReport(string $startDate = null, string $endDate = null): array
    {
        $query = DB::table('trainers as t')
            ->leftJoin('course_batches as cb', 't.id', '=', 'cb.trainer_id')
            ->leftJoin('courses as c', 'cb.course_id', '=', 'c.id')
            ->leftJoin('enrollments as e', 'cb.id', '=', 'e.batch_id')
            ->where('t.is_active', true);

        if ($startDate && $endDate) {
            $query->whereBetween('cb.start_date', [$startDate, $endDate]);
        }

        return $query->select([
            't.id',
            't.name',
            't.specialization',
            't.hourly_rate',
            DB::raw('COUNT(DISTINCT cb.id) as total_batches'),
            DB::raw('COUNT(DISTINCT e.id) as total_students'),
            DB::raw('SUM(c.duration_hours) as total_hours'),
            DB::raw('SUM(cb.capacity) as total_capacity'),
            DB::raw('AVG(cb.capacity) as avg_batch_size'),
            DB::raw('SUM(e.total_amount) as total_revenue_generated')
        ])
            ->groupBy('t.id', 't.name', 't.specialization', 't.hourly_rate')
            ->orderBy('total_revenue_generated', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get account IDs
     */
    private function getTrainerCostAccountId(): int
    {
        return (int) DB::table('accounts')->where('code', '5.1.1')->value('id');
    }

    private function getDeliveryCostAccountId(): int
    {
        return (int) DB::table('accounts')->where('code', '5.1.2')->value('id');
    }

    private function getCashAccountId(): int
    {
        return (int) DB::table('accounts')->where('code', '1.1.2.01')->value('id');
    }
}

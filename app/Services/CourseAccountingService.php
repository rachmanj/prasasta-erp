<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\CourseBatch;
use App\Models\InstallmentPayment;
use App\Models\Course;
use App\Models\Master\Customer;
use App\Services\Accounting\PostingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseAccountingService
{
    public function __construct(
        private PostingService $postingService
    ) {}

    /**
     * Create journal entries for enrollment
     */
    public function createEnrollmentJournalEntry(Enrollment $enrollment): int
    {
        if ($enrollment->is_accounted_for) {
            throw new \Exception('Enrollment already accounted for');
        }

        $course = $enrollment->batch->course;
        $student = $enrollment->student;
        $totalAmount = $enrollment->total_amount;

        // Get account IDs
        $arAccountId = $this->getAccountsReceivableAccountId();
        $deferredRevenueAccountId = $this->getDeferredRevenueAccountId($course->category_id);
        $revenueAccountId = $this->getRevenueAccountId($course->category_id);

        $lines = [];

        // Calculate PPN if applicable (assuming 11% for courses)
        $ppnRate = 0.11;
        $baseAmount = $totalAmount / (1 + $ppnRate);
        $ppnAmount = $totalAmount - $baseAmount;

        // Debit: Accounts Receivable
        $lines[] = [
            'account_id' => $arAccountId,
            'debit' => $totalAmount,
            'credit' => 0,
            'project_id' => null, // Will be enhanced in Phase 2
            'fund_id' => null,
            'dept_id' => null,
            'memo' => "AR - Course: {$course->name} - Student: {$student->name}",
        ];

        // Credit: Deferred Revenue (base amount)
        $lines[] = [
            'account_id' => $deferredRevenueAccountId,
            'debit' => 0,
            'credit' => $baseAmount,
            'project_id' => null,
            'fund_id' => null,
            'dept_id' => null,
            'memo' => "Deferred Revenue - {$course->name}",
        ];

        // Credit: PPN Output (if applicable)
        if ($ppnAmount > 0) {
            $ppnOutputAccountId = $this->getPPNOutputAccountId();
            $lines[] = [
                'account_id' => $ppnOutputAccountId,
                'debit' => 0,
                'credit' => $ppnAmount,
                'project_id' => null,
                'fund_id' => null,
                'dept_id' => null,
                'memo' => "PPN Output - Course: {$course->name}",
            ];
        }

        try {
            $journalId = $this->postingService->postJournal([
                'date' => $enrollment->enrollment_date->toDateString(),
                'description' => "Course Enrollment - {$course->name} - {$student->name}",
                'source_type' => 'enrollment',
                'source_id' => $enrollment->id,
                'lines' => $lines,
                'posted_by' => auth()->id(),
                'status' => 'posted',
            ]);

            // Update enrollment with journal entry reference
            $enrollment->update([
                'journal_entry_id' => $journalId,
                'is_accounted_for' => true,
                'accounted_at' => now(),
            ]);

            Log::info("Created journal entry {$journalId} for enrollment {$enrollment->id}");

            return $journalId;
        } catch (\Exception $e) {
            Log::error("Failed to create journal entry for enrollment {$enrollment->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process payment journal entry
     */
    public function processPaymentJournalEntry(InstallmentPayment $payment): int
    {
        if ($payment->is_accounted_for) {
            throw new \Exception('Payment already accounted for');
        }

        $enrollment = $payment->enrollment;
        $student = $enrollment->student;
        $paidAmount = $payment->paid_amount;

        // Get account IDs
        $cashAccountId = $this->getCashAccountId();
        $arAccountId = $this->getAccountsReceivableAccountId();

        $lines = [];

        // Debit: Cash/Bank
        $lines[] = [
            'account_id' => $cashAccountId,
            'debit' => $paidAmount,
            'credit' => 0,
            'project_id' => null,
            'fund_id' => null,
            'dept_id' => null,
            'memo' => "Payment received - {$student->name} - Installment #{$payment->installment_number}",
        ];

        // Credit: Accounts Receivable
        $lines[] = [
            'account_id' => $arAccountId,
            'debit' => 0,
            'credit' => $paidAmount,
            'project_id' => null,
            'fund_id' => null,
            'dept_id' => null,
            'memo' => "Settle AR - {$student->name} - Installment #{$payment->installment_number}",
        ];

        try {
            $journalId = $this->postingService->postJournal([
                'date' => $payment->paid_date->toDateString(),
                'description' => "Course Payment - {$student->name} - Installment #{$payment->installment_number}",
                'source_type' => 'installment_payment',
                'source_id' => $payment->id,
                'lines' => $lines,
                'posted_by' => auth()->id(),
                'status' => 'posted',
            ]);

            // Update payment with journal entry reference
            $payment->update([
                'journal_entry_id' => $journalId,
                'is_accounted_for' => true,
                'accounted_at' => now(),
            ]);

            Log::info("Created journal entry {$journalId} for payment {$payment->id}");

            return $journalId;
        } catch (\Exception $e) {
            Log::error("Failed to create journal entry for payment {$payment->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Recognize revenue when course batch starts
     */
    public function recognizeRevenueForBatch(CourseBatch $batch): int
    {
        if ($batch->revenue_recognized) {
            throw new \Exception('Revenue already recognized for this batch');
        }

        $course = $batch->course;
        $enrollments = $batch->enrollments()->where('status', 'enrolled')->get();

        if ($enrollments->isEmpty()) {
            Log::info("No enrollments found for batch {$batch->id}, skipping revenue recognition");
            return 0;
        }

        $totalRevenue = $enrollments->sum('total_amount');
        $baseRevenue = $totalRevenue / 1.11; // Remove PPN from total

        // Get account IDs
        $deferredRevenueAccountId = $this->getDeferredRevenueAccountId($course->category_id);
        $revenueAccountId = $this->getRevenueAccountId($course->category_id);

        $lines = [];

        // Debit: Deferred Revenue
        $lines[] = [
            'account_id' => $deferredRevenueAccountId,
            'debit' => $baseRevenue,
            'credit' => 0,
            'project_id' => null,
            'fund_id' => null,
            'dept_id' => null,
            'memo' => "Recognize deferred revenue - {$course->name} - Batch: {$batch->batch_code}",
        ];

        // Credit: Course Revenue
        $lines[] = [
            'account_id' => $revenueAccountId,
            'debit' => 0,
            'credit' => $baseRevenue,
            'project_id' => null,
            'fund_id' => null,
            'dept_id' => null,
            'memo' => "Course revenue recognized - {$course->name} - Batch: {$batch->batch_code}",
        ];

        try {
            $journalId = $this->postingService->postJournal([
                'date' => $batch->start_date->toDateString(),
                'description' => "Revenue Recognition - {$course->name} - Batch: {$batch->batch_code}",
                'source_type' => 'course_batch',
                'source_id' => $batch->id,
                'lines' => $lines,
                'posted_by' => auth()->id(),
                'status' => 'posted',
            ]);

            // Update batch with revenue recognition info
            $batch->update([
                'revenue_recognized' => true,
                'revenue_recognized_at' => now(),
                'revenue_recognition_journal_id' => $journalId,
            ]);

            Log::info("Created revenue recognition journal entry {$journalId} for batch {$batch->id}");

            return $journalId;
        } catch (\Exception $e) {
            Log::error("Failed to create revenue recognition journal entry for batch {$batch->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle course cancellation with proper GL entries
     */
    public function handleCourseCancellation(Enrollment $enrollment, string $reason = null): int
    {
        $course = $enrollment->batch->course;
        $student = $enrollment->student;
        $totalAmount = $enrollment->total_amount;

        // Calculate PPN if applicable (assuming 11% for courses)
        $ppnRate = 0.11;
        $baseAmount = $totalAmount / (1 + $ppnRate);
        $ppnAmount = $totalAmount - $baseAmount;

        // Get account IDs
        $arAccountId = $this->getAccountsReceivableAccountId();
        $deferredRevenueAccountId = $this->getDeferredRevenueAccountId($course->category_id);
        $revenueAccountId = $this->getRevenueAccountId($course->category_id);
        $cancellationRevenueAccountId = $this->getCancellationRevenueAccountId();
        $ppnOutputAccountId = $this->getPPNOutputAccountId();

        $lines = [];

        // Check if revenue has been recognized for this batch
        $batch = $enrollment->batch;
        $batch->refresh(); // Ensure we have the latest data
        $isRevenueRecognized = $batch->revenue_recognized;

        if ($isRevenueRecognized) {
            // If revenue was already recognized, reverse the course revenue and create cancellation revenue
            $lines[] = [
                'account_id' => $revenueAccountId,
                'debit' => $baseAmount,
                'credit' => 0,
                'project_id' => null,
                'fund_id' => null,
                'dept_id' => null,
                'memo' => "Reverse course revenue - Cancelled enrollment - {$student->name}",
            ];

            $lines[] = [
                'account_id' => $cancellationRevenueAccountId,
                'debit' => 0,
                'credit' => $baseAmount,
                'project_id' => null,
                'fund_id' => null,
                'dept_id' => null,
                'memo' => "Course cancellation revenue - {$student->name}",
            ];
        } else {
            // If revenue was not recognized, reverse the original enrollment entry
            $lines[] = [
                'account_id' => $arAccountId,
                'debit' => 0,
                'credit' => $totalAmount,
                'project_id' => null,
                'fund_id' => null,
                'dept_id' => null,
                'memo' => "Reverse AR - Cancelled enrollment - {$student->name}",
            ];

            $lines[] = [
                'account_id' => $deferredRevenueAccountId,
                'debit' => $baseAmount,
                'credit' => 0,
                'project_id' => null,
                'fund_id' => null,
                'dept_id' => null,
                'memo' => "Reverse deferred revenue - Cancelled enrollment - {$student->name}",
            ];

            // Reverse PPN if applicable
            if ($ppnAmount > 0) {
                $lines[] = [
                    'account_id' => $ppnOutputAccountId,
                    'debit' => $ppnAmount,
                    'credit' => 0,
                    'project_id' => null,
                    'fund_id' => null,
                    'dept_id' => null,
                    'memo' => "Reverse PPN Output - Cancelled enrollment - {$student->name}",
                ];
            }
        }

        try {
            $journalId = $this->postingService->postJournal([
                'date' => now()->toDateString(),
                'description' => "Course Cancellation - {$course->name} - {$student->name} - Reason: {$reason}",
                'source_type' => 'enrollment_cancellation',
                'source_id' => $enrollment->id,
                'lines' => $lines,
                'posted_by' => auth()->id(),
                'status' => 'posted',
            ]);

            Log::info("Created cancellation journal entry {$journalId} for enrollment {$enrollment->id}");

            return $journalId;
        } catch (\Exception $e) {
            Log::error("Failed to create cancellation journal entry for enrollment {$enrollment->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get account IDs with category-specific logic
     */
    public function getAccountsReceivableAccountId(): int
    {
        return (int) DB::table('accounts')->where('code', '1.1.4')->value('id');
    }

    public function getDeferredRevenueAccountId(int $categoryId = null): int
    {
        if ($categoryId) {
            $category = DB::table('course_categories')->where('id', $categoryId)->first();
            if ($category) {
                $accountCode = $this->getCategorySpecificAccountCode($category->name, 'deferred');
                $accountId = DB::table('accounts')->where('code', $accountCode)->value('id');
                if ($accountId) {
                    return (int) $accountId;
                }
            }
        }
        return (int) DB::table('accounts')->where('code', '2.1.5')->value('id');
    }

    public function getRevenueAccountId(int $categoryId = null): int
    {
        if ($categoryId) {
            $category = DB::table('course_categories')->where('id', $categoryId)->first();
            if ($category) {
                $accountCode = $this->getCategorySpecificAccountCode($category->name, 'revenue');
                $accountId = DB::table('accounts')->where('code', $accountCode)->value('id');
                if ($accountId) {
                    return (int) $accountId;
                }
            }
        }
        return (int) DB::table('accounts')->where('code', '4.1.1')->value('id');
    }

    private function getCategorySpecificAccountCode(string $categoryName, string $type): ?string
    {
        $categoryMapping = [
            'Digital Marketing' => [
                'deferred' => '2.1.5.1',
                'revenue' => '4.1.1.1'
            ],
            'Data Analytics' => [
                'deferred' => '2.1.5.2',
                'revenue' => '4.1.1.2'
            ],
            'Project Management' => [
                'deferred' => '2.1.5.3',
                'revenue' => '4.1.1.3'
            ]
        ];

        return $categoryMapping[$categoryName][$type] ?? null;
    }

    public function getPPNOutputAccountId(): int
    {
        return (int) DB::table('accounts')->where('code', '2.1.3')->value('id');
    }

    private function getCashAccountId(): int
    {
        return (int) DB::table('accounts')->where('code', '1.1.2.01')->value('id');
    }

    private function getCancellationRevenueAccountId(): int
    {
        return (int) DB::table('accounts')->where('code', '4.1.1.3')->value('id');
    }
}

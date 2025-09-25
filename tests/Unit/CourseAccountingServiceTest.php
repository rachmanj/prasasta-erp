<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\CourseAccountingService;
use App\Models\Enrollment;
use App\Models\InstallmentPayment;
use App\Models\CourseBatch;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Master\Customer;
use App\Models\PaymentPlan;
use App\Models\Accounting\Journal;
use App\Models\Accounting\Account;
use App\Models\Accounting\JournalLine;
use Illuminate\Support\Facades\DB;

class CourseAccountingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CourseAccountingService $service;
    protected CourseCategory $category;
    protected Course $course;
    protected Customer $student;
    protected CourseBatch $batch;
    protected PaymentPlan $paymentPlan;
    protected Enrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        // Skip seeder for now due to data issues

        $this->service = app(CourseAccountingService::class);
        $this->setupTestData();
    }

    protected function setupTestData(): void
    {
        // Create course category
        $this->category = CourseCategory::create([
            'code' => 'DM-CAT-001',
            'name' => 'Digital Marketing',
            'description' => 'Digital Marketing Courses',
            'is_active' => true
        ]);

        // Create course
        $this->course = Course::create([
            'code' => 'DM-001',
            'name' => 'Digital Marketing Fundamentals',
            'description' => 'Basic digital marketing course',
            'category_id' => $this->category->id,
            'base_price' => 8000000,
            'duration_hours' => 40,
            'capacity' => 20,
            'status' => 'active'
        ]);

        // Create student
        $this->student = Customer::create([
            'code' => 'CUST-001',
            'name' => 'PT Maju Bersama',
            'email' => 'info@majubersama.com',
            'phone' => '081234567890',
            'company' => 'PT Maju Bersama'
        ]);

        // Create course batch
        $this->batch = CourseBatch::create([
            'course_id' => $this->course->id,
            'batch_code' => 'DM-BATCH-001',
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(30),
            'capacity' => 20,
            'status' => 'planned'
        ]);

        // Create payment plan
        $this->paymentPlan = PaymentPlan::create([
            'code' => 'PP-001',
            'name' => '4 Installments',
            'installment_count' => 4,
            'installment_amount' => 2000000,
            'installment_interval_days' => 30
        ]);

        // Create enrollment
        $this->enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'batch_id' => $this->batch->id,
            'payment_plan_id' => $this->paymentPlan->id,
            'enrollment_date' => now(),
            'total_amount' => 8000000,
            'status' => 'enrolled'
        ]);

        // Create required accounts for testing
        $this->createRequiredAccounts();
    }

    protected function createRequiredAccounts(): void
    {
        // Create basic accounts needed for course accounting (use firstOrCreate to avoid duplicates)
        \App\Models\Accounting\Account::firstOrCreate(
            ['code' => '1.1.4'],
            [
                'name' => 'Accounts Receivable - Trade',
                'type' => 'asset',
                'is_postable' => true
            ]
        );

        \App\Models\Accounting\Account::firstOrCreate(
            ['code' => '2.1.5.1'],
            [
                'name' => 'Deferred Revenue - Digital Marketing',
                'type' => 'liability',
                'is_postable' => true
            ]
        );

        \App\Models\Accounting\Account::firstOrCreate(
            ['code' => '2.1.3'],
            [
                'name' => 'PPN Output',
                'type' => 'liability',
                'is_postable' => true
            ]
        );

        \App\Models\Accounting\Account::firstOrCreate(
            ['code' => '1.1.2.01'],
            [
                'name' => 'Cash - Bank Account',
                'type' => 'asset',
                'is_postable' => true
            ]
        );

        \App\Models\Accounting\Account::firstOrCreate(
            ['code' => '4.1.1.3'],
            [
                'name' => 'Course Cancellation Revenue',
                'type' => 'income',
                'is_postable' => true
            ]
        );

        \App\Models\Accounting\Account::firstOrCreate(
            ['code' => '2.1.5.2'],
            [
                'name' => 'Deferred Revenue - Data Analytics',
                'type' => 'liability',
                'is_postable' => true
            ]
        );

        \App\Models\Accounting\Account::firstOrCreate(
            ['code' => '4.1.1.2'],
            [
                'name' => 'Course Revenue - Data Analytics',
                'type' => 'income',
                'is_postable' => true
            ]
        );

        \App\Models\Accounting\Account::firstOrCreate(
            ['code' => '4.1.1.1'],
            [
                'name' => 'Course Revenue - Digital Marketing',
                'type' => 'income',
                'is_postable' => true
            ]
        );

        \App\Models\Accounting\Account::firstOrCreate(
            ['code' => '4.1.1.3'],
            [
                'name' => 'Cancellation Revenue',
                'type' => 'income',
                'is_postable' => true
            ]
        );

        \App\Models\Accounting\Account::firstOrCreate(
            ['code' => '1.1.2.01'],
            [
                'name' => 'Cash - Bank BCA',
                'type' => 'asset',
                'is_postable' => true
            ]
        );
    }

    public function test_create_enrollment_journal_entry(): void
    {
        $journalId = $this->service->createEnrollmentJournalEntry($this->enrollment);

        $this->assertIsInt($journalId);
        $this->assertGreaterThan(0, $journalId);

        // Verify journal entry exists
        $journal = Journal::find($journalId);
        $this->assertNotNull($journal);
        $this->assertEquals('enrollment', $journal->source_type);
        $this->assertEquals($this->enrollment->id, $journal->source_id);
        $this->assertStringContainsString('Course Enrollment', $journal->description);

        // Verify journal lines
        $lines = JournalLine::where('journal_id', $journalId)->get();

        // Debug: Let's see what lines were actually created
        if ($lines->count() === 0) {
            $this->fail('No journal lines were created. Journal ID: ' . $journalId);
        }

        $this->assertCount(3, $lines);

        // Get the account IDs for verification
        $arAccountId = $this->service->getAccountsReceivableAccountId();
        $deferredAccountId = $this->service->getDeferredRevenueAccountId($this->category->id);
        $ppnAccountId = $this->service->getPPNOutputAccountId();

        // Verify debit (Accounts Receivable)
        $arLine = $lines->where('account_id', $arAccountId)->first();
        $this->assertNotNull($arLine);
        $this->assertEquals(8000000, $arLine->debit);
        $this->assertEquals(0, $arLine->credit);

        // Verify credit (Deferred Revenue)
        $deferredLine = $lines->where('account_id', $deferredAccountId)->first();
        $this->assertNotNull($deferredLine);
        $this->assertEquals(0, $deferredLine->debit);
        $this->assertEquals(7207207.21, $deferredLine->credit);

        // Verify credit (PPN Output)
        $ppnLine = $lines->where('account_id', $ppnAccountId)->first();
        $this->assertNotNull($ppnLine);
        $this->assertEquals(0, $ppnLine->debit);
        $this->assertEquals(792792.79, $ppnLine->credit);

        // Verify journal is balanced
        $totalDebit = $lines->sum('debit');
        $totalCredit = $lines->sum('credit');
        $this->assertEqualsWithDelta($totalDebit, $totalCredit, 0.01);
    }

    public function test_process_payment_journal_entry(): void
    {
        // Create installment payment
        $payment = InstallmentPayment::create([
            'enrollment_id' => $this->enrollment->id,
            'installment_number' => 1,
            'due_date' => now(),
            'amount' => 2000000,
            'paid_amount' => 2000000,
            'status' => 'paid',
            'paid_date' => now()
        ]);

        $journalId = $this->service->processPaymentJournalEntry($payment);

        $this->assertIsInt($journalId);
        $this->assertGreaterThan(0, $journalId);

        // Verify journal entry exists
        $journal = Journal::find($journalId);
        $this->assertNotNull($journal);
        $this->assertEquals('installment_payment', $journal->source_type);
        $this->assertEquals($payment->id, $journal->source_id);
        $this->assertStringContainsString('Course Payment', $journal->description);

        // Verify journal lines
        $lines = JournalLine::where('journal_id', $journalId)->get();
        $this->assertCount(2, $lines);

        // Get account IDs for verification
        $cashAccountId = \App\Models\Accounting\Account::where('code', '1.1.2.01')->first()->id;
        $arAccountId = $this->service->getAccountsReceivableAccountId();

        // Verify debit (Cash)
        $cashLine = $lines->where('account_id', $cashAccountId)->first();
        $this->assertNotNull($cashLine);
        $this->assertEquals(2000000, $cashLine->debit);
        $this->assertEquals(0, $cashLine->credit);

        // Verify credit (Accounts Receivable)
        $arLine = $lines->where('account_id', $arAccountId)->first();
        $this->assertNotNull($arLine);
        $this->assertEquals(0, $arLine->debit);
        $this->assertEquals(2000000, $arLine->credit);

        // Verify journal is balanced
        $totalDebit = $lines->sum('debit');
        $totalCredit = $lines->sum('credit');
        $this->assertEqualsWithDelta($totalDebit, $totalCredit, 0.01);
    }

    public function test_recognize_revenue_for_batch(): void
    {
        // First create enrollment journal entry
        $this->service->createEnrollmentJournalEntry($this->enrollment);

        // Start the batch
        $this->batch->update(['status' => 'ongoing']);

        $journalId = $this->service->recognizeRevenueForBatch($this->batch);

        $this->assertIsInt($journalId);
        $this->assertGreaterThan(0, $journalId);

        // Verify journal entry exists
        $journal = Journal::find($journalId);
        $this->assertNotNull($journal);
        $this->assertEquals('course_batch', $journal->source_type);
        $this->assertEquals($this->batch->id, $journal->source_id);
        $this->assertStringContainsString('Revenue Recognition', $journal->description);

        // Verify journal lines
        $lines = JournalLine::where('journal_id', $journalId)->get();
        $this->assertCount(2, $lines);

        // Get account IDs for verification
        $deferredAccountId = $this->service->getDeferredRevenueAccountId($this->category->id);
        $revenueAccountId = $this->service->getRevenueAccountId($this->category->id);

        // Verify debit (Deferred Revenue)
        $deferredLine = $lines->where('account_id', $deferredAccountId)->first();
        $this->assertNotNull($deferredLine);
        $this->assertEquals(7207207.21, $deferredLine->debit);
        $this->assertEquals(0, $deferredLine->credit);

        // Verify credit (Course Revenue)
        $revenueLine = $lines->where('account_id', $revenueAccountId)->first();
        $this->assertNotNull($revenueLine);
        $this->assertEquals(0, $revenueLine->debit);
        $this->assertEquals(7207207.21, $revenueLine->credit);

        // Verify journal is balanced
        $totalDebit = $lines->sum('debit');
        $totalCredit = $lines->sum('credit');
        $this->assertEqualsWithDelta($totalDebit, $totalCredit, 0.01);

        // Verify batch is marked as revenue recognized
        $this->batch->refresh();
        $this->assertTrue($this->batch->revenue_recognized);
        $this->assertNotNull($this->batch->revenue_recognized_at);
        $this->assertEquals($journalId, $this->batch->revenue_recognition_journal_id);
    }

    public function test_handle_course_cancellation(): void
    {
        // First create enrollment journal entry
        $this->service->createEnrollmentJournalEntry($this->enrollment);

        $journalId = $this->service->handleCourseCancellation($this->enrollment, 'Student request');

        $this->assertIsInt($journalId);
        $this->assertGreaterThan(0, $journalId);

        // Verify journal entry exists
        $journal = Journal::find($journalId);
        $this->assertNotNull($journal);
        $this->assertEquals('enrollment_cancellation', $journal->source_type);
        $this->assertEquals($this->enrollment->id, $journal->source_id);
        $this->assertStringContainsString('Course Cancellation', $journal->description);

        // Verify journal lines
        $lines = JournalLine::where('journal_id', $journalId)->get();
        $this->assertCount(3, $lines);

        // Get account IDs for verification
        $deferredAccountId = $this->service->getDeferredRevenueAccountId($this->category->id);
        $arAccountId = $this->service->getAccountsReceivableAccountId();
        $ppnAccountId = $this->service->getPPNOutputAccountId();

        // Verify debit (Deferred Revenue)
        $deferredLine = $lines->where('account_id', $deferredAccountId)->first();
        $this->assertNotNull($deferredLine);
        $this->assertEquals(7207207.21, $deferredLine->debit);
        $this->assertEquals(0, $deferredLine->credit);

        // Verify credit (Accounts Receivable)
        $arLine = $lines->where('account_id', $arAccountId)->first();
        $this->assertNotNull($arLine);
        $this->assertEquals(0, $arLine->debit);
        $this->assertEquals(8000000, $arLine->credit);

        // Verify debit (PPN Output)
        $ppnLine = $lines->where('account_id', $ppnAccountId)->first();
        $this->assertNotNull($ppnLine);
        $this->assertEquals(792792.79, $ppnLine->debit);
        $this->assertEquals(0, $ppnLine->credit);

        // Verify journal is balanced
        $totalDebit = $lines->sum('debit');
        $totalCredit = $lines->sum('credit');
        $this->assertEqualsWithDelta($totalDebit, $totalCredit, 0.01);
    }

    public function test_get_accounts_receivable_account_id(): void
    {
        $accountId = $this->service->getAccountsReceivableAccountId();

        $this->assertIsInt($accountId);
        $this->assertGreaterThan(0, $accountId);

        $account = Account::find($accountId);
        $this->assertNotNull($account);
        $this->assertEquals('1.1.4', $account->code);
        $this->assertStringContainsString('Accounts Receivable', $account->name);
    }

    public function test_get_deferred_revenue_account_id(): void
    {
        $accountId = $this->service->getDeferredRevenueAccountId($this->category->id);

        $this->assertIsInt($accountId);
        $this->assertGreaterThan(0, $accountId);

        $account = Account::find($accountId);
        $this->assertNotNull($account);
        $this->assertEquals('2.1.5.1', $account->code);
        $this->assertStringContainsString('Deferred Revenue', $account->name);
    }

    public function test_get_revenue_account_id(): void
    {
        $accountId = $this->service->getRevenueAccountId($this->category->id);

        $this->assertIsInt($accountId);
        $this->assertGreaterThan(0, $accountId);

        $account = Account::find($accountId);
        $this->assertNotNull($account);
        $this->assertEquals('4.1.1.1', $account->code);
        $this->assertStringContainsString('Course Revenue', $account->name);
    }

    public function test_get_ppn_output_account_id(): void
    {
        $accountId = $this->service->getPPNOutputAccountId();

        $this->assertIsInt($accountId);
        $this->assertGreaterThan(0, $accountId);

        $account = Account::find($accountId);
        $this->assertNotNull($account);
        $this->assertEquals('2.1.3', $account->code);
        $this->assertStringContainsString('PPN Output', $account->name);
    }

    public function test_ppn_calculation(): void
    {
        $grossAmount = 8000000;
        $ppnRate = 0.11;

        $netAmount = $grossAmount / (1 + $ppnRate);
        $ppnAmount = $grossAmount - $netAmount;

        $this->assertEquals(792792.79, round($ppnAmount, 2));
        $this->assertEquals(7207207.21, round($netAmount, 2));
    }

    public function test_category_specific_account_mapping(): void
    {
        // Test Digital Marketing category
        $digitalMarketingDeferred = $this->service->getDeferredRevenueAccountId($this->category->id);
        $digitalMarketingRevenue = $this->service->getRevenueAccountId($this->category->id);

        $this->assertIsInt($digitalMarketingDeferred);
        $this->assertIsInt($digitalMarketingRevenue);

        // Test Data Analytics category (create category first)
        $analyticsCategory = CourseCategory::create([
            'code' => 'DA-CAT-001',
            'name' => 'Data Analytics',
            'description' => 'Data Analytics Courses',
            'is_active' => true
        ]);

        $dataAnalyticsDeferred = $this->service->getDeferredRevenueAccountId($analyticsCategory->id);
        $dataAnalyticsRevenue = $this->service->getRevenueAccountId($analyticsCategory->id);

        $this->assertIsInt($dataAnalyticsDeferred);
        $this->assertIsInt($dataAnalyticsRevenue);

        // Test Project Management category (create category first)
        $projectCategory = CourseCategory::create([
            'code' => 'PM-CAT-001',
            'name' => 'Project Management',
            'description' => 'Project Management Courses',
            'is_active' => true
        ]);

        $projectManagementDeferred = $this->service->getDeferredRevenueAccountId($projectCategory->id);
        $projectManagementRevenue = $this->service->getRevenueAccountId($projectCategory->id);

        $this->assertIsInt($projectManagementDeferred);
        $this->assertIsInt($projectManagementRevenue);
    }

    public function test_enrollment_with_different_categories(): void
    {
        // Create Data Analytics category and course
        $analyticsCategory = CourseCategory::create([
            'code' => 'DA-CAT-002',
            'name' => 'Data Analytics',
            'description' => 'Data Analytics Courses',
            'is_active' => true
        ]);

        $analyticsCourse = Course::create([
            'code' => 'DA-001',
            'name' => 'Data Analytics Bootcamp',
            'description' => 'Advanced data analytics course',
            'category_id' => $analyticsCategory->id,
            'base_price' => 12000000,
            'duration_hours' => 60,
            'capacity' => 15,
            'status' => 'active'
        ]);

        $analyticsBatch = CourseBatch::create([
            'course_id' => $analyticsCourse->id,
            'batch_code' => 'DA-BATCH-001',
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(45),
            'capacity' => 15,
            'status' => 'planned'
        ]);

        $analyticsEnrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'batch_id' => $analyticsBatch->id,
            'payment_plan_id' => $this->paymentPlan->id,
            'enrollment_date' => now(),
            'total_amount' => 12000000,
            'status' => 'enrolled'
        ]);

        $journalId = $this->service->createEnrollmentJournalEntry($analyticsEnrollment);

        // Verify journal entry uses correct account codes for Data Analytics
        $lines = JournalLine::where('journal_id', $journalId)->get();

        $deferredAccountId = \App\Models\Accounting\Account::where('code', '2.1.5.2')->first()->id;
        $deferredLine = $lines->where('account_id', $deferredAccountId)->first();
        $this->assertNotNull($deferredLine);
        $this->assertEquals(10810810.81, $deferredLine->credit); // 12M - PPN

        $revenueAccountId = \App\Models\Accounting\Account::where('code', '4.1.1.2')->first()->id;
        $revenueLine = $lines->where('account_id', $revenueAccountId)->first();
        $this->assertNull($revenueLine); // Should not exist in enrollment journal
    }

    public function test_duplicate_revenue_recognition_prevention(): void
    {
        // Create a fresh batch for this test
        $freshBatch = CourseBatch::create([
            'course_id' => $this->course->id,
            'batch_code' => 'DM-BATCH-002',
            'start_date' => now()->addDays(30),
            'end_date' => now()->addDays(60),
            'capacity' => 20,
            'status' => 'ongoing'
        ]);

        // Create enrollment for the fresh batch
        $freshEnrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'batch_id' => $freshBatch->id,
            'payment_plan_id' => $this->paymentPlan->id,
            'enrollment_date' => now(),
            'total_amount' => 8000000,
            'status' => 'enrolled'
        ]);

        // First create enrollment journal entry
        $this->service->createEnrollmentJournalEntry($freshEnrollment);

        // Recognize revenue
        $journalId1 = $this->service->recognizeRevenueForBatch($freshBatch);

        // Try to recognize revenue again
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Revenue already recognized for this batch');
        $this->service->recognizeRevenueForBatch($freshBatch);

        // Verify only one revenue recognition journal exists
        $revenueJournals = Journal::where('source_type', 'course_batch')
            ->where('source_id', $freshBatch->id)
            ->where('description', 'like', '%Revenue Recognition%')
            ->count();

        $this->assertEquals(1, $revenueJournals);
    }

    public function test_enrollment_after_revenue_recognition_cancellation(): void
    {
        // Create enrollment journal entry
        $this->service->createEnrollmentJournalEntry($this->enrollment);

        // Start batch and recognize revenue
        $this->batch->update(['status' => 'ongoing']);
        $this->service->recognizeRevenueForBatch($this->batch);

        // Cancel enrollment after revenue recognition
        $cancellationJournalId = $this->service->handleCourseCancellation($this->enrollment, 'Student request');

        // Verify cancellation journal entry
        $journal = Journal::find($cancellationJournalId);
        $this->assertNotNull($journal);
        $this->assertStringContainsString('Course Cancellation', $journal->description);

        // Verify journal lines for cancellation
        $lines = JournalLine::where('journal_id', $cancellationJournalId)->get();
        $this->assertCount(2, $lines);

        // Get account IDs for verification
        $revenueAccountId = $this->service->getRevenueAccountId($this->category->id);
        $cancellationAccountId = \App\Models\Accounting\Account::where('code', '4.1.1.3')->first()->id;

        // Should debit Course Revenue (reverse the recognized revenue)
        $revenueLine = $lines->where('account_id', $revenueAccountId)->first();
        $this->assertNotNull($revenueLine);
        $this->assertEquals(7207207.21, $revenueLine->debit);

        // Should credit Cancellation Revenue
        $cancellationLine = $lines->where('account_id', $cancellationAccountId)->first();
        $this->assertNotNull($cancellationLine);
        $this->assertEquals(7207207.21, $cancellationLine->credit);
    }
}

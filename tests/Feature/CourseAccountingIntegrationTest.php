<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseBatch;
use App\Models\Master\Customer;
use App\Models\Enrollment;
use App\Models\PaymentPlan;
use App\Models\InstallmentPayment;
use App\Models\Accounting\Journal;
use App\Models\Accounting\Account;
use App\Models\Accounting\JournalLine;
use App\Services\CourseAccountingService;
use App\Events\EnrollmentCreated;
use App\Events\PaymentReceived;
use App\Events\BatchStarted;
use App\Events\CourseCancelled;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;

class CourseAccountingIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected CourseAccountingService $service;
    protected CourseCategory $category;
    protected Course $course;
    protected CourseBatch $batch;
    protected Customer $student;
    protected PaymentPlan $paymentPlan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->seed();

        $this->user = User::factory()->create();
        $this->user->givePermissionTo([
            'enrollments.create',
            'installment_payments.create',
            'course_batches.update',
            'enrollments.cancel'
        ]);

        $this->actingAs($this->user);
        $this->service = app(CourseAccountingService::class);
        $this->setupTestData();
    }

    protected function setupTestData(): void
    {
        // Create course category
        $this->category = CourseCategory::create([
            'name' => 'Digital Marketing',
            'description' => 'Digital Marketing Courses'
        ]);

        // Create course
        $this->course = Course::create([
            'code' => 'DM-001',
            'name' => 'Digital Marketing Fundamentals',
            'description' => 'Basic digital marketing course',
            'category_id' => $this->category->id,
            'base_price' => 8000000,
            'status' => 'active'
        ]);

        // Create student
        $this->student = Customer::create([
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
            'status' => 'scheduled'
        ]);

        // Create payment plan
        $this->paymentPlan = PaymentPlan::create([
            'name' => '4 Installments',
            'installment_count' => 4,
            'installment_amount' => 2000000
        ]);
    }

    public function test_complete_enrollment_to_revenue_recognition_workflow(): void
    {
        // Step 1: Create enrollment
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'batch_id' => $this->batch->id,
            'payment_plan_id' => $this->paymentPlan->id,
            'enrollment_date' => now(),
            'total_amount' => 8000000,
            'status' => 'active'
        ]);

        // Verify enrollment is not accounted for initially
        $this->assertFalse($enrollment->is_accounted_for);
        $this->assertNull($enrollment->journal_entry_id);

        // Step 2: Trigger enrollment accounting (simulate event)
        $journalId = $this->service->createEnrollmentJournalEntry($enrollment);

        // Verify enrollment is now accounted for
        $enrollment->refresh();
        $this->assertTrue($enrollment->is_accounted_for);
        $this->assertEquals($journalId, $enrollment->journal_entry_id);
        $this->assertNotNull($enrollment->accounted_at);

        // Verify journal entry
        $journal = Journal::find($journalId);
        $this->assertNotNull($journal);
        $this->assertEquals('enrollment', $journal->source_type);
        $this->assertEquals($enrollment->id, $journal->source_id);

        // Verify journal lines
        $lines = JournalLine::where('journal_id', $journalId)->get();
        $this->assertCount(3, $lines);

        // Verify Accounts Receivable (Debit)
        $arLine = $lines->where('account_code', '1.1.4')->first();
        $this->assertEquals(8000000, $arLine->debit);
        $this->assertEquals(0, $arLine->credit);

        // Verify Deferred Revenue (Credit)
        $deferredLine = $lines->where('account_code', '2.1.5.1')->first();
        $this->assertEquals(0, $deferredLine->debit);
        $this->assertEquals(7207207.21, $deferredLine->credit);

        // Verify PPN Output (Credit)
        $ppnLine = $lines->where('account_code', '2.1.3')->first();
        $this->assertEquals(0, $ppnLine->debit);
        $this->assertEquals(792792.79, $ppnLine->credit);

        // Verify journal is balanced
        $totalDebit = $lines->sum('debit');
        $totalCredit = $lines->sum('credit');
        $this->assertEqualsWithDelta($totalDebit, $totalCredit, 0.01);

        // Step 3: Process payment
        $payment = InstallmentPayment::create([
            'enrollment_id' => $enrollment->id,
            'installment_number' => 1,
            'due_date' => now(),
            'amount' => 2000000,
            'status' => 'paid',
            'paid_at' => now()
        ]);

        // Verify payment is not accounted for initially
        $this->assertFalse($payment->is_accounted_for);
        $this->assertNull($payment->journal_entry_id);

        // Trigger payment accounting
        $paymentJournalId = $this->service->processPaymentJournalEntry($payment);

        // Verify payment is now accounted for
        $payment->refresh();
        $this->assertTrue($payment->is_accounted_for);
        $this->assertEquals($paymentJournalId, $payment->journal_entry_id);
        $this->assertNotNull($payment->accounted_at);

        // Verify payment journal entry
        $paymentJournal = Journal::find($paymentJournalId);
        $this->assertNotNull($paymentJournal);
        $this->assertEquals('installment_payment', $paymentJournal->source_type);
        $this->assertEquals($payment->id, $paymentJournal->source_id);

        // Verify payment journal lines
        $paymentLines = JournalLine::where('journal_id', $paymentJournalId)->get();
        $this->assertCount(2, $paymentLines);

        // Verify Cash (Debit)
        $cashLine = $paymentLines->where('account_code', '1.1.2.01')->first();
        $this->assertEquals(2000000, $cashLine->debit);
        $this->assertEquals(0, $cashLine->credit);

        // Verify Accounts Receivable (Credit)
        $arPaymentLine = $paymentLines->where('account_code', '1.1.4')->first();
        $this->assertEquals(0, $arPaymentLine->debit);
        $this->assertEquals(2000000, $arPaymentLine->credit);

        // Step 4: Start batch and recognize revenue
        $this->batch->update(['status' => 'ongoing']);

        // Verify batch is not revenue recognized initially
        $this->assertFalse($this->batch->revenue_recognized);
        $this->assertNull($this->batch->revenue_recognition_journal_id);

        // Trigger revenue recognition
        $revenueJournalId = $this->service->recognizeRevenueForBatch($this->batch);

        // Verify batch is now revenue recognized
        $this->batch->refresh();
        $this->assertTrue($this->batch->revenue_recognized);
        $this->assertEquals($revenueJournalId, $this->batch->revenue_recognition_journal_id);
        $this->assertNotNull($this->batch->revenue_recognized_at);

        // Verify revenue recognition journal entry
        $revenueJournal = Journal::find($revenueJournalId);
        $this->assertNotNull($revenueJournal);
        $this->assertEquals('course_batch', $revenueJournal->source_type);
        $this->assertEquals($this->batch->id, $revenueJournal->source_id);

        // Verify revenue recognition journal lines
        $revenueLines = JournalLine::where('journal_id', $revenueJournalId)->get();
        $this->assertCount(2, $revenueLines);

        // Verify Deferred Revenue (Debit)
        $deferredRevenueLine = $revenueLines->where('account_code', '2.1.5.1')->first();
        $this->assertEquals(7207207.21, $deferredRevenueLine->debit);
        $this->assertEquals(0, $deferredRevenueLine->credit);

        // Verify Course Revenue (Credit)
        $courseRevenueLine = $revenueLines->where('account_code', '4.1.1.1')->first();
        $this->assertEquals(0, $courseRevenueLine->debit);
        $this->assertEquals(7207207.21, $courseRevenueLine->credit);

        // Verify revenue recognition journal is balanced
        $totalDebit = $revenueLines->sum('debit');
        $totalCredit = $revenueLines->sum('credit');
        $this->assertEqualsWithDelta($totalDebit, $totalCredit, 0.01);
    }

    public function test_course_cancellation_workflow(): void
    {
        // Create enrollment and process accounting
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'batch_id' => $this->batch->id,
            'payment_plan_id' => $this->paymentPlan->id,
            'enrollment_date' => now(),
            'total_amount' => 8000000,
            'status' => 'active'
        ]);

        // Create enrollment journal entry
        $this->service->createEnrollmentJournalEntry($enrollment);

        // Cancel enrollment
        $cancellationJournalId = $this->service->handleCourseCancellation($enrollment, 'Student request');

        // Verify cancellation journal entry
        $cancellationJournal = Journal::find($cancellationJournalId);
        $this->assertNotNull($cancellationJournal);
        $this->assertEquals('enrollment', $cancellationJournal->source_type);
        $this->assertEquals($enrollment->id, $cancellationJournal->source_id);
        $this->assertStringContainsString('Course Cancellation', $cancellationJournal->description);

        // Verify cancellation journal lines
        $cancellationLines = JournalLine::where('journal_id', $cancellationJournalId)->get();
        $this->assertCount(2, $cancellationLines);

        // Verify Deferred Revenue (Debit)
        $deferredCancellationLine = $cancellationLines->where('account_code', '2.1.5.1')->first();
        $this->assertEquals(7207207.21, $deferredCancellationLine->debit);
        $this->assertEquals(0, $deferredCancellationLine->credit);

        // Verify Cancellation Revenue (Credit)
        $cancellationRevenueLine = $cancellationLines->where('account_code', '4.1.1.3')->first();
        $this->assertEquals(0, $cancellationRevenueLine->debit);
        $this->assertEquals(7207207.21, $cancellationRevenueLine->credit);

        // Verify cancellation journal is balanced
        $totalDebit = $cancellationLines->sum('debit');
        $totalCredit = $cancellationLines->sum('credit');
        $this->assertEqualsWithDelta($totalDebit, $totalCredit, 0.01);
    }

    public function test_cancellation_after_revenue_recognition_workflow(): void
    {
        // Create enrollment and process full workflow
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'batch_id' => $this->batch->id,
            'payment_plan_id' => $this->paymentPlan->id,
            'enrollment_date' => now(),
            'total_amount' => 8000000,
            'status' => 'active'
        ]);

        // Create enrollment journal entry
        $this->service->createEnrollmentJournalEntry($enrollment);

        // Start batch and recognize revenue
        $this->batch->update(['status' => 'ongoing']);
        $this->service->recognizeRevenueForBatch($this->batch);

        // Cancel enrollment after revenue recognition
        $cancellationJournalId = $this->service->handleCourseCancellation($enrollment, 'Student request');

        // Verify cancellation journal entry
        $cancellationJournal = Journal::find($cancellationJournalId);
        $this->assertNotNull($cancellationJournal);
        $this->assertStringContainsString('Course Cancellation', $cancellationJournal->description);

        // Verify cancellation journal lines
        $cancellationLines = JournalLine::where('journal_id', $cancellationJournalId)->get();
        $this->assertCount(2, $cancellationLines);

        // Should debit Course Revenue (reverse the recognized revenue)
        $courseRevenueLine = $cancellationLines->where('account_code', '4.1.1.1')->first();
        $this->assertNotNull($courseRevenueLine);
        $this->assertEquals(7207207.21, $courseRevenueLine->debit);
        $this->assertEquals(0, $courseRevenueLine->credit);

        // Should credit Cancellation Revenue
        $cancellationRevenueLine = $cancellationLines->where('account_code', '4.1.1.3')->first();
        $this->assertNotNull($cancellationRevenueLine);
        $this->assertEquals(0, $cancellationRevenueLine->debit);
        $this->assertEquals(7207207.21, $cancellationRevenueLine->credit);
    }

    public function test_multiple_enrollments_same_batch_workflow(): void
    {
        // Create multiple students
        $student2 = Customer::create([
            'name' => 'CV Teknologi Jaya',
            'email' => 'info@teknologijaya.com',
            'phone' => '081234567891',
            'company' => 'CV Teknologi Jaya'
        ]);

        // Create first enrollment
        $enrollment1 = Enrollment::create([
            'student_id' => $this->student->id,
            'batch_id' => $this->batch->id,
            'payment_plan_id' => $this->paymentPlan->id,
            'enrollment_date' => now(),
            'total_amount' => 8000000,
            'status' => 'active'
        ]);

        // Create second enrollment
        $enrollment2 = Enrollment::create([
            'student_id' => $student2->id,
            'batch_id' => $this->batch->id,
            'payment_plan_id' => $this->paymentPlan->id,
            'enrollment_date' => now(),
            'total_amount' => 8000000,
            'status' => 'active'
        ]);

        // Process accounting for both enrollments
        $this->service->createEnrollmentJournalEntry($enrollment1);
        $this->service->createEnrollmentJournalEntry($enrollment2);

        // Verify both enrollments are accounted for
        $this->assertTrue($enrollment1->fresh()->is_accounted_for);
        $this->assertTrue($enrollment2->fresh()->is_accounted_for);

        // Start batch and recognize revenue
        $this->batch->update(['status' => 'ongoing']);
        $revenueJournalId = $this->service->recognizeRevenueForBatch($this->batch);

        // Verify revenue recognition journal entry
        $revenueJournal = Journal::find($revenueJournalId);
        $this->assertNotNull($revenueJournal);

        // Verify revenue recognition journal lines
        $revenueLines = JournalLine::where('journal_id', $revenueJournalId)->get();
        $this->assertCount(2, $revenueLines);

        // Verify Deferred Revenue (Debit) - should be double amount
        $deferredRevenueLine = $revenueLines->where('account_code', '2.1.5.1')->first();
        $this->assertEquals(14414414.42, $deferredRevenueLine->debit); // 2 * 7207207.21

        // Verify Course Revenue (Credit) - should be double amount
        $courseRevenueLine = $revenueLines->where('account_code', '4.1.1.1')->first();
        $this->assertEquals(14414414.42, $courseRevenueLine->credit); // 2 * 7207207.21
    }

    public function test_event_driven_architecture(): void
    {
        Event::fake();

        // Create enrollment
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'batch_id' => $this->batch->id,
            'payment_plan_id' => $this->paymentPlan->id,
            'enrollment_date' => now(),
            'total_amount' => 8000000,
            'status' => 'active'
        ]);

        // Dispatch enrollment created event
        EnrollmentCreated::dispatch($enrollment);

        // Verify event was dispatched
        Event::assertDispatched(EnrollmentCreated::class, function ($event) use ($enrollment) {
            return $event->enrollment->id === $enrollment->id;
        });

        // Create payment
        $payment = InstallmentPayment::create([
            'enrollment_id' => $enrollment->id,
            'installment_number' => 1,
            'due_date' => now(),
            'amount' => 2000000,
            'status' => 'paid',
            'paid_at' => now()
        ]);

        // Dispatch payment received event
        PaymentReceived::dispatch($payment);

        // Verify event was dispatched
        Event::assertDispatched(PaymentReceived::class, function ($event) use ($payment) {
            return $event->payment->id === $payment->id;
        });

        // Start batch
        $this->batch->update(['status' => 'ongoing']);

        // Dispatch batch started event
        BatchStarted::dispatch($this->batch);

        // Verify event was dispatched
        Event::assertDispatched(BatchStarted::class, function ($event) {
            return $event->batch->id === $this->batch->id;
        });

        // Cancel enrollment
        $enrollment->update(['status' => 'cancelled']);

        // Dispatch course cancelled event
        CourseCancelled::dispatch($enrollment, 'Student request');

        // Verify event was dispatched
        Event::assertDispatched(CourseCancelled::class, function ($event) use ($enrollment) {
            return $event->enrollment->id === $enrollment->id;
        });
    }

    public function test_account_balance_verification(): void
    {
        // Create enrollment and process accounting
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'batch_id' => $this->batch->id,
            'payment_plan_id' => $this->paymentPlan->id,
            'enrollment_date' => now(),
            'total_amount' => 8000000,
            'status' => 'active'
        ]);

        // Get initial account balances
        $arAccount = Account::where('code', '1.1.4')->first();
        $deferredAccount = Account::where('code', '2.1.5.1')->first();
        $ppnAccount = Account::where('code', '2.1.3')->first();
        $cashAccount = Account::where('code', '1.1.2.01')->first();
        $revenueAccount = Account::where('code', '4.1.1.1')->first();

        $initialARBalance = $this->getAccountBalance($arAccount->id);
        $initialDeferredBalance = $this->getAccountBalance($deferredAccount->id);
        $initialPPNBalance = $this->getAccountBalance($ppnAccount->id);
        $initialCashBalance = $this->getAccountBalance($cashAccount->id);
        $initialRevenueBalance = $this->getAccountBalance($revenueAccount->id);

        // Create enrollment journal entry
        $this->service->createEnrollmentJournalEntry($enrollment);

        // Verify account balances after enrollment
        $this->assertEquals($initialARBalance + 8000000, $this->getAccountBalance($arAccount->id));
        $this->assertEquals($initialDeferredBalance + 7207207.21, $this->getAccountBalance($deferredAccount->id));
        $this->assertEquals($initialPPNBalance + 792792.79, $this->getAccountBalance($ppnAccount->id));

        // Process payment
        $payment = InstallmentPayment::create([
            'enrollment_id' => $enrollment->id,
            'installment_number' => 1,
            'due_date' => now(),
            'amount' => 2000000,
            'status' => 'paid',
            'paid_at' => now()
        ]);

        $this->service->processPaymentJournalEntry($payment);

        // Verify account balances after payment
        $this->assertEquals($initialARBalance + 6000000, $this->getAccountBalance($arAccount->id)); // 8M - 2M
        $this->assertEquals($initialCashBalance + 2000000, $this->getAccountBalance($cashAccount->id));

        // Start batch and recognize revenue
        $this->batch->update(['status' => 'ongoing']);
        $this->service->recognizeRevenueForBatch($this->batch);

        // Verify account balances after revenue recognition
        $this->assertEquals($initialDeferredBalance, $this->getAccountBalance($deferredAccount->id)); // Back to initial
        $this->assertEquals($initialRevenueBalance + 7207207.21, $this->getAccountBalance($revenueAccount->id));
    }

    public function test_duplicate_revenue_recognition_prevention(): void
    {
        // Create enrollment and process accounting
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'batch_id' => $this->batch->id,
            'payment_plan_id' => $this->paymentPlan->id,
            'enrollment_date' => now(),
            'total_amount' => 8000000,
            'status' => 'active'
        ]);

        $this->service->createEnrollmentJournalEntry($enrollment);

        // Start batch and recognize revenue
        $this->batch->update(['status' => 'ongoing']);
        $revenueJournalId1 = $this->service->recognizeRevenueForBatch($this->batch);

        // Try to recognize revenue again
        $revenueJournalId2 = $this->service->recognizeRevenueForBatch($this->batch);

        // Should return the same journal ID
        $this->assertEquals($revenueJournalId1, $revenueJournalId2);

        // Verify only one revenue recognition journal exists
        $revenueJournals = Journal::where('source_type', 'course_batch')
            ->where('source_id', $this->batch->id)
            ->where('description', 'like', '%Revenue Recognition%')
            ->count();

        $this->assertEquals(1, $revenueJournals);
    }

    public function test_performance_with_multiple_transactions(): void
    {
        $startTime = microtime(true);

        // Create 10 enrollments
        for ($i = 1; $i <= 10; $i++) {
            $student = Customer::create([
                'name' => "Student {$i}",
                'email' => "student{$i}@test.com",
                'phone' => "08123456789{$i}",
                'company' => "Company {$i}"
            ]);

            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'batch_id' => $this->batch->id,
                'payment_plan_id' => $this->paymentPlan->id,
                'enrollment_date' => now(),
                'total_amount' => 8000000,
                'status' => 'active'
            ]);

            $this->service->createEnrollmentJournalEntry($enrollment);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Assert that 10 enrollments can be processed within 5 seconds
        $this->assertLessThan(5.0, $executionTime, '10 enrollments should be processed within 5 seconds');

        // Verify all enrollments are accounted for
        $accountedEnrollments = Enrollment::where('is_accounted_for', true)->count();
        $this->assertEquals(10, $accountedEnrollments);

        // Verify all journal entries are created
        $enrollmentJournals = Journal::where('source_type', 'enrollment')->count();
        $this->assertEquals(10, $enrollmentJournals);
    }

    protected function getAccountBalance(int $accountId): float
    {
        $debit = DB::table('journal_lines')
            ->where('account_id', $accountId)
            ->sum('debit');

        $credit = DB::table('journal_lines')
            ->where('account_id', $accountId)
            ->sum('credit');

        return $debit - $credit;
    }
}

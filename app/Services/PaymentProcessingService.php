<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\InstallmentPayment;
use App\Models\RevenueRecognition;
use App\Models\PaymentPlan;
use Carbon\Carbon;

class PaymentProcessingService
{
    /**
     * Generate installment payments for an enrollment
     */
    public function generateInstallmentPayments(Enrollment $enrollment): array
    {
        if (!$enrollment->paymentPlan) {
            throw new \Exception('Enrollment must have a payment plan');
        }

        $paymentPlan = $enrollment->paymentPlan;
        $totalAmount = $enrollment->total_amount;

        // Calculate down payment
        $downPaymentAmount = $paymentPlan->calculateDownPaymentAmount($totalAmount);
        $remainingAmount = $totalAmount - $downPaymentAmount;

        // Calculate installment amount
        $installmentAmount = $remainingAmount / $paymentPlan->installment_count;

        $installments = [];
        $currentDate = $enrollment->enrollment_date;

        // Create down payment if applicable
        if ($downPaymentAmount > 0) {
            $installments[] = InstallmentPayment::create([
                'enrollment_id' => $enrollment->id,
                'installment_number' => 0, // Down payment
                'amount' => $downPaymentAmount,
                'due_date' => $currentDate,
                'status' => 'pending',
            ]);
        }

        // Create regular installments
        for ($i = 1; $i <= $paymentPlan->installment_count; $i++) {
            $dueDate = $currentDate->copy()->addDays($paymentPlan->installment_interval_days * $i);

            $installments[] = InstallmentPayment::create([
                'enrollment_id' => $enrollment->id,
                'installment_number' => $i,
                'amount' => $installmentAmount,
                'due_date' => $dueDate,
                'status' => 'pending',
            ]);
        }

        return $installments;
    }

    /**
     * Process payment for an installment
     */
    public function processPayment(InstallmentPayment $installment, float $paidAmount, string $paymentMethod = null, string $referenceNumber = null): InstallmentPayment
    {
        $installment->markAsPaid($paidAmount, $paymentMethod, $referenceNumber);

        // Update enrollment status if all installments are paid
        $this->checkEnrollmentCompletion($installment->enrollment);

        return $installment;
    }

    /**
     * Update overdue installments with late fees
     */
    public function updateOverdueInstallments(): int
    {
        $overdueInstallments = InstallmentPayment::where('status', 'pending')
            ->where('due_date', '<', Carbon::today())
            ->get();

        $updatedCount = 0;

        foreach ($overdueInstallments as $installment) {
            if ($installment->enrollment->paymentPlan) {
                $lateFeePercentage = $installment->enrollment->paymentPlan->late_fee_percentage ?? 0;
                $installment->updateLateFee($lateFeePercentage);
                $updatedCount++;
            }
        }

        return $updatedCount;
    }

    /**
     * Generate revenue recognition entries
     */
    public function generateRevenueRecognition(Enrollment $enrollment): RevenueRecognition
    {
        // Create deferred revenue entry
        $deferredRevenue = RevenueRecognition::createDeferredRevenue(
            $enrollment,
            $enrollment->total_amount,
            'Deferred revenue from course enrollment'
        );

        return $deferredRevenue;
    }

    /**
     * Recognize revenue when course batch starts
     */
    public function recognizeRevenueForBatch(CourseBatch $batch): array
    {
        $recognitions = [];

        // Get all enrollments for this batch
        $enrollments = $batch->enrollments()->where('status', 'enrolled')->get();

        foreach ($enrollments as $enrollment) {
            // Find deferred revenue for this enrollment
            $deferredRevenue = $enrollment->revenueRecognitions()
                ->where('type', 'deferred')
                ->where('is_posted', false)
                ->first();

            if ($deferredRevenue) {
                // Recognize the revenue
                $deferredRevenue->recognize('Revenue recognized as course batch started');
                $recognitions[] = $deferredRevenue;
            }
        }

        return $recognitions;
    }

    /**
     * Get payment summary for an enrollment
     */
    public function getPaymentSummary(Enrollment $enrollment): array
    {
        $installments = $enrollment->installmentPayments;

        $summary = [
            'total_amount' => $enrollment->total_amount,
            'total_paid' => $installments->where('status', 'paid')->sum('paid_amount'),
            'total_pending' => $installments->where('status', 'pending')->sum('amount'),
            'total_overdue' => $installments->where('status', 'overdue')->sum('total_amount'),
            'late_fees' => $installments->sum('late_fee'),
            'installment_count' => $installments->count(),
            'paid_count' => $installments->where('status', 'paid')->count(),
            'pending_count' => $installments->where('status', 'pending')->count(),
            'overdue_count' => $installments->where('status', 'overdue')->count(),
        ];

        $summary['completion_percentage'] = $summary['total_amount'] > 0
            ? round(($summary['total_paid'] / $summary['total_amount']) * 100, 2)
            : 0;

        return $summary;
    }

    /**
     * Check if enrollment is fully paid
     */
    private function checkEnrollmentCompletion(Enrollment $enrollment): void
    {
        $summary = $this->getPaymentSummary($enrollment);

        if ($summary['completion_percentage'] >= 100) {
            $enrollment->update(['status' => 'completed']);
        }
    }

    /**
     * Get overdue payments report
     */
    public function getOverduePaymentsReport(int $daysOverdue = null): array
    {
        $query = InstallmentPayment::where('status', 'overdue')
            ->with(['enrollment.student', 'enrollment.batch.course']);

        if ($daysOverdue) {
            $query->whereRaw('DATEDIFF(NOW(), due_date) >= ?', [$daysOverdue]);
        }

        return $query->get()->map(function ($installment) {
            return [
                'id' => $installment->id,
                'student_name' => $installment->enrollment->student->name,
                'course_name' => $installment->enrollment->batch->course->name,
                'batch_code' => $installment->enrollment->batch->batch_code,
                'installment_number' => $installment->installment_number,
                'amount' => $installment->amount,
                'late_fee' => $installment->late_fee,
                'total_amount' => $installment->total_amount,
                'due_date' => $installment->due_date,
                'days_overdue' => $installment->days_overdue,
            ];
        })->toArray();
    }
}

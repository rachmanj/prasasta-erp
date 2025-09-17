<?php

namespace App\Jobs;

use App\Models\Enrollment;
use App\Services\PaymentProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateInstallmentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $enrollment;

    /**
     * Create a new job instance.
     */
    public function __construct(Enrollment $enrollment)
    {
        $this->enrollment = $enrollment;
    }

    /**
     * Execute the job.
     */
    public function handle(PaymentProcessingService $paymentService): void
    {
        try {
            // Check if installments already exist
            if ($this->enrollment->installmentPayments()->count() > 0) {
                Log::info("Installments already exist for enrollment ID: {$this->enrollment->id}");
                return;
            }

            // Generate installments
            $installments = $paymentService->generateInstallmentPayments($this->enrollment);

            Log::info("Generated {$installments->count()} installments for enrollment ID: {$this->enrollment->id}");

            // Generate deferred revenue
            $paymentService->generateRevenueRecognition($this->enrollment);

            Log::info("Generated deferred revenue for enrollment ID: {$this->enrollment->id}");
        } catch (\Exception $e) {
            Log::error("Failed to generate installments for enrollment ID: {$this->enrollment->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateInstallmentsJob failed for enrollment ID: {$this->enrollment->id}. Error: " . $exception->getMessage());
    }
}

<?php

namespace App\Jobs;

use App\Services\PaymentProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateOverduePaymentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(PaymentProcessingService $paymentService): void
    {
        try {
            $updatedCount = $paymentService->updateOverdueInstallments();

            Log::info("Updated {$updatedCount} overdue installments");

            // TODO: Future development - Send email notifications for overdue payments
            // $this->sendOverdueNotifications();

        } catch (\Exception $e) {
            Log::error("Failed to update overdue payments. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("UpdateOverduePaymentsJob failed. Error: " . $exception->getMessage());
    }

    /**
     * TODO: Future development - Send overdue payment notifications
     */
    private function sendOverdueNotifications(): void
    {
        // This will be implemented in future development
        // - Email notifications for overdue payments
        // - SMS notifications for critical overdue payments
    }
}

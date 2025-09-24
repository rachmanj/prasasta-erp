<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Services\CourseAccountingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessPaymentAccounting implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private CourseAccountingService $accountingService
    ) {}

    public function handle(PaymentReceived $event): void
    {
        try {
            $this->accountingService->processPaymentJournalEntry($event->payment);
            Log::info("Successfully processed accounting for payment {$event->payment->id}");
        } catch (\Exception $e) {
            Log::error("Failed to process accounting for payment {$event->payment->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(PaymentReceived $event, \Throwable $exception): void
    {
        Log::error("ProcessPaymentAccounting failed for payment {$event->payment->id}: " . $exception->getMessage());
    }
}

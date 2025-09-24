<?php

namespace App\Listeners;

use App\Events\BatchStarted;
use App\Services\CourseAccountingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessRevenueRecognition implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private CourseAccountingService $accountingService
    ) {}

    public function handle(BatchStarted $event): void
    {
        try {
            $this->accountingService->recognizeRevenueForBatch($event->batch);
            Log::info("Successfully processed revenue recognition for batch {$event->batch->id}");
        } catch (\Exception $e) {
            Log::error("Failed to process revenue recognition for batch {$event->batch->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(BatchStarted $event, \Throwable $exception): void
    {
        Log::error("ProcessRevenueRecognition failed for batch {$event->batch->id}: " . $exception->getMessage());
    }
}

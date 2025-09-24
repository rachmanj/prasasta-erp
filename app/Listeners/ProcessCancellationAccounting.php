<?php

namespace App\Listeners;

use App\Events\CourseCancelled;
use App\Services\CourseAccountingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessCancellationAccounting implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private CourseAccountingService $accountingService
    ) {}

    public function handle(CourseCancelled $event): void
    {
        try {
            $this->accountingService->handleCourseCancellation($event->enrollment, $event->reason);
            Log::info("Successfully processed cancellation accounting for enrollment {$event->enrollment->id}");
        } catch (\Exception $e) {
            Log::error("Failed to process cancellation accounting for enrollment {$event->enrollment->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(CourseCancelled $event, \Throwable $exception): void
    {
        Log::error("ProcessCancellationAccounting failed for enrollment {$event->enrollment->id}: " . $exception->getMessage());
    }
}

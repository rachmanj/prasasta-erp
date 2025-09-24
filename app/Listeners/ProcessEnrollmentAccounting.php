<?php

namespace App\Listeners;

use App\Events\EnrollmentCreated;
use App\Services\CourseAccountingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessEnrollmentAccounting implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private CourseAccountingService $accountingService
    ) {}

    public function handle(EnrollmentCreated $event): void
    {
        try {
            $this->accountingService->createEnrollmentJournalEntry($event->enrollment);
            Log::info("Successfully processed accounting for enrollment {$event->enrollment->id}");
        } catch (\Exception $e) {
            Log::error("Failed to process accounting for enrollment {$event->enrollment->id}: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(EnrollmentCreated $event, \Throwable $exception): void
    {
        Log::error("ProcessEnrollmentAccounting failed for enrollment {$event->enrollment->id}: " . $exception->getMessage());
    }
}

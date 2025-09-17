<?php

namespace App\Listeners;

use App\Events\EnrollmentCreated;
use App\Jobs\GenerateInstallmentsJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class EnrollmentCreatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EnrollmentCreated $event): void
    {
        try {
            // Dispatch job to generate installments
            GenerateInstallmentsJob::dispatch($event->enrollment);

            Log::info("Dispatched GenerateInstallmentsJob for enrollment ID: {$event->enrollment->id}");
        } catch (\Exception $e) {
            Log::error("Failed to dispatch GenerateInstallmentsJob for enrollment ID: {$event->enrollment->id}. Error: " . $e->getMessage());
        }
    }
}

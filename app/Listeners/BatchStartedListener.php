<?php

namespace App\Listeners;

use App\Events\BatchStarted;
use App\Jobs\RecognizeRevenueJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class BatchStartedListener implements ShouldQueue
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
    public function handle(BatchStarted $event): void
    {
        try {
            // Dispatch job to recognize revenue
            RecognizeRevenueJob::dispatch($event->batch);

            Log::info("Dispatched RecognizeRevenueJob for batch ID: {$event->batch->id}");
        } catch (\Exception $e) {
            Log::error("Failed to dispatch RecognizeRevenueJob for batch ID: {$event->batch->id}. Error: " . $e->getMessage());
        }
    }
}

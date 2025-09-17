<?php

namespace App\Jobs;

use App\Models\CourseBatch;
use App\Services\PaymentProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RecognizeRevenueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $batch;

    /**
     * Create a new job instance.
     */
    public function __construct(CourseBatch $batch)
    {
        $this->batch = $batch;
    }

    /**
     * Execute the job.
     */
    public function handle(PaymentProcessingService $paymentService): void
    {
        try {
            // Check if batch has already had revenue recognized
            $existingRecognitions = $this->batch->enrollments()
                ->whereHas('revenueRecognitions', function ($query) {
                    $query->where('type', 'recognized')
                        ->where('recognition_date', $this->batch->start_date);
                })
                ->count();

            if ($existingRecognitions > 0) {
                Log::info("Revenue already recognized for batch ID: {$this->batch->id}");
                return;
            }

            // Recognize revenue for all enrollments in this batch
            $recognitions = $paymentService->recognizeRevenueForBatch($this->batch);

            Log::info("Recognized revenue for {$recognitions->count()} enrollments in batch ID: {$this->batch->id}");
        } catch (\Exception $e) {
            Log::error("Failed to recognize revenue for batch ID: {$this->batch->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("RecognizeRevenueJob failed for batch ID: {$this->batch->id}. Error: " . $exception->getMessage());
    }
}

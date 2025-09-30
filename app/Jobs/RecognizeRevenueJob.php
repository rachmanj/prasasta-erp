<?php

namespace App\Jobs;

use App\Models\CourseBatch;
use App\Services\CourseAccountingService;
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
    public function handle(CourseAccountingService $courseAccountingService): void
    {
        try {
            Log::info("Starting revenue recognition for batch ID: {$this->batch->id} - {$this->batch->batch_code}");

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

            // Get all enrollments for this batch
            $enrollments = $this->batch->enrollments()->where('status', 'enrolled')->get();

            if ($enrollments->isEmpty()) {
                Log::info("No enrollments found for batch ID: {$this->batch->id}");
                return;
            }

            $recognizedCount = 0;
            $totalAmount = 0;

            // Recognize revenue for each enrollment
            foreach ($enrollments as $enrollment) {
                try {
                    $result = $courseAccountingService->recognizeRevenue($enrollment, $this->batch->start_date);

                    if ($result) {
                        $recognizedCount++;
                        $totalAmount += $enrollment->total_amount;
                        Log::info("Recognized revenue for enrollment ID: {$enrollment->id}, Amount: Rp " . number_format($enrollment->total_amount, 0, ',', '.'));
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to recognize revenue for enrollment ID: {$enrollment->id}. Error: " . $e->getMessage());
                }
            }

            Log::info("Revenue recognition completed for batch ID: {$this->batch->id}. Recognized: {$recognizedCount} enrollments, Total Amount: Rp " . number_format($totalAmount, 0, ',', '.'));
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

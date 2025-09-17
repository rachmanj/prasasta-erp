<?php

namespace App\Jobs;

use App\Services\Export\ExcelExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GenerateExcelReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reportType;
    protected $data;
    protected $userEmail;
    protected $filename;

    /**
     * Create a new job instance.
     */
    public function __construct(string $reportType, array $data, string $userEmail = null, string $filename = null)
    {
        $this->reportType = $reportType;
        $this->data = $data;
        $this->userEmail = $userEmail;
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $excelService = new ExcelExportService();
            $filepath = null;

            switch ($this->reportType) {
                case 'payment_aging':
                    $filepath = $excelService->exportPaymentAgingReport($this->data);
                    break;
                case 'payment_collection':
                    $filepath = $excelService->exportPaymentCollectionReport($this->data);
                    break;
                case 'revenue_recognition':
                    $filepath = $excelService->exportRevenueRecognitionReport($this->data);
                    break;
                case 'course_performance':
                    $filepath = $excelService->exportCoursePerformanceReport($this->data);
                    break;
                case 'trainer_performance':
                    $filepath = $excelService->exportTrainerPerformanceReport($this->data);
                    break;
                default:
                    throw new \Exception("Unknown report type: {$this->reportType}");
            }

            Log::info("Excel report generated successfully", [
                'report_type' => $this->reportType,
                'filepath' => $filepath,
                'user_email' => $this->userEmail
            ]);

            // Send email notification if user email is provided
            if ($this->userEmail) {
                $this->sendEmailNotification($filepath);
            }

        } catch (\Exception $e) {
            Log::error("Failed to generate Excel report", [
                'report_type' => $this->reportType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Send email notification with Excel attachment
     */
    protected function sendEmailNotification(string $filepath): void
    {
        try {
            $filename = basename($filepath);
            $reportName = ucfirst(str_replace('_', ' ', $this->reportType)) . ' Report';

            Mail::raw("Your {$reportName} has been generated successfully.", function ($message) use ($filepath, $filename, $reportName) {
                $message->to($this->userEmail)
                    ->subject("{$reportName} - Generated")
                    ->attach($filepath, [
                        'as' => $filename,
                        'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ]);
            });

            Log::info("Email notification sent successfully", [
                'user_email' => $this->userEmail,
                'filename' => $filename
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to send email notification", [
                'user_email' => $this->userEmail,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Excel report generation job failed", [
            'report_type' => $this->reportType,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}

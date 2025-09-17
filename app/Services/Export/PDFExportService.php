<?php

namespace App\Services\Export;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class PDFExportService
{
    protected $dompdf;
    protected $options;

    public function __construct()
    {
        $this->options = new Options();
        $this->options->set('defaultFont', 'Arial');
        $this->options->set('isRemoteEnabled', true);
        $this->options->set('isHtml5ParserEnabled', true);
        $this->options->set('isPhpEnabled', true);
        
        $this->dompdf = new Dompdf($this->options);
    }

    /**
     * Generate PDF from view
     */
    public function generateFromView(string $view, array $data = [], string $filename = null): string
    {
        $html = View::make($view, $data)->render();
        
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        
        if ($filename) {
            $output = $this->dompdf->output();
            file_put_contents(storage_path('app/exports/' . $filename), $output);
            return storage_path('app/exports/' . $filename);
        }
        
        return $this->dompdf->output();
    }

    /**
     * Generate PDF from HTML string
     */
    public function generateFromHtml(string $html, string $filename = null): string
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        
        if ($filename) {
            $output = $this->dompdf->output();
            file_put_contents(storage_path('app/exports/' . $filename), $output);
            return storage_path('app/exports/' . $filename);
        }
        
        return $this->dompdf->output();
    }

    /**
     * Generate payment aging report PDF
     */
    public function generatePaymentAgingReport(array $data): string
    {
        $filename = 'payment_aging_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf';
        
        return $this->generateFromView('exports.pdf.payment-aging', [
            'data' => $data,
            'generated_at' => Carbon::now(),
            'company_name' => 'Prasasta ERP',
        ], $filename);
    }

    /**
     * Generate payment collection report PDF
     */
    public function generatePaymentCollectionReport(array $data): string
    {
        $filename = 'payment_collection_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf';
        
        return $this->generateFromView('exports.pdf.payment-collection', [
            'data' => $data,
            'generated_at' => Carbon::now(),
            'company_name' => 'Prasasta ERP',
        ], $filename);
    }

    /**
     * Generate revenue recognition report PDF
     */
    public function generateRevenueRecognitionReport(array $data): string
    {
        $filename = 'revenue_recognition_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf';
        
        return $this->generateFromView('exports.pdf.revenue-recognition', [
            'data' => $data,
            'generated_at' => Carbon::now(),
            'company_name' => 'Prasasta ERP',
        ], $filename);
    }

    /**
     * Generate course performance report PDF
     */
    public function generateCoursePerformanceReport(array $data): string
    {
        $filename = 'course_performance_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf';
        
        return $this->generateFromView('exports.pdf.course-performance', [
            'data' => $data,
            'generated_at' => Carbon::now(),
            'company_name' => 'Prasasta ERP',
        ], $filename);
    }

    /**
     * Generate trainer performance report PDF
     */
    public function generateTrainerPerformanceReport(array $data): string
    {
        $filename = 'trainer_performance_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf';
        
        return $this->generateFromView('exports.pdf.trainer-performance', [
            'data' => $data,
            'generated_at' => Carbon::now(),
            'company_name' => 'Prasasta ERP',
        ], $filename);
    }

    /**
     * Stream PDF to browser
     */
    public function stream(string $filename = 'report.pdf'): void
    {
        $this->dompdf->stream($filename);
    }

    /**
     * Download PDF
     */
    public function download(string $filename = 'report.pdf'): void
    {
        $this->dompdf->stream($filename, ['Attachment' => 1]);
    }

    /**
     * Get PDF content as string
     */
    public function getContent(): string
    {
        return $this->dompdf->output();
    }

    /**
     * Set paper size and orientation
     */
    public function setPaper(string $size = 'A4', string $orientation = 'portrait'): self
    {
        $this->dompdf->setPaper($size, $orientation);
        return $this;
    }

    /**
     * Set custom options
     */
    public function setOptions(array $options): self
    {
        foreach ($options as $key => $value) {
            $this->options->set($key, $value);
        }
        
        $this->dompdf = new Dompdf($this->options);
        return $this;
    }
}

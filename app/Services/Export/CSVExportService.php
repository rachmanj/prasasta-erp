<?php

namespace App\Services\Export;

use League\Csv\Writer;
use League\Csv\Reader;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CSVExportService
{
    /**
     * Export payment aging report to CSV
     */
    public function exportPaymentAgingReport(array $data): string
    {
        $filename = 'payment_aging_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);
        
        $csv = Writer::createFromPath($filepath, 'w+');
        $csv->setOutputBOM(Writer::BOM_UTF8);
        
        // Add headers
        $csv->insertOne(['Aging Range', 'Amount (Rp)', 'Count', 'Percentage']);
        
        // Add data rows
        foreach ($data['aging_data'] as $range => $info) {
            $csv->insertOne([
                $range,
                number_format($info['amount'], 0, ',', '.'),
                $info['count'],
                $info['percentage'] . '%'
            ]);
        }
        
        return $filepath;
    }

    /**
     * Export payment collection report to CSV
     */
    public function exportPaymentCollectionReport(array $data): string
    {
        $filename = 'payment_collection_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);
        
        $csv = Writer::createFromPath($filepath, 'w+');
        $csv->setOutputBOM(Writer::BOM_UTF8);
        
        // Add headers
        $csv->insertOne([
            'Student Name',
            'Course Name',
            'Payment Date',
            'Amount (Rp)',
            'Payment Method',
            'Late Fee (Rp)'
        ]);
        
        // Add data rows
        foreach ($data['payments'] as $payment) {
            $csv->insertOne([
                $payment->enrollment->student->name ?? 'N/A',
                $payment->enrollment->batch->course->name ?? 'N/A',
                $payment->paid_date ? $payment->paid_date->format('d/m/Y') : 'N/A',
                number_format($payment->paid_amount, 0, ',', '.'),
                $payment->payment_method ?? 'N/A',
                number_format($payment->late_fee_amount, 0, ',', '.')
            ]);
        }
        
        return $filepath;
    }

    /**
     * Export revenue recognition report to CSV
     */
    public function exportRevenueRecognitionReport(array $data): string
    {
        $filename = 'revenue_recognition_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);
        
        $csv = Writer::createFromPath($filepath, 'w+');
        $csv->setOutputBOM(Writer::BOM_UTF8);
        
        // Add headers
        $csv->insertOne([
            'Student Name',
            'Course Name',
            'Recognition Date',
            'Amount (Rp)',
            'Type',
            'Status',
            'Description'
        ]);
        
        // Add data rows
        foreach ($data['revenue_data'] as $revenue) {
            $csv->insertOne([
                $revenue->enrollment->student->name ?? 'N/A',
                $revenue->enrollment->batch->course->name ?? 'N/A',
                $revenue->recognition_date->format('d/m/Y'),
                number_format($revenue->amount, 0, ',', '.'),
                ucfirst($revenue->type),
                ucfirst($revenue->posted_status),
                $revenue->description ?? 'N/A'
            ]);
        }
        
        return $filepath;
    }

    /**
     * Export course performance report to CSV
     */
    public function exportCoursePerformanceReport(array $data): string
    {
        $filename = 'course_performance_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);
        
        $csv = Writer::createFromPath($filepath, 'w+');
        $csv->setOutputBOM(Writer::BOM_UTF8);
        
        // Add headers
        $csv->insertOne([
            'Course Name',
            'Course Code',
            'Batches',
            'Total Enrollments',
            'Total Revenue (Rp)',
            'Average Enrollment/Batch',
            'Capacity Utilization %'
        ]);
        
        // Add data rows
        foreach ($data['performance_data'] as $course) {
            $csv->insertOne([
                $course['course_name'],
                $course['course_code'],
                $course['batch_count'],
                $course['total_enrollments'],
                number_format($course['total_revenue'], 0, ',', '.'),
                $course['average_enrollment_per_batch'],
                $course['capacity_utilization'] . '%'
            ]);
        }
        
        return $filepath;
    }

    /**
     * Export trainer performance report to CSV
     */
    public function exportTrainerPerformanceReport(array $data): string
    {
        $filename = 'trainer_performance_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);
        
        $csv = Writer::createFromPath($filepath, 'w+');
        $csv->setOutputBOM(Writer::BOM_UTF8);
        
        // Add headers
        $csv->insertOne([
            'Trainer Name',
            'Type',
            'Batches',
            'Total Enrollments',
            'Total Revenue (Rp)',
            'Trainer Revenue (Rp)',
            'Revenue Share %',
            'Hourly Rate (Rp)',
            'Batch Rate (Rp)'
        ]);
        
        // Add data rows
        foreach ($data['performance_data'] as $trainer) {
            $csv->insertOne([
                $trainer['trainer_name'],
                ucfirst($trainer['trainer_type']),
                $trainer['batch_count'],
                $trainer['total_enrollments'],
                number_format($trainer['total_revenue'], 0, ',', '.'),
                number_format($trainer['trainer_revenue'], 0, ',', '.'),
                $trainer['revenue_share_percentage'] . '%',
                number_format($trainer['hourly_rate'] ?? 0, 0, ',', '.'),
                number_format($trainer['batch_rate'] ?? 0, 0, ',', '.')
            ]);
        }
        
        return $filepath;
    }

    /**
     * Export bulk enrollment data to CSV
     */
    public function exportBulkEnrollmentData(array $enrollments): string
    {
        $filename = 'bulk_enrollment_data_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);
        
        $csv = Writer::createFromPath($filepath, 'w+');
        $csv->setOutputBOM(Writer::BOM_UTF8);
        
        // Add headers
        $csv->insertOne([
            'Student ID',
            'Student Name',
            'Student Email',
            'Student Phone',
            'Course Name',
            'Batch Code',
            'Enrollment Date',
            'Status',
            'Total Amount (Rp)',
            'Payment Plan',
            'Emergency Contact Name',
            'Emergency Contact Phone'
        ]);
        
        // Add data rows
        foreach ($enrollments as $enrollment) {
            $csv->insertOne([
                $enrollment->student->student_id ?? 'N/A',
                $enrollment->student->name ?? 'N/A',
                $enrollment->student->email ?? 'N/A',
                $enrollment->student->phone ?? 'N/A',
                $enrollment->batch->course->name ?? 'N/A',
                $enrollment->batch->batch_code ?? 'N/A',
                $enrollment->enrollment_date->format('d/m/Y'),
                ucfirst($enrollment->status),
                number_format($enrollment->total_amount, 0, ',', '.'),
                $enrollment->paymentPlan->name ?? 'N/A',
                $enrollment->student->emergency_contact_name ?? 'N/A',
                $enrollment->student->emergency_contact_phone ?? 'N/A'
            ]);
        }
        
        return $filepath;
    }

    /**
     * Export bulk payment data to CSV
     */
    public function exportBulkPaymentData(array $payments): string
    {
        $filename = 'bulk_payment_data_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);
        
        $csv = Writer::createFromPath($filepath, 'w+');
        $csv->setOutputBOM(Writer::BOM_UTF8);
        
        // Add headers
        $csv->insertOne([
            'Student Name',
            'Course Name',
            'Batch Code',
            'Installment Number',
            'Amount (Rp)',
            'Due Date',
            'Paid Amount (Rp)',
            'Paid Date',
            'Late Fee (Rp)',
            'Status',
            'Payment Reference'
        ]);
        
        // Add data rows
        foreach ($payments as $payment) {
            $csv->insertOne([
                $payment->enrollment->student->name ?? 'N/A',
                $payment->enrollment->batch->course->name ?? 'N/A',
                $payment->enrollment->batch->batch_code ?? 'N/A',
                $payment->installment_number,
                number_format($payment->amount, 0, ',', '.'),
                $payment->due_date->format('d/m/Y'),
                number_format($payment->paid_amount, 0, ',', '.'),
                $payment->paid_date ? $payment->paid_date->format('d/m/Y') : 'N/A',
                number_format($payment->late_fee_amount, 0, ',', '.'),
                ucfirst($payment->status),
                $payment->payment_reference ?? 'N/A'
            ]);
        }
        
        return $filepath;
    }

    /**
     * Create CSV from array data
     */
    public function createFromArray(array $data, array $headers = [], string $filename = null): string
    {
        if (!$filename) {
            $filename = 'export_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        }
        
        $filepath = storage_path('app/exports/' . $filename);
        
        $csv = Writer::createFromPath($filepath, 'w+');
        $csv->setOutputBOM(Writer::BOM_UTF8);
        
        // Add headers if provided
        if (!empty($headers)) {
            $csv->insertOne($headers);
        }
        
        // Add data rows
        foreach ($data as $row) {
            $csv->insertOne($row);
        }
        
        return $filepath;
    }

    /**
     * Read CSV file
     */
    public function readCsv(string $filepath): array
    {
        $csv = Reader::createFromPath($filepath, 'r');
        $csv->setHeaderOffset(0);
        
        return iterator_to_array($csv->getRecords());
    }

    /**
     * Get CSV content as string
     */
    public function getContent(string $filepath): string
    {
        return file_get_contents($filepath);
    }

    /**
     * Download CSV file
     */
    public function download(string $filepath, string $filename = null): void
    {
        if (!$filename) {
            $filename = basename($filepath);
        }
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        
        readfile($filepath);
        exit;
    }
}

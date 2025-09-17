<?php

namespace App\Services\Export;

use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ExcelExportService
{
    /**
     * Export payment aging report to Excel
     */
    public function exportPaymentAgingReport(array $data): string
    {
        $filename = 'payment_aging_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        
        // Create simple Excel export using the older Laravel Excel version
        $exportData = [];
        $exportData[] = ['Aging Range', 'Amount (Rp)', 'Count', 'Percentage'];
        
        foreach ($data['aging_data'] as $range => $info) {
            $exportData[] = [
                $range,
                number_format($info['amount'], 0, ',', '.'),
                $info['count'],
                $info['percentage'] . '%'
            ];
        }
        
        Excel::create('Payment Aging Report', function($excel) use ($exportData) {
            $excel->sheet('Payment Aging', function($sheet) use ($exportData) {
                $sheet->fromArray($exportData, null, 'A1', false, false);
                $sheet->row(1, function($row) {
                    $row->setFontWeight('bold');
                    $row->setBackground('#E3F2FD');
                });
            });
        })->store('xlsx', storage_path('app/exports'));
        
        return storage_path('app/exports/' . $filename);
    }

    /**
     * Export payment collection report to Excel
     */
    public function exportPaymentCollectionReport(array $data): string
    {
        $filename = 'payment_collection_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        
        $exportData = [];
        $exportData[] = ['Student Name', 'Course Name', 'Payment Date', 'Amount (Rp)', 'Payment Method', 'Late Fee (Rp)'];
        
        foreach ($data['payments'] as $payment) {
            $exportData[] = [
                $payment->enrollment->student->name ?? 'N/A',
                $payment->enrollment->batch->course->name ?? 'N/A',
                $payment->paid_date ? $payment->paid_date->format('d/m/Y') : 'N/A',
                number_format($payment->paid_amount, 0, ',', '.'),
                $payment->payment_method ?? 'N/A',
                number_format($payment->late_fee_amount, 0, ',', '.')
            ];
        }
        
        Excel::create('Payment Collection Report', function($excel) use ($exportData) {
            $excel->sheet('Payment Collection', function($sheet) use ($exportData) {
                $sheet->fromArray($exportData, null, 'A1', false, false);
                $sheet->row(1, function($row) {
                    $row->setFontWeight('bold');
                    $row->setBackground('#E8F5E8');
                });
            });
        })->store('xlsx', storage_path('app/exports'));
        
        return storage_path('app/exports/' . $filename);
    }

    /**
     * Export revenue recognition report to Excel
     */
    public function exportRevenueRecognitionReport(array $data): string
    {
        $filename = 'revenue_recognition_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        
        $exportData = [];
        $exportData[] = ['Student Name', 'Course Name', 'Recognition Date', 'Amount (Rp)', 'Type', 'Status', 'Description'];
        
        foreach ($data['revenue_data'] as $revenue) {
            $exportData[] = [
                $revenue->enrollment->student->name ?? 'N/A',
                $revenue->enrollment->batch->course->name ?? 'N/A',
                $revenue->recognition_date->format('d/m/Y'),
                number_format($revenue->amount, 0, ',', '.'),
                ucfirst($revenue->type),
                ucfirst($revenue->posted_status),
                $revenue->description ?? 'N/A'
            ];
        }
        
        Excel::create('Revenue Recognition Report', function($excel) use ($exportData) {
            $excel->sheet('Revenue Recognition', function($sheet) use ($exportData) {
                $sheet->fromArray($exportData, null, 'A1', false, false);
                $sheet->row(1, function($row) {
                    $row->setFontWeight('bold');
                    $row->setBackground('#FFF3E0');
                });
            });
        })->store('xlsx', storage_path('app/exports'));
        
        return storage_path('app/exports/' . $filename);
    }

    /**
     * Export course performance report to Excel
     */
    public function exportCoursePerformanceReport(array $data): string
    {
        $filename = 'course_performance_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        
        $exportData = [];
        $exportData[] = ['Course Name', 'Course Code', 'Batches', 'Enrollments', 'Revenue (Rp)', 'Avg Enrollment/Batch', 'Capacity Utilization'];
        
        foreach ($data['performance_data'] as $course) {
            $exportData[] = [
                $course['course_name'],
                $course['course_code'],
                $course['batch_count'],
                $course['total_enrollments'],
                number_format($course['total_revenue'], 0, ',', '.'),
                $course['average_enrollment_per_batch'],
                $course['capacity_utilization'] . '%'
            ];
        }
        
        Excel::create('Course Performance Report', function($excel) use ($exportData) {
            $excel->sheet('Course Performance', function($sheet) use ($exportData) {
                $sheet->fromArray($exportData, null, 'A1', false, false);
                $sheet->row(1, function($row) {
                    $row->setFontWeight('bold');
                    $row->setBackground('#F3E5F5');
                });
            });
        })->store('xlsx', storage_path('app/exports'));
        
        return storage_path('app/exports/' . $filename);
    }

    /**
     * Export trainer performance report to Excel
     */
    public function exportTrainerPerformanceReport(array $data): string
    {
        $filename = 'trainer_performance_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';
        
        $exportData = [];
        $exportData[] = ['Trainer Name', 'Type', 'Batches', 'Enrollments', 'Total Revenue (Rp)', 'Trainer Revenue (Rp)', 'Revenue Share %', 'Hourly Rate (Rp)', 'Batch Rate (Rp)'];
        
        foreach ($data['performance_data'] as $trainer) {
            $exportData[] = [
                $trainer['trainer_name'],
                ucfirst($trainer['trainer_type']),
                $trainer['batch_count'],
                $trainer['total_enrollments'],
                number_format($trainer['total_revenue'], 0, ',', '.'),
                number_format($trainer['trainer_revenue'], 0, ',', '.'),
                $trainer['revenue_share_percentage'] . '%',
                number_format($trainer['hourly_rate'] ?? 0, 0, ',', '.'),
                number_format($trainer['batch_rate'] ?? 0, 0, ',', '.')
            ];
        }
        
        Excel::create('Trainer Performance Report', function($excel) use ($exportData) {
            $excel->sheet('Trainer Performance', function($sheet) use ($exportData) {
                $sheet->fromArray($exportData, null, 'A1', false, false);
                $sheet->row(1, function($row) {
                    $row->setFontWeight('bold');
                    $row->setBackground('#E0F2F1');
                });
            });
        })->store('xlsx', storage_path('app/exports'));
        
        return storage_path('app/exports/' . $filename);
    }

    /**
     * Download Excel file
     */
    public function download(string $filename, string $exportClass, array $data): void
    {
        Excel::download(new $exportClass($data), $filename);
    }
}
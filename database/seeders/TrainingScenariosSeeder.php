<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Accounting\Journal;
use App\Models\Accounting\JournalLine;
use App\Models\Accounting\SalesInvoice;
use App\Models\Accounting\SalesInvoiceLine;
use App\Models\Accounting\PurchaseInvoice;
use App\Models\Accounting\PurchaseInvoiceLine;
use App\Models\Accounting\SalesReceipt;
use App\Models\Accounting\PurchasePayment;
use App\Models\Accounting\CashExpense;
use App\Models\Enrollment;
use App\Models\InstallmentPayment;
use App\Models\RevenueRecognition;
use App\Models\AssetDepreciationRun;
use App\Models\AssetDepreciationEntry;
use App\Models\AssetDisposal;
use App\Models\AssetMovement;
use Carbon\Carbon;

class TrainingScenariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->createTrainingJournals();
        // $this->createTrainingInvoices();
        // $this->createTrainingReceipts();
        // $this->createTrainingPayments();
        // $this->createTrainingCashExpenses();
        // $this->createTrainingEnrollments();
        // $this->createTrainingInstallmentPayments();
        // $this->createTrainingRevenueRecognition();
        // $this->createTrainingAssetDepreciation();
        // $this->createTrainingAssetDisposals();
        // $this->createTrainingAssetMovements();
    }

    private function createTrainingJournals()
    {
        // Scenario 1: Donation Recording
        $donationJournal = Journal::create([
            'journal_no' => 'JNL-202501-000001',
            'description' => 'Donation from Yayasan Pendidikan Indonesia for scholarship program',
            'date' => '2025-01-15',
            'status' => 'posted',
            'posted_by' => 2, // Siti (Approver)
            'posted_at' => '2025-01-15 10:30:00',
        ]);

        JournalLine::create([
            'journal_id' => $donationJournal->id,
            'account_code' => '1.1.1.01', // Cash - Bank Account
            'description' => 'Donation received for scholarship program',
            'debit' => 50000000,
            'credit' => 0,
        ]);

        JournalLine::create([
            'journal_id' => $donationJournal->id,
            'account_code' => '4.3.1.01', // Restricted Donation Revenue
            'description' => 'Restricted donation revenue for scholarships',
            'debit' => 0,
            'credit' => 50000000,
        ]);

        // Scenario 2: Office Supply Purchase
        $officeSupplyJournal = Journal::create([
            'journal_no' => 'JNL-202501-000002',
            'description' => 'Office supplies purchase - stationery and materials',
            'date' => '2025-01-20',
            'status' => 'posted',
            'posted_by' => 2, // Siti (Approver)
            'posted_at' => '2025-01-20 14:15:00',
            'fund_id' => 1, // General Operating Fund
            'project_id' => null,
            'department_id' => 5, // Administration Department
        ]);

        JournalLine::create([
            'journal_id' => $officeSupplyJournal->id,
            'account_code' => '5.1.2.01', // Office Supplies Expense
            'description' => 'Office supplies and stationery',
            'debit' => 2250000,
            'credit' => 0,
            'fund_id' => 1,
            'project_id' => null,
            'department_id' => 5,
        ]);

        JournalLine::create([
            'journal_id' => $officeSupplyJournal->id,
            'account_code' => '2.1.3.01', // PPN Masukan
            'description' => 'PPN Masukan on office supplies',
            'debit' => 275000,
            'credit' => 0,
            'fund_id' => 1,
            'project_id' => null,
            'department_id' => 5,
        ]);

        JournalLine::create([
            'journal_id' => $officeSupplyJournal->id,
            'account_code' => '2.1.1.01', // Accounts Payable
            'description' => 'Payable to PT Office Supplies',
            'debit' => 0,
            'credit' => 2525000,
            'fund_id' => 1,
            'project_id' => null,
            'department_id' => 5,
        ]);

        // Scenario 3: Revenue Recognition
        $revenueJournal = Journal::create([
            'journal_no' => 'JNL-202501-000003',
            'description' => 'Revenue recognition for Digital Marketing course completion',
            'date' => '2025-01-31',
            'status' => 'posted',
            'posted_by' => 2, // Siti (Approver)
            'posted_at' => '2025-01-31 16:45:00',
            'fund_id' => 1, // General Operating Fund
            'project_id' => 1, // Digital Marketing Course Development
            'department_id' => 3, // Training Department
        ]);

        JournalLine::create([
            'journal_id' => $revenueJournal->id,
            'account_code' => '3.1.2.01', // Deferred Revenue
            'description' => 'Deferred revenue recognition',
            'debit' => 12000000,
            'credit' => 0,
            'fund_id' => 1,
            'project_id' => 1,
            'department_id' => 3,
        ]);

        JournalLine::create([
            'journal_id' => $revenueJournal->id,
            'account_code' => '4.1.1.01', // Course Revenue
            'description' => 'Course revenue recognition',
            'debit' => 0,
            'credit' => 12000000,
            'fund_id' => 1,
            'project_id' => 1,
            'department_id' => 3,
        ]);
    }

    private function createTrainingInvoices()
    {
        // Sales Invoice - Digital Marketing Course
        $salesInvoice = SalesInvoice::create([
            'invoice_no' => 'SINV-202501-000001',
            'customer_id' => 1, // PT Maju Bersama
            'date' => '2025-01-15',
            'due_date' => '2025-02-14',
            'terms_days' => 30,
            'subtotal' => 13513513.51,
            'tax_amount' => 1486486.49,
            'total' => 15000000,
            'status' => 'posted',
            'posted_by' => 2, // Siti (Approver)
            'posted_at' => '2025-01-15 11:00:00',
            'fund_id' => 1,
            'project_id' => 1,
            'department_id' => 3,
        ]);

        SalesInvoiceLine::create([
            'sales_invoice_id' => $salesInvoice->id,
            'description' => 'Digital Marketing Fundamentals Course',
            'quantity' => 1,
            'unit_price' => 13513513.51,
            'total' => 13513513.51,
            'account_code' => '4.1.1.01', // Course Revenue
        ]);

        // Purchase Invoice - Laptops
        $purchaseInvoice = PurchaseInvoice::create([
            'invoice_no' => 'PINV-202501-000001',
            'vendor_id' => 1, // PT Komputer Maju
            'date' => '2025-01-10',
            'due_date' => '2025-01-25',
            'terms_days' => 15,
            'subtotal' => 76576576.58,
            'tax_amount' => 8423423.42,
            'total' => 85000000,
            'status' => 'posted',
            'posted_by' => 2, // Siti (Approver)
            'posted_at' => '2025-01-10 15:30:00',
            'fund_id' => 3, // Equipment Fund
            'project_id' => 3, // IT Infrastructure Upgrade
            'department_id' => 1, // IT Department
        ]);

        PurchaseInvoiceLine::create([
            'purchase_invoice_id' => $purchaseInvoice->id,
            'description' => 'Dell Laptop Inspiron 15 3000 - 10 units',
            'quantity' => 10,
            'unit_price' => 7657657.66,
            'total' => 76576576.58,
            'account_code' => '1.2.1.01', // IT Equipment Asset
        ]);

        // Purchase Invoice - Office Supplies
        $officeInvoice = PurchaseInvoice::create([
            'invoice_no' => 'PINV-202501-000002',
            'vendor_id' => 2, // PT Office Supplies
            'date' => '2025-01-20',
            'due_date' => '2025-02-04',
            'terms_days' => 15,
            'subtotal' => 2250000,
            'tax_amount' => 275000,
            'total' => 2525000,
            'status' => 'posted',
            'posted_by' => 2, // Siti (Approver)
            'posted_at' => '2025-01-20 14:30:00',
            'fund_id' => 1, // General Operating Fund
            'project_id' => null,
            'department_id' => 5, // Administration Department
        ]);

        PurchaseInvoiceLine::create([
            'purchase_invoice_id' => $officeInvoice->id,
            'description' => 'Office supplies and stationery',
            'quantity' => 1,
            'unit_price' => 2250000,
            'total' => 2250000,
            'account_code' => '5.1.2.01', // Office Supplies Expense
        ]);
    }

    private function createTrainingReceipts()
    {
        // Sales Receipt - Down Payment
        SalesReceipt::create([
            'receipt_no' => 'SR-202501-000001',
            'customer_id' => 2, // Andi Pratama
            'date' => '2025-01-15',
            'amount' => 3000000,
            'payment_method' => 'cash',
            'reference' => 'Down payment for Data Analytics course',
            'status' => 'posted',
            'posted_by' => 2, // Siti (Approver)
            'posted_at' => '2025-01-15 16:00:00',
            'fund_id' => 1,
            'project_id' => 2, // Data Analytics Training Program
            'department_id' => 3,
        ]);

        // Sales Receipt - Course Payment
        SalesReceipt::create([
            'receipt_no' => 'SR-202501-000002',
            'customer_id' => 1, // PT Maju Bersama
            'date' => '2025-01-20',
            'amount' => 15000000,
            'payment_method' => 'bank_transfer',
            'reference' => 'Payment for Digital Marketing course',
            'status' => 'posted',
            'posted_by' => 2, // Siti (Approver)
            'posted_at' => '2025-01-20 10:00:00',
            'fund_id' => 1,
            'project_id' => 1, // Digital Marketing Course Development
            'department_id' => 3,
        ]);
    }

    private function createTrainingPayments()
    {
        // Purchase Payment - Laptops
        PurchasePayment::create([
            'payment_no' => 'PP-202501-000001',
            'vendor_id' => 1, // PT Komputer Maju
            'date' => '2025-01-25',
            'amount' => 85000000,
            'payment_method' => 'bank_transfer',
            'reference' => 'Payment for laptop purchase',
            'status' => 'posted',
            'posted_by' => 2, // Siti (Approver)
            'posted_at' => '2025-01-25 14:00:00',
            'fund_id' => 3, // Equipment Fund
            'project_id' => 3, // IT Infrastructure Upgrade
            'department_id' => 1,
        ]);

        // Purchase Payment - Office Supplies
        PurchasePayment::create([
            'payment_no' => 'PP-202501-000002',
            'vendor_id' => 2, // PT Office Supplies
            'date' => '2025-02-04',
            'amount' => 2525000,
            'payment_method' => 'bank_transfer',
            'reference' => 'Payment for office supplies',
            'status' => 'posted',
            'posted_by' => 2, // Siti (Approver)
            'posted_at' => '2025-02-04 11:30:00',
            'fund_id' => 1, // General Operating Fund
            'project_id' => null,
            'department_id' => 5,
        ]);
    }

    private function createTrainingCashExpenses()
    {
        // Cash Expense - Training Materials
        CashExpense::create([
            'expense_no' => 'CEV-202501-000001',
            'date' => '2025-01-18',
            'description' => 'Training materials and handouts',
            'amount' => 500000,
            'account_code' => '5.1.3.01', // Training Materials Expense
            'status' => 'posted',
            'posted_by' => 2, // Siti (Approver)
            'posted_at' => '2025-01-18 13:00:00',
            'created_by' => 4, // Ahmad (Cashier)
            'fund_id' => 1,
            'project_id' => 1, // Digital Marketing Course Development
            'department_id' => 3,
        ]);

        // Cash Expense - Office Supplies
        CashExpense::create([
            'expense_no' => 'CEV-202501-000002',
            'date' => '2025-01-22',
            'description' => 'Petty cash for office supplies',
            'amount' => 250000,
            'account_code' => '5.1.2.01', // Office Supplies Expense
            'status' => 'posted',
            'posted_by' => 2, // Siti (Approver)
            'posted_at' => '2025-01-22 15:30:00',
            'created_by' => 4, // Ahmad (Cashier)
            'fund_id' => 1,
            'project_id' => null,
            'department_id' => 5,
        ]);
    }

    private function createTrainingEnrollments()
    {
        // Enrollment - Digital Marketing Course
        Enrollment::create([
            'student_id' => 1, // PT Maju Bersama
            'batch_id' => 1, // DM-2025-01
            'enrollment_date' => '2025-01-15',
            'course_fee' => 15000000,
            'payment_plan_id' => 1, // Full Payment
            'status' => 'completed',
            'completion_date' => '2025-02-28',
            'completion_percentage' => 100,
        ]);

        // Enrollment - Data Analytics Course
        Enrollment::create([
            'student_id' => 2, // Andi Pratama
            'batch_id' => 2, // DA-2025-01
            'enrollment_date' => '2025-01-15',
            'course_fee' => 12000000,
            'payment_plan_id' => 3, // 3 Installments
            'status' => 'active',
            'completion_percentage' => 60,
        ]);

        // Enrollment - Project Management Course
        Enrollment::create([
            'student_id' => 3, // CV Teknologi Mandiri
            'batch_id' => 3, // PMP-2025-01
            'enrollment_date' => '2025-01-20',
            'course_fee' => 10000000,
            'payment_plan_id' => 2, // 2 Installments
            'status' => 'active',
            'completion_percentage' => 40,
        ]);
    }

    private function createTrainingInstallmentPayments()
    {
        // Installment Payment - Data Analytics Course
        InstallmentPayment::create([
            'enrollment_id' => 2, // Andi Pratama - Data Analytics
            'installment_number' => 1,
            'due_date' => '2025-01-15',
            'amount' => 4000000,
            'paid_date' => '2025-01-15',
            'amount_paid' => 4000000,
            'status' => 'paid',
            'payment_method' => 'cash',
        ]);

        InstallmentPayment::create([
            'enrollment_id' => 2, // Andi Pratama - Data Analytics
            'installment_number' => 2,
            'due_date' => '2025-02-15',
            'amount' => 4000000,
            'paid_date' => null,
            'amount_paid' => 0,
            'status' => 'pending',
            'payment_method' => null,
        ]);

        InstallmentPayment::create([
            'enrollment_id' => 2, // Andi Pratama - Data Analytics
            'installment_number' => 3,
            'due_date' => '2025-03-15',
            'amount' => 4000000,
            'paid_date' => null,
            'amount_paid' => 0,
            'status' => 'pending',
            'payment_method' => null,
        ]);

        // Installment Payment - Project Management Course
        InstallmentPayment::create([
            'enrollment_id' => 3, // CV Teknologi Mandiri - Project Management
            'installment_number' => 1,
            'due_date' => '2025-01-20',
            'amount' => 5000000,
            'paid_date' => '2025-01-20',
            'amount_paid' => 5000000,
            'status' => 'paid',
            'payment_method' => 'bank_transfer',
        ]);

        InstallmentPayment::create([
            'enrollment_id' => 3, // CV Teknologi Mandiri - Project Management
            'installment_number' => 2,
            'due_date' => '2025-02-20',
            'amount' => 5000000,
            'paid_date' => null,
            'amount_paid' => 0,
            'status' => 'pending',
            'payment_method' => null,
        ]);
    }

    private function createTrainingRevenueRecognition()
    {
        // Revenue Recognition - Digital Marketing Course (100% complete)
        RevenueRecognition::create([
            'enrollment_id' => 1, // PT Maju Bersama - Digital Marketing
            'recognition_date' => '2025-02-28',
            'total_amount' => 15000000,
            'earned_amount' => 15000000,
            'unearned_amount' => 0,
            'recognition_percentage' => 100,
            'status' => 'recognized',
        ]);

        // Revenue Recognition - Data Analytics Course (60% complete)
        RevenueRecognition::create([
            'enrollment_id' => 2, // Andi Pratama - Data Analytics
            'recognition_date' => '2025-01-31',
            'total_amount' => 12000000,
            'earned_amount' => 7200000,
            'unearned_amount' => 4800000,
            'recognition_percentage' => 60,
            'status' => 'partial',
        ]);

        // Revenue Recognition - Project Management Course (40% complete)
        RevenueRecognition::create([
            'enrollment_id' => 3, // CV Teknologi Mandiri - Project Management
            'recognition_date' => '2025-01-31',
            'total_amount' => 10000000,
            'earned_amount' => 4000000,
            'unearned_amount' => 6000000,
            'recognition_percentage' => 40,
            'status' => 'partial',
        ]);
    }

    private function createTrainingAssetDepreciation()
    {
        // Depreciation Run - January 2025
        $depreciationRun = AssetDepreciationRun::create([
            'period' => '2025-01',
            'status' => 'posted',
            'total_depreciation' => 2777778, // Total depreciation for the month
            'journal_id' => null, // Will be set when journal is created
            'posted_at' => '2025-01-31 17:00:00',
            'created_by' => 2, // Siti (Approver)
            'posted_by' => 2, // Siti (Approver)
        ]);

        // Depreciation Entry - Laptop 1
        AssetDepreciationEntry::create([
            'asset_id' => 1, // LAPTOP-001
            'depreciation_run_id' => $depreciationRun->id,
            'period' => '2025-01',
            'amount' => 236111, // 8,500,000 / 36 months
            'book_value_before' => 8500000,
            'book_value_after' => 8263889,
            'fund_id' => 3, // Equipment Fund
            'project_id' => 3, // IT Infrastructure Upgrade
            'department_id' => 1, // IT Department
        ]);

        // Depreciation Entry - Laptop 2
        AssetDepreciationEntry::create([
            'asset_id' => 2, // LAPTOP-002
            'depreciation_run_id' => $depreciationRun->id,
            'period' => '2025-01',
            'amount' => 236111, // 8,500,000 / 36 months
            'book_value_before' => 8500000,
            'book_value_after' => 8263889,
            'fund_id' => 3, // Equipment Fund
            'project_id' => 3, // IT Infrastructure Upgrade
            'department_id' => 1, // IT Department
        ]);

        // Depreciation Entry - Office Desk
        AssetDepreciationEntry::create([
            'asset_id' => 3, // DESK-001
            'depreciation_run_id' => $depreciationRun->id,
            'period' => '2025-01',
            'amount' => 41667, // 2,500,000 / 60 months
            'book_value_before' => 2500000,
            'book_value_after' => 2458333,
            'fund_id' => 1, // General Operating Fund
            'project_id' => null,
            'department_id' => 2, // Finance Department
        ]);

        // Depreciation Entry - Company Car
        AssetDepreciationEntry::create([
            'asset_id' => 4, // CAR-001
            'depreciation_run_id' => $depreciationRun->id,
            'period' => '2025-01',
            'amount' => 2500000, // 150,000,000 / 60 months
            'book_value_before' => 150000000,
            'book_value_after' => 147500000,
            'fund_id' => 1, // General Operating Fund
            'project_id' => null,
            'department_id' => 5, // Administration Department
        ]);
    }

    private function createTrainingAssetDisposals()
    {
        // Asset Disposal - Old Projector
        AssetDisposal::create([
            'asset_id' => 5, // PROJECTOR-001
            'disposal_date' => '2025-01-25',
            'disposal_method' => 'sale',
            'disposal_amount' => 500000,
            'book_value' => 350000,
            'gain_loss' => 150000, // Gain
            'reason' => 'Replaced with newer model',
            'status' => 'posted',
            'posted_by' => 2, // Siti (Approver)
            'posted_at' => '2025-01-25 16:00:00',
            'fund_id' => 3, // Equipment Fund
            'project_id' => 3, // IT Infrastructure Upgrade
            'department_id' => 3, // Training Department
        ]);
    }

    private function createTrainingAssetMovements()
    {
        // Asset Movement - Laptop transfer
        AssetMovement::create([
            'asset_id' => 1, // LAPTOP-001
            'movement_date' => '2025-01-20',
            'movement_type' => 'transfer',
            'from_location' => 'IT Department',
            'to_location' => 'Training Lab 1',
            'from_department_id' => 1, // IT Department
            'to_department_id' => 3, // Training Department
            'reason' => 'Transfer to training lab for course delivery',
            'status' => 'approved',
            'approved_by' => 2, // Siti (Approver)
            'approved_at' => '2025-01-20 14:00:00',
        ]);
    }
}

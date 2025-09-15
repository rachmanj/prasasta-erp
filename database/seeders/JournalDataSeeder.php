<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JournalDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample journal entries for testing reports

        // Journal 1: Purchase Transaction
        $journal1 = DB::table('journals')->insertGetId([
            'journal_no' => 'JNL-2025-000003',
            'date' => '2025-09-01',
            'description' => 'Purchase of Laptop Dell Inspiron - 2 units',
            'status' => 'posted',
            'source_type' => 'manual',
            'source_id' => 1,
            'posted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Journal Lines for Purchase
        DB::table('journal_lines')->insert([
            [
                'journal_id' => $journal1,
                'account_id' => 3, // Cash on Hand
                'debit' => 17000000,
                'credit' => 0,
                'memo' => 'Purchase of Laptop Dell Inspiron - 2 units',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'journal_id' => $journal1,
                'account_id' => 8, // Accounts Receivable - Trade
                'debit' => 0,
                'credit' => 17000000,
                'memo' => 'Accounts Payable - PT Supplier Utama',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Journal 2: Sales Transaction
        $journal2 = DB::table('journals')->insertGetId([
            'journal_no' => 'JNL-2025-000004',
            'date' => '2025-09-05',
            'description' => 'Sale of Laptop Dell Inspiron - 1 unit',
            'status' => 'posted',
            'source_type' => 'manual',
            'source_id' => 2,
            'posted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Journal Lines for Sales
        DB::table('journal_lines')->insert([
            [
                'journal_id' => $journal2,
                'account_id' => 8, // Accounts Receivable - Trade
                'debit' => 9500000,
                'credit' => 0,
                'memo' => 'Accounts Receivable - CV Sejahtera Abadi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'journal_id' => $journal2,
                'account_id' => 3, // Cash on Hand
                'debit' => 0,
                'credit' => 9500000,
                'memo' => 'Sales Revenue - Laptop Dell Inspiron',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Journal 3: Cash Expense
        $journal3 = DB::table('journals')->insertGetId([
            'journal_no' => 'JNL-2025-000005',
            'date' => '2025-09-08',
            'description' => 'Office Supplies Purchase',
            'status' => 'posted',
            'source_type' => 'manual',
            'source_id' => 3,
            'posted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Journal Lines for Cash Expense
        DB::table('journal_lines')->insert([
            [
                'journal_id' => $journal3,
                'account_id' => 3, // Cash on Hand
                'debit' => 500000,
                'credit' => 0,
                'memo' => 'Office Supplies Expense',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'journal_id' => $journal3,
                'account_id' => 3, // Cash on Hand
                'debit' => 0,
                'credit' => 500000,
                'memo' => 'Cash Payment for Office Supplies',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Journal 4: Cash Receipt
        $journal4 = DB::table('journals')->insertGetId([
            'journal_no' => 'JNL-2025-000006',
            'date' => '2025-09-10',
            'description' => 'Cash Receipt from Customer',
            'status' => 'posted',
            'source_type' => 'manual',
            'source_id' => 4,
            'posted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Journal Lines for Cash Receipt
        DB::table('journal_lines')->insert([
            [
                'journal_id' => $journal4,
                'account_id' => 3, // Cash on Hand
                'debit' => 10000000,
                'credit' => 0,
                'memo' => 'Cash Receipt from CV Sejahtera Abadi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'journal_id' => $journal4,
                'account_id' => 8, // Accounts Receivable - Trade
                'debit' => 0,
                'credit' => 10000000,
                'memo' => 'Payment Received from CV Sejahtera Abadi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Journal 5: Purchase Payment
        $journal5 = DB::table('journals')->insertGetId([
            'journal_no' => 'JNL-2025-000007',
            'date' => '2025-09-12',
            'description' => 'Payment to Vendor',
            'status' => 'posted',
            'source_type' => 'manual',
            'source_id' => 5,
            'posted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Journal Lines for Purchase Payment
        DB::table('journal_lines')->insert([
            [
                'journal_id' => $journal5,
                'account_id' => 8, // Accounts Receivable - Trade
                'debit' => 17000000,
                'credit' => 0,
                'memo' => 'Payment to PT Supplier Utama',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'journal_id' => $journal5,
                'account_id' => 3, // Cash on Hand
                'debit' => 0,
                'credit' => 17000000,
                'memo' => 'Cash Payment to PT Supplier Utama',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Journal 6: Additional Expense
        $journal6 = DB::table('journals')->insertGetId([
            'journal_no' => 'JNL-2025-000008',
            'date' => '2025-09-14',
            'description' => 'Transportation Costs',
            'status' => 'posted',
            'source_type' => 'manual',
            'source_id' => 6,
            'posted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Journal Lines for Additional Expense
        DB::table('journal_lines')->insert([
            [
                'journal_id' => $journal6,
                'account_id' => 3, // Cash on Hand
                'debit' => 200000,
                'credit' => 0,
                'memo' => 'Transportation Expense',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'journal_id' => $journal6,
                'account_id' => 3, // Cash on Hand
                'debit' => 0,
                'credit' => 200000,
                'memo' => 'Cash Payment for Transportation',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

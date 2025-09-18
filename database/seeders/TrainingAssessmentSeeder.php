<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrainingAssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createTrainingQuestions();
        $this->createTrainingScenarios();
        $this->createTrainingAnswers();
    }

    private function createTrainingQuestions()
    {
        $questions = [
            // Beginner Level Questions
            [
                'id' => 1,
                'question' => 'A student calls to enroll in the "Digital Marketing Fundamentals" course. The course costs Rp 8,000,000 and they want to pay in 4 installments. Which menu should you navigate to first?',
                'type' => 'multiple_choice',
                'level' => 'beginner',
                'category' => 'course_management',
                'options' => json_encode([
                    'A' => 'Courses → Courses',
                    'B' => 'Courses → Enrollments',
                    'C' => 'Master Data → Customers',
                    'D' => 'Sales → Sales Invoices'
                ]),
                'correct_answer' => 'B',
                'explanation' => 'The enrollment process starts with creating a new enrollment record in the Courses → Enrollments menu.',
            ],
            [
                'id' => 2,
                'question' => 'You received an invoice from "PT Office Supplies" for Rp 2,500,000 for office supplies. The invoice includes 11% PPN. What is the PPN amount on this invoice?',
                'type' => 'multiple_choice',
                'level' => 'beginner',
                'category' => 'tax_calculation',
                'options' => json_encode([
                    'A' => 'Rp 275,000',
                    'B' => 'Rp 225,000',
                    'C' => 'Rp 250,000',
                    'D' => 'Rp 200,000'
                ]),
                'correct_answer' => 'A',
                'explanation' => 'PPN = Rp 2,500,000 × 11% = Rp 275,000',
            ],
            [
                'id' => 3,
                'question' => 'Which accounts should be debited when processing a purchase invoice for office supplies?',
                'type' => 'multiple_choice',
                'level' => 'beginner',
                'category' => 'accounting',
                'options' => json_encode([
                    'A' => 'Office Supplies Expense, PPN Masukan',
                    'B' => 'Office Supplies Expense only',
                    'C' => 'Accounts Payable only',
                    'D' => 'Cash, Office Supplies Expense'
                ]),
                'correct_answer' => 'A',
                'explanation' => 'Purchase invoices debit the expense account and PPN Masukan (input tax), and credit Accounts Payable.',
            ],

            // Intermediate Level Questions
            [
                'id' => 4,
                'question' => 'A 3-month course was completed 60% by the end of January. The total course fee is Rp 12,000,000, and the student has paid Rp 4,000,000 in installments. How much revenue should be recognized in January?',
                'type' => 'multiple_choice',
                'level' => 'intermediate',
                'category' => 'revenue_recognition',
                'options' => json_encode([
                    'A' => 'Rp 4,000,000 (amount paid)',
                    'B' => 'Rp 7,200,000 (60% of total)',
                    'C' => 'Rp 12,000,000 (full amount)',
                    'D' => 'Rp 0 (no recognition yet)'
                ]),
                'correct_answer' => 'B',
                'explanation' => 'Revenue recognition is based on course completion percentage, not payment received. 60% × Rp 12,000,000 = Rp 7,200,000',
            ],
            [
                'id' => 5,
                'question' => 'You need to run monthly depreciation for January 2025. The company has 5 laptops purchased for Rp 10,000,000 each with 36-month useful life. What is the monthly depreciation per laptop?',
                'type' => 'multiple_choice',
                'level' => 'intermediate',
                'category' => 'asset_management',
                'options' => json_encode([
                    'A' => 'Rp 277,778',
                    'B' => 'Rp 250,000',
                    'C' => 'Rp 300,000',
                    'D' => 'Rp 333,333'
                ]),
                'correct_answer' => 'A',
                'explanation' => 'Monthly depreciation = Rp 10,000,000 ÷ 36 months = Rp 277,778',
            ],
            [
                'id' => 6,
                'question' => 'Which menu should you use to run monthly depreciation for fixed assets?',
                'type' => 'multiple_choice',
                'level' => 'intermediate',
                'category' => 'asset_management',
                'options' => json_encode([
                    'A' => 'Fixed Assets → Assets',
                    'B' => 'Fixed Assets → Depreciation Runs',
                    'C' => 'Accounting → Manual Journal',
                    'D' => 'Reports → Asset Reports'
                ]),
                'correct_answer' => 'B',
                'explanation' => 'Monthly depreciation is run through the Fixed Assets → Depreciation Runs menu.',
            ],

            // Advanced Level Questions
            [
                'id' => 7,
                'question' => 'PT Prasasta Education Center received a donation of Rp 50,000,000 from "Yayasan Pendidikan Indonesia" for scholarship programs. The donation is restricted for IT course scholarships only. Which accounts should be used for this transaction?',
                'type' => 'multiple_choice',
                'level' => 'advanced',
                'category' => 'accounting',
                'options' => json_encode([
                    'A' => 'Debit: Cash, Credit: Donation Revenue',
                    'B' => 'Debit: Cash, Credit: Restricted Donation Revenue',
                    'C' => 'Debit: Cash, Credit: Scholarship Fund',
                    'D' => 'Debit: Cash, Credit: Unrestricted Revenue'
                ]),
                'correct_answer' => 'B',
                'explanation' => 'Restricted donations must be credited to Restricted Donation Revenue to maintain proper fund accounting.',
            ],
            [
                'id' => 8,
                'question' => 'The company paid Rp 5,000,000 to a trainer who is not a tax resident. The payment is subject to 20% withholding tax. What is the withholding tax amount?',
                'type' => 'multiple_choice',
                'level' => 'advanced',
                'category' => 'tax_compliance',
                'options' => json_encode([
                    'A' => 'Rp 1,000,000',
                    'B' => 'Rp 500,000',
                    'C' => 'Rp 750,000',
                    'D' => 'Rp 1,250,000'
                ]),
                'correct_answer' => 'A',
                'explanation' => 'PPh 26 withholding tax = Rp 5,000,000 × 20% = Rp 1,000,000',
            ],
            [
                'id' => 9,
                'question' => 'When should PPh 26 withholding tax be remitted to the tax office?',
                'type' => 'multiple_choice',
                'level' => 'advanced',
                'category' => 'tax_compliance',
                'options' => json_encode([
                    'A' => 'End of month',
                    'B' => 'Within 10 days',
                    'C' => 'Within 15 days',
                    'D' => 'End of quarter'
                ]),
                'correct_answer' => 'C',
                'explanation' => 'PPh 26 withholding tax must be remitted within 15 days after the end of the month.',
            ],

            // Practical Exercise Questions
            [
                'id' => 10,
                'question' => 'You need to create a sales invoice for a Digital Marketing course sold to PT Maju Bersama for Rp 15,000,000 including 11% PPN. What is the subtotal amount before tax?',
                'type' => 'calculation',
                'level' => 'intermediate',
                'category' => 'invoice_processing',
                'options' => json_encode([]),
                'correct_answer' => '13513513.51',
                'explanation' => 'Subtotal = Rp 15,000,000 ÷ 1.11 = Rp 13,513,513.51',
            ],
            [
                'id' => 11,
                'question' => 'A student enrolled in a Data Analytics course with 3 installments. The course fee is Rp 12,000,000. What is the amount of each installment?',
                'type' => 'calculation',
                'level' => 'beginner',
                'category' => 'payment_plans',
                'options' => json_encode([]),
                'correct_answer' => '4000000',
                'explanation' => 'Each installment = Rp 12,000,000 ÷ 3 = Rp 4,000,000',
            ],
            [
                'id' => 12,
                'question' => 'Calculate the monthly depreciation for an asset with acquisition cost Rp 8,500,000, salvage value Rp 850,000, and useful life 36 months.',
                'type' => 'calculation',
                'level' => 'intermediate',
                'category' => 'asset_management',
                'options' => json_encode([]),
                'correct_answer' => '212500',
                'explanation' => 'Monthly depreciation = (Rp 8,500,000 - Rp 850,000) ÷ 36 = Rp 212,500',
            ],
        ];

        foreach ($questions as $question) {
            DB::table('training_questions')->updateOrInsert(
                ['id' => $question['id']],
                $question
            );
        }
    }

    private function createTrainingScenarios()
    {
        $scenarios = [
            [
                'id' => 1,
                'title' => 'Month-End Closing Process',
                'description' => 'You are responsible for closing the month of January 2025. Complete all necessary steps including journal approvals, revenue recognition, depreciation runs, and period closing.',
                'level' => 'advanced',
                'category' => 'month_end_closing',
                'steps' => json_encode([
                    'Review all pending journal entries',
                    'Approve and post journals',
                    'Run monthly depreciation',
                    'Recognize revenue for completed courses',
                    'Close the period',
                    'Generate month-end reports'
                ]),
                'expected_outcomes' => json_encode([
                    'All journals posted',
                    'Depreciation calculated and posted',
                    'Revenue properly recognized',
                    'Period closed successfully',
                    'Reports generated'
                ]),
            ],
            [
                'id' => 2,
                'title' => 'Student Payment Collection',
                'description' => 'A student has an overdue payment for the Data Analytics course. The student wants to make a partial payment and set up a new payment plan. Process the payment and update the enrollment.',
                'level' => 'intermediate',
                'category' => 'payment_processing',
                'steps' => json_encode([
                    'Review student enrollment and payment history',
                    'Process partial payment',
                    'Update payment plan if needed',
                    'Generate receipt',
                    'Update enrollment status'
                ]),
                'expected_outcomes' => json_encode([
                    'Payment recorded correctly',
                    'Receipt generated',
                    'Enrollment updated',
                    'Payment plan adjusted'
                ]),
            ],
            [
                'id' => 3,
                'title' => 'Asset Disposal and Gain/Loss Calculation',
                'description' => 'The company is disposing of an old projector that was purchased for Rp 3,500,000 and has accumulated depreciation of Rp 2,800,000. The disposal value is Rp 500,000. Calculate the gain/loss and create the necessary journal entries.',
                'level' => 'advanced',
                'category' => 'asset_disposal',
                'steps' => json_encode([
                    'Calculate book value',
                    'Determine gain/loss',
                    'Create disposal record',
                    'Generate journal entries',
                    'Update asset status'
                ]),
                'expected_outcomes' => json_encode([
                    'Book value calculated correctly',
                    'Gain/loss determined',
                    'Disposal recorded',
                    'Journal entries created',
                    'Asset status updated'
                ]),
            ],
        ];

        foreach ($scenarios as $scenario) {
            DB::table('training_scenarios')->updateOrInsert(
                ['id' => $scenario['id']],
                $scenario
            );
        }
    }

    private function createTrainingAnswers()
    {
        $answers = [
            // Sample answers for assessment
            [
                'question_id' => 1,
                'user_id' => 2, // Budi (Accountant)
                'answer' => 'B',
                'is_correct' => true,
                'answered_at' => '2025-01-27 10:00:00',
            ],
            [
                'question_id' => 2,
                'user_id' => 2,
                'answer' => 'A',
                'is_correct' => true,
                'answered_at' => '2025-01-27 10:05:00',
            ],
            [
                'question_id' => 3,
                'user_id' => 2,
                'answer' => 'A',
                'is_correct' => true,
                'answered_at' => '2025-01-27 10:10:00',
            ],
            [
                'question_id' => 4,
                'user_id' => 2,
                'answer' => 'B',
                'is_correct' => true,
                'answered_at' => '2025-01-27 10:15:00',
            ],
            [
                'question_id' => 5,
                'user_id' => 2,
                'answer' => 'A',
                'is_correct' => true,
                'answered_at' => '2025-01-27 10:20:00',
            ],
        ];

        foreach ($answers as $answer) {
            DB::table('training_answers')->updateOrInsert(
                [
                    'question_id' => $answer['question_id'],
                    'user_id' => $answer['user_id']
                ],
                $answer
            );
        }
    }
}

# Course Management System - Accounting Integration Test Scenarios

## Overview

This document provides comprehensive test scenarios to validate the integration of the Course Management System with the Accounting System, including automatic journal entries, revenue recognition, payment processing, and financial reporting.

## Test Environment Setup

### Prerequisites

1. **User Roles Setup:**

    - Superadmin: Full access to all course and accounting features
    - Accountant: Can create transactions and view financial reports
    - Approver: Can approve transactions and post journals
    - Cashier: Can process payments and receipts

2. **Master Data:**

    - Course Categories: Digital Marketing, Data Analytics, Project Management
    - Courses: Multiple courses with different pricing structures
    - Students: Various student profiles for enrollment testing
    - Trainers: Trainer profiles for batch assignment
    - Payment Plans: Different installment structures

3. **Chart of Accounts:**
    - Accounts Receivable: 1.1.4 (Trade)
    - Deferred Revenue: 2.1.5.x (Category-specific)
    - Course Revenue: 4.1.1.x (Category-specific)
    - Taxes Payable: 2.1.3 (PPN Output)
    - Cash: 1.1.2.01

---

## Scenario 1: Course Enrollment Workflow

### 1.1 Create Course Enrollment

**Objective:** Test automatic journal entry generation when a student enrolls in a course

**Steps:**

1. Login as Accountant
2. Navigate to Course Management → Enrollments → Create
3. Create enrollment with following details:

    ```
    Student: PT Maju Bersama
    Course: Digital Marketing Fundamentals
    Batch: DM-BATCH-001
    Payment Plan: 4 Installments
    Total Amount: Rp 8,000,000
    Enrollment Date: 2025-09-14
    ```

**Expected Results:**

-   Enrollment created successfully
-   Automatic journal entry generated with:
    -   **Debit**: 1.1.4 - Accounts Receivable - Trade: Rp 8,000,000
    -   **Credit**: 2.1.5.1 - Deferred Revenue - Digital Marketing: Rp 7,207,207.21
    -   **Credit**: 2.1.3 - Taxes Payable - PPN Output: Rp 792,792.79
-   `is_accounted_for` flag set to `true`
-   `journal_entry_id` populated
-   `accounted_at` timestamp recorded

### 1.2 Verify Journal Entry Details

**Objective:** Validate journal entry structure and calculations

**Steps:**

1. Navigate to Accounting → GL Detail
2. Filter by date: 2025-09-14
3. Look for journal entry with description containing "Course Enrollment"

**Expected Results:**

-   Journal entry shows proper source type: "enrollment"
-   Journal entry shows proper source ID: enrollment ID
-   Debit and credit totals balance
-   PPN calculation is correct (11% of gross amount)
-   Net revenue calculation is correct (gross - PPN)

---

## Scenario 2: Payment Processing Workflow

### 2.1 Process First Installment Payment

**Objective:** Test payment journal entry generation

**Steps:**

1. Login as Cashier
2. Navigate to Course Management → Installment Payments
3. Process payment for the enrollment created in Scenario 1.1:

    ```
    Amount: Rp 2,000,000
    Payment Date: 2025-09-14
    Payment Method: Bank Transfer
    ```

**Expected Results:**

-   Payment processed successfully
-   Automatic journal entry generated with:
    -   **Debit**: 1.1.2.01 - Cash: Rp 2,000,000
    -   **Credit**: 1.1.4 - Accounts Receivable - Trade: Rp 2,000,000
-   `is_accounted_for` flag set to `true`
-   `journal_entry_id` populated
-   `accounted_at` timestamp recorded

### 2.2 Verify Payment Journal Entry

**Objective:** Validate payment journal entry structure

**Steps:**

1. Navigate to Accounting → GL Detail
2. Filter by date: 2025-09-14
3. Look for journal entry with description containing "Payment Received"

**Expected Results:**

-   Journal entry shows proper source type: "installment_payment"
-   Journal entry shows proper source ID: payment ID
-   Debit and credit totals balance
-   Cash account is debited
-   Accounts Receivable is credited

---

## Scenario 3: Revenue Recognition Workflow

### 3.1 Start Course Batch

**Objective:** Test automatic revenue recognition when batch starts

**Steps:**

1. Login as Accountant
2. Navigate to Course Management → Course Batches
3. Update batch status from "scheduled" to "ongoing"
4. Set batch start date to current date

**Expected Results:**

-   Batch status updated successfully
-   Automatic revenue recognition journal entry generated:
    -   **Debit**: 2.1.5.1 - Deferred Revenue - Digital Marketing: Rp 7,207,207.21
    -   **Credit**: 4.1.1.1 - Course Revenue - Digital Marketing: Rp 7,207,207.21
-   `revenue_recognized` flag set to `true`
-   `revenue_recognized_at` timestamp recorded
-   `revenue_recognition_journal_id` populated

### 3.2 Verify Revenue Recognition Journal Entry

**Objective:** Validate revenue recognition journal entry

**Steps:**

1. Navigate to Accounting → GL Detail
2. Filter by date: current date
3. Look for journal entry with description containing "Revenue Recognition"

**Expected Results:**

-   Journal entry shows proper source type: "course_batch"
-   Journal entry shows proper source ID: batch ID
-   Debit and credit totals balance
-   Deferred Revenue is debited (reduced)
-   Course Revenue is credited (increased)

---

## Scenario 4: Course Cancellation Workflow

### 4.1 Cancel Course Enrollment

**Objective:** Test cancellation journal entry generation

**Steps:**

1. Login as Accountant
2. Navigate to Course Management → Enrollments
3. Cancel the enrollment created in Scenario 1.1
4. Provide cancellation reason: "Student request"

**Expected Results:**

-   Enrollment cancelled successfully
-   Automatic cancellation journal entry generated:
    -   **Debit**: 2.1.5.1 - Deferred Revenue - Digital Marketing: Rp 7,207,207.21
    -   **Credit**: 4.1.1.3 - Course Revenue - Cancellation: Rp 7,207,207.21
-   `is_cancelled` flag set to `true`
-   `cancelled_at` timestamp recorded
-   `cancellation_journal_id` populated

### 4.2 Verify Cancellation Journal Entry

**Objective:** Validate cancellation journal entry

**Steps:**

1. Navigate to Accounting → GL Detail
2. Filter by date: current date
3. Look for journal entry with description containing "Course Cancellation"

**Expected Results:**

-   Journal entry shows proper source type: "enrollment"
-   Journal entry shows proper source ID: enrollment ID
-   Debit and credit totals balance
-   Deferred Revenue is debited (reversed)
-   Cancellation Revenue is credited

---

## Scenario 5: Course Financial Reports

### 5.1 Course Profitability Report

**Objective:** Test course profitability analysis

**Steps:**

1. Login as Accountant
2. Navigate to Reports → Courses → Course Financial Reports
3. Click on "Course Profitability Report"
4. Review the report data

**Expected Results:**

-   Report shows all active courses
-   Revenue metrics are accurate:
    -   Total Revenue: Rp 8,000,000
    -   Recognized Revenue: Rp 7,207,207.21
    -   Deferred Revenue: Rp 0 (after recognition)
-   Enrollment metrics are correct:
    -   Total Enrollments: 1
    -   Revenue per Enrollment: Rp 8,000,000
-   Utilization rates are calculated correctly

### 5.2 Outstanding Receivables Report

**Objective:** Test outstanding receivables tracking

**Steps:**

1. Navigate to "Outstanding Receivables Report"
2. Review the report data

**Expected Results:**

-   Report shows enrollment with outstanding balance
-   Outstanding amount: Rp 6,000,000 (remaining installments)
-   Payment progress: 25% (1 of 4 installments paid)
-   Days since last payment: 0 (if paid today)

### 5.3 Revenue Recognition Report

**Objective:** Test revenue recognition tracking

**Steps:**

1. Navigate to "Revenue Recognition Report"
2. Review the report data

**Expected Results:**

-   Report shows batch with revenue recognition
-   Total Deferred Revenue: Rp 7,207,207.21 (before recognition)
-   Recognized Revenue: Rp 7,207,207.21 (after recognition)
-   Remaining Deferred: Rp 0
-   Recognition Percentage: 100%

### 5.4 Payment Collection Report

**Objective:** Test payment collection performance

**Steps:**

1. Navigate to "Payment Collection Report"
2. Review the report data

**Expected Results:**

-   Report shows student payment progress
-   Total Amount: Rp 8,000,000
-   Paid Amount: Rp 2,000,000
-   Outstanding: Rp 6,000,000
-   Payment Progress: 25%
-   Last Payment Date: 2025-09-14

---

## Scenario 6: Multi-dimensional Tracking

### 6.1 Course with Project Dimension

**Objective:** Test multi-dimensional tracking in journal entries

**Steps:**

1. Create enrollment with project dimension:
    ```
    Student: PT Maju Bersama
    Course: Digital Marketing Fundamentals
    Project: Marketing Campaign 2025
    Fund: Training Budget
    Department: Marketing
    ```

**Expected Results:**

-   Journal entries include project, fund, and department dimensions
-   Financial reports can be filtered by dimensions
-   Multi-dimensional analysis is available

---

## Scenario 7: Event-Driven Architecture

### 7.1 Test Event Listeners

**Objective:** Verify event listeners are working correctly

**Steps:**

1. Monitor Laravel logs during enrollment creation
2. Verify events are dispatched:
    - `EnrollmentCreated` event
    - `ProcessEnrollmentAccounting` listener triggered
3. Monitor during payment processing:
    - `PaymentReceived` event
    - `ProcessPaymentAccounting` listener triggered
4. Monitor during batch start:
    - `BatchStarted` event
    - `ProcessRevenueRecognition` listener triggered

**Expected Results:**

-   All events are dispatched correctly
-   All listeners are triggered automatically
-   Journal entries are created without manual intervention
-   No errors in Laravel logs

---

## Scenario 8: Error Handling and Edge Cases

### 8.1 Invalid Payment Amount

**Objective:** Test error handling for invalid payments

**Steps:**

1. Try to process payment with amount greater than outstanding balance
2. Try to process payment with negative amount
3. Try to process payment with zero amount

**Expected Results:**

-   Appropriate error messages displayed
-   No journal entries created for invalid payments
-   System maintains data integrity

### 8.2 Duplicate Revenue Recognition

**Objective:** Test prevention of duplicate revenue recognition

**Steps:**

1. Try to recognize revenue for batch that's already recognized
2. Try to start batch that's already started

**Expected Results:**

-   System prevents duplicate revenue recognition
-   Appropriate error messages displayed
-   No duplicate journal entries created

### 8.3 Cancellation After Revenue Recognition

**Objective:** Test cancellation after revenue has been recognized

**Steps:**

1. Cancel enrollment after batch has started (revenue recognized)
2. Verify proper reversal entries

**Expected Results:**

-   Proper reversal journal entries created
-   Deferred Revenue properly reversed
-   Course Revenue properly adjusted
-   System maintains accounting accuracy

---

## Scenario 9: Performance and Scalability

### 9.1 Bulk Enrollment Processing

**Objective:** Test system performance with multiple enrollments

**Steps:**

1. Create 50 enrollments simultaneously
2. Monitor system performance
3. Verify all journal entries are created correctly

**Expected Results:**

-   System handles bulk operations efficiently
-   All journal entries are created correctly
-   No performance degradation
-   All events and listeners work correctly

### 9.2 Large Financial Reports

**Objective:** Test report performance with large datasets

**Steps:**

1. Generate financial reports with 1000+ enrollments
2. Monitor report generation time
3. Verify report accuracy

**Expected Results:**

-   Reports generate within acceptable time limits
-   Report data is accurate
-   Pagination works correctly
-   Export functionality works properly

---

## Test Data Requirements

### Sample Courses

-   Digital Marketing Fundamentals (Rp 8,000,000)
-   Data Analytics Bootcamp (Rp 12,000,000)
-   Project Management Professional (Rp 6,000,000)

### Sample Students

-   PT Maju Bersama
-   CV Teknologi Jaya
-   PT Digital Solutions

### Sample Payment Plans

-   Full Payment (1 installment)
-   2 Installments
-   4 Installments
-   6 Installments

### Sample Trainers

-   Ahmad Wijaya (Digital Marketing)
-   Siti Rahayu (Data Analytics)
-   Budi Santoso (Project Management)

---

## Success Criteria

### Functional Requirements

-   ✅ All journal entries are created automatically
-   ✅ Double-entry bookkeeping is maintained
-   ✅ PPN calculations are accurate
-   ✅ Revenue recognition follows accounting principles
-   ✅ Financial reports are accurate and complete
-   ✅ Event-driven architecture works correctly

### Performance Requirements

-   ✅ Journal entries created within 2 seconds
-   ✅ Financial reports load within 5 seconds
-   ✅ System handles 100+ concurrent users
-   ✅ Database queries are optimized

### Security Requirements

-   ✅ Permission-based access control works
-   ✅ Audit trails are maintained
-   ✅ Data integrity is preserved
-   ✅ No unauthorized access to financial data

---

## Test Execution Checklist

### Pre-Test Setup

-   [ ] Database is clean and seeded
-   [ ] All required users and roles exist
-   [ ] Chart of Accounts is properly configured
-   [ ] Course categories and courses exist
-   [ ] Students and trainers are available

### Test Execution

-   [ ] Scenario 1: Course Enrollment Workflow
-   [ ] Scenario 2: Payment Processing Workflow
-   [ ] Scenario 3: Revenue Recognition Workflow
-   [ ] Scenario 4: Course Cancellation Workflow
-   [ ] Scenario 5: Course Financial Reports
-   [ ] Scenario 6: Multi-dimensional Tracking
-   [ ] Scenario 7: Event-Driven Architecture
-   [ ] Scenario 8: Error Handling and Edge Cases
-   [ ] Scenario 9: Performance and Scalability

### Post-Test Validation

-   [ ] All journal entries are balanced
-   [ ] Financial reports are accurate
-   [ ] No errors in system logs
-   [ ] Performance metrics are acceptable
-   [ ] Security requirements are met

---

## Troubleshooting Guide

### Common Issues

1. **Journal entries not created:**

    - Check event listeners are registered
    - Verify event dispatching in controllers
    - Check Laravel logs for errors

2. **PPN calculations incorrect:**

    - Verify PPN rate (11%)
    - Check calculation formula
    - Validate account codes

3. **Financial reports showing incorrect data:**

    - Verify database relationships
    - Check report queries
    - Validate data integrity

4. **Performance issues:**
    - Check database indexes
    - Optimize report queries
    - Monitor system resources

### Debug Commands

```bash
# Test course accounting service
php artisan test:course-accounting

# Check journal entries
php artisan tinker
>>> App\Models\Accounting\Journal::latest()->take(5)->get()

# Verify event listeners
php artisan event:list
```

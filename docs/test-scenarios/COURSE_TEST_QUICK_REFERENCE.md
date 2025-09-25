# Course Integration Test - Quick Reference Card

## 🎯 Test Objectives

Validate complete integration of Course Management System with Accounting System, including automatic journal entries, revenue recognition, payment processing, and financial reporting.

## 📋 Pre-Test Checklist

-   [ ] Login as Accountant (superadmin@prasasta.com)
-   [ ] Verify course categories and courses exist
-   [ ] Check students and payment plans are available
-   [ ] Ensure Chart of Accounts is properly configured
-   [ ] Verify course-specific accounts exist (2.1.5.x, 4.1.1.x series)

## 🔄 Test Scenarios Summary

### Scenario 1: Course Enrollment Workflow

| Step | Action                 | Expected Result                                  |
| ---- | ---------------------- | ------------------------------------------------ |
| 1.1  | Create enrollment      | Journal: AR Dr 8M, Deferred Cr 7.2M, PPN Cr 0.8M |
| 1.2  | Verify journal entry   | Balanced journal with proper accounts            |
| 1.3  | Check enrollment flags | is_accounted_for = true, journal_entry_id set    |

### Scenario 2: Payment Processing Workflow

| Step | Action                 | Expected Result                               |
| ---- | ---------------------- | --------------------------------------------- |
| 2.1  | Process payment        | Journal: Cash Dr 2M, AR Cr 2M                 |
| 2.2  | Verify payment journal | Balanced journal with proper accounts         |
| 2.3  | Check payment flags    | is_accounted_for = true, journal_entry_id set |

### Scenario 3: Revenue Recognition Workflow

| Step | Action             | Expected Result                            |
| ---- | ------------------ | ------------------------------------------ |
| 3.1  | Start course batch | Batch status = 'ongoing'                   |
| 3.2  | Recognize revenue  | Journal: Deferred Dr 7.2M, Revenue Cr 7.2M |
| 3.3  | Check batch flags  | revenue_recognized = true, journal_id set  |

### Scenario 4: Course Cancellation Workflow

| Step | Action              | Expected Result                                 |
| ---- | ------------------- | ----------------------------------------------- |
| 4.1  | Cancel enrollment   | Journal: Deferred Dr 7.2M, Cancellation Cr 7.2M |
| 4.2  | Verify cancellation | Proper reversal entries                         |

### Scenario 5: Course Financial Reports

| Step | Action                  | Expected Result                         |
| ---- | ----------------------- | --------------------------------------- |
| 5.1  | Course Profitability    | Shows revenue, enrollments, utilization |
| 5.2  | Outstanding Receivables | Shows payment progress and aging        |
| 5.3  | Revenue Recognition     | Shows deferred vs recognized revenue    |
| 5.4  | Payment Collection      | Shows collection performance metrics    |

### Scenario 6: Event-Driven Architecture

| Step | Action                     | Expected Result                     |
| ---- | -------------------------- | ----------------------------------- |
| 6.1  | Dispatch EnrollmentCreated | Event listener triggers accounting  |
| 6.2  | Dispatch PaymentReceived   | Event listener processes payment    |
| 6.3  | Dispatch BatchStarted      | Event listener recognizes revenue   |
| 6.4  | Dispatch CourseCancelled   | Event listener handles cancellation |

## 🧪 Test Commands

### Run All Course Tests

```bash
# Run all course-related tests
php artisan test --filter=Course

# Run specific test files
php artisan test tests/Unit/CourseAccountingServiceTest.php
php artisan test tests/Feature/CourseFinancialReportTest.php
php artisan test tests/Feature/CourseAccountingIntegrationTest.php
```

### Manual Testing Commands

```bash
# Test course accounting service
php artisan test:course-accounting

# Check journal entries
php artisan tinker
>>> App\Models\Accounting\Journal::latest()->take(5)->get()

# Verify account balances
>>> App\Models\Accounting\Account::where('code', '1.1.4')->first()->balance()
```

## 📊 Expected Test Data

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

## 🔍 Key Validations

### Journal Entry Validations

-   ✅ All journal entries are balanced (debit = credit)
-   ✅ Proper account codes are used
-   ✅ PPN calculations are accurate (11%)
-   ✅ Source type and ID are correctly set
-   ✅ Journal descriptions are meaningful

### Business Logic Validations

-   ✅ Revenue recognition follows accounting principles
-   ✅ Deferred revenue is properly managed
-   ✅ Payment processing reduces AR balance
-   ✅ Cancellations create proper reversal entries
-   ✅ Duplicate revenue recognition is prevented

### Performance Validations

-   ✅ Journal entries created within 2 seconds
-   ✅ Financial reports load within 5 seconds
-   ✅ 10+ concurrent enrollments processed efficiently
-   ✅ Database queries are optimized

### Security Validations

-   ✅ Permission-based access control works
-   ✅ Audit trails are maintained
-   ✅ Data integrity is preserved
-   ✅ No unauthorized access to financial data

## 🚨 Common Issues & Solutions

### Issue: Journal entries not created

**Solution:**

-   Check event listeners are registered
-   Verify event dispatching in controllers
-   Check Laravel logs for errors
-   Ensure PostingService is working

### Issue: PPN calculations incorrect

**Solution:**

-   Verify PPN rate (11%)
-   Check calculation formula: `gross * 0.11`
-   Validate account codes (2.1.3)

### Issue: Financial reports showing incorrect data

**Solution:**

-   Verify database relationships
-   Check report queries in controller
-   Validate data integrity
-   Clear cache if needed

### Issue: Performance issues

**Solution:**

-   Check database indexes
-   Optimize report queries
-   Monitor system resources
-   Use database query logging

## 📈 Success Metrics

### Functional Success

-   ✅ 100% of test scenarios pass
-   ✅ All journal entries are balanced
-   ✅ Financial reports are accurate
-   ✅ Event-driven architecture works

### Performance Success

-   ✅ Journal creation < 2 seconds
-   ✅ Report generation < 5 seconds
-   ✅ Handles 100+ concurrent users
-   ✅ Database queries optimized

### Quality Success

-   ✅ No critical bugs found
-   ✅ Code coverage > 80%
-   ✅ Security vulnerabilities addressed
-   ✅ Documentation is complete

## 🔧 Debug Commands

```bash
# Check event listeners
php artisan event:list

# Monitor Laravel logs
tail -f storage/logs/laravel.log

# Check database connections
php artisan tinker
>>> DB::connection()->getPdo()

# Verify permissions
>>> auth()->user()->getAllPermissions()
```

## 📝 Test Report Template

### Test Execution Summary

-   **Date:** [Test Date]
-   **Tester:** [Tester Name]
-   **Environment:** [Development/Staging/Production]
-   **Test Duration:** [Duration]

### Test Results

-   **Total Scenarios:** 9
-   **Passed:** [X]
-   **Failed:** [X]
-   **Skipped:** [X]
-   **Success Rate:** [X]%

### Issues Found

1. [Issue 1]
2. [Issue 2]
3. [Issue 3]

### Recommendations

1. [Recommendation 1]
2. [Recommendation 2]
3. [Recommendation 3]

### Sign-off

-   **QA Lead:** [Name] - [Date]
-   **Development Lead:** [Name] - [Date]
-   **Product Owner:** [Name] - [Date]

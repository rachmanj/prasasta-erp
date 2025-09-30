# COURSE-SPECIFIC PROFIT & LOSS REPORT TEST SCENARIO

**Prasasta Training Institute**  
**Test Scenario**: Course-Specific Profit & Loss Analysis  
**Date**: January 29, 2025  
**Tester**: AI Assistant  
**Environment**: Development (http://localhost:8000)

---

## 📋 **TEST OBJECTIVE**

Test the ability to generate profit and loss reports for specific courses, including revenue recognition, cost analysis, and profitability metrics using the existing Course Financial Reports system.

---

## 🎯 **TEST SCENARIO OVERVIEW**

### **Scenario**: Digital Marketing Fundamentals Course Profitability Analysis

**Business Context**: PT Prasasta Education Center wants to analyze the profitability of their "Digital Marketing Fundamentals" course (Course ID: 1) to make strategic decisions about pricing, capacity, and resource allocation.

**Test Data Available**:

-   Course: Digital Marketing Fundamentals (ID: 1, Code: COURSE-001)
-   Base Price: Rp 8,000,000
-   1 Active Batch with 1 Enrollment
-   Total Revenue: Rp 8,000,000
-   Journal Entries: Posted enrollment with AR, Deferred Revenue, and PPN

---

## 🔍 **DETAILED TEST STEPS**

### **Step 1: Access Course Financial Reports**

1. **Navigate to Reports Menu**

    - Login as Accountant (budi@prasasta.com / password)
    - Go to: Reports → Course Financial Reports
    - URL: `http://localhost:8000/reports/course-financial`

2. **Expected Result**:
    - Course Financial Reports index page loads
    - Menu shows: Profitability, Revenue Recognition, Outstanding Receivables, Payment Collection

### **Step 2: Access Course Profitability Report**

1. **Click on "Profitability" Menu Item**

    - URL: `http://localhost:8000/reports/course-financial/profitability`

2. **Expected Result**:
    - Course Profitability Analysis page loads
    - DataTable shows all courses with profitability metrics
    - Filters available: Start Date, End Date, Course Category

### **Step 3: Apply Course-Specific Filters**

1. **Set Date Range Filter**

    - Start Date: 2025-09-01
    - End Date: 2025-09-30
    - Click "Apply Filters"

2. **Expected Result**:
    - DataTable refreshes with filtered data
    - Only courses with activity in September 2025 are shown

### **Step 4: Analyze Digital Marketing Course Data**

1. **Locate Digital Marketing Fundamentals Course**

    - Course Code: COURSE-001
    - Course Name: Digital Marketing Fundamentals
    - Category: Digital Marketing

2. **Verify Profitability Metrics**:
    - **Base Price**: Rp 8,000,000
    - **Total Batches**: 1
    - **Total Enrollments**: 1
    - **Total Revenue**: Rp 8,000,000
    - **Recognized Revenue**: Rp 7,207,207.21 (after PPN)
    - **Deferred Revenue**: Rp 0 (if revenue recognized)
    - **Revenue per Enrollment**: Rp 8,000,000
    - **Utilization Rate**: Calculated based on capacity

### **Step 5: Test Summary Cards**

1. **Verify Summary Statistics**:
    - Total Courses: 1 (filtered)
    - Total Revenue: Rp 8,000,000
    - Total Enrollments: 1
    - Average Utilization: Calculated percentage

### **Step 6: Test Export Functionality**

1. **Click "Export Excel" Button**
    - Verify export functionality works
    - Check if filtered data is exported correctly

---

## 🧪 **ADVANCED TESTING SCENARIOS**

### **Scenario A: Multiple Course Comparison**

1. **Remove Date Filters**

    - Set filters to show all courses
    - Compare profitability across different courses

2. **Expected Analysis**:
    - Digital Marketing: Rp 8,000,000 revenue
    - Data Analytics: Rp 0 revenue (no enrollments)
    - Project Management: Rp 0 revenue (no enrollments)
    - IT Fundamentals: Rp 0 revenue (no enrollments)
    - Social Media Marketing: Rp 0 revenue (no enrollments)

### **Scenario B: Category-Based Filtering**

1. **Filter by Course Category**

    - Select "Digital Marketing" category
    - Verify only Digital Marketing courses appear

2. **Expected Result**:
    - Only Digital Marketing Fundamentals course shown
    - Other categories filtered out

### **Scenario C: Date Range Analysis**

1. **Test Different Date Ranges**

    - Current Month: September 2025
    - Previous Month: August 2025
    - Custom Range: 2025-09-01 to 2025-09-30

2. **Expected Results**:
    - September 2025: Shows Digital Marketing course with revenue
    - August 2025: Shows no courses (no activity)
    - Custom Range: Same as September results

---

## 🔧 **TECHNICAL IMPLEMENTATION DETAILS**

### **Backend Services Used**

1. **CourseFinancialReportController**

    - Method: `getCourseProfitabilityData()`
    - Route: `/reports/course-financial/profitability/data`
    - Returns: DataTable-compatible JSON data

2. **Database Queries**

    ```sql
    SELECT c.id, c.code, c.name, cc.name as category_name, c.base_price,
           COUNT(DISTINCT cb.id) as total_batches,
           COUNT(DISTINCT e.id) as total_enrollments,
           SUM(e.total_amount) as total_revenue,
           SUM(rr.amount) as recognized_revenue
    FROM courses c
    LEFT JOIN course_categories cc ON c.category_id = cc.id
    LEFT JOIN course_batches cb ON c.id = cb.course_id
    LEFT JOIN enrollments e ON cb.id = e.batch_id
    LEFT JOIN revenue_recognitions rr ON e.id = rr.enrollment_id
    WHERE c.status = 'active'
    GROUP BY c.id, c.code, c.name, cc.name, c.base_price
    ```

3. **CourseCostManagementService**
    - Method: `calculateCourseProfitability(int $courseId, string $startDate = null, string $endDate = null)`
    - Returns: Detailed profitability analysis for specific course

### **Frontend Components**

1. **DataTable Integration**

    - Server-side processing
    - AJAX data loading
    - Real-time filtering
    - Export functionality

2. **Filter Controls**

    - Date range pickers
    - Category dropdown
    - Apply/Reset buttons

3. **Summary Cards**
    - Total courses count
    - Total revenue sum
    - Total enrollments sum
    - Average utilization rate

---

## 📊 **EXPECTED TEST RESULTS**

### **Digital Marketing Fundamentals Course Analysis**

| **Metric**             | **Expected Value**             | **Calculation**                    |
| ---------------------- | ------------------------------ | ---------------------------------- |
| Course Code            | COURSE-001                     | From database                      |
| Course Name            | Digital Marketing Fundamentals | From database                      |
| Category               | Digital Marketing              | From course_categories             |
| Base Price             | Rp 8,000,000                   | From courses.base_price            |
| Total Batches          | 1                              | COUNT(DISTINCT course_batches)     |
| Total Enrollments      | 1                              | COUNT(DISTINCT enrollments)        |
| Total Revenue          | Rp 8,000,000                   | SUM(enrollments.total_amount)      |
| Recognized Revenue     | Rp 7,207,207.21                | After PPN deduction (11%)          |
| Deferred Revenue       | Rp 0                           | If revenue already recognized      |
| Revenue per Enrollment | Rp 8,000,000                   | total_revenue / total_enrollments  |
| Utilization Rate       | Calculated %                   | enrollments / (batches × capacity) |

### **Summary Cards Expected Values**

| **Card**            | **Expected Value**    |
| ------------------- | --------------------- |
| Total Courses       | 1 (when filtered)     |
| Total Revenue       | Rp 8,000,000          |
| Total Enrollments   | 1                     |
| Average Utilization | Calculated percentage |

---

## 🚨 **POTENTIAL ISSUES & SOLUTIONS**

### **Issue 1: No Data Displayed**

-   **Cause**: Date filters too restrictive
-   **Solution**: Expand date range or remove filters
-   **Test**: Try different date ranges

### **Issue 2: Revenue Recognition Not Showing**

-   **Cause**: Revenue recognition job not executed
-   **Solution**: Run queue worker or manual revenue recognition
-   **Test**: Check journal entries for revenue recognition

### **Issue 3: Export Functionality Not Working**

-   **Cause**: JavaScript error or missing implementation
-   **Solution**: Check browser console for errors
-   **Test**: Verify export button click handler

### **Issue 4: Slow Data Loading**

-   **Cause**: Large dataset or missing indexes
-   **Solution**: Check database performance
-   **Test**: Monitor query execution time

---

## ✅ **SUCCESS CRITERIA**

### **Primary Success Criteria**

1. ✅ Course Profitability page loads successfully
2. ✅ Digital Marketing course appears in the report
3. ✅ All profitability metrics display correctly
4. ✅ Filters work as expected
5. ✅ Summary cards show accurate totals
6. ✅ Export functionality works (if implemented)

### **Secondary Success Criteria**

1. ✅ Multiple course comparison works
2. ✅ Category filtering functions properly
3. ✅ Date range filtering operates correctly
4. ✅ DataTable features (sorting, pagination) work
5. ✅ Responsive design displays properly on different screen sizes

---

## 📝 **TEST EXECUTION CHECKLIST**

-   [ ] Login as Accountant user
-   [ ] Navigate to Course Financial Reports
-   [ ] Access Profitability report
-   [ ] Apply date range filters
-   [ ] Verify Digital Marketing course data
-   [ ] Check all profitability metrics
-   [ ] Test summary cards
-   [ ] Test export functionality
-   [ ] Test category filtering
-   [ ] Test different date ranges
-   [ ] Verify responsive design
-   [ ] Check browser console for errors
-   [ ] Document any issues found

---

## 🔄 **FOLLOW-UP ACTIONS**

### **If Test Passes**

1. Document successful test execution
2. Update test documentation with actual results
3. Consider additional test scenarios
4. Plan performance testing for larger datasets

### **If Test Fails**

1. Document specific failure points
2. Investigate root causes
3. Implement fixes
4. Re-run test scenario
5. Update documentation with solutions

---

## 📚 **RELATED DOCUMENTATION**

-   **Course Financial Reports**: `/docs/architecture.md` (lines 956-960)
-   **Course Accounting Service**: `/app/Services/CourseAccountingService.php`
-   **Course Cost Management**: `/app/Services/CourseCostManagementService.php`
-   **Course Financial Controller**: `/app/Http/Controllers/CourseFinancialReportController.php`
-   **Profit & Loss Report**: `/docs/PROFIT_AND_LOSS_REPORT.md`

---

**Test Scenario Created**: January 29, 2025  
**Last Updated**: January 29, 2025  
**Status**: Ready for Execution

# REVENUE RECOGNITION TEST SCENARIOS - STORY-BASED TESTING

**Prasasta Training Institute**  
**Test Scenarios**: Revenue Recognition System Validation  
**Date**: January 29, 2025  
**Tester**: AI Assistant  
**Environment**: Development (http://localhost:8000)

---

## 📋 **TEST OBJECTIVE**

Validate the newly implemented revenue recognition system including automatic recognition triggers, manual batch controls, and enhanced reporting capabilities through comprehensive story-based testing scenarios.

---

## 🎯 **BUSINESS CONTEXT**

**Scenario**: PT Prasasta Education Center is launching their Digital Marketing Fundamentals course and needs to validate that the revenue recognition system works correctly for proper financial reporting and compliance.

**Key Stakeholders**:

-   **Finance Manager**: Needs accurate P&L reporting
-   **Course Coordinator**: Manages batch scheduling and enrollment
-   **Accountant**: Ensures proper accounting entries
-   **Management**: Reviews financial performance

---

## 🧪 **TEST SCENARIOS**

### **SCENARIO 1: Automatic Revenue Recognition Workflow**

**Story**: "The Digital Marketing course batch is scheduled to start tomorrow. We have 3 students enrolled and paid. The course coordinator needs to start the batch, and the system should automatically recognize all deferred revenue."

**Test Steps**:

1. **Setup**: Verify existing enrollment data
2. **Action**: Change batch status from 'planned' to 'ongoing'
3. **Validation**: Check automatic revenue recognition
4. **Verification**: Validate journal entries and reporting

**Expected Results**:

-   BatchStarted event triggered
-   RecognizeRevenueJob dispatched
-   Journal entries created (Deferred Revenue → Course Revenue)
-   Revenue recognition records created
-   Enhanced reporting shows "Fully Recognized" status

---

### **SCENARIO 2: Manual Batch Start Control**

**Story**: "The course coordinator wants to manually start a batch before the scheduled start date. The system should validate the conditions and trigger revenue recognition."

**Test Steps**:

1. **Setup**: Create batch with 'planned' status
2. **Action**: Use manual "Start" button
3. **Validation**: Check system validation and processing
4. **Verification**: Confirm revenue recognition triggered

**Expected Results**:

-   Manual start button available
-   System validates batch conditions
-   Batch status changes to 'ongoing'
-   Revenue recognition job dispatched
-   Success message displayed

---

### **SCENARIO 3: Enhanced Reporting Validation**

**Story**: "The Finance Manager needs to review course profitability with the new revenue recognition metrics. The report should show recognition status, dates, and comprehensive financial analysis."

**Test Steps**:

1. **Setup**: Access Course Financial Reports
2. **Action**: Navigate to Profitability report
3. **Validation**: Check new columns and metrics
4. **Verification**: Validate calculations and status badges

**Expected Results**:

-   Recognition Status column with badges
-   Recognition Date column
-   Additional summary cards
-   Real-time calculations
-   Proper Indonesian Rupiah formatting

---

### **SCENARIO 4: Partial Revenue Recognition**

**Story**: "A course batch has mixed enrollment statuses - some students have completed payment, others are still pending. The system should handle partial recognition correctly."

**Test Steps**:

1. **Setup**: Create batch with mixed enrollment statuses
2. **Action**: Start batch with partial enrollments
3. **Validation**: Check partial recognition handling
4. **Verification**: Validate reporting shows "Partially Recognized"

**Expected Results**:

-   Only paid enrollments recognized
-   Partial recognition status displayed
-   Accurate deferred revenue calculations
-   Proper status badge (Partially Recognized)

---

### **SCENARIO 5: Error Handling and Edge Cases**

**Story**: "The system should handle edge cases gracefully - duplicate recognition attempts, invalid batch states, and missing data scenarios."

**Test Steps**:

1. **Setup**: Create various edge case scenarios
2. **Action**: Attempt invalid operations
3. **Validation**: Check error handling
4. **Verification**: Confirm system stability

**Expected Results**:

-   Duplicate recognition prevention
-   Proper error messages
-   System remains stable
-   Logging captures issues

---

## 🔍 **DETAILED TEST EXECUTION PLAN**

### **Pre-Test Setup**

1. **Database Verification**:

    - Check existing course data
    - Verify enrollment records
    - Confirm batch statuses
    - Validate account mappings

2. **System State Check**:
    - Queue worker running
    - Event listeners active
    - Routes properly configured
    - Permissions set correctly

### **Test Execution Order**

1. **Scenario 1**: Automatic Revenue Recognition
2. **Scenario 2**: Manual Batch Start Control
3. **Scenario 3**: Enhanced Reporting Validation
4. **Scenario 4**: Partial Revenue Recognition
5. **Scenario 5**: Error Handling and Edge Cases

---

## 📊 **EXPECTED TEST RESULTS**

### **Database Changes**

| **Table**                | **Expected Changes**        | **Validation**                |
| ------------------------ | --------------------------- | ----------------------------- |
| **revenue_recognitions** | New recognition records     | Count and amount verification |
| **journals**             | Revenue recognition entries | Debit/Credit validation       |
| **journal_lines**        | Account transfers           | Proper account mapping        |
| **course_batches**       | Status updates              | Status change tracking        |

### **UI/UX Validation**

| **Component**          | **Expected Behavior**     | **Test Method**             |
| ---------------------- | ------------------------- | --------------------------- |
| **Batch Status**       | Changes to 'ongoing'      | Visual confirmation         |
| **Recognition Status** | Shows appropriate badge   | Color and text validation   |
| **Summary Cards**      | Updates with new metrics  | Real-time calculation check |
| **Error Messages**     | Displays helpful messages | Error scenario testing      |

### **Business Logic Validation**

| **Feature**             | **Expected Result**          | **Validation Method** |
| ----------------------- | ---------------------------- | --------------------- |
| **Automatic Trigger**   | Event fired on status change | Log analysis          |
| **Manual Control**      | Button works correctly       | Click testing         |
| **Revenue Calculation** | Accurate amounts             | Database verification |
| **Status Updates**      | Real-time reporting          | UI refresh testing    |

---

## 🚨 **POTENTIAL ISSUES & SOLUTIONS**

### **Issue 1: Queue Worker Not Running**

-   **Symptoms**: Jobs not processing
-   **Solution**: Start queue worker
-   **Command**: `php artisan queue:work`

### **Issue 2: Event Not Triggering**

-   **Symptoms**: No revenue recognition
-   **Solution**: Check event/listener registration
-   **Validation**: Verify EventServiceProvider

### **Issue 3: Permission Errors**

-   **Symptoms**: Access denied
-   **Solution**: Check user permissions
-   **Validation**: Verify role assignments

### **Issue 4: Data Inconsistency**

-   **Symptoms**: Incorrect calculations
-   **Solution**: Check database integrity
-   **Validation**: Verify foreign key relationships

---

## ✅ **SUCCESS CRITERIA**

### **Primary Success Criteria**

1. ✅ Automatic revenue recognition triggers correctly
2. ✅ Manual batch start control functions properly
3. ✅ Enhanced reporting displays accurate data
4. ✅ Status badges show correct recognition states
5. ✅ Summary cards calculate metrics correctly

### **Secondary Success Criteria**

1. ✅ Error handling works gracefully
2. ✅ System performance remains stable
3. ✅ User experience is intuitive
4. ✅ Data integrity is maintained
5. ✅ Logging captures important events

---

## 📝 **TEST EXECUTION CHECKLIST**

### **Pre-Test**

-   [ ] Verify database state
-   [ ] Check queue worker status
-   [ ] Confirm user permissions
-   [ ] Validate system configuration

### **Test Execution**

-   [ ] Execute Scenario 1: Automatic Recognition
-   [ ] Execute Scenario 2: Manual Control
-   [ ] Execute Scenario 3: Enhanced Reporting
-   [ ] Execute Scenario 4: Partial Recognition
-   [ ] Execute Scenario 5: Error Handling

### **Post-Test**

-   [ ] Document test results
-   [ ] Verify data integrity
-   [ ] Check system logs
-   [ ] Update test documentation

---

## 🔄 **FOLLOW-UP ACTIONS**

### **If Tests Pass**

1. Document successful test execution
2. Update implementation documentation
3. Plan production deployment
4. Create user training materials

### **If Tests Fail**

1. Document specific failure points
2. Investigate root causes
3. Implement fixes
4. Re-run test scenarios
5. Update system documentation

---

## 📚 **RELATED DOCUMENTATION**

-   **Implementation Summary**: `docs/REVENUE_RECOGNITION_IMPLEMENTATION_SUMMARY.md`
-   **Training Materials**: `docs/ERP-TRAINING-MATERIALS.md`
-   **Course Test Scenarios**: `docs/test-scenarios/COURSE_SPECIFIC_PROFIT_LOSS_TEST_SCENARIO.md`
-   **System Architecture**: `docs/architecture.md`

---

**Test Scenarios Created**: January 29, 2025  
**Status**: Ready for Execution  
**Next Step**: Execute tests using Chrome DevTools automation

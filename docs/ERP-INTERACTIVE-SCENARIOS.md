# ERP System Interactive Scenarios

## Story-Based Learning Exercises

**Purpose**: Interactive scenarios that help employees explore and understand the ERP system's capabilities through realistic business situations.

**Format**: Each scenario includes a story, step-by-step instructions, decision points, and reflection questions.

## Testing Status ✅ COMPLETED

**Last Updated**: 2025-01-15

**Overall Status**: All 4 primary scenarios have been successfully tested and validated.

### Completed Scenarios:

- ✅ **Scenario 1: Donation Recording** - Successfully tested Rp 50,000,000 donation journal entry with SAK compliance
- ✅ **Scenario 2: Office Supply Purchase** - Successfully tested Rp 2,500,000 office supplies transaction with balanced debits/credits
- ✅ **Scenario 3: Customer Invoice** - Successfully tested PT Mandiri Sejahtera invoice (Rp 15,000,000 including 11% PPN)
- ✅ **Scenario 4: Complex Asset Purchase** - Successfully tested PT Komputer Maju supplier and 10 computers (Rp 67,567,570)

### Reporting Functionality Testing:

- ✅ **Trial Balance Report** - Successfully loaded and displayed financial data with proper date filtering
- ✅ **GL Detail Report** - Successfully validated date range filtering and comprehensive transaction details
- ✅ **Asset Reports** - Successfully accessed with proper permission controls

### Indonesian Business Compliance Validation:

- ✅ **Currency & Tax Compliance** - Indonesian Rupiah formatting, PPN tax handling at 11%
- ✅ **Accounting Standards** - SAK compliance context and proper chart of accounts structure
- ✅ **Company Structures** - Indonesian naming conventions (PT, Yayasan, CV)

**System Status**: Production-ready with comprehensive Indonesian business compliance and enterprise-grade functionality.

---

## Table of Contents

1. [Beginner Scenarios](#1-beginner-scenarios)
2. [Intermediate Scenarios](#2-intermediate-scenarios)
3. [Advanced Scenarios](#3-advanced-scenarios)
4. [Role-Specific Scenarios](#4-role-specific-scenarios)
5. [Crisis Management Scenarios](#5-crisis-management-scenarios)

---

## 1. Beginner Scenarios

### Scenario 1: The First Day - New Accountant Onboarding

**Story**: You're Sarah, a new accountant at Yayasan Pendidikan Maju, a foundation focused on education. Today is your first day, and your supervisor, Pak Budi, has asked you to record your first transaction: a donation of Rp 50,000,000 received from PT Maju Jaya for the "Scholarship Fund" project. This donation must be recorded according to Indonesian Accounting Standards (SAK-ETAP).

**Your Task**: Record this donation properly in the system.

**Step-by-Step Guide**:

1. Log into the ERP system using your accountant credentials
2. Navigate to Journals → Create New Journal
3. Fill in the basic information:
   - Date: Today's date
   - Description: "Donation from PT Maju Jaya for Scholarship Fund"
4. Add the first journal line:
   - Account: Select "Cash" account
   - Debit: Rp 50,000,000
   - Project: Select "Scholarship Fund"
   - Memo: "Donation received"
5. Add the second journal line:
   - Account: Select "Donation Revenue" account
   - Credit: Rp 50,000,000
   - Project: Select "Scholarship Fund"
   - Memo: "Donation revenue"
6. Verify the balance shows zero
7. Save the journal entry

**Decision Points**:

- What happens if you accidentally enter the wrong amount?
- How do you know which accounts to use?
- Why is it important to assign this to the "Scholarship Fund" project?

**Reflection Questions**:

- Why must debits equal credits in every journal entry?
- How does assigning transactions to projects help the organization?
- What would happen if you forgot to assign the transaction to a project?

**Learning Objectives**:

- Understand basic journal entry creation
- Learn about account selection
- Understand project/dimension assignment
- Practice data entry accuracy

---

### Scenario 2: The Office Supply Purchase

**Story**: As the office manager, you need to purchase office supplies for Rp 2,500,000. You've found a good deal at Toko Alat Kantor and want to pay cash. However, you need to record this transaction properly in the accounting system according to Indonesian accounting standards, including proper PPN handling if applicable.

**Your Task**: Create a journal entry for this office supply purchase.

**Step-by-Step Guide**:

1. Navigate to Journals → Create New Journal
2. Fill in the basic information:
   - Date: Today's date
   - Description: "Office supplies purchase - Toko Alat Kantor"
3. Add the first journal line:
   - Account: Select "Office Supplies Expense"
   - Debit: Rp 2,500,000
   - Department: Select "Administration"
   - Memo: "Office supplies for general use"
4. Add the second journal line:
   - Account: Select "Cash"
   - Credit: Rp 2,500,000
   - Memo: "Cash payment made"
5. Verify the balance
6. Save the journal entry

**Decision Points**:

- Should this be recorded as an expense or an asset?
- Which department should this be assigned to?
- What if you paid by bank transfer instead of cash?

**Reflection Questions**:

- What's the difference between recording something as an expense vs. an asset?
- How does department assignment help with cost tracking?
- What additional information might be useful to include?

**Learning Objectives**:

- Understand expense vs. asset classification
- Learn about department assignment
- Practice cash transaction recording

---

### Scenario 3: The Customer Invoice

**Story**: Yayasan Pendidikan Maju provides consulting services to PT Mandiri Sejahtera. You've completed a training program for their employees and need to invoice them for Rp 15,000,000 (including 11% PPN). The payment terms are Net 30 days. This invoice must comply with Indonesian tax regulations and SAK standards.

**Your Task**: Create a sales invoice for this service.

**Step-by-Step Guide**:

1. Navigate to AR → Sales Invoices → Create New
2. Fill in the invoice header:
   - Customer: Select "PT Mandiri Sejahtera"
   - Date: Today's date
   - Due Date: 30 days from today
   - Terms: "Net 30"
   - Description: "Training program for employees"
3. Add invoice line:
   - Account: Select "Consulting Revenue"
   - Description: "Employee training program"
   - Quantity: 1
   - Unit Price: Rp 13,513,514 (before tax)
   - Amount: Rp 13,513,514
4. Add tax line:
   - Tax Code: Select "PPN Output 11%"
   - Tax Amount: Rp 1,486,486
5. Review totals:
   - Subtotal: Rp 13,513,514
   - Tax: Rp 1,486,486
   - Total: Rp 15,000,000
6. Save as draft

**Decision Points**:

- How do you calculate the amount before tax?
- What happens if the customer doesn't pay on time?
- Should you assign this to a specific project?

**Reflection Questions**:

- Why is it important to separate the tax amount?
- How does the due date affect cash flow planning?
- What happens when this invoice is posted?

**Learning Objectives**:

- Understand invoice creation process
- Learn about tax calculations
- Understand payment terms
- Practice customer selection

---

## 2. Intermediate Scenarios

### Scenario 4: The Complex Asset Purchase

**Story**: Yayasan Pendidikan Maju is expanding its computer lab and needs to purchase 10 new computers. The total cost is Rp 75,000,000 (including 11% PPN). The computers will be used for the "Digital Learning" project and assigned to the "IT Department". Each computer has a useful life of 3 years. Asset registration must follow Indonesian accounting standards for fixed assets.

**Your Task**: Record this asset purchase and set up depreciation.

**Step-by-Step Guide**:

1. First, create the purchase invoice:
   - Navigate to AP → Purchase Invoices → Create New
   - Vendor: Select "PT Komputer Maju"
   - Date: Today's date
   - Due Date: 30 days from today
   - Description: "Computer lab expansion - 10 units"
2. Add invoice line:
   - Account: Select "Computer Equipment" (asset account)
   - Description: "10 units desktop computers"
   - Quantity: 10
   - Unit Price: Rp 6,756,757 (before tax)
   - Amount: Rp 67,567,567
3. Add tax line:
   - Tax Code: Select "PPN Input 11%"
   - Tax Amount: Rp 7,432,433
4. Review totals and save
5. Now, register the assets:
   - Navigate to Assets → Assets → Create New
   - For each computer:
     - Name: "Desktop Computer - Unit 1" (through Unit 10)
     - Category: Select "Computer Equipment"
     - Acquisition Cost: Rp 7,500,000 (total cost per unit)
     - Purchase Date: Today's date
     - Useful Life: 36 months
     - Depreciation Method: Straight-Line
     - Project: "Digital Learning"
     - Department: "IT Department"
     - Location: "Computer Lab Room 1"
6. Save each asset

**Decision Points**:

- Should you create one asset record for all 10 computers or separate records?
- How do you handle the PPN Input tax?
- What happens to depreciation if you change the useful life?

**Reflection Questions**:

- Why is it important to track individual assets?
- How does depreciation affect the organization's financial statements?
- What reports can you generate from this asset data?

**Learning Objectives**:

- Understand asset purchase process
- Learn about asset registration
- Understand depreciation setup
- Practice multi-unit asset handling

---

### Scenario 5: The Partial Payment Dilemma

**Story**: PT Mandiri Sejahtera has sent a partial payment of Rp 10,000,000 for the training invoice (Rp 15,000,000 total). They've indicated they'll pay the remaining Rp 5,000,000 next month. You need to record this payment and track the remaining balance.

**Your Task**: Process the partial payment and allocate it properly.

**Step-by-Step Guide**:

1. Navigate to AR → Sales Receipts → Create New
2. Fill in receipt header:
   - Customer: Select "PT Mandiri Sejahtera"
   - Date: Today's date
   - Description: "Partial payment for training invoice"
   - Total Amount: Rp 10,000,000
3. Add receipt line:
   - Account: Select "Cash" (or "Bank Account" if paid by transfer)
   - Amount: Rp 10,000,000
   - Memo: "Partial payment received"
4. Allocate to invoice:
   - Select the training invoice
   - Allocate Rp 10,000,000 to this invoice
   - Review remaining balance: Rp 5,000,000
5. Save and post the receipt

**Decision Points**:

- How do you handle the remaining balance?
- What if the customer wants to pay in installments?
- How do you track multiple partial payments?

**Reflection Questions**:

- Why is it important to allocate payments to specific invoices?
- How does this affect the customer's account balance?
- What reports will show the remaining balance?

**Learning Objectives**:

- Understand payment processing
- Learn about payment allocation
- Understand partial payment handling
- Practice customer balance tracking

---

### Scenario 6: The Month-End Depreciation Run

**Story**: It's the end of January 2025, and you need to run depreciation for all fixed assets. This is a critical month-end process that affects the organization's financial statements. You need to ensure all assets are properly depreciated and the journal entries are created.

**Your Task**: Run the monthly depreciation process.

**Step-by-Step Guide**:

1. Navigate to Assets → Depreciation Runs → Create New
2. Select the period: "2025-01"
3. Review the depreciation preview:
   - Check which assets will be depreciated
   - Verify depreciation amounts
   - Review total depreciation for the month
4. Run the depreciation process:
   - Click "Run Depreciation"
   - Confirm the action
   - Wait for the process to complete
5. Review the generated journal entries:
   - Check that depreciation expense is debited
   - Check that accumulated depreciation is credited
   - Verify the total amounts
6. Post the journal entries

**Decision Points**:

- What happens if an asset is disposed during the month?
- How do you handle assets with different depreciation methods?
- What if the depreciation amount seems incorrect?

**Reflection Questions**:

- Why is depreciation important for financial reporting?
- How does depreciation affect the organization's profit?
- What happens to asset book values after depreciation?

**Learning Objectives**:

- Understand depreciation process
- Learn about automated journal creation
- Understand month-end procedures
- Practice system controls

---

## 3. Advanced Scenarios

### Scenario 7: The Multi-Project Budget Analysis

**Story**: Yayasan Pendidikan Maju is preparing its quarterly board meeting and needs a comprehensive analysis of expenses by project. The organization has three main projects: "Scholarship Fund", "Digital Learning", and "Community Outreach". You need to generate reports showing actual expenses vs. budget for each project.

**Your Task**: Generate and analyze multi-dimensional reports.

**Step-by-Step Guide**:

1. Navigate to Reports → General Ledger Detail
2. Set report parameters:
   - Date Range: Last 3 months
   - Group By: Project
   - Include: All accounts
3. Generate the report
4. Analyze the data:
   - Review expenses by project
   - Compare actual vs. budget
   - Identify variances
5. Create a summary report:
   - Export to Excel
   - Create charts showing project performance
   - Highlight significant variances
6. Prepare recommendations:
   - Identify projects over budget
   - Suggest cost control measures
   - Recommend budget adjustments

**Decision Points**:

- How do you handle shared expenses across projects?
- What constitutes a significant variance?
- How do you present the data to the board?

**Reflection Questions**:

- Why is multi-dimensional reporting important?
- How does this data help with decision-making?
- What other reports would be useful for the board?

**Learning Objectives**:

- Understand advanced reporting
- Learn about budget analysis
- Understand variance analysis
- Practice data interpretation

---

### Scenario 8: The Asset Disposal Audit

**Story**: During an internal audit, you discover that several old computers were disposed of last month, but the disposal transactions weren't properly recorded in the system. You need to investigate and correct these transactions to ensure accurate financial records.

**Your Task**: Investigate and correct the asset disposal records.

**Step-by-Step Guide**:

1. Investigate the missing disposals:
   - Check asset records for disposed status
   - Review disposal documentation
   - Identify which assets were disposed
2. Create disposal records:
   - Navigate to Assets → Asset Disposals → Create New
   - For each disposed asset:
     - Select the asset
     - Enter disposal date
     - Select disposal type (scrap, sale, donation)
     - Enter disposal proceeds (if any)
     - Enter disposal reason
3. Calculate gain/loss:
   - System calculates book value at disposal
   - Compares with disposal proceeds
   - Determines gain or loss
4. Review and post disposals:
   - Check all calculations
   - Post the disposal transactions
   - Verify journal entries are created
5. Update asset status:
   - Ensure all disposed assets are marked as disposed
   - Update location records
   - Remove from active asset list

**Decision Points**:

- How do you handle assets disposed without proper documentation?
- What if disposal proceeds are unknown?
- How do you prevent this from happening again?

**Reflection Questions**:

- Why is proper asset disposal recording important?
- How does this affect the organization's financial statements?
- What controls can prevent this issue in the future?

**Learning Objectives**:

- Understand asset disposal process
- Learn about audit procedures
- Understand gain/loss calculations
- Practice error correction

---

## 4. Role-Specific Scenarios

### Scenario 9: The Approver's Dilemma - Unusual Transaction

**Story**: As an approver, you're reviewing a journal entry submitted by an accountant. The entry is for Rp 100,000,000 in "Consulting Revenue" from a new customer you've never heard of. The accountant claims it's for a major consulting project, but something seems unusual about the transaction.

**Your Task**: Investigate and decide whether to approve this transaction.

**Step-by-Step Guide**:

1. Review the journal entry:
   - Check all details carefully
   - Verify account selections
   - Review supporting documentation
2. Investigate the customer:
   - Check customer master data
   - Verify customer information
   - Look for previous transactions
3. Check for supporting documents:
   - Request contract or agreement
   - Verify project details
   - Check approval from management
4. Make your decision:
   - Approve if everything checks out
   - Reject if there are issues
   - Request additional information if needed
5. Document your decision:
   - Add comments to the journal entry
   - Communicate with the accountant
   - Update approval records

**Decision Points**:

- What red flags should you look for?
- How do you verify the legitimacy of a transaction?
- What if you can't get supporting documentation?

**Reflection Questions**:

- What is your responsibility as an approver?
- How do you balance efficiency with control?
- What happens if you approve a fraudulent transaction?

**Learning Objectives**:

- Understand approval responsibilities
- Learn about fraud detection
- Understand control procedures
- Practice decision-making

---

### Scenario 10: The Cashier's Challenge - Multiple Payments

**Story**: As a cashier, you're processing payments at the end of the day. You have several payments to record:

1. Cash payment of Rp 5,000,000 from PT Mandiri Sejahtera
2. Bank transfer of Rp 3,000,000 from PT Maju Jaya
3. Check payment of Rp 2,000,000 from PT Sejahtera Abadi

You need to process all these payments accurately and efficiently.

**Your Task**: Process all payments and reconcile the cash register.

**Step-by-Step Guide**:

1. Process the first payment:
   - Navigate to AR → Sales Receipts → Create New
   - Select PT Mandiri Sejahtera
   - Enter cash payment details
   - Allocate to appropriate invoices
2. Process the second payment:
   - Create new receipt for PT Maju Jaya
   - Enter bank transfer details
   - Allocate to invoices
3. Process the third payment:
   - Create new receipt for PT Sejahtera Abadi
   - Enter check payment details
   - Allocate to invoices
4. Reconcile cash register:
   - Count physical cash
   - Compare with system totals
   - Investigate any discrepancies
5. Complete end-of-day procedures:
   - Print receipt summary
   - Update cash register records
   - Prepare bank deposit slip

**Decision Points**:

- How do you handle payment allocation when customers have multiple invoices?
- What if the physical cash doesn't match the system?
- How do you handle bounced checks?

**Reflection Questions**:

- Why is cash reconciliation important?
- How do you ensure accuracy in payment processing?
- What controls prevent errors and fraud?

**Learning Objectives**:

- Understand payment processing
- Learn about cash reconciliation
- Understand end-of-day procedures
- Practice accuracy and control

---

## 5. Crisis Management Scenarios

### Scenario 11: The System Crash Recovery

**Story**: The ERP system crashed during a critical month-end closing process. When the system comes back online, you discover that some transactions were lost and others were duplicated. You need to recover the data and ensure the month-end closing is completed accurately.

**Your Task**: Recover from the system crash and complete month-end closing.

**Step-by-Step Guide**:

1. Assess the damage:
   - Check which transactions were lost
   - Identify duplicated transactions
   - Review system logs
2. Recover lost transactions:
   - Recreate lost journal entries
   - Verify all supporting documentation
   - Ensure proper approvals
3. Remove duplicates:
   - Identify duplicate transactions
   - Reverse duplicate entries
   - Verify no data is lost
4. Complete month-end closing:
   - Run depreciation if not completed
   - Reconcile all accounts
   - Close the period
5. Document the recovery:
   - Record what happened
   - Document recovery actions
   - Recommend preventive measures

**Decision Points**:

- How do you prioritize which transactions to recover first?
- What if you can't find supporting documentation?
- How do you prevent this from happening again?

**Reflection Questions**:

- Why is data backup important?
- How do you ensure data integrity after a crash?
- What procedures should be in place for system recovery?

**Learning Objectives**:

- Understand disaster recovery
- Learn about data integrity
- Understand system controls
- Practice crisis management

---

### Scenario 12: The Audit Finding Response

**Story**: External auditors have found several issues in your organization's financial records:

1. Missing supporting documentation for some transactions
2. Inconsistent project assignments
3. Unreconciled bank accounts
4. Missing asset disposals

You need to address these findings and implement controls to prevent future issues.

**Your Task**: Address audit findings and implement corrective measures.

**Step-by-Step Guide**:

1. Address missing documentation:
   - Identify transactions without support
   - Gather or recreate documentation
   - Update transaction records
2. Fix inconsistent project assignments:
   - Review all transactions
   - Correct project assignments
   - Implement validation rules
3. Reconcile bank accounts:
   - Compare bank statements with system records
   - Identify and resolve discrepancies
   - Update bank reconciliation records
4. Record missing asset disposals:
   - Identify disposed assets
   - Create disposal records
   - Calculate and record gains/losses
5. Implement preventive controls:
   - Update procedures
   - Add validation rules
   - Train staff on new requirements

**Decision Points**:

- How do you prioritize which findings to address first?
- What if you can't find missing documentation?
- How do you ensure staff follow new procedures?

**Reflection Questions**:

- Why are audit findings important?
- How do you prevent future audit issues?
- What role does training play in compliance?

**Learning Objectives**:

- Understand audit compliance
- Learn about corrective actions
- Understand preventive controls
- Practice process improvement

---

## Conclusion

These interactive scenarios provide realistic, hands-on experience with the ERP system. Each scenario is designed to:

- **Build Confidence**: Start with simple tasks and progress to complex ones
- **Develop Skills**: Practice real-world situations employees will encounter
- **Encourage Thinking**: Decision points and reflection questions promote deeper understanding
- **Ensure Understanding**: Learning objectives clarify what should be mastered

**Tips for Using These Scenarios**:

1. **Practice Regularly**: Use scenarios for ongoing training and refresher courses
2. **Customize**: Adapt scenarios to your organization's specific needs
3. **Group Learning**: Use scenarios for team training and discussion
4. **Assessment**: Use scenarios as part of competency evaluation
5. **Continuous Improvement**: Update scenarios based on system changes and user feedback

Remember: The goal is not just to complete the tasks, but to understand the business logic and develop the judgment needed to handle real-world situations effectively.

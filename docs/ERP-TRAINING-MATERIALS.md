# ERP System Training Materials

## Comprehensive Employee Training Guide

**System**: Prasasta ERP - Enterprise Resource Planning System  
**Version**: Latest (Production Ready)  
**Target Audience**: All employees using the ERP system  
**Training Duration**: 4-6 hours (can be split into modules)  
**System Status**: ✅ Fully Functional - All role-based scenarios tested and working

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [User Roles & Permissions](#2-user-roles--permissions)
3. [Core Modules Training](#3-core-modules-training)
4. [Interactive Scenarios](#4-interactive-scenarios)
5. [Best Practices](#5-best-practices)
6. [Troubleshooting Guide](#6-troubleshooting-guide)
7. [Assessment Questions](#7-assessment-questions)

---

## 1. System Overview

### What is Prasasta ERP?

Prasasta ERP is a comprehensive enterprise resource planning system designed specifically for Indonesian organizations, particularly Yayasan (non-profit foundations). It provides complete financial management, asset tracking, and operational control in one integrated platform, fully compliant with Indonesian Accounting Standards (SAK) and tax regulations.

### Key Benefits

-   **Financial Control**: Complete double-entry accounting with automated journal posting
-   **Asset Management**: Track fixed assets from acquisition to disposal with automated depreciation
-   **Compliance**: Indonesian tax compliance (PPN, PPh withholding tax, SAK standards) and regulatory reporting
-   **Multi-dimensional Tracking**: Projects, funds, and departments for detailed cost analysis
-   **Audit Trail**: Complete history of all transactions and changes
-   **Role-based Security**: Granular permissions ensuring data security

### System Architecture

-   **Backend**: Laravel 12 with MySQL database
-   **Frontend**: AdminLTE 3 responsive interface
-   **Security**: Spatie Laravel Permission for role-based access control
-   **Reporting**: Built-in reports with CSV/PDF export capabilities

### Indonesian Accounting Standards Compliance

The system is designed to comply with Indonesian Accounting Standards (Standar Akuntansi Keuangan - SAK):

### Revenue Recognition Principles

Understanding deferred and recognized revenue is crucial for proper financial reporting:

#### **Deferred Revenue (Pendapatan Ditangguhkan)**

-   **Definition**: Money received in advance for services that haven't been delivered yet
-   **Example**: Student pays Rp 8,000,000 for a course that starts next month
-   **Accounting Treatment**: Recorded as liability until service is delivered
-   **Journal Entry**:
    ```
    Debit:  Cash/Bank                    Rp 8,000,000
    Credit: Deferred Revenue             Rp 7,207,207.21
    Credit: PPN Output                   Rp 792,792.79
    ```

#### **Recognized Revenue (Pendapatan Diakui)**

-   **Definition**: Revenue that can be recorded as earned income because the service has been delivered
-   **Example**: Course starts, training begins - now revenue can be recognized
-   **Accounting Treatment**: Transferred from liability to income
-   **Journal Entry**:
    ```
    Debit:  Deferred Revenue             Rp 7,207,207.21
    Credit: Course Revenue              Rp 7,207,207.21
    ```

#### **Revenue Recognition Triggers**

-   **Course Start**: When batch begins (recommended)
-   **Course Completion**: When batch ends
-   **Proportional**: Over course duration (advanced)

#### **Business Impact**

-   **Cash Flow**: Immediate (when payment received)
-   **Profit Recognition**: Only when service delivered
-   **Tax Compliance**: PPN collected but revenue recognition follows service delivery
-   **Financial Reporting**: Deferred revenue shows as liability until recognized

-   **SAK-ETAP**: For entities without public accountability
-   **SAK-EMKM**: For micro, small, and medium enterprises
-   **SAK-IFRS**: For entities with public accountability
-   **Chart of Accounts**: Based on Indonesian CoA structure
-   **Tax Compliance**: PPN (VAT), PPh (Income Tax), and withholding tax calculations
-   **Reporting**: Indonesian financial statement formats

---

## 2. User Roles & Permissions

### Role Overview

The system has five main user roles, each with specific permissions:

#### 1. Super Admin

-   **Full system access** - can perform all operations
-   **User management** - create, edit, delete users and roles
-   **System configuration** - manage all master data
-   **Reports** - access all reports and exports

#### 2. Accountant

-   **Financial data entry** - create journals, invoices, receipts
-   **Master data viewing** - view customers, vendors, accounts
-   **Asset viewing** - view assets and categories
-   **Reports** - access financial reports

#### 3. Approver

-   **Transaction approval** - post journals and transactions
-   **Asset operations** - run depreciation, approve disposals
-   **Movement approval** - approve asset transfers
-   **Reports** - access management reports

#### 4. Cashier

-   **Cash operations** - create receipts and payments
-   **Journal creation** - basic journal entries
-   **Reports** - view cash-related reports

#### 5. Auditor

-   **Read-only access** - view all reports and data
-   **Audit trail** - access complete transaction history
-   **No modification rights** - cannot change any data

### Permission Matrix

| Function         | Super Admin | Accountant | Approver | Cashier | Auditor   |
| ---------------- | ----------- | ---------- | -------- | ------- | --------- |
| Create Journals  | ✅          | ✅         | ❌       | ✅      | ❌        |
| Post Journals    | ✅          | ❌         | ✅       | ❌      | ❌        |
| Create Invoices  | ✅          | ✅         | ❌       | ❌      | ❌        |
| Post Invoices    | ✅          | ❌         | ✅       | ❌      | ❌        |
| Asset Management | ✅          | View Only  | Full     | ❌      | View Only |
| User Management  | ✅          | ❌         | ❌       | ❌      | ❌        |
| Reports          | ✅          | ✅         | ✅       | ✅      | ✅        |

---

## 3. Core Modules Training

### Module 1: General Ledger & Journals

#### Overview

The General Ledger is the foundation of the accounting system. All financial transactions flow through journal entries that must balance (total debits = total credits).

#### Key Concepts

-   **Chart of Accounts**: Organized list of all accounts following Indonesian CoA structure (Assets, Liabilities, Net Assets, Income, Expenses)
-   **Journal Entries**: Records of financial transactions with debit and credit amounts
-   **Balancing**: Every journal entry must have equal total debits and credits
-   **Dimensions**: Projects, Funds, Departments for detailed tracking
-   **Indonesian Standards**: All transactions must comply with SAK (Standar Akuntansi Keuangan)

#### How to Create a Journal Entry

1. **Navigate**: Go to Journals → Create New Journal
2. **Basic Information**:

    - Date: Transaction date
    - Description: Brief description of the transaction
    - Journal Number: Auto-generated (JNL-YYYYMM-######)

3. **Add Journal Lines**:

    - Account: Select from Chart of Accounts
    - Debit/Credit: Enter amount (only one per line)
    - Dimensions: Optional - Project, Fund, Department
    - Memo: Additional details

4. **Balance Check**: System shows running balance
5. **Save**: Save as draft or post immediately

#### Example Journal Entry

**Transaction**: Purchase office supplies for Rp 500,000 cash

| Account                 | Debit      | Credit     | Memo                     |
| ----------------------- | ---------- | ---------- | ------------------------ |
| Office Supplies Expense | Rp 500,000 |            | Office supplies purchase |
| Cash                    |            | Rp 500,000 | Payment made             |

### Module 2: Accounts Receivable (AR)

#### Overview

AR manages money owed to your organization by customers. It tracks invoices, payments, and outstanding balances.

#### Key Components

-   **Sales Orders**: Customer purchase requests
-   **Sales Invoices**: Bills sent to customers (must include PPN if applicable)
-   **Sales Receipts**: Customer payments received
-   **Aging Reports**: Outstanding balances by customer
-   **Tax Compliance**: PPN Output (VAT on sales) and PPh withholding tax

#### Sales Invoice Process

1. **Create Sales Invoice**:

    - Customer: Select from customer list
    - Date: Invoice date
    - Due Date: Payment due date
    - Terms: Payment terms (e.g., Net 30)

2. **Add Invoice Lines**:

    - Account: Revenue account
    - Description: Service/product description
    - Quantity: Number of units
    - Unit Price: Price per unit
    - Tax Code: PPN Output if applicable

3. **Review Totals**: System calculates subtotal, tax, and total
4. **Save**: Save as draft
5. **Post**: Approver posts to General Ledger

#### Payment Receipt Process

1. **Create Sales Receipt**:

    - Customer: Select customer
    - Date: Payment date
    - Amount: Payment amount

2. **Allocate to Invoices**:

    - Select invoices to apply payment
    - Allocate payment amounts
    - System calculates remaining balance

3. **Post**: Creates journal entry (Dr Cash, Cr AR)

### Module 3: Accounts Payable (AP)

#### Overview

AP manages money your organization owes to vendors. It tracks purchase orders, invoices, and payments.

#### Key Components

-   **Purchase Orders**: Orders placed with vendors
-   **Purchase Invoices**: Bills received from vendors (must include PPN if applicable)
-   **Purchase Payments**: Payments made to vendors
-   **Aging Reports**: Outstanding balances by vendor
-   **Tax Compliance**: PPN Input (VAT on purchases) and PPh withholding tax

#### Purchase Invoice Process

1. **Create Purchase Invoice**:

    - Vendor: Select from vendor list
    - Date: Invoice date
    - Due Date: Payment due date
    - Reference: PO number if applicable

2. **Add Invoice Lines**:

    - Account: Expense or asset account
    - Description: Service/product description
    - Quantity: Number of units
    - Unit Price: Price per unit
    - Tax Code: PPN Input if applicable

3. **Review Totals**: System calculates subtotal, tax, and total
4. **Save**: Save as draft
5. **Post**: Approver posts to General Ledger

#### Payment Process

1. **Create Purchase Payment**:

    - Vendor: Select vendor
    - Date: Payment date
    - Amount: Payment amount

2. **Allocate to Invoices**:

    - Select invoices to pay
    - Allocate payment amounts
    - System calculates remaining balance

3. **Post**: Creates journal entry (Dr AP, Cr Cash/Bank)

### Module 4: Fixed Asset Management

#### Overview

Fixed Asset Management tracks your organization's long-term assets from acquisition to disposal, including automated depreciation calculations.

#### Key Components

-   **Asset Categories**: Grouping of similar assets (Equipment, Furniture, Vehicles)
-   **Asset Master Data**: Individual asset records
-   **Depreciation Runs**: Monthly automated depreciation calculations
-   **Asset Disposal**: Retirement and disposal of assets
-   **Asset Movement**: Transfer of assets between locations/custodians

#### Asset Registration Process

1. **Create Asset**:

    - Basic Info: Name, description, serial number
    - Financial: Acquisition cost, purchase date, vendor
    - Depreciation: Method, useful life, residual value
    - Dimensions: Project, fund, department
    - Location: Physical location and custodian

2. **Asset Categories**:
    - Predefined categories with default settings
    - Each category maps to specific GL accounts
    - Default depreciation methods and useful lives

#### Depreciation Process

1. **Monthly Depreciation Run**:

    - System calculates depreciation for all active assets
    - Creates journal entries automatically
    - Updates asset book values
    - Generates depreciation schedule

2. **Depreciation Methods**:
    - **Straight-Line**: Equal amount each period
    - **Declining Balance**: Higher amounts in early periods

#### Asset Disposal Process

1. **Create Disposal**:

    - Asset: Select asset to dispose
    - Disposal Date: When asset was disposed
    - Disposal Type: Sale, scrap, donation, trade-in
    - Proceeds: Amount received (if any)

2. **Gain/Loss Calculation**:
    - System calculates gain/loss automatically
    - Book Value vs. Disposal Proceeds
    - Creates journal entry for disposal

### Module 5: Reporting & Analytics

#### Overview

The system provides comprehensive reporting capabilities for financial analysis, compliance, and management decision-making.

#### Key Reports

1. **Trial Balance**: All account balances at a point in time
2. **General Ledger Detail**: Detailed transaction history by account
3. **AR Aging**: Outstanding receivables by customer and age
4. **AP Aging**: Outstanding payables by vendor and age
5. **Asset Register**: Complete listing of all assets
6. **Depreciation Schedule**: Monthly depreciation by asset
7. **Cash Ledger**: Bank account transaction history
8. **Indonesian Financial Statements**: Laporan Keuangan sesuai SAK

#### Report Features

-   **Date Range Selection**: Customize report periods
-   **Export Options**: CSV, PDF, Excel formats
-   **Filtering**: By account, customer, vendor, project, etc.
-   **Drill-down**: Click to see detailed transactions

---

## 4. Interactive Scenarios

### Scenario 1: New Employee Onboarding

**Role**: Accountant  
**Situation**: You're a new accountant at Yayasan Pendidikan Maju. Your first task is to record a donation received.

**Steps to Complete**:

1. Log into the system with your accountant credentials
2. Navigate to Journals → Create New Journal
3. Create a journal entry for a Rp 10,000,000 donation received in cash
4. Use appropriate accounts (Cash and Donation Revenue)
5. Assign to the "Education Fund" project
6. Save the journal entry as DRAFT
7. **Note**: As an Accountant, you cannot post the journal - it must be approved by an Approver

**Questions**:

-   What accounts would you use for this transaction?
-   Why is it important to assign the donation to a specific fund?
-   What happens if your debits don't equal your credits?
-   Why can't you post the journal entry yourself?

### Scenario 2: Journal Entry Approval Workflow

**Role**: Approver  
**Situation**: You need to review and approve journal entries created by accountants before they can be posted to the General Ledger.

**Steps to Complete**:

1. Navigate to Journals → Journal List
2. Review pending journal entries in DRAFT status
3. Select the donation journal entry created by the accountant
4. Review the journal details:
    - Verify debits equal credits
    - Check account selections are appropriate
    - Confirm dimensions (project, fund, department) are assigned
    - Review memo descriptions for clarity
5. **Approve and Post** the journal entry
6. Verify the journal status changes from DRAFT to POSTED
7. Check that the Trial Balance now shows the posted amounts

**Questions**:

-   What is the difference between DRAFT and POSTED status?
-   Why is separation of duties important in journal approval?
-   What happens to the General Ledger when you post a journal?
-   How can you verify the journal was posted correctly?

### Scenario 3: Monthly Depreciation Run

**Role**: Approver  
**Situation**: It's month-end, and you need to run depreciation for all fixed assets.

**Steps to Complete**:

1. Navigate to Assets → Depreciation Runs
2. Select the current month (e.g., 2025-01)
3. Review the depreciation preview
4. Check the total depreciation amount
5. Run the depreciation process
6. Review the generated journal entries
7. **Post the depreciation journal entries** to update asset values

**Questions**:

-   What happens to asset book values after depreciation?
-   Why is depreciation important for financial reporting?
-   What accounts are affected by depreciation entries?
-   Why must depreciation journals be posted by an Approver?

### Scenario 4: Customer Payment Processing

**Role**: Cashier  
**Situation**: A customer has sent a payment of Rp 2,500,000 for an outstanding invoice of Rp 3,000,000.

**Steps to Complete**:

1. Navigate to AR → Sales Receipts → Create New
2. Select the customer from the list
3. Enter the payment amount (Rp 2,500,000)
4. Allocate the payment to the specific invoice
5. Review the remaining balance
6. Save as DRAFT (Cashier cannot post)
7. **Note**: Receipt must be approved by Approver before posting

**Questions**:

-   How do you handle partial payments?
-   What happens to the remaining Rp 500,000 balance?
-   Why is it important to allocate payments to specific invoices?
-   Why can't you post the receipt yourself?

### Scenario 5: Asset Disposal

**Role**: Approver  
**Situation**: An old computer (Asset ID: COMP-001) is being sold for Rp 2,000,000. The computer's book value is Rp 1,500,000.

**Steps to Complete**:

1. Navigate to Assets → Asset Disposals → Create New
2. Select the computer asset
3. Enter disposal date and type (Sale)
4. Enter disposal proceeds (Rp 2,000,000)
5. Review the gain calculation
6. **Approve and post** the disposal journal entry

**Questions**:

-   What type of gain/loss is this transaction?
-   Which accounts are affected by the disposal?
-   Why is it important to track asset disposals?
-   Why must disposal journals be posted by an Approver?

### Scenario 6: Vendor Invoice Processing

**Role**: Accountant  
**Situation**: You received an invoice from Office Supplies Co. for Rp 1,200,000 (including Rp 120,000 PPN).

**Steps to Complete**:

1. Navigate to AP → Purchase Invoices → Create New
2. Select Office Supplies Co. as vendor
3. Enter invoice details and due date
4. Add line items for office supplies
5. Apply PPN Input tax code
6. Review totals and save as DRAFT
7. **Note**: Invoice must be approved by Approver before posting

**Questions**:

-   What is PPN and why is it important for Indonesian businesses?
-   How does the system handle tax calculations according to Indonesian tax law?
-   What happens when an Approver posts this invoice?
-   What are the Indonesian tax reporting requirements for PPN?

### Scenario 7: Financial Reporting

**Role**: Auditor  
**Situation**: You need to prepare a Trial Balance report for the board meeting.

**Steps to Complete**:

1. Navigate to Reports → Trial Balance
2. Select the current month-end date
3. Review the report for accuracy
4. **Note**: Only POSTED transactions appear in reports
5. Export the report to PDF
6. Check that total debits equal total credits

**Questions**:

-   What does a Trial Balance tell you?
-   Why must debits equal credits?
-   How can you verify the report accuracy?
-   Why don't DRAFT transactions appear in reports?

### Scenario 8: Multi-dimensional Tracking

**Role**: Accountant  
**Situation**: You need to record expenses for three different projects and track them separately.

**Steps to Complete**:

1. Create a journal entry for various project expenses
2. Assign each expense line to different projects
3. Assign to appropriate funds
4. Assign to relevant departments
5. Review the journal entry
6. Save as DRAFT
7. **Note**: Journal must be approved by Approver before posting

**Questions**:

-   Why is multi-dimensional tracking important?
-   How can you use this data for project analysis?
-   What reports can you generate with this information?
-   Why can't you post multi-dimensional journals yourself?

### Scenario 9: Period Close Process

**Role**: Approver  
**Situation**: Month-end closing process - you need to close January 2025.

**Steps to Complete**:

1. Navigate to Periods → Period Management
2. Review all pending transactions for January
3. **Ensure all journals are posted** (not just saved as DRAFT)
4. Run depreciation if not done
5. Close the period for January 2025
6. Verify the period is closed

**Questions**:

-   Why is period closing important?
-   What happens to transactions after a period is closed?
-   What controls prevent posting to closed periods?
-   Why must all journals be posted before closing a period?

### Scenario 10: Complete Journal Creation-to-Posting Workflow

**Role**: Accountant → Approver  
**Situation**: Demonstrate the complete workflow from journal creation by an Accountant to approval and posting by an Approver.

**Part A - Accountant Role**:

1. Log in as Accountant (budi@prasasta.com)
2. Navigate to Journals → Create New Journal
3. Create a journal entry for office renovation expenses:
    - Description: "Office renovation project - January 2025"
    - Line 1: Debit Office Renovation Expense (Rp 15,000,000)
    - Line 2: Credit Cash (Rp 15,000,000)
    - Assign to "Administration" department and "General Fund"
4. Save as DRAFT
5. **Verify**: Check that "Post Journal" button is disabled
6. Log out

**Part B - Approver Role**:

1. Log in as Approver (siti@prasasta.com)
2. Navigate to Journals → Journal List
3. Find the office renovation journal entry
4. Review the journal details:
    - Verify debits equal credits
    - Check account selections
    - Confirm dimensions are assigned
5. **Approve and Post** the journal entry
6. Verify status changes from DRAFT to POSTED
7. Navigate to Reports → Trial Balance
8. **Verify**: Check that the Trial Balance now shows the posted amounts
9. Navigate to Reports → GL Detail
10. **Verify**: Check that the journal entry appears in GL Detail report

**Questions**:

-   Why is this two-step process important for internal controls?
-   What happens to the General Ledger when a journal is posted?
-   How can you verify that a journal was posted correctly?
-   What's the difference between DRAFT and POSTED status?
-   Why don't DRAFT transactions appear in financial reports?

---

## 5. Best Practices

### Data Entry Best Practices

1. **Always Verify Balances**: Ensure debits equal credits before posting
2. **Use Descriptive Memos**: Clear descriptions help with audit trails
3. **Assign Dimensions**: Always assign projects, funds, and departments when applicable
4. **Review Before Posting**: Double-check all amounts and accounts
5. **Use Consistent Naming**: Follow organization naming conventions
6. **Indonesian Standards**: Ensure all entries comply with SAK requirements
7. **Tax Compliance**: Properly handle PPN, PPh, and withholding taxes

### Security Best Practices

1. **Strong Passwords**: Use complex passwords and change regularly
2. **Logout Properly**: Always logout when finished
3. **Role-based Access**: Only access functions appropriate to your role
4. **Report Suspicious Activity**: Report any unusual system behavior
5. **Data Backup**: Understand backup procedures and recovery processes

### Workflow Best Practices

1. **Follow Approval Chains**: Respect the approval hierarchy
2. **Document Decisions**: Keep records of important decisions
3. **Regular Reconciliation**: Reconcile accounts regularly
4. **Timely Processing**: Process transactions promptly
5. **Communication**: Communicate with team members about system issues

### Reporting Best Practices

1. **Regular Reports**: Generate reports on schedule
2. **Verify Data**: Always verify report data before distribution
3. **Export Formats**: Use appropriate export formats for recipients
4. **Archive Reports**: Keep historical reports for reference
5. **Trend Analysis**: Use reports to identify trends and patterns

---

## 6. Troubleshooting Guide

### Common Issues and Solutions

#### Issue: "Access Denied" or "403 Forbidden"

**Symptoms**: Cannot access certain pages or functions
**Solution**:

-   Verify you have the correct role assigned
-   Check if your role has the required permissions
-   Contact system administrator if permissions seem incorrect
-   **Note**: Recent system updates have enhanced role permissions - all roles now have appropriate access to their required functionality

#### Issue: "Journal Entry Not Balanced"

**Symptoms**: Error message when trying to save journal entry
**Solution**:

-   Check that total debits equal total credits
-   Verify all amounts are entered correctly
-   Ensure no negative amounts where not allowed

#### Issue: "Cannot Post to Closed Period"

**Symptoms**: Error when trying to post transactions
**Solution**:

-   Check if the period is closed
-   Contact approver to reopen period if needed
-   Use correct transaction date

#### Issue: "Asset Depreciation Not Calculating"

**Symptoms**: Depreciation amounts are zero or incorrect
**Solution**:

-   Verify asset is active and not disposed
-   Check asset acquisition date and useful life
-   Ensure depreciation method is set correctly

#### Issue: "Customer/Vendor Not Found"

**Symptoms**: Cannot find customer or vendor in dropdown
**Solution**:

-   Check if customer/vendor is active
-   Verify spelling of name
-   Contact admin to add new customer/vendor

#### Issue: "Report Data Missing"

**Symptoms**: Reports show incomplete or missing data
**Solution**:

-   Check date range selection
-   Verify user has permission to view data
-   Ensure data has been posted to GL

### Getting Help

1. **System Administrator**: Contact for user access and permissions
2. **IT Support**: Contact for technical issues and system problems
3. **Training Team**: Contact for additional training and questions
4. **Documentation**: Refer to this training guide and system help
5. **User Manual**: Detailed procedures for each module

### Indonesian Accounting Terminology

-   **SAK**: Standar Akuntansi Keuangan (Indonesian Accounting Standards)
-   **PPN**: Pajak Pertambahan Nilai (Value Added Tax)
-   **PPh**: Pajak Penghasilan (Income Tax)
-   **CoA**: Chart of Accounts (Daftar Akun)
-   **GL**: General Ledger (Buku Besar)
-   **AR**: Accounts Receivable (Piutang Usaha)
-   **AP**: Accounts Payable (Hutang Usaha)
-   **Yayasan**: Indonesian foundation/non-profit organization
-   **PT**: Perseroan Terbatas (Limited Company)
-   **CV**: Commanditaire Vennootschap (Limited Partnership)

---

## 7. Assessment Questions

### Knowledge Check Questions

#### General Ledger

1. What is the fundamental rule of double-entry bookkeeping?
2. Name three types of accounts in the Chart of Accounts.
3. What happens if a journal entry doesn't balance?
4. How do you assign dimensions to journal entries?
5. What is the difference between DRAFT and POSTED journal status?
6. Why can't Accountants post their own journal entries?

#### Accounts Receivable

1. What is the difference between a Sales Order and Sales Invoice?
2. How do you handle partial payments from customers?
3. What information is included in an AR Aging report?
4. When should you use PPN Output tax code?

#### Accounts Payable

1. What is the difference between a Purchase Order and Purchase Invoice?
2. How do you allocate payments to multiple vendor invoices?
3. What information is included in an AP Aging report?
4. When should you use PPN Input tax code?

#### Fixed Assets

1. What is depreciation and why is it important?
2. Name two depreciation methods available in the system.
3. How do you calculate gain or loss on asset disposal?
4. What information is tracked for each asset?

#### Reporting

1. What is a Trial Balance and what does it show?
2. How do you export reports to different formats?
3. What is the purpose of multi-dimensional reporting?
4. How often should you generate financial reports?
5. Why don't DRAFT transactions appear in financial reports?
6. How can you verify that a journal entry was posted correctly?

### Practical Exercises

#### Exercise 1: Journal Entry Creation

Create a journal entry for the following transaction:

-   Purchased office equipment for Rp 5,000,000
-   Paid Rp 2,000,000 cash and financed Rp 3,000,000
-   Equipment assigned to "Administration" department

#### Exercise 2: Invoice Processing

Process a customer invoice for:

-   Consulting services: Rp 2,000,000
-   PPN Output: 11%
-   Customer: PT Maju Jaya
-   Due in 30 days

#### Exercise 3: Asset Management

Register a new asset:

-   Laptop computer
-   Cost: Rp 1,500,000
-   Useful life: 3 years
-   Straight-line depreciation
-   Assigned to "IT Department"

#### Exercise 4: Payment Allocation

Allocate a customer payment of Rp 1,500,000 to:

-   Invoice #INV-001: Rp 800,000
-   Invoice #INV-002: Rp 700,000
-   Record any remaining balance

#### Exercise 5: Journal Approval Workflow

Demonstrate the complete posting workflow:

1. **As Accountant**: Create a journal entry for equipment purchase (Rp 5,000,000)
2. **As Accountant**: Save as DRAFT and verify you cannot post
3. **As Approver**: Review and approve the journal entry
4. **As Approver**: Post the journal entry
5. **As Approver**: Verify the journal appears in Trial Balance and GL Detail reports

### Final Assessment

#### Scenario-Based Questions

**Scenario A**: You're processing month-end closing for December 2025. What steps would you take to ensure all transactions are properly recorded and the period can be closed?

**Scenario B**: A customer disputes an invoice amount. How would you investigate and resolve this issue using the ERP system?

**Scenario C**: Your organization is disposing of several old assets. How would you process these disposals and ensure proper accounting treatment?

**Scenario D**: You need to prepare a financial report for the board of directors. What reports would you generate and how would you present the information?

**Scenario E**: An Accountant has created several journal entries but they're not appearing in the Trial Balance report. What could be the issue and how would you resolve it?

---

## Conclusion

This comprehensive training guide provides the foundation for effective use of the Prasasta ERP system. Regular practice, following best practices, and continuous learning will ensure successful system adoption and optimal business outcomes.

Remember:

-   **Accuracy**: Always verify data before posting
-   **Security**: Follow security protocols and use appropriate permissions
-   **Separation of Duties**: Respect the creation vs. approval workflow
-   **Posting Workflow**: Understand the difference between DRAFT and POSTED status
-   **Communication**: Work with your team and seek help when needed
-   **Continuous Learning**: Stay updated with system enhancements and new features

For additional support, refer to the system help documentation or contact your system administrator.

---

**Training Completion Certificate**

Upon completion of this training program, participants should be able to:

-   Navigate the ERP system confidently
-   Perform their role-specific functions accurately
-   Follow proper workflows and approval processes
-   Understand the posting workflow (DRAFT → POSTED)
-   Demonstrate separation of duties between creation and approval
-   Generate and interpret reports
-   Troubleshoot common issues
-   Apply best practices for data entry and security

**Next Steps**:

-   Practice with the interactive scenarios
-   Complete the assessment questions
-   Schedule follow-up training sessions as needed
-   Join the user community for ongoing support and tips

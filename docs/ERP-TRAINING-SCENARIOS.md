# ERP System Training Scenarios

## Interactive Story-Based Learning for Indonesian Business Operations

**Purpose**: Comprehensive training materials with realistic business scenarios for each user role in the ERP system, demonstrating creation and approval workflows with reporting integration.

**Target Audience**: Employees who will use the ERP system in their daily operations
**Context**: Indonesian business environment with SAK compliance, Rupiah currency, and Indonesian tax regulations

---

## Table of Contents

1. [System Overview](#system-overview)
2. [Role-Based Scenarios](#role-based-scenarios)
   - [Super Admin Scenarios](#super-admin-scenarios)
   - [Accountant Scenarios](#accountant-scenarios)
   - [Approver Scenarios](#approver-scenarios)
   - [Cashier Scenarios](#cashier-scenarios)
   - [Auditor Scenarios](#auditor-scenarios)
3. [Cross-Role Workflow Scenarios](#cross-role-workflow-scenarios)
4. [Reporting Integration Examples](#reporting-integration-examples)
5. [Assessment Questions](#assessment-questions)

---

## System Overview

### ERP System Modules

- **General Ledger (GL)**: Core accounting with double-entry bookkeeping
- **Accounts Receivable (AR)**: Customer invoicing and payment collection
- **Accounts Payable (AP)**: Vendor invoicing and payment processing
- **Fixed Assets**: Asset management, depreciation, and disposal
- **Reporting**: Financial reports, aging analysis, and asset reports
- **Master Data**: Customers, vendors, accounts, projects, funds, departments

### Key Business Processes

1. **Transaction Creation** → **Approval** → **Posting** → **Reporting**
2. **Asset Acquisition** → **Depreciation** → **Disposal/Movement**
3. **Invoice Processing** → **Payment Allocation** → **Aging Analysis**

---

## Role-Based Scenarios

### Super Admin Scenarios

#### Scenario 1: System Setup and User Management

**Context**: PT Maju Bersama is implementing the ERP system for the first time.

**Story**: As the Super Admin, you need to set up the system for PT Maju Bersama, a growing Indonesian company with 50 employees. The company has multiple departments (Finance, Operations, Sales) and various projects funded by different sources.

**Actions**:

1. **Create User Accounts**

   - Create accounts for Finance Manager (Budi Santoso), Operations Manager (Siti Nurhaliza), and Sales Manager (Ahmad Wijaya)
   - Assign appropriate roles: accountant, approver, cashier respectively
   - Set up department structure: Finance, Operations, Sales, HR

2. **Configure Master Data**

   - Set up customer accounts for major clients: PT Mandiri Sejahtera, CV Berkah Jaya, Yayasan Pendidikan Indonesia
   - Create vendor accounts for suppliers: PT Komputer Maju, Toko Alat Kantor Jakarta
   - Configure project codes: PROJ-2024-001 (Office Renovation), PROJ-2024-002 (IT Infrastructure)

3. **Verify System Access**
   - Test login for each created user
   - Verify role-based permissions are working correctly
   - Check that users can only access their assigned modules

**Expected Results**:

- All users can log in successfully
- Role-based access control is functioning
- Master data is properly configured for business operations

**Reporting Verification**:

- Access Admin → Users to verify all accounts are created
- Check Reports → User Activity (if available) to confirm system access

---

### Accountant Scenarios

#### Scenario 1: Monthly Journal Entry Processing

**Context**: End of month closing for PT Maju Bersama, January 2024.

**Story**: As Budi Santoso (Accountant), you need to process various journal entries for January 2024 closing. The company has several transactions that need to be recorded according to SAK (Standar Akuntansi Keuangan) standards.

**Actions**:

1. **Create Donation Journal Entry**

   - **Transaction**: PT Mandiri Sejahtera donated Rp 50,000,000 for office renovation project
   - **Accounts**: Debit Cash (1.1.1.001) Rp 50,000,000, Credit Donation Revenue (4.2.1.001) Rp 50,000,000
   - **Dimensions**: Project: PROJ-2024-001, Fund: FUND-2024-001, Department: Finance
   - **Reference**: JNL-202401-000001

2. **Create Office Supply Purchase Journal**

   - **Transaction**: Purchased office supplies from Toko Alat Kantor Jakarta for Rp 2,500,000
   - **Accounts**: Debit Office Supplies (1.1.3.001) Rp 2,500,000, Credit Accounts Payable (2.1.1.001) Rp 2,500,000
   - **Dimensions**: Project: PROJ-2024-001, Department: Operations
   - **Reference**: JNL-202401-000002

3. **Create Customer Invoice**
   - **Customer**: CV Berkah Jaya
   - **Service**: IT Consulting Services
   - **Amount**: Rp 15,000,000 (including 11% PPN)
   - **Breakdown**: Service Revenue Rp 13,513,514, PPN Keluaran Rp 1,486,486
   - **Due Date**: 30 days from invoice date

**Expected Results**:

- All journal entries are created in **DRAFT** status
- Balances are properly calculated (debits = credits)
- Dimensions are correctly assigned
- Invoices are generated with proper numbering
- **IMPORTANT**: Entries remain in draft status and cannot be posted by accountant role
- **Sales Invoice**: Created but not posted (DRAFT status)

**Reporting Verification**:

- **Trial Balance Report**: Shows all accounts with **ZERO totals** (draft entries not posted)
- **GL Detail Report**: Displays all journal entries with **"Draft" status**
- **AR Aging Report**: Shows CV Berkah Jaya invoice in current period
- **Sales Invoice Report**: Shows invoice created but not posted
- **Note**: All amounts show as zero in reports because entries are not yet posted

#### Scenario 2: Asset Acquisition and Registration

**Context**: PT Maju Bersama purchased new computer equipment for the office.

**Story**: The company purchased 10 desktop computers from PT Komputer Maju for Rp 67,567,570 (including 11% PPN). As the accountant, you need to register these assets in the system.

**Actions**:

1. **Create Purchase Invoice**

   - **Vendor**: PT Komputer Maju
   - **Items**: 10 units Desktop Computer @ Rp 6,756,757 each
   - **Total**: Rp 67,567,570 (including 11% PPN)
   - **Account**: Computer Equipment (1.2.1.001)

2. **Register Assets**

   - Create asset category: "Computer Equipment"
   - Register 10 individual computers with serial numbers
   - Assign to Finance Department
   - Set depreciation method: Straight Line, 4 years useful life

3. **Create Asset Journal Entry**
   - **Accounts**: Debit Computer Equipment (1.2.1.001) Rp 60,900,900, Debit PPN Masukan (1.1.2.001) Rp 6,666,670, Credit Accounts Payable (2.1.1.001) Rp 67,567,570

**Expected Results**:

- Assets are properly registered in the system
- Purchase invoice is created and linked to assets
- Journal entry balances correctly
- **IMPORTANT**: Asset journal entry remains in **DRAFT** status

**Reporting Verification**:

- **Asset Register Report**: Shows all 10 computers with acquisition details
- **Asset Summary Report**: Displays total computer equipment value
- **AP Aging Report**: Shows PT Komputer Maju invoice
- **Purchase Invoice Report**: Shows invoice created but not posted
- **Note**: Asset values may not reflect in reports until journal entry is posted by approver

#### Scenario 3: Purchase Invoice Processing

**Context**: PT Maju Bersama received invoices from various vendors for monthly operations.

**Story**: As Budi Santoso (Accountant), you need to process several purchase invoices received from vendors. These invoices must be recorded accurately with proper PPN handling according to Indonesian tax regulations.

**Actions**:

1. **Create Office Supplies Purchase Invoice**

   - **Vendor**: Toko Alat Kantor Jakarta
   - **Items**: Office supplies for Rp 3,000,000 (including 11% PPN)
   - **Breakdown**: Supplies Rp 2,702,703, PPN Masukan Rp 297,297
   - **Account**: Office Supplies Expense (5.1.1.001)
   - **Due Date**: 30 days from invoice date
   - **Reference**: PINV-202401-000001

2. **Create Professional Services Invoice**

   - **Vendor**: PT Konsultan Indonesia
   - **Service**: Legal consulting services
   - **Amount**: Rp 8,000,000 (including 11% PPN)
   - **Breakdown**: Professional Services Rp 7,207,207, PPN Masukan Rp 792,793
   - **Account**: Professional Services Expense (5.1.2.001)
   - **Due Date**: 15 days from invoice date
   - **Reference**: PINV-202401-000002

3. **Create Equipment Maintenance Invoice**

   - **Vendor**: PT Teknologi Maju
   - **Service**: Computer maintenance contract
   - **Amount**: Rp 1,500,000 (including 11% PPN)
   - **Breakdown**: Maintenance Expense Rp 1,351,351, PPN Masukan Rp 148,649
   - **Account**: Equipment Maintenance Expense (5.1.3.001)
   - **Due Date**: 20 days from invoice date
   - **Reference**: PINV-202401-000003

**Expected Results**:

- All purchase invoices are created in **DRAFT** status
- PPN calculations are accurate (11% rate)
- Vendor information is properly recorded
- Due dates are set according to payment terms
- **IMPORTANT**: Invoices remain in draft status and cannot be posted by accountant role

**Reporting Verification**:

- **AP Aging Report**: Shows all vendor invoices in current period
- **Purchase Invoice Report**: Shows invoices created but not posted
- **Vendor Balance Report**: Shows outstanding amounts per vendor
- **Note**: All amounts show as zero in reports because invoices are not yet posted

---

### Approver Scenarios

#### Scenario 1: Journal Entry Approval and Posting Workflow

**Context**: Approving and posting journal entries created by accountants.

**Story**: As Siti Nurhaliza (Approver), you need to review and approve the journal entries created by Budi Santoso for January 2024 closing. You must ensure all entries are accurate and comply with SAK standards before posting. This demonstrates the critical **separation of duties** between creation and approval.

**Actions**:

1. **Review Draft Journal Entries**

   - Access Journals → Manual Journals
   - **Verify all entries are in "DRAFT" status** (created by accountant but not posted)
   - Review JNL-202401-000001 (Donation entry)
   - Verify account codes, amounts, and dimensions
   - Check supporting documentation
   - **Confirm**: Accountant cannot post these entries (permission denied)

2. **Approve and Post Entries** (Approver Role Only)

   - **Post JNL-202401-000001** (Donation entry) - Status changes from Draft → Posted
   - **Post JNL-202401-000002** (Office supplies entry) - Status changes from Draft → Posted
   - **Post JNL-202401-000003** (Asset acquisition entry) - Status changes from Draft → Posted
   - **Verify**: Only approver role can perform posting action

3. **Review Posted Entries**
   - Verify entries are now in **"Posted"** status
   - Check that account balances are updated in GL
   - Confirm entries are **no longer editable**

**Expected Results**:

- All journal entries are successfully **posted** (status changed from Draft to Posted)
- Account balances are **updated** in the General Ledger
- Entries are **locked** and no longer editable
- **Separation of duties** is maintained (accountant creates, approver posts)

**Reporting Verification**:

- **Trial Balance Report**: Shows **updated account balances** (no longer zero)
- **GL Detail Report**: Displays posted entries with **"Posted" status**
- **Period Close Report**: Shows January 2024 is ready for closing
- **Before/After Comparison**: Demonstrate the difference between draft and posted entries

#### Scenario 2: Asset Depreciation Run

**Context**: Monthly depreciation processing for fixed assets.

**Story**: It's February 1, 2024, and you need to run monthly depreciation for all fixed assets. The computers purchased in January need their first depreciation entry.

**Actions**:

1. **Create Depreciation Run**

   - Access Assets → Depreciation Runs
   - Create new run for February 2024
   - Select all active assets for depreciation

2. **Calculate Depreciation**

   - System calculates monthly depreciation for each asset
   - Review depreciation amounts (Computer: Rp 1,268,769 per month)
   - Verify calculations are correct

3. **Post Depreciation Entries**
   - Post depreciation journal entries
   - Verify accounts: Debit Depreciation Expense, Credit Accumulated Depreciation

**Expected Results**:

- Depreciation entries are created and posted
- Asset book values are updated
- Depreciation expense is recorded

**Reporting Verification**:

- **Depreciation Schedule Report**: Shows monthly depreciation for all assets
- **Asset Summary Report**: Displays updated book values
- **GL Detail Report**: Shows depreciation journal entries

#### Scenario 3: Asset Disposal Approval

**Context**: Disposing of obsolete computer equipment.

**Story**: One of the computers (Asset ID: COMP-001) is no longer functional and needs to be disposed of. The disposal will result in a loss since the asset has remaining book value.

**Actions**:

1. **Create Disposal Record**

   - Access Assets → Disposals
   - Create disposal for COMP-001
   - Set disposal date: February 15, 2024
   - Enter disposal reason: "Equipment failure"

2. **Calculate Disposal Gain/Loss**

   - System calculates remaining book value
   - Enter disposal proceeds: Rp 0 (no salvage value)
   - Calculate disposal loss: Rp 3,806,307

3. **Post Disposal Entry**
   - Post disposal journal entry
   - Verify accounts: Debit Accumulated Depreciation, Debit Loss on Disposal, Credit Computer Equipment

**Expected Results**:

- Asset is marked as disposed
- Disposal loss is properly recorded
- Asset register is updated

**Reporting Verification**:

- **Disposal Summary Report**: Shows disposed asset details
- **Asset Register Report**: Shows COMP-001 as disposed
- **GL Detail Report**: Shows disposal journal entry

---

### Cashier Scenarios

#### Scenario 1: Daily Cash Receipt Processing

**Context**: Processing daily cash receipts from customers.

**Story**: As Ahmad Wijaya (Cashier), you receive cash payments from customers throughout the day. You need to record these receipts and allocate them to outstanding invoices.

**Actions**:

1. **Record Cash Receipt**

   - **Customer**: CV Berkah Jaya
   - **Amount**: Rp 15,000,000
   - **Payment Method**: Cash
   - **Reference**: Receipt #001/2024

2. **Allocate to Invoice**

   - Allocate payment to Invoice #INV-2024-001 (Rp 15,000,000)
   - Verify allocation matches invoice amount
   - Confirm payment allocation

3. **Post Receipt**
   - Post the receipt entry
   - Verify accounts: Debit Cash, Credit Accounts Receivable

**Expected Results**:

- Receipt is recorded in **DRAFT** status
- Customer account balance is updated
- Invoice allocation is properly recorded
- **IMPORTANT**: Receipt remains in draft status and cannot be posted by cashier role

**Reporting Verification**:

- **AR Aging Report**: Shows CV Berkah Jaya balance (unchanged until posted)
- **Sales Receipt Report**: Shows receipt created but not posted
- **Cash Ledger Report**: Shows receipt entry in draft status
- **Note**: Receipt must be approved by Approver before posting

#### Scenario 2: Petty Cash Expense Recording

**Context**: Recording small office expenses paid from petty cash.

**Story**: You need to record various petty cash expenses for office operations, including coffee supplies, parking fees, and small office supplies.

**Actions**:

1. **Create Cash Expense**

   - **Description**: "Office coffee supplies"
   - **Amount**: Rp 150,000
   - **Account**: Office Supplies (1.1.3.001)
   - **Payment Method**: Cash

2. **Create Multiple Expenses**

   - Parking fees: Rp 25,000 (Transportation Expense)
   - Small office supplies: Rp 75,000 (Office Supplies)
   - Total petty cash used: Rp 250,000

3. **Post Expenses**
   - Post all cash expense entries
   - Verify accounts: Debit respective expense accounts, Credit Cash

**Expected Results**:

- All expenses are recorded and posted
- Cash account is reduced by total expenses
- Expense accounts are properly debited

**Reporting Verification**:

- **Cash Ledger Report**: Shows all petty cash transactions
- **GL Detail Report**: Shows expense journal entries
- **Trial Balance Report**: Shows updated expense account balances

#### Scenario 3: Purchase Payment Processing

**Context**: Processing vendor payments for approved purchase invoices.

**Story**: As Ahmad Wijaya (Cashier), you need to process payments to vendors for the purchase invoices that have been approved and posted by the approver. This demonstrates the **payment authorization** workflow.

**Actions**:

1. **Create Purchase Payment**

   - **Vendor**: Toko Alat Kantor Jakarta
   - **Amount**: Rp 3,000,000
   - **Payment Method**: Bank Transfer
   - **Reference**: Payment #001/2024
   - **Date**: February 15, 2024

2. **Allocate to Invoice**

   - Allocate payment to PINV-202401-000001 (Office supplies invoice)
   - Verify allocation matches invoice amount
   - Confirm payment allocation
   - **Status**: Payment created in **DRAFT** status

3. **Create Additional Payments**

   - **Vendor**: PT Konsultan Indonesia
   - **Amount**: Rp 8,000,000
   - **Payment Method**: Bank Transfer
   - **Allocate to**: PINV-202401-000002 (Professional services invoice)
   - **Status**: Payment created in **DRAFT** status

   - **Vendor**: PT Teknologi Maju
   - **Amount**: Rp 1,500,000
   - **Payment Method**: Bank Transfer
   - **Allocate to**: PINV-202401-000003 (Equipment maintenance invoice)
   - **Status**: Payment created in **DRAFT** status

**Expected Results**:

- All purchase payments are recorded in **DRAFT** status
- Vendor invoice allocations are properly recorded
- Payment references are generated
- **IMPORTANT**: Payments remain in draft status and cannot be posted by cashier role

**Reporting Verification**:

- **AP Aging Report**: Shows vendor balances (unchanged until payments posted)
- **Purchase Payment Report**: Shows payments created but not posted
- **Vendor Balance Report**: Shows outstanding amounts per vendor
- **Note**: Payments must be approved by Approver before posting

---

### Auditor Scenarios

#### Scenario 1: Monthly Financial Review

**Context**: Conducting monthly financial review and audit procedures.

**Story**: As Maria Magdalena (Auditor), you need to review the financial records for January 2024 to ensure accuracy and compliance with SAK standards. You'll focus on key accounts and verify supporting documentation.

**Actions**:

1. **Review Trial Balance**

   - Access Reports → Trial Balance
   - Verify total debits equal total credits
   - Check for any unusual account balances
   - Review period: January 2024

2. **Analyze GL Detail**

   - Access Reports → GL Detail
   - Review all journal entries for January
   - Verify account codes and amounts
   - Check for proper documentation references

3. **Review AR Aging**

   - Access Reports → AR Aging
   - Analyze customer payment patterns
   - Identify overdue accounts
   - Verify aging calculations

4. **Review Asset Register**
   - Access Reports → Asset Register
   - Verify asset acquisitions and disposals
   - Check depreciation calculations
   - Confirm asset valuations

**Expected Results**:

- All reports are accessible and accurate
- Financial records are properly maintained
- No discrepancies found in review

**Reporting Verification**:

- **Trial Balance Report**: Confirms balanced books
- **GL Detail Report**: Shows complete transaction history
- **AR Aging Report**: Identifies collection issues
- **Asset Register Report**: Confirms asset management

#### Scenario 2: Compliance Audit Trail

**Context**: Verifying compliance with Indonesian tax regulations and SAK standards.

**Story**: You need to verify that all transactions comply with Indonesian tax regulations, particularly PPN (VAT) handling and proper account classifications.

**Actions**:

1. **Review PPN Transactions**

   - Check all PPN Masukan (Input VAT) entries
   - Verify PPN Keluaran (Output VAT) calculations
   - Confirm 11% tax rate application
   - Review tax code assignments

2. **Verify Account Classifications**

   - Check asset account classifications
   - Verify revenue account categories
   - Confirm expense account groupings
   - Review balance sheet classifications

3. **Review Supporting Documentation**
   - Verify invoice references
   - Check approval workflows
   - Confirm posting authorizations
   - Review audit trail completeness

**Expected Results**:

- All tax calculations are correct
- Account classifications comply with SAK
- Supporting documentation is complete
- Audit trail is properly maintained

**Reporting Verification**:

- **Withholding Recap Report**: Shows tax compliance
- **GL Detail Report**: Confirms proper tax entries
- **Trial Balance Report**: Verifies account classifications

---

## Cross-Role Workflow Scenarios

### Scenario 1: Complete Sales Invoice-to-Payment Workflow

**Context**: End-to-end business process demonstrating the complete sales cycle with proper approval workflows.

**Story**: PT Maju Bersama provides IT consulting services to CV Berkah Jaya. This scenario demonstrates the complete workflow from invoice creation by the accountant, approval by the approver, payment processing by the cashier, and final approval by the approver.

**Workflow**:

1. **Accountant (Budi)**: Creates sales invoice for IT consulting services
2. **Approver (Siti)**: Reviews and posts the sales invoice
3. **Cashier (Ahmad)**: Records customer payment receipt
4. **Approver (Siti)**: Reviews and posts the sales receipt
5. **Auditor (Maria)**: Reviews the complete transaction cycle

**Actions**:

1. **Sales Invoice Creation** (Accountant Role)

   - **Transaction**: IT consulting services for CV Berkah Jaya
   - **Amount**: Rp 25,000,000 (including 11% PPN)
   - **Breakdown**: Service Revenue Rp 22,522,523, PPN Keluaran Rp 2,477,477
   - **Due Date**: 30 days from invoice date
   - **Reference**: INV-2024-001
   - **Status**: Invoice created in **DRAFT** status
   - **Verification**: Accountant cannot post the invoice (permission denied)

2. **Sales Invoice Approval and Posting** (Approver Role)

   - **Review**: Access Sales → Sales Invoices
   - **Verify**: Invoice is in "DRAFT" status, created by accountant
   - **Validate**: Customer information, amounts, PPN calculations
   - **Post**: Change status from Draft → Posted
   - **Confirm**: AR account is debited, Revenue account is credited
   - **Verify**: Invoice is now locked and uneditable

3. **Sales Receipt Creation** (Cashier Role)

   - **Transaction**: Customer payment received from CV Berkah Jaya
   - **Amount**: Rp 25,000,000
   - **Payment Method**: Bank Transfer
   - **Allocation**: To INV-2024-001
   - **Reference**: Receipt #001/2024
   - **Status**: Receipt created in **DRAFT** status
   - **Verification**: Cashier cannot post the receipt (permission denied)

4. **Sales Receipt Approval and Posting** (Approver Role)

   - **Review**: Access Sales → Sales Receipts
   - **Verify**: Receipt is in "DRAFT" status, created by cashier
   - **Validate**: Payment amount, invoice allocation, supporting documentation
   - **Post**: Change status from Draft → Posted
   - **Confirm**: Cash account is debited, AR account is credited
   - **Verify**: Receipt is now locked and uneditable

5. **Transaction Review** (Auditor Role)
   - **Review**: Complete transaction cycle in GL Detail Report
   - **Verify**: Both invoice and receipt show "Posted" status
   - **Confirm**: AR Aging shows zero balance for CV Berkah Jaya
   - **Validate**: Trial Balance reflects updated account balances
   - **Check**: Separation of duties was properly maintained throughout

**Expected Results**:

- **Complete Sales Cycle**: Invoice creation → Invoice approval → Payment processing → Payment approval
- **Separation of Duties**: Accountant creates invoices, Cashier processes payments, Approver posts both
- **Status Progression**: Draft → Posted (with proper authorization for each step)
- **Data Integrity**: Account balances updated only after proper approval
- **Audit Trail**: Complete transaction history with user attribution

**Reporting Verification**:

- **Before Invoice Posting**: AR Aging shows zero, Trial Balance shows zero revenue
- **After Invoice Posting**: AR Aging shows CV Berkah Jaya balance, Trial Balance shows revenue
- **Before Receipt Posting**: AR Aging shows outstanding balance, Cash shows zero
- **After Receipt Posting**: AR Aging shows zero balance, Cash shows payment amount
- **GL Detail Report**: Shows complete transaction history with user attribution
- **Trial Balance Report**: Confirms balanced books with updated account values

### Scenario 2: Complete Purchase Invoice-to-Payment Workflow

**Context**: End-to-end business process demonstrating the complete purchase cycle with proper approval workflows.

**Story**: PT Maju Bersama purchases office supplies from Toko Alat Kantor Jakarta. This scenario demonstrates the complete workflow from purchase invoice creation by the accountant, approval by the approver, payment processing by the cashier, and final approval by the approver.

**Workflow**:

1. **Accountant (Budi)**: Creates purchase invoice for office supplies
2. **Approver (Siti)**: Reviews and posts the purchase invoice
3. **Cashier (Ahmad)**: Records vendor payment
4. **Approver (Siti)**: Reviews and posts the purchase payment
5. **Auditor (Maria)**: Reviews the complete transaction cycle

**Actions**:

1. **Purchase Invoice Creation** (Accountant Role)

   - **Transaction**: Office supplies from Toko Alat Kantor Jakarta
   - **Amount**: Rp 3,000,000 (including 11% PPN)
   - **Breakdown**: Office Supplies Rp 2,702,703, PPN Masukan Rp 297,297
   - **Due Date**: 30 days from invoice date
   - **Reference**: PINV-202401-000001
   - **Status**: Invoice created in **DRAFT** status
   - **Verification**: Accountant cannot post the invoice (permission denied)

2. **Purchase Invoice Approval and Posting** (Approver Role)

   - **Review**: Access Purchases → Purchase Invoices
   - **Verify**: Invoice is in "DRAFT" status, created by accountant
   - **Validate**: Vendor information, amounts, PPN calculations
   - **Post**: Change status from Draft → Posted
   - **Confirm**: AP account is credited, Expense account is debited
   - **Verify**: Invoice is now locked and uneditable

3. **Purchase Payment Creation** (Cashier Role)

   - **Transaction**: Payment to Toko Alat Kantor Jakarta
   - **Amount**: Rp 3,000,000
   - **Payment Method**: Bank Transfer
   - **Allocation**: To PINV-202401-000001
   - **Reference**: Payment #001/2024
   - **Status**: Payment created in **DRAFT** status
   - **Verification**: Cashier cannot post the payment (permission denied)

4. **Purchase Payment Approval and Posting** (Approver Role)

   - **Review**: Access Purchases → Purchase Payments
   - **Verify**: Payment is in "DRAFT" status, created by cashier
   - **Validate**: Payment amount, invoice allocation, supporting documentation
   - **Post**: Change status from Draft → Posted
   - **Confirm**: AP account is debited, Cash/Bank account is credited
   - **Verify**: Payment is now locked and uneditable

5. **Transaction Review** (Auditor Role)
   - **Review**: Complete transaction cycle in GL Detail Report
   - **Verify**: Both invoice and payment show "Posted" status
   - **Confirm**: AP Aging shows zero balance for Toko Alat Kantor Jakarta
   - **Validate**: Trial Balance reflects updated account balances
   - **Check**: Separation of duties was properly maintained throughout

**Expected Results**:

- **Complete Purchase Cycle**: Invoice creation → Invoice approval → Payment processing → Payment approval
- **Separation of Duties**: Accountant creates invoices, Cashier processes payments, Approver posts both
- **Status Progression**: Draft → Posted (with proper authorization for each step)
- **Data Integrity**: Account balances updated only after proper approval
- **Audit Trail**: Complete transaction history with user attribution

**Reporting Verification**:

- **Before Invoice Posting**: AP Aging shows zero, Trial Balance shows zero expenses
- **After Invoice Posting**: AP Aging shows vendor balance, Trial Balance shows expenses
- **Before Payment Posting**: AP Aging shows outstanding balance, Cash shows zero
- **After Payment Posting**: AP Aging shows zero balance, Cash shows payment amount
- **GL Detail Report**: Shows complete transaction history with user attribution
- **Trial Balance Report**: Confirms balanced books with updated account values

### Scenario 3: Complete Journal Entry Creation-to-Posting Workflow

**Context**: End-to-end business process demonstrating the critical separation of duties between creation and approval.

**Story**: PT Maju Bersama needs to record a significant office equipment purchase. This scenario demonstrates the complete workflow from journal entry creation by the accountant to approval and posting by the approver, showing how the system enforces proper internal controls.

**Workflow**:

1. **Accountant (Budi)**: Creates journal entry for office equipment purchase
2. **Approver (Siti)**: Reviews and posts the journal entry
3. **Auditor (Maria)**: Reviews the complete transaction cycle and reports

**Actions**:

1. **Journal Entry Creation** (Accountant Role)

   - **Transaction**: Purchase office equipment from PT Komputer Maju for Rp 25,000,000
   - **Accounts**: Debit Office Equipment (1.2.1.002) Rp 22,522,523, Debit PPN Masukan (1.1.2.001) Rp 2,477,477, Credit Accounts Payable (2.1.1.001) Rp 25,000,000
   - **Dimensions**: Project: PROJ-2024-001, Department: Operations
   - **Reference**: JNL-202401-000004
   - **Status**: Entry created in **DRAFT** status
   - **Verification**: Accountant cannot post the entry (permission denied)

2. **Journal Entry Approval and Posting** (Approver Role)

   - **Review**: Access Journals → Manual Journals
   - **Verify**: Entry is in "DRAFT" status, created by accountant
   - **Validate**: Account codes, amounts, dimensions, and supporting documentation
   - **Post**: Change status from Draft → Posted
   - **Confirm**: Entry is now locked and uneditable
   - **Verify**: Account balances are updated in General Ledger

3. **Transaction Review** (Auditor Role)
   - **Review**: Complete transaction cycle in GL Detail Report
   - **Verify**: Entry shows "Posted" status with proper audit trail
   - **Confirm**: Trial Balance reflects updated account balances
   - **Validate**: Separation of duties was properly maintained

**Expected Results**:

- **Separation of Duties**: Accountant creates, Approver posts, Auditor reviews
- **Status Progression**: Draft → Posted (with proper authorization)
- **Data Integrity**: Account balances updated only after proper approval
- **Audit Trail**: Complete transaction history with user attribution

**Reporting Verification**:

- **Before Posting**: Trial Balance shows zero balances, GL Detail shows "Draft" status
- **After Posting**: Trial Balance shows updated balances, GL Detail shows "Posted" status
- **GL Detail Report**: Shows complete transaction history with user attribution
- **Trial Balance Report**: Confirms balanced books with updated account values

#### Scenario 2: Sales Invoice Approval and Posting

**Context**: Approving and posting sales invoices created by accountants.

**Story**: As Siti Nurhaliza (Approver), you need to review and approve the sales invoice created by Budi Santoso for CV Berkah Jaya's IT consulting services. This demonstrates the critical **separation of duties** for revenue recognition.

**Actions**:

1. **Review Draft Sales Invoice**

   - Access Sales → Sales Invoices
   - **Verify invoice is in "DRAFT" status** (created by accountant but not posted)
   - Review INV-2024-001 (CV Berkah Jaya invoice)
   - Verify customer information, amounts, and PPN calculations
   - Check supporting documentation
   - **Confirm**: Accountant cannot post this invoice (permission denied)

2. **Approve and Post Invoice** (Approver Role Only)

   - **Post INV-2024-001** (CV Berkah Jaya invoice) - Status changes from Draft → Posted
   - **Verify**: Only approver role can perform posting action
   - **Confirm**: AR account is debited, Revenue account is credited
   - **Check**: PPN Keluaran account is properly credited

3. **Review Posted Invoice**
   - Verify invoice is now in **"Posted"** status
   - Check that AR account balance is updated
   - Confirm invoice is **no longer editable**
   - Verify revenue recognition is complete

**Expected Results**:

- Sales invoice is successfully **posted** (status changed from Draft to Posted)
- AR account balance is **updated** with invoice amount
- Revenue is **recognized** in the General Ledger
- Invoice is **locked** and no longer editable
- **Separation of duties** is maintained (accountant creates, approver posts)

**Reporting Verification**:

- **AR Aging Report**: Shows CV Berkah Jaya with outstanding balance
- **GL Detail Report**: Displays posted invoice with **"Posted" status**
- **Trial Balance Report**: Shows updated AR and Revenue account balances
- **Sales Invoice Report**: Shows invoice as posted and active

#### Scenario 3: Purchase Invoice Approval and Posting

**Context**: Approving and posting purchase invoices created by accountants.

**Story**: You need to review and approve the purchase invoices created by Budi Santoso for various vendor services. This demonstrates proper **vendor management** and **expense recognition** controls.

**Actions**:

1. **Review Draft Purchase Invoices**

   - Access Purchases → Purchase Invoices
   - **Verify all invoices are in "DRAFT" status** (created by accountant but not posted)
   - Review PINV-202401-000001 (Office supplies)
   - Review PINV-202401-000002 (Professional services)
   - Review PINV-202401-000003 (Equipment maintenance)
   - Verify vendor information, amounts, and PPN calculations
   - **Confirm**: Accountant cannot post these invoices (permission denied)

2. **Approve and Post Invoices** (Approver Role Only)

   - **Post PINV-202401-000001** (Office supplies) - Status changes from Draft → Posted
   - **Post PINV-202401-000002** (Professional services) - Status changes from Draft → Posted
   - **Post PINV-202401-000003** (Equipment maintenance) - Status changes from Draft → Posted
   - **Verify**: Only approver role can perform posting action
   - **Confirm**: AP account is credited, Expense accounts are debited
   - **Check**: PPN Masukan account is properly debited

3. **Review Posted Invoices**
   - Verify all invoices are now in **"Posted"** status
   - Check that AP account balance is updated
   - Confirm expense accounts are properly debited
   - Verify invoices are **no longer editable**

**Expected Results**:

- All purchase invoices are successfully **posted** (status changed from Draft to Posted)
- AP account balance is **updated** with total invoice amounts
- Expense accounts are **recognized** in the General Ledger
- PPN Masukan is properly recorded for tax credit
- **Separation of duties** is maintained (accountant creates, approver posts)

**Reporting Verification**:

- **AP Aging Report**: Shows all vendors with outstanding balances
- **GL Detail Report**: Displays posted invoices with **"Posted" status**
- **Trial Balance Report**: Shows updated AP and Expense account balances
- **Purchase Invoice Report**: Shows invoices as posted and active
- **Vendor Balance Report**: Shows outstanding amounts per vendor

#### Scenario 4: Asset Depreciation Run

**Context**: Monthly depreciation processing for fixed assets.

**Story**: It's February 1, 2024, and you need to run monthly depreciation for all fixed assets. The computers purchased in January need their first depreciation entry.

**Actions**:

1. **Create Depreciation Run**

   - Access Assets → Depreciation Runs
   - Create new run for February 2024
   - Select all active assets for depreciation

2. **Calculate Depreciation**

   - System calculates monthly depreciation for each asset
   - Review depreciation amounts (Computer: Rp 1,268,769 per month)
   - Verify calculations are correct

3. **Post Depreciation Entries**
   - Post depreciation journal entries
   - Verify accounts: Debit Depreciation Expense, Credit Accumulated Depreciation

**Expected Results**:

- Depreciation entries are created and posted
- Asset book values are updated
- Depreciation expense is recorded

**Reporting Verification**:

- **Depreciation Schedule Report**: Shows monthly depreciation for all assets
- **Asset Summary Report**: Displays updated book values
- **GL Detail Report**: Shows depreciation journal entries

#### Scenario 5: Sales Receipt Approval and Posting

**Context**: Approving and posting sales receipts created by cashiers.

**Story**: You need to review and approve the sales receipt created by Ahmad Wijaya for CV Berkah Jaya's payment. This demonstrates proper **cash management** and **receipt processing** controls.

**Actions**:

1. **Review Draft Sales Receipt**

   - Access Sales → Sales Receipts
   - **Verify receipt is in "DRAFT" status** (created by cashier but not posted)
   - Review Receipt #001/2024 (CV Berkah Jaya payment)
   - Verify customer information, payment amount, and invoice allocation
   - Check supporting documentation (bank deposit slip)
   - **Confirm**: Cashier cannot post this receipt (permission denied)

2. **Approve and Post Receipt** (Approver Role Only)

   - **Post Receipt #001/2024** (CV Berkah Jaya payment) - Status changes from Draft → Posted
   - **Verify**: Only approver role can perform posting action
   - **Confirm**: Cash account is debited, AR account is credited
   - **Check**: Invoice allocation is properly recorded

3. **Review Posted Receipt**
   - Verify receipt is now in **"Posted"** status
   - Check that AR account balance is updated (reduced)
   - Confirm cash account balance is updated (increased)
   - Verify receipt is **no longer editable**

**Expected Results**:

- Sales receipt is successfully **posted** (status changed from Draft to Posted)
- AR account balance is **reduced** by payment amount
- Cash account balance is **increased** by payment amount
- Invoice is marked as **paid** in the system
- **Separation of duties** is maintained (cashier creates, approver posts)

**Reporting Verification**:

- **AR Aging Report**: Shows CV Berkah Jaya balance reduced to zero
- **GL Detail Report**: Displays posted receipt with **"Posted" status**
- **Trial Balance Report**: Shows updated Cash and AR account balances
- **Sales Receipt Report**: Shows receipt as posted and active
- **Cash Ledger Report**: Shows cash receipt entry

#### Scenario 6: Purchase Payment Approval and Posting

**Context**: Approving and posting purchase payments created by cashiers.

**Story**: You need to review and approve the purchase payments created by Ahmad Wijaya for various vendor invoices. This demonstrates proper **vendor payment** and **cash disbursement** controls.

**Actions**:

1. **Review Draft Purchase Payments**

   - Access Purchases → Purchase Payments
   - **Verify all payments are in "DRAFT" status** (created by cashier but not posted)
   - Review Payment #001/2024 (Toko Alat Kantor Jakarta)
   - Review Payment #002/2024 (PT Konsultan Indonesia)
   - Review Payment #003/2024 (PT Teknologi Maju)
   - Verify vendor information, payment amounts, and invoice allocations
   - **Confirm**: Cashier cannot post these payments (permission denied)

2. **Approve and Post Payments** (Approver Role Only)

   - **Post Payment #001/2024** (Office supplies) - Status changes from Draft → Posted
   - **Post Payment #002/2024** (Professional services) - Status changes from Draft → Posted
   - **Post Payment #003/2024** (Equipment maintenance) - Status changes from Draft → Posted
   - **Verify**: Only approver role can perform posting action
   - **Confirm**: AP account is debited, Cash/Bank account is credited
   - **Check**: Invoice allocations are properly recorded

3. **Review Posted Payments**
   - Verify all payments are now in **"Posted"** status
   - Check that AP account balance is updated (reduced)
   - Confirm cash/bank account balance is updated (reduced)
   - Verify payments are **no longer editable**

**Expected Results**:

- All purchase payments are successfully **posted** (status changed from Draft to Posted)
- AP account balance is **reduced** by total payment amounts
- Cash/Bank account balance is **reduced** by total payment amounts
- Vendor invoices are marked as **paid** in the system
- **Separation of duties** is maintained (cashier creates, approver posts)

**Reporting Verification**:

- **AP Aging Report**: Shows all vendor balances reduced to zero
- **GL Detail Report**: Displays posted payments with **"Posted" status**
- **Trial Balance Report**: Shows updated AP and Cash/Bank account balances
- **Purchase Payment Report**: Shows payments as posted and active
- **Vendor Balance Report**: Shows zero outstanding amounts per vendor

---

## Reporting Integration Examples

### Example 1: Monthly Financial Close Process

**Context**: End-of-month financial closing with comprehensive reporting.

**Process**:

1. **Accountant**: Creates all journal entries for the month
2. **Approver**: Posts all entries and runs depreciation
3. **Auditor**: Reviews all reports for accuracy

**Key Reports**:

- **Trial Balance**: Confirms balanced books
- **GL Detail**: Shows all transactions
- **AR Aging**: Identifies collection issues
- **AP Aging**: Shows vendor payment status
- **Asset Register**: Confirms asset values
- **Depreciation Schedule**: Shows monthly depreciation

### Example 2: Asset Management Reporting

**Context**: Comprehensive asset management with disposal and movement tracking.

**Process**:

1. **Accountant**: Registers new assets
2. **Approver**: Runs monthly depreciation
3. **Approver**: Processes asset disposals and movements
4. **Auditor**: Reviews asset reports

**Key Reports**:

- **Asset Register**: Complete asset listing
- **Depreciation Schedule**: Monthly depreciation details
- **Disposal Summary**: Disposed assets with gain/loss
- **Movement Log**: Asset location changes
- **Asset Summary**: High-level asset overview

---

## Assessment Questions

### Knowledge Assessment

1. **What is the correct sequence for processing a sales invoice?**

   - A) Create → Post → Receive Payment
   - B) Create → Receive Payment → Post
   - C) Post → Create → Receive Payment
   - D) Receive Payment → Create → Post

2. **Which account should be debited when recording a cash receipt from a customer?**

   - A) Accounts Receivable
   - B) Cash
   - C) Revenue
   - D) Accounts Payable

3. **What is the correct PPN rate for Indonesian businesses?**

   - A) 10%
   - B) 11%
   - C) 12%
   - D) 15%

4. **Which role is responsible for posting journal entries?**

   - A) Accountant
   - B) Approver
   - C) Cashier
   - D) Auditor

5. **What happens to journal entries created by accountants before they are posted?**

   - A) They are automatically posted
   - B) They remain in draft status until approved
   - C) They are deleted after 24 hours
   - D) They are sent to the auditor for review

6. **What happens to sales invoices created by accountants before they are posted?**

   - A) They are automatically posted
   - B) They remain in draft status until approved by approver
   - C) They are sent directly to customers
   - D) They are deleted after 48 hours

7. **What happens to purchase payments created by cashiers before they are posted?**

   - A) They are automatically posted
   - B) They remain in draft status until approved by approver
   - C) They are sent directly to vendors
   - D) They are processed immediately

8. **What happens to an asset's book value after depreciation is posted?**
   - A) Increases
   - B) Decreases
   - C) Stays the same
   - D) Depends on the asset type

### Practical Exercises

1. **Create a complete journal entry** for office rent payment of Rp 5,000,000, including proper account codes and dimensions.

2. **Process an asset disposal** with remaining book value of Rp 2,000,000 and disposal proceeds of Rp 500,000.

3. **Generate and interpret** an AR Aging report showing customer payment patterns.

4. **Verify trial balance** for a given period and identify any discrepancies.

5. **Create and post** a sales invoice with proper PPN calculation and customer allocation.

6. **Demonstrate the posting workflow** by creating a journal entry as accountant, then switching to approver role to post it, and finally verifying the status change in reports.

7. **Demonstrate the complete sales cycle** by creating a sales invoice as accountant, approving it as approver, processing payment as cashier, and approving payment as approver.

8. **Demonstrate the complete purchase cycle** by creating a purchase invoice as accountant, approving it as approver, processing payment as cashier, and approving payment as approver.

### Scenario-Based Questions

1. **A customer disputes an invoice amount. How would you handle this situation using the ERP system?**

2. **An asset was moved to a different department. What steps are required to record this movement?**

3. **A vendor payment was made but not properly allocated to invoices. How would you correct this?**

4. **Monthly depreciation run shows incorrect amounts. What should you do to investigate and resolve this?**

5. **A journal entry was posted incorrectly. What is the proper procedure to correct this error?**

6. **An accountant created a journal entry but it's not showing up in the Trial Balance report. What could be the reason and how would you resolve it?**

7. **A sales invoice was created by an accountant but the customer says they haven't received it. What could be the issue and how would you resolve it?**

8. **A purchase payment was processed by a cashier but the vendor says they haven't received the payment. What could be the issue and how would you resolve it?**

---

## Conclusion

These training scenarios provide comprehensive, hands-on experience with the ERP system for each user role. The story-based approach helps employees understand not just how to use the system, but why each step is important in the overall business process.

**Key Learning Objectives Achieved**:

- Understanding of role-based responsibilities and **separation of duties**
- Knowledge of **creation and approval workflows** with proper status progression
- Experience with **posting workflow** (Draft → Posted status changes)
- Understanding of **reporting before and after posting** (zero vs. actual balances)
- Experience with **complete transaction cycles** (invoice creation → approval → payment processing → payment approval)
- Understanding of **internal controls** and approval workflows for all transaction types
- Experience with reporting and verification
- Compliance with Indonesian business standards
- Integration of multiple business processes

**Next Steps**:

1. Complete all scenarios for your assigned role
2. Practice cross-role workflows with colleagues
3. Review reporting outputs for each scenario
4. Take the assessment questions to verify understanding
5. Apply learned skills to actual business operations

Remember: The ERP system is designed to support Indonesian business practices and SAK compliance. Always ensure proper documentation and approval workflows are followed for all transactions.

# ERP System Comprehensive Business Process Training

## Complete Workflow Training with Creation, Approval, and Reporting Integration

**Purpose**: Comprehensive training materials with realistic business scenarios demonstrating complete business processes from creation to approval and reporting integration, showing how transactions flow through the ERP system and appear in financial reports.

**Target Audience**: Employees who will use the ERP system in their daily operations  
**Context**: Indonesian business environment with SAK compliance, Rupiah currency, and Indonesian tax regulations  
**Company**: PT Maju Bersama (Indonesian consulting company)

---

## Table of Contents

1. [Business Process Overview](#business-process-overview)
2. [Purchase Process Workflows](#purchase-process-workflows)
   - [Purchase Order → Purchase Invoice → Purchase Payment](#purchase-order--purchase-invoice--purchase-payment)
3. [Sales Process Workflows](#sales-process-workflows)
   - [Sales Order → Sales Invoice → Sales Receipt](#sales-order--sales-invoice--sales-receipt)
4. [Cash Expense Workflows](#cash-expense-workflows)
   - [Cash Expense Creation and Approval](#cash-expense-creation-and-approval)
5. [Reporting Integration Examples](#reporting-integration-examples)
   - [GL Detail Report Analysis](#gl-detail-report-analysis)
   - [Balance Sheet Impact](#balance-sheet-impact)
   - [Profit and Loss Impact](#profit-and-loss-impact)
6. [Assessment and Practical Exercises](#assessment-and-practical-exercises)

---

## Prerequisites: Master Data Setup

### Before Starting Business Processes

To ensure all reports show meaningful data, complete these master data setup steps:

#### 1. Create Customers

- Navigate to **Sales → Customers**
- Create at least 3 customers:
  - **PT Maju Bersama** (Corporate customer)
  - **CV Sejahtera Abadi** (Small business)
  - **Toko Sumber Rejeki** (Retail store)

#### 2. Create Vendors

- Navigate to **Purchase → Vendors**
- Create at least 3 vendors:
  - **PT Supplier Utama** (Main supplier)
  - **CV Distributor Jaya** (Distributor)
  - **Toko Alat Kantor** (Office supplies)

#### 3. Create Products

- Navigate to **Inventory → Products**
- Create at least 5 products:
  - **Laptop Dell Inspiron** (Rp 8,500,000)
  - **Printer HP LaserJet** (Rp 2,500,000)
  - **Kertas A4** (Rp 50,000 per ream)
  - **Pulpen Pilot** (Rp 5,000)
  - **Stapler Max** (Rp 25,000)

#### 4. Create Projects (Optional)

- Navigate to **Accounting → Projects**
- Create projects: **Office Renovation**, **IT Upgrade**, **Marketing Campaign**

---

## Business Process Overview

### ERP System Business Processes

The ERP system manages complete business cycles with proper internal controls:

1. **Purchase Cycle**: Purchase Order → Purchase Invoice → Purchase Payment
2. **Sales Cycle**: Sales Order → Sales Invoice → Sales Receipt
3. **Cash Management**: Cash Expense → Approval → Posting
4. **Asset Management**: Asset Acquisition → Depreciation → Disposal

### Key Principles

- **Separation of Duties**: Creation vs Approval roles
- **Status Workflow**: Draft → Posted (with proper authorization)
- **Double-Entry Bookkeeping**: Every transaction affects at least two accounts
- **Indonesian Compliance**: SAK standards, PPN tax (11%), Rupiah currency
- **Audit Trail**: Complete transaction history with user attribution

### User Roles and Responsibilities

- **Accountant**: Creates transactions (invoices, expenses, journals)
- **Approver**: Reviews and posts transactions to General Ledger
- **Cashier**: Processes payments and receipts
- **Auditor**: Reviews completed transactions and reports
- **Super Admin**: System administration and user management

---

## Purchase Process Workflows

### Purchase Order → Purchase Invoice → Purchase Payment

**Complete Business Scenario**: PT Maju Bersama purchases office equipment from Toko Alat Kantor Jakarta

#### Phase 1: Purchase Order Creation (Accountant Role)

**Context**: PT Maju Bersama needs to purchase office equipment for their Jakarta office expansion.

**Story**: As Budi Santoso (Accountant), you receive a request from the Operations Manager to purchase office equipment. You need to create a purchase order to formalize the procurement process.

**Actions**:

1. **Create Purchase Order**

   - **Navigation**: Orders → Purchase Orders → Create New
   - **Vendor**: Toko Alat Kantor Jakarta
   - **Date**: 15 January 2025
   - **Description**: Office equipment for Jakarta office expansion
   - **Reference**: PO-202501-000001

2. **Add Purchase Order Lines**

   - **Line 1**: Office Desk (Quantity: 5, Unit Price: Rp 2,500,000)
   - **Line 2**: Office Chair (Quantity: 10, Unit Price: Rp 1,200,000)
   - **Line 3**: Filing Cabinet (Quantity: 3, Unit Price: Rp 3,000,000)
   - **Account**: Office Equipment (Asset Account)
   - **Tax Code**: PPN Input (11%)

3. **Review Totals**

   - **Subtotal**: Rp 30,500,000
   - **PPN Input**: Rp 3,355,000
   - **Total**: Rp 33,855,000

4. **Save Purchase Order**
   - **Status**: Draft
   - **Verification**: Purchase order created but not yet approved

**Expected Results**:

- Purchase order created in **DRAFT** status
- Vendor commitment established
- Procurement process documented
- Ready for approval workflow

**Reporting Verification**:

- **Purchase Order Report**: Shows PO-202501-000001 in draft status
- **Vendor Analysis**: Shows pending orders with Toko Alat Kantor Jakarta
- **GL Detail Report**: No impact (PO is not a financial transaction)

#### Phase 2: Purchase Invoice Creation (Accountant Role)

**Context**: Toko Alat Kantor Jakarta delivers the office equipment and sends the invoice.

**Story**: The vendor has delivered the equipment and sent invoice INV-TOKO-001/2025. You need to create a purchase invoice to record the actual purchase transaction.

**Actions**:

1. **Create Purchase Invoice**

   - **Navigation**: Purchases → Purchase Invoices → Create New
   - **Vendor**: Toko Alat Kantor Jakarta
   - **Date**: 20 January 2025
   - **Due Date**: 19 February 2025 (30 days)
   - **Reference**: PINV-202501-000001
   - **Purchase Order**: Link to PO-202501-000001

2. **Add Purchase Invoice Lines**

   - **Line 1**: Office Desk (Quantity: 5, Unit Price: Rp 2,500,000, Account: Office Equipment)
   - **Line 2**: Office Chair (Quantity: 10, Unit Price: Rp 1,200,000, Account: Office Equipment)
   - **Line 3**: Filing Cabinet (Quantity: 3, Unit Price: Rp 3,000,000, Account: Office Equipment)
   - **Tax Code**: PPN Input (11%)

3. **Review Totals**

   - **Subtotal**: Rp 30,500,000
   - **PPN Input**: Rp 3,355,000
   - **Total**: Rp 33,855,000

4. **Save Purchase Invoice**
   - **Status**: Draft
   - **Verification**: Accountant cannot post the invoice (permission denied)

**Expected Results**:

- Purchase invoice created in **DRAFT** status
- Vendor liability established
- Asset acquisition documented
- Ready for approval workflow

**Reporting Verification**:

- **Purchase Invoice Report**: Shows PINV-202501-000001 in draft status
- **AP Aging Report**: No impact (invoice not yet posted)
- **GL Detail Report**: No impact (invoice not yet posted)

#### Phase 3: Purchase Invoice Approval (Approver Role)

**Context**: The purchase invoice needs to be reviewed and approved before posting to the General Ledger.

**Story**: As Siti Rahayu (Approver), you need to review the purchase invoice created by the accountant and approve it for posting.

**Actions**:

1. **Review Purchase Invoice**

   - **Navigation**: Purchases → Purchase Invoices
   - **Filter**: Show draft invoices
   - **Select**: PINV-202501-000001

2. **Validate Invoice Details**

   - **Vendor**: Toko Alat Kantor Jakarta ✓
   - **Amounts**: Rp 33,855,000 total ✓
   - **PPN Calculation**: Rp 3,355,000 (11% of Rp 30,500,000) ✓
   - **Account Classification**: Office Equipment ✓
   - **Supporting Documents**: Invoice INV-TOKO-001/2025 ✓

3. **Post Purchase Invoice**
   - **Action**: Click "Post" button
   - **Confirmation**: Confirm posting with SweetAlert
   - **Status Change**: Draft → Posted
   - **Timestamp**: Posted by Siti Rahayu at 20 January 2025 14:30

**Expected Results**:

- Purchase invoice status changes to **POSTED**
- Journal entry created automatically
- Accounts Payable increased by Rp 33,855,000
- Office Equipment increased by Rp 30,500,000
- PPN Input increased by Rp 3,355,000

**Journal Entry Created**:

```
Dr Office Equipment        Rp 30,500,000
Dr PPN Input              Rp  3,355,000
Cr Accounts Payable       Rp 33,855,000
```

**Reporting Verification**:

- **AP Aging Report**: Shows Toko Alat Kantor Jakarta with Rp 33,855,000 balance
- **GL Detail Report**: Shows purchase invoice journal entry
- **Trial Balance Report**: Shows updated account balances
- **Balance Sheet**: Shows increased Assets (Office Equipment + PPN Input) and Liabilities (AP)

#### Phase 4: Purchase Payment Creation (Cashier Role)

**Context**: Payment is due to Toko Alat Kantor Jakarta for the office equipment purchase.

**Story**: As Ahmad Wijaya (Cashier), you need to process the payment to Toko Alat Kantor Jakarta for the office equipment invoice.

**Actions**:

1. **Create Purchase Payment**

   - **Navigation**: Purchases → Purchase Payments → Create New
   - **Vendor**: Toko Alat Kantor Jakarta
   - **Date**: 18 February 2025
   - **Amount**: Rp 33,855,000
   - **Payment Method**: Bank Transfer
   - **Reference**: PP-202502-000001

2. **Allocate Payment to Invoice**

   - **Select Invoice**: PINV-202501-000001
   - **Allocation Amount**: Rp 33,855,000
   - **Remaining Balance**: Rp 0
   - **Verification**: Full payment allocation

3. **Save Purchase Payment**
   - **Status**: Draft
   - **Verification**: Cashier cannot post the payment (permission denied)

**Expected Results**:

- Purchase payment created in **DRAFT** status
- Payment allocation recorded
- Vendor balance updated (but not yet posted)
- Ready for approval workflow

**Reporting Verification**:

- **Purchase Payment Report**: Shows PP-202502-000001 in draft status
- **AP Aging Report**: No change (payment not yet posted)
- **Cash Ledger Report**: No impact (payment not yet posted)

#### Phase 5: Purchase Payment Approval (Approver Role)

**Context**: The purchase payment needs to be reviewed and approved before posting to the General Ledger.

**Story**: As Siti Rahayu (Approver), you need to review the purchase payment created by the cashier and approve it for posting.

**Actions**:

1. **Review Purchase Payment**

   - **Navigation**: Purchases → Purchase Payments
   - **Filter**: Show draft payments
   - **Select**: PP-202502-000001

2. **Validate Payment Details**

   - **Vendor**: Toko Alat Kantor Jakarta ✓
   - **Amount**: Rp 33,855,000 ✓
   - **Allocation**: To PINV-202501-000001 ✓
   - **Payment Method**: Bank Transfer ✓
   - **Supporting Documents**: Bank transfer receipt ✓

3. **Post Purchase Payment**
   - **Action**: Click "Post" button
   - **Confirmation**: Confirm posting with SweetAlert
   - **Status Change**: Draft → Posted
   - **Timestamp**: Posted by Siti Rahayu at 18 February 2025 10:15

**Expected Results**:

- Purchase payment status changes to **POSTED**
- Journal entry created automatically
- Accounts Payable decreased by Rp 33,855,000
- Bank Account decreased by Rp 33,855,000
- Vendor balance cleared

**Journal Entry Created**:

```
Dr Accounts Payable       Rp 33,855,000
Cr Bank Account           Rp 33,855,000
```

**Reporting Verification**:

- **AP Aging Report**: Shows Toko Alat Kantor Jakarta with Rp 0 balance
- **GL Detail Report**: Shows purchase payment journal entry
- **Trial Balance Report**: Shows updated account balances
- **Balance Sheet**: Shows decreased Liabilities (AP) and Assets (Bank)

#### Phase 6: Complete Purchase Cycle Review (Auditor Role)

**Context**: Review the complete purchase cycle to ensure proper internal controls and data integrity.

**Story**: As Maria Sari (Auditor), you need to review the complete purchase cycle from order to payment to ensure proper controls were maintained.

**Actions**:

1. **Review Purchase Order**

   - **Navigation**: Orders → Purchase Orders
   - **Select**: PO-202501-000001
   - **Verify**: Order details match invoice

2. **Review Purchase Invoice**

   - **Navigation**: Purchases → Purchase Invoices
   - **Select**: PINV-202501-000001
   - **Verify**: Invoice posted by approver, amounts correct

3. **Review Purchase Payment**

   - **Navigation**: Purchases → Purchase Payments
   - **Select**: PP-202502-000001
   - **Verify**: Payment posted by approver, full allocation

4. **Review Financial Reports**
   - **GL Detail Report**: Shows complete transaction history
   - **AP Aging Report**: Shows zero balance for vendor
   - **Trial Balance Report**: Shows balanced books
   - **Balance Sheet**: Shows net effect on assets and liabilities

**Expected Results**:

- Complete purchase cycle documented
- Separation of duties maintained
- All transactions properly posted
- Financial reports reflect accurate balances
- Audit trail complete with user attribution

**Final Reporting Impact**:

- **Office Equipment**: Increased by Rp 30,500,000
- **PPN Input**: Increased by Rp 3,355,000 (recoverable tax)
- **Bank Account**: Decreased by Rp 33,855,000
- **Accounts Payable**: Net change zero (invoice + payment)
- **Net Effect**: Office equipment acquired for Rp 30,500,000 net cost

---

## Sales Process Workflows

### Sales Order → Sales Invoice → Sales Receipt

**Complete Business Scenario**: PT Maju Bersama provides IT consulting services to CV Berkah Jaya

#### Phase 1: Sales Order Creation (Accountant Role)

**Context**: CV Berkah Jaya requests IT consulting services for their digital transformation project.

**Story**: As Budi Santoso (Accountant), you receive a service request from CV Berkah Jaya. You need to create a sales order to formalize the service agreement.

**Actions**:

1. **Create Sales Order**

   - **Navigation**: Orders → Sales Orders → Create New
   - **Customer**: CV Berkah Jaya
   - **Date**: 10 January 2025
   - **Description**: IT consulting services for digital transformation
   - **Reference**: SO-202501-000001

2. **Add Sales Order Lines**

   - **Line 1**: System Analysis (Quantity: 40 hours, Unit Price: Rp 500,000)
   - **Line 2**: Database Design (Quantity: 30 hours, Unit Price: Rp 600,000)
   - **Line 3**: Implementation Support (Quantity: 20 hours, Unit Price: Rp 400,000)
   - **Account**: Consulting Revenue
   - **Tax Code**: PPN Output (11%)

3. **Review Totals**

   - **Subtotal**: Rp 38,000,000
   - **PPN Output**: Rp 4,180,000
   - **Total**: Rp 42,180,000

4. **Save Sales Order**
   - **Status**: Draft
   - **Verification**: Sales order created but not yet approved

**Expected Results**:

- Sales order created in **DRAFT** status
- Customer commitment established
- Service agreement documented
- Ready for approval workflow

**Reporting Verification**:

- **Sales Order Report**: Shows SO-202501-000001 in draft status
- **Customer Analysis**: Shows pending orders with CV Berkah Jaya
- **GL Detail Report**: No impact (SO is not a financial transaction)

#### Phase 2: Sales Invoice Creation (Accountant Role)

**Context**: Services have been delivered and invoice needs to be created for billing.

**Story**: The IT consulting services have been completed. You need to create a sales invoice to bill CV Berkah Jaya for the services rendered.

**Actions**:

1. **Create Sales Invoice**

   - **Navigation**: Sales → Sales Invoices → Create New
   - **Customer**: CV Berkah Jaya
   - **Date**: 25 January 2025
   - **Due Date**: 24 February 2025 (30 days)
   - **Reference**: INV-202501-000001
   - **Sales Order**: Link to SO-202501-000001

2. **Add Sales Invoice Lines**

   - **Line 1**: System Analysis (Quantity: 40 hours, Unit Price: Rp 500,000, Account: Consulting Revenue)
   - **Line 2**: Database Design (Quantity: 30 hours, Unit Price: Rp 600,000, Account: Consulting Revenue)
   - **Line 3**: Implementation Support (Quantity: 20 hours, Unit Price: Rp 400,000, Account: Consulting Revenue)
   - **Tax Code**: PPN Output (11%)

3. **Review Totals**

   - **Subtotal**: Rp 38,000,000
   - **PPN Output**: Rp 4,180,000
   - **Total**: Rp 42,180,000

4. **Save Sales Invoice**
   - **Status**: Draft
   - **Verification**: Accountant cannot post the invoice (permission denied)

**Expected Results**:

- Sales invoice created in **DRAFT** status
- Customer receivable established
- Revenue recognition documented
- Ready for approval workflow

**Reporting Verification**:

- **Sales Invoice Report**: Shows INV-202501-000001 in draft status
- **AR Aging Report**: No impact (invoice not yet posted)
- **GL Detail Report**: No impact (invoice not yet posted)

#### Phase 3: Sales Invoice Approval (Approver Role)

**Context**: The sales invoice needs to be reviewed and approved before posting to the General Ledger.

**Story**: As Siti Rahayu (Approver), you need to review the sales invoice created by the accountant and approve it for posting.

**Actions**:

1. **Review Sales Invoice**

   - **Navigation**: Sales → Sales Invoices
   - **Filter**: Show draft invoices
   - **Select**: INV-202501-000001

2. **Validate Invoice Details**

   - **Customer**: CV Berkah Jaya ✓
   - **Amounts**: Rp 42,180,000 total ✓
   - **PPN Calculation**: Rp 4,180,000 (11% of Rp 38,000,000) ✓
   - **Account Classification**: Consulting Revenue ✓
   - **Supporting Documents**: Service delivery confirmation ✓

3. **Post Sales Invoice**
   - **Action**: Click "Post" button
   - **Confirmation**: Confirm posting with SweetAlert
   - **Status Change**: Draft → Posted
   - **Timestamp**: Posted by Siti Rahayu at 25 January 2025 16:45

**Expected Results**:

- Sales invoice status changes to **POSTED**
- Journal entry created automatically
- Accounts Receivable increased by Rp 42,180,000
- Consulting Revenue increased by Rp 38,000,000
- PPN Output increased by Rp 4,180,000

**Journal Entry Created**:

```
Dr Accounts Receivable     Rp 42,180,000
Cr Consulting Revenue     Rp 38,000,000
Cr PPN Output             Rp  4,180,000
```

**Reporting Verification**:

- **AR Aging Report**: Shows CV Berkah Jaya with Rp 42,180,000 balance
- **GL Detail Report**: Shows sales invoice journal entry
- **Trial Balance Report**: Shows updated account balances
- **Balance Sheet**: Shows increased Assets (AR) and Liabilities (PPN Output)
- **Profit & Loss**: Shows increased Revenue

#### Phase 4: Sales Receipt Creation (Cashier Role)

**Context**: CV Berkah Jaya has made payment for the consulting services.

**Story**: As Ahmad Wijaya (Cashier), you receive payment from CV Berkah Jaya for the consulting services invoice.

**Actions**:

1. **Create Sales Receipt**

   - **Navigation**: Sales → Sales Receipts → Create New
   - **Customer**: CV Berkah Jaya
   - **Date**: 20 February 2025
   - **Amount**: Rp 42,180,000
   - **Payment Method**: Bank Transfer
   - **Reference**: SR-202502-000001

2. **Allocate Receipt to Invoice**

   - **Select Invoice**: INV-202501-000001
   - **Allocation Amount**: Rp 42,180,000
   - **Remaining Balance**: Rp 0
   - **Verification**: Full payment allocation

3. **Save Sales Receipt**
   - **Status**: Draft
   - **Verification**: Cashier cannot post the receipt (permission denied)

**Expected Results**:

- Sales receipt created in **DRAFT** status
- Payment allocation recorded
- Customer balance updated (but not yet posted)
- Ready for approval workflow

**Reporting Verification**:

- **Sales Receipt Report**: Shows SR-202502-000001 in draft status
- **AR Aging Report**: No change (receipt not yet posted)
- **Cash Ledger Report**: No impact (receipt not yet posted)

#### Phase 5: Sales Receipt Approval (Approver Role)

**Context**: The sales receipt needs to be reviewed and approved before posting to the General Ledger.

**Story**: As Siti Rahayu (Approver), you need to review the sales receipt created by the cashier and approve it for posting.

**Actions**:

1. **Review Sales Receipt**

   - **Navigation**: Sales → Sales Receipts
   - **Filter**: Show draft receipts
   - **Select**: SR-202502-000001

2. **Validate Receipt Details**

   - **Customer**: CV Berkah Jaya ✓
   - **Amount**: Rp 42,180,000 ✓
   - **Allocation**: To INV-202501-000001 ✓
   - **Payment Method**: Bank Transfer ✓
   - **Supporting Documents**: Bank transfer confirmation ✓

3. **Post Sales Receipt**
   - **Action**: Click "Post" button
   - **Confirmation**: Confirm posting with SweetAlert
   - **Status Change**: Draft → Posted
   - **Timestamp**: Posted by Siti Rahayu at 20 February 2025 14:20

**Expected Results**:

- Sales receipt status changes to **POSTED**
- Journal entry created automatically
- Accounts Receivable decreased by Rp 42,180,000
- Bank Account increased by Rp 42,180,000
- Customer balance cleared

**Journal Entry Created**:

```
Dr Bank Account            Rp 42,180,000
Cr Accounts Receivable     Rp 42,180,000
```

**Reporting Verification**:

- **AR Aging Report**: Shows CV Berkah Jaya with Rp 0 balance
- **GL Detail Report**: Shows sales receipt journal entry
- **Trial Balance Report**: Shows updated account balances
- **Balance Sheet**: Shows increased Assets (Bank) and decreased Assets (AR)
- **Cash Flow**: Shows cash inflow from operations

#### Phase 6: Complete Sales Cycle Review (Auditor Role)

**Context**: Review the complete sales cycle to ensure proper internal controls and data integrity.

**Story**: As Maria Sari (Auditor), you need to review the complete sales cycle from order to receipt to ensure proper controls were maintained.

**Actions**:

1. **Review Sales Order**

   - **Navigation**: Orders → Sales Orders
   - **Select**: SO-202501-000001
   - **Verify**: Order details match invoice

2. **Review Sales Invoice**

   - **Navigation**: Sales → Sales Invoices
   - **Select**: INV-202501-000001
   - **Verify**: Invoice posted by approver, amounts correct

3. **Review Sales Receipt**

   - **Navigation**: Sales → Sales Receipts
   - **Select**: SR-202502-000001
   - **Verify**: Receipt posted by approver, full allocation

4. **Review Financial Reports**
   - **GL Detail Report**: Shows complete transaction history
   - **AR Aging Report**: Shows zero balance for customer
   - **Trial Balance Report**: Shows balanced books
   - **Balance Sheet**: Shows net effect on assets
   - **Profit & Loss**: Shows revenue recognition

**Expected Results**:

- Complete sales cycle documented
- Separation of duties maintained
- All transactions properly posted
- Financial reports reflect accurate balances
- Audit trail complete with user attribution

**Final Reporting Impact**:

- **Consulting Revenue**: Increased by Rp 38,000,000
- **PPN Output**: Increased by Rp 4,180,000 (tax liability)
- **Bank Account**: Increased by Rp 42,180,000
- **Accounts Receivable**: Net change zero (invoice + receipt)
- **Net Effect**: Revenue recognized and cash received

---

## Cash Expense Workflows

### Cash Expense Creation and Approval

**Complete Business Scenario**: PT Maju Bersama records various office expenses paid from petty cash

#### Phase 1: Cash Expense Creation (Accountant Role)

**Context**: Various office expenses need to be recorded for petty cash reimbursement.

**Story**: As Budi Santoso (Accountant), you need to record several office expenses that were paid from petty cash and need to be reimbursed.

**Actions**:

1. **Create Cash Expense - Office Supplies**

   - **Navigation**: Journals → Cash Expenses → Create New
   - **Date**: 22 January 2025
   - **Description**: Office supplies purchase
   - **Expense Account**: Office Supplies Expense
   - **Cash Account**: Petty Cash
   - **Amount**: Rp 750,000
   - **Project**: Jakarta Office (optional)
   - **Fund**: General Fund (optional)
   - **Department**: Administration (optional)

2. **Create Cash Expense - Transportation**

   - **Date**: 22 January 2025
   - **Description**: Client meeting transportation
   - **Expense Account**: Transportation Expense
   - **Cash Account**: Petty Cash
   - **Amount**: Rp 150,000
   - **Project**: CV Berkah Jaya Project (optional)

3. **Create Cash Expense - Meals**

   - **Date**: 22 January 2025
   - **Description**: Client lunch meeting
   - **Expense Account**: Meals & Entertainment Expense
   - **Cash Account**: Petty Cash
   - **Amount**: Rp 300,000
   - **Project**: CV Berkah Jaya Project (optional)

4. **Save Cash Expenses**
   - **Status**: All expenses created in **DRAFT** status
   - **Verification**: Accountant cannot post the expenses (permission denied)

**Expected Results**:

- Three cash expenses created in **DRAFT** status
- Expense details documented
- Petty cash usage tracked
- Ready for approval workflow

**Reporting Verification**:

- **Cash Expense Report**: Shows three expenses in draft status
- **GL Detail Report**: No impact (expenses not yet posted)
- **Trial Balance Report**: No impact (expenses not yet posted)

#### Phase 2: Cash Expense Approval (Approver Role)

**Context**: The cash expenses need to be reviewed and approved before posting to the General Ledger.

**Story**: As Siti Rahayu (Approver), you need to review the cash expenses created by the accountant and approve them for posting.

**Actions**:

1. **Review Cash Expenses**

   - **Navigation**: Journals → Cash Expenses
   - **Filter**: Show draft expenses
   - **Review**: All three expenses created by accountant

2. **Validate Expense Details**

   - **Office Supplies**: Rp 750,000 ✓
   - **Transportation**: Rp 150,000 ✓
   - **Meals**: Rp 300,000 ✓
   - **Total**: Rp 1,200,000 ✓
   - **Supporting Documents**: Receipts attached ✓

3. **Post Cash Expenses**
   - **Action**: Post each expense individually
   - **Confirmation**: Confirm posting with SweetAlert for each
   - **Status Change**: Draft → Posted for all expenses
   - **Timestamp**: Posted by Siti Rahayu at 22 January 2025 17:30

**Expected Results**:

- All cash expenses status changes to **POSTED**
- Journal entries created automatically
- Expense accounts increased
- Petty Cash decreased

**Journal Entries Created**:

```
Dr Office Supplies Expense     Rp   750,000
Cr Petty Cash                 Rp   750,000

Dr Transportation Expense     Rp   150,000
Cr Petty Cash                 Rp   150,000

Dr Meals & Entertainment       Rp   300,000
Cr Petty Cash                 Rp   300,000
```

**Reporting Verification**:

- **GL Detail Report**: Shows all three cash expense journal entries
- **Trial Balance Report**: Shows updated account balances
- **Balance Sheet**: Shows decreased Assets (Petty Cash)
- **Profit & Loss**: Shows increased Expenses

#### Phase 3: Cash Expense Review (Auditor Role)

**Context**: Review the cash expenses to ensure proper controls and documentation.

**Story**: As Maria Sari (Auditor), you need to review the cash expenses to ensure proper controls were maintained and expenses are properly documented.

**Actions**:

1. **Review Cash Expenses**

   - **Navigation**: Journals → Cash Expenses
   - **Select**: All three posted expenses
   - **Verify**: All expenses posted by approver

2. **Validate Supporting Documentation**

   - **Office Supplies**: Receipt from Toko Alat Kantor ✓
   - **Transportation**: Taxi receipt ✓
   - **Meals**: Restaurant receipt ✓

3. **Review Financial Impact**

   - **Total Expenses**: Rp 1,200,000
   - **Petty Cash Reduction**: Rp 1,200,000
   - **Expense Classification**: Proper account classification ✓

4. **Review Reports**
   - **GL Detail Report**: Shows complete transaction history
   - **Trial Balance Report**: Shows balanced books
   - **Profit & Loss**: Shows expense recognition
   - **Cash Ledger Report**: Shows petty cash transactions

**Expected Results**:

- All cash expenses properly documented
- Separation of duties maintained
- Supporting documentation verified
- Financial reports reflect accurate balances
- Audit trail complete with user attribution

**Final Reporting Impact**:

- **Office Supplies Expense**: Increased by Rp 750,000
- **Transportation Expense**: Increased by Rp 150,000
- **Meals & Entertainment**: Increased by Rp 300,000
- **Petty Cash**: Decreased by Rp 1,200,000
- **Net Effect**: Expenses recognized and cash reduced

---

## Reporting Integration Examples

### GL Detail Report Analysis

**Purpose**: Demonstrate how transactions flow through the General Ledger and appear in detailed reports.

#### Sample GL Detail Report (January 2025)

**Report Parameters**:

- **Date Range**: 1 January 2025 to 31 January 2025
- **Account**: All Accounts
- **Status**: Posted Only

**Key Transactions Shown**:

1. **Office Equipment Purchase** (20 January 2025)

   ```
   Dr Office Equipment        Rp 30,500,000
   Dr PPN Input              Rp  3,355,000
   Cr Accounts Payable       Rp 33,855,000
   ```

2. **Consulting Revenue Recognition** (25 January 2025)

   ```
   Dr Accounts Receivable     Rp 42,180,000
   Cr Consulting Revenue     Rp 38,000,000
   Cr PPN Output             Rp  4,180,000
   ```

3. **Cash Expenses** (22 January 2025)
   ```
   Dr Office Supplies Expense Rp   750,000
   Dr Transportation Expense Rp   150,000
   Dr Meals & Entertainment   Rp   300,000
   Cr Petty Cash             Rp 1,200,000
   ```

**Report Features**:

- **Transaction Details**: Date, reference, description, user
- **Account Balances**: Running balances for each account
- **Filtering**: By date range, account, user, status
- **Export**: PDF, Excel, CSV formats available

### Balance Sheet Impact

**Purpose**: Show how business transactions affect the Balance Sheet.

#### Balance Sheet (31 January 2025)

**Assets**:

- **Current Assets**:

  - Cash and Bank: Rp 8,325,000 (decreased by purchase payment)
  - Petty Cash: Rp 1,800,000 (decreased by expenses)
  - Accounts Receivable: Rp 42,180,000 (increased by sales invoice)
  - PPN Input: Rp 3,355,000 (increased by purchase)

- **Fixed Assets**:
  - Office Equipment: Rp 30,500,000 (increased by purchase)

**Liabilities**:

- **Current Liabilities**:
  - Accounts Payable: Rp 33,855,000 (increased by purchase invoice)
  - PPN Output: Rp 4,180,000 (increased by sales invoice)

**Equity**:

- **Retained Earnings**: Net effect of revenue and expenses

**Key Observations**:

- Assets increased by office equipment acquisition
- Liabilities increased by purchase invoice and sales tax
- Net working capital affected by timing of payments

### Profit and Loss Impact

**Purpose**: Show how business transactions affect the Profit and Loss statement.

#### Profit & Loss Statement (January 2025)

**Revenue**:

- Consulting Revenue: Rp 38,000,000 (from sales invoice)

**Expenses**:

- Office Supplies Expense: Rp 750,000
- Transportation Expense: Rp 150,000
- Meals & Entertainment: Rp 300,000
- **Total Expenses**: Rp 1,200,000

**Net Income**: Rp 36,800,000

**Key Observations**:

- Revenue recognized when invoice posted
- Expenses recognized when cash expenses posted
- Net income reflects business performance
- Tax implications (PPN) tracked separately

---

## Complete Data Creation for Reports Demonstration

### Comprehensive Transaction Creation

To demonstrate all reports with meaningful data, create the following transactions in sequence:

#### Phase 1: Purchase Transactions (Creates AP Aging, AP Balances, GL Detail data)

**1. Create Purchase Order**

- User: Budi Santoso (Accountant)
- Navigate to **Purchase → Purchase Orders → New Order**
- Vendor: PT Supplier Utama
- Items:
  - Laptop Dell Inspiron: 2 units @ Rp 8,500,000 = Rp 17,000,000
  - Printer HP LaserJet: 1 unit @ Rp 2,500,000 = Rp 2,500,000
- Total: Rp 19,500,000
- Status: Draft → Posted (by Approver)

**2. Create Purchase Invoice**

- User: Budi Santoso (Accountant)
- Navigate to **Purchase → Purchase Invoices → New Invoice**
- Reference: PO-2025-000001
- Vendor: PT Supplier Utama
- Items: Same as Purchase Order
- Total: Rp 19,500,000
- Status: Draft → Posted (by Approver)

**3. Create Purchase Payment**

- User: Sari Dewi (Cashier)
- Navigate to **Accounting → Purchase Payments → New Payment**
- Vendor: PT Supplier Utama
- Amount: Rp 19,500,000
- Payment Method: Bank Transfer
- Status: Draft → Posted (by Approver)

#### Phase 2: Sales Transactions (Creates AR Aging, AR Balances, GL Detail data)

**4. Create Sales Order**

- User: Budi Santoso (Accountant)
- Navigate to **Sales → Sales Orders → New Order**
- Customer: CV Sejahtera Abadi
- Items:
  - Laptop Dell Inspiron: 1 unit @ Rp 9,500,000 = Rp 9,500,000
  - Kertas A4: 10 reams @ Rp 50,000 = Rp 500,000
- Total: Rp 10,000,000
- Status: Draft → Posted (by Approver)

**5. Create Sales Invoice**

- User: Budi Santoso (Accountant)
- Navigate to **Sales → Sales Invoices → New Invoice**
- Reference: SO-2025-000001
- Customer: CV Sejahtera Abadi
- Items: Same as Sales Order
- Total: Rp 10,000,000
- Status: Draft → Posted (by Approver)

**6. Create Sales Receipt**

- User: Sari Dewi (Cashier)
- Navigate to **Accounting → Sales Receipts → New Receipt**
- Customer: CV Sejahtera Abadi
- Amount: Rp 10,000,000
- Payment Method: Bank Transfer
- Status: Draft → Posted (by Approver)

#### Phase 3: Cash Expenses (Creates Cash Ledger, GL Detail data)

**7. Create Cash Expenses**

- User: Budi Santoso (Accountant)
- Navigate to **Accounting → Cash Expenses → New Expense**
- Create multiple expenses:
  - Office Supplies: Rp 500,000
  - Transportation: Rp 200,000
  - Meals & Entertainment: Rp 300,000
- Status: Draft → Posted (by Approver)

#### Phase 4: Additional Transactions for Comprehensive Reporting

**8. Create Credit Sales (for AR Aging demonstration)**

- Create Sales Invoice for PT Maju Bersama: Rp 5,000,000
- **DO NOT** create Sales Receipt (leaves outstanding balance)

**9. Create Credit Purchases (for AP Aging demonstration)**

- Create Purchase Invoice from CV Distributor Jaya: Rp 3,000,000
- **DO NOT** create Purchase Payment (leaves outstanding balance)

**10. Create Mixed Payment Scenarios**

- Partial payment to PT Supplier Utama: Rp 5,000,000 (leaves balance)
- Partial receipt from Toko Sumber Rejeki: Rp 2,000,000 (leaves balance)

### Expected Report Data After Complete Setup

#### GL Detail Report

- Shows all journal entries from Purchase Orders, Sales Orders, Cash Expenses
- Displays account codes, debit/credit amounts, transaction dates
- Demonstrates double-entry bookkeeping principles

#### Trial Balance Report

- Shows account balances with total debits = total credits
- Displays current balances for all accounts
- Demonstrates balanced books

#### AR Aging Report

- Shows outstanding receivables by customer
- Displays aging buckets: Current, 31-60, 61-90, 91+ days
- Includes PT Maju Bersama and Toko Sumber Rejeki balances

#### AP Aging Report

- Shows outstanding payables by vendor
- Displays aging buckets: Current, 31-60, 61-90, 91+ days
- Includes CV Distributor Jaya balance

#### AR Balances Report

- Shows customer balances (invoices vs receipts)
- Displays net receivable amounts
- Includes all customers with transactions

#### AP Balances Report

- Shows vendor balances (invoices vs payments)
- Displays net payable amounts
- Includes all vendors with transactions

#### Cash Ledger Report

- Shows cash account movements
- Displays opening balance, transactions, running balance
- Demonstrates cash flow tracking

---

## Assessment and Practical Exercises

### Knowledge Assessment Questions

#### Purchase Process Questions

1. **What is the correct sequence for the purchase process?**

   - A) Purchase Order → Purchase Payment → Purchase Invoice
   - B) Purchase Order → Purchase Invoice → Purchase Payment
   - C) Purchase Invoice → Purchase Order → Purchase Payment
   - D) Purchase Payment → Purchase Order → Purchase Invoice

2. **Who can post a purchase invoice to the General Ledger?**

   - A) Accountant only
   - B) Approver only
   - C) Cashier only
   - D) Any user with posting permission

3. **What accounts are affected when a purchase invoice is posted?**
   - A) Debit Expense/Asset, Credit Accounts Payable
   - B) Debit Accounts Payable, Credit Expense/Asset
   - C) Debit Cash, Credit Accounts Payable
   - D) Debit Accounts Payable, Credit Cash

#### Sales Process Questions

4. **What is the correct sequence for the sales process?**

   - A) Sales Order → Sales Receipt → Sales Invoice
   - B) Sales Order → Sales Invoice → Sales Receipt
   - C) Sales Invoice → Sales Order → Sales Receipt
   - D) Sales Receipt → Sales Order → Sales Invoice

5. **What accounts are affected when a sales invoice is posted?**

   - A) Debit Accounts Receivable, Credit Revenue
   - B) Debit Revenue, Credit Accounts Receivable
   - C) Debit Cash, Credit Revenue
   - D) Debit Revenue, Credit Cash

6. **What is the Indonesian PPN tax rate?**
   - A) 10%
   - B) 11%
   - C) 12%
   - D) 15%

#### Cash Expense Questions

7. **Who can create cash expenses?**

   - A) Approver only
   - B) Accountant only
   - C) Cashier only
   - D) Any user with expense permission

8. **What is the typical journal entry for a cash expense?**
   - A) Debit Expense, Credit Cash
   - B) Debit Cash, Credit Expense
   - C) Debit Expense, Credit Accounts Payable
   - D) Debit Accounts Payable, Credit Expense

### Practical Exercises

#### Exercise 1: Complete Purchase Cycle

**Scenario**: PT Maju Bersama needs to purchase computer equipment from PT Komputer Maju.

**Requirements**:

1. Create purchase order for 5 computers at Rp 8,000,000 each
2. Create purchase invoice when equipment is delivered
3. Process payment to vendor
4. Review complete cycle in reports

**Deliverables**:

- Purchase Order: PO-202501-000002
- Purchase Invoice: PINV-202501-000002
- Purchase Payment: PP-202501-000002
- Report analysis showing complete cycle

#### Exercise 2: Complete Sales Cycle

**Scenario**: PT Maju Bersama provides software development services to PT Teknologi Baru.

**Requirements**:

1. Create sales order for software development project
2. Create sales invoice when project is completed
3. Process customer payment
4. Review complete cycle in reports

**Deliverables**:

- Sales Order: SO-202501-000002
- Sales Invoice: INV-202501-000002
- Sales Receipt: SR-202501-000002
- Report analysis showing complete cycle

#### Exercise 3: Cash Expense Management

**Scenario**: Record various office expenses for the month.

**Requirements**:

1. Record office supplies expense (Rp 500,000)
2. Record transportation expense (Rp 200,000)
3. Record meals expense (Rp 400,000)
4. Review expense impact on financial reports

**Deliverables**:

- Three cash expense records
- GL Detail report showing all expenses
- Profit & Loss impact analysis

### Scenario-Based Questions

#### Scenario 1: Purchase Invoice Discrepancy

**Situation**: A purchase invoice shows Rp 5,000,000 but the purchase order was for Rp 4,500,000.

**Questions**:

1. What should the accountant do when creating the purchase invoice?
2. What should the approver verify before posting?
3. How should the discrepancy be handled?

#### Scenario 2: Customer Payment Shortage

**Situation**: A customer payment of Rp 10,000,000 is received for an invoice of Rp 12,000,000.

**Questions**:

1. How should the cashier record the partial payment?
2. What will the AR Aging report show?
3. How should the remaining balance be handled?

#### Scenario 3: Cash Expense Documentation

**Situation**: An employee submits a cash expense without proper receipt.

**Questions**:

1. What should the accountant do?
2. What should the approver verify?
3. What controls should be in place?

### Competency Evaluation

#### Beginner Level (Score: 70-79%)

- Can create basic transactions
- Understands basic workflow
- Can navigate system menus
- Understands basic reporting

#### Intermediate Level (Score: 80-89%)

- Can complete full business cycles
- Understands approval workflows
- Can analyze basic reports
- Understands internal controls

#### Advanced Level (Score: 90-100%)

- Can troubleshoot transaction issues
- Understands complex reporting
- Can train other users
- Understands business impact

---

## Conclusion

This comprehensive training material provides complete business process workflows demonstrating:

1. **Complete Business Cycles**: From order creation to payment processing
2. **Separation of Duties**: Creation vs approval roles and responsibilities
3. **Status Workflows**: Draft → Posted with proper authorization
4. **Reporting Integration**: How transactions flow through financial reports
5. **Indonesian Compliance**: SAK standards, PPN tax, Rupiah currency
6. **Internal Controls**: Proper approval processes and audit trails

The training scenarios use realistic Indonesian business contexts with proper company names, currency formatting, and tax compliance, ensuring practical applicability for Indonesian users.

**Key Learning Outcomes**:

- Understanding complete business processes
- Proper use of approval workflows
- Integration with financial reporting
- Compliance with Indonesian standards
- Internal control maintenance

**Next Steps**:

1. Complete all practical exercises
2. Review financial reports after each transaction
3. Understand the impact on Balance Sheet and P&L
4. Practice troubleshooting common issues
5. Train other team members using these scenarios

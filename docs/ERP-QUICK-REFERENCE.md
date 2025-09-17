# ERP System Quick Reference Guide

## Common Tasks and Procedures

**Purpose**: Quick reference for daily ERP system operations  
**Audience**: All ERP system users  
**Format**: Step-by-step procedures for common tasks  
**Compliance**: Indonesian Accounting Standards (SAK) and tax regulations

---

## Table of Contents

1. [Login and Navigation](#1-login-and-navigation)
2. [Journal Entries](#2-journal-entries)
3. [Accounts Receivable](#3-accounts-receivable)
4. [Accounts Payable](#4-accounts-payable)
5. [Fixed Assets](#5-fixed-assets)
6. [Reports](#6-reports)
7. [Common Issues](#7-common-issues)

---

## 1. Login and Navigation

### Login Process

1. Open web browser
2. Navigate to ERP system URL
3. Enter username and password
4. Click "Login"
5. Select appropriate role if multiple roles assigned

### Main Navigation

- **Dashboard**: Overview of key metrics and quick actions
- **Journals**: Manual journal entry creation and management
- **AR**: Accounts Receivable (Sales Invoices, Receipts)
- **AP**: Accounts Payable (Purchase Invoices, Payments)
- **Assets**: Fixed Asset management
- **Reports**: Financial and operational reports
- **Admin**: User and system administration (admin only)

### Quick Actions

- **Create Journal**: Journals → Create New
- **View Reports**: Reports → Select Report Type
- **Manage Assets**: Assets → Select Asset Type
- **Process Payments**: AR/AP → Receipts/Payments

---

## 2. Journal Entries

### Create Journal Entry

1. Navigate to **Journals → Create New**
2. Fill in header information:
   - Date: Transaction date
   - Description: Brief description
   - Journal No: Auto-generated
3. Add journal lines:
   - Account: Select from dropdown
   - Debit/Credit: Enter amount (only one per line)
   - Dimensions: Project, Fund, Department (optional)
   - Memo: Additional details
4. Verify balance shows zero
5. Click **Save** (draft) or **Post** (immediate posting)

### Common Journal Entry Types

#### Cash Purchase (excluding PPN)

```
Dr. Expense Account    Rp 1,000,000
    Cr. Cash          Rp 1,000,000
```

#### Bank Transfer

```
Dr. Bank Account      Rp 5,000,000
    Cr. Cash          Rp 5,000,000
```

#### Accrued Expense

```
Dr. Expense Account    Rp 2,500,000
    Cr. Accrued Payable Rp 2,500,000
```

#### Purchase with PPN

```
Dr. Expense Account    Rp 1,000,000
Dr. PPN Masukan        Rp 110,000
    Cr. Cash          Rp 1,110,000
```

### Journal Entry Rules

- ✅ Total debits must equal total credits
- ✅ Use descriptive memos
- ✅ Assign dimensions when applicable
- ❌ Don't mix debits and credits on same line
- ❌ Don't post without reviewing balance

---

## 3. Accounts Receivable

### Create Sales Invoice

1. Navigate to **AR → Sales Invoices → Create New**
2. Fill in header:
   - Customer: Select from list
   - Date: Invoice date
   - Due Date: Payment due date
   - Terms: Payment terms
3. Add invoice lines:
   - Account: Revenue account
   - Description: Service/product description
   - Quantity: Number of units
   - Unit Price: Price per unit
   - Tax Code: PPN Output if applicable
4. Review totals
5. Click **Save** (draft) or **Post** (immediate posting)

### Process Customer Payment

1. Navigate to **AR → Sales Receipts → Create New**
2. Fill in header:
   - Customer: Select customer
   - Date: Payment date
   - Amount: Payment amount
3. Add receipt line:
   - Account: Cash or Bank Account
   - Amount: Payment amount
4. Allocate to invoices:
   - Select invoices to apply payment
   - Allocate payment amounts
5. Click **Save** and **Post**

### AR Aging Report

1. Navigate to **Reports → AR Aging**
2. Select date range
3. Click **Generate Report**
4. Export to PDF/Excel if needed

---

## 4. Accounts Payable

### Create Purchase Invoice

1. Navigate to **AP → Purchase Invoices → Create New**
2. Fill in header:
   - Vendor: Select from list
   - Date: Invoice date
   - Due Date: Payment due date
   - Reference: PO number if applicable
3. Add invoice lines:
   - Account: Expense or Asset account
   - Description: Service/product description
   - Quantity: Number of units
   - Unit Price: Price per unit
   - Tax Code: PPN Input if applicable
4. Review totals
5. Click **Save** (draft) or **Post** (immediate posting)

### Process Vendor Payment

1. Navigate to **AP → Purchase Payments → Create New**
2. Fill in header:
   - Vendor: Select vendor
   - Date: Payment date
   - Amount: Payment amount
3. Add payment line:
   - Account: Cash or Bank Account
   - Amount: Payment amount
4. Allocate to invoices:
   - Select invoices to pay
   - Allocate payment amounts
5. Click **Save** and **Post**

### AP Aging Report

1. Navigate to **Reports → AP Aging**
2. Select date range
3. Click **Generate Report**
4. Export to PDF/Excel if needed

---

## 5. Fixed Assets

### Register New Asset

1. Navigate to **Assets → Assets → Create New**
2. Fill in asset details:
   - Name: Asset name
   - Category: Select asset category
   - Acquisition Cost: Purchase price
   - Purchase Date: Date acquired
   - Useful Life: Months of useful life
   - Depreciation Method: Straight-line or Declining Balance
   - Dimensions: Project, Fund, Department
   - Location: Physical location
3. Click **Save**

### Run Depreciation

1. Navigate to **Assets → Depreciation Runs → Create New**
2. Select period (YYYY-MM)
3. Review depreciation preview
4. Click **Run Depreciation**
5. Review generated journal entries
6. Click **Post** to create journal entries

### Dispose Asset

1. Navigate to **Assets → Asset Disposals → Create New**
2. Fill in disposal details:
   - Asset: Select asset to dispose
   - Disposal Date: Date of disposal
   - Disposal Type: Sale, Scrap, Donation, etc.
   - Disposal Proceeds: Amount received (if any)
   - Disposal Reason: Reason for disposal
3. Review gain/loss calculation
4. Click **Save** and **Post**

### Asset Reports

- **Asset Register**: Complete asset listing
- **Depreciation Schedule**: Monthly depreciation by asset
- **Disposal Summary**: Disposed assets with gains/losses
- **Movement Log**: Asset transfer history

---

## 6. Reports

### Trial Balance

1. Navigate to **Reports → Trial Balance**
2. Select date (month-end)
3. Click **Generate Report**
4. Verify debits equal credits
5. Export to PDF/Excel

### General Ledger Detail

1. Navigate to **Reports → GL Detail**
2. Select parameters:
   - Date range
   - Account(s)
   - Dimensions (optional)
3. Click **Generate Report**
4. Review transaction details
5. Export if needed

### Cash Ledger

1. Navigate to **Reports → Cash Ledger**
2. Select bank account
3. Select date range
4. Click **Generate Report**
5. Compare with bank statement

### Export Options

- **PDF**: For printing and archiving
- **Excel**: For analysis and manipulation
- **CSV**: For data import/export

---

## 7. Common Issues

### Journal Entry Not Balanced

**Problem**: Error message "Journal entry not balanced"
**Solution**:

- Check that total debits equal total credits
- Verify all amounts are entered correctly
- Ensure no negative amounts where not allowed

### Cannot Post to Closed Period

**Problem**: Error "Cannot post to closed period"
**Solution**:

- Check if the period is closed
- Contact approver to reopen period if needed
- Use correct transaction date

### Customer/Vendor Not Found

**Problem**: Cannot find customer or vendor in dropdown
**Solution**:

- Check if customer/vendor is active
- Verify spelling of name
- Contact admin to add new customer/vendor

### Asset Depreciation Not Calculating

**Problem**: Depreciation amounts are zero or incorrect
**Solution**:

- Verify asset is active and not disposed
- Check asset acquisition date and useful life
- Ensure depreciation method is set correctly

### Report Data Missing

**Problem**: Reports show incomplete or missing data
**Solution**:

- Check date range selection
- Verify user has permission to view data
- Ensure data has been posted to GL

### System Performance Issues

**Problem**: System is slow or unresponsive
**Solution**:

- Check internet connection
- Clear browser cache
- Try different browser
- Contact IT support if problem persists

---

## Keyboard Shortcuts

### General Navigation

- **Ctrl + S**: Save current form
- **Ctrl + N**: New record
- **Ctrl + F**: Find/Search
- **Esc**: Cancel current operation
- **Tab**: Move to next field
- **Shift + Tab**: Move to previous field

### Data Entry

- **Ctrl + C**: Copy
- **Ctrl + V**: Paste
- **Ctrl + Z**: Undo (where supported)
- **F2**: Edit current cell/field
- **Enter**: Confirm entry

### Reports

- **Ctrl + P**: Print report
- **Ctrl + E**: Export report
- **F5**: Refresh report
- **Ctrl + F**: Find in report

---

## Contact Information

### System Support

- **IT Support**: it-support@company.com
- **System Admin**: admin@company.com
- **Training Team**: training@company.com

### Emergency Contacts

- **System Down**: Call IT support immediately
- **Data Issues**: Contact system admin
- **Security Concerns**: Report to IT security team

### Help Resources

- **User Manual**: Available in system help section
- **Training Materials**: Check training portal
- **Video Tutorials**: Available on company intranet
- **FAQ**: Frequently asked questions section

---

## Best Practices Summary

### Data Entry

- ✅ Always verify balances before posting
- ✅ Use descriptive memos and descriptions
- ✅ Assign dimensions when applicable
- ✅ Review all entries before saving
- ❌ Don't post without reviewing
- ❌ Don't use generic descriptions

### Security

- ✅ Use strong passwords
- ✅ Logout when finished
- ✅ Only access functions appropriate to your role
- ✅ Report suspicious activity
- ❌ Don't share login credentials
- ❌ Don't leave system unattended

### Workflow

- ✅ Follow approval chains
- ✅ Process transactions promptly
- ✅ Communicate with team members
- ✅ Keep documentation current
- ❌ Don't bypass approval processes
- ❌ Don't leave transactions in draft status

### Reporting

- ✅ Generate reports on schedule
- ✅ Verify data before distribution
- ✅ Use appropriate export formats
- ✅ Archive reports for reference
- ❌ Don't distribute unverified reports
- ❌ Don't ignore report discrepancies

---

**Remember**: When in doubt, ask for help. It's better to ask questions than to make mistakes that could affect the organization's financial records.

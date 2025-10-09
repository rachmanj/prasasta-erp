# Purchase Invoice & Payment Test Scenarios

## Overview

This document provides comprehensive story-based test scenarios for the enhanced Purchase Invoice and Purchase Payment features, including their integration with the accounting system and journal entry generation.

## Test Environment Setup

-   **Company**: PT Prasasta Education Center
-   **User**: Budi Santoso (Accountant)
-   **Date**: September 30, 2025
-   **Currency**: Indonesian Rupiah (IDR)
-   **Tax Rate**: PPN 11%

---

## Scenario 1: Complete Purchase Invoice Workflow with Enhanced Features

### Story Context

PT Prasasta Education Center needs to purchase office supplies from PT Office Supplies. The accountant will create a purchase invoice using the enhanced features including PO integration, discount functionality, and automatic calculations.

### Test Steps

#### Step 1: Create Purchase Invoice

1. **Navigate**: Accounting → Purchase → Purchase Invoices → Create
2. **Fill Header Information**:
    - **Invoice Number**: Auto-generated (PINV-202509-000001)
    - **Date**: 2025-09-30
    - **Reference Number**: REF-2025-001
    - **Vendor**: PT Office Supplies
    - **PO Number**: PO-202509-000001 (if available)
    - **Due Date**: Auto-calculated (30 days = 2025-10-30)
    - **Terms**: 30 days
    - **Notes**: Office supplies for Q4 2025

#### Step 2: Add Invoice Lines with Discount

1. **Line 1**:

    - **Item**: A4 Paper (500 sheets)
    - **Quantity**: 10
    - **Unit Price**: Rp 50,000
    - **Discount %**: 10%
    - **Discount Amount**: Rp 50,000
    - **Amount after Discount**: Rp 450,000
    - **VAT**: Rp 49,500 (11%)
    - **Total**: Rp 499,500

2. **Line 2**:
    - **Item**: Ballpoint Pens
    - **Quantity**: 50
    - **Unit Price**: Rp 5,000
    - **Discount %**: 5%
    - **Discount Amount**: Rp 12,500
    - **Amount after Discount**: Rp 237,500
    - **VAT**: Rp 26,125 (11%)
    - **Total**: Rp 263,625

#### Step 3: Review Totals

-   **Subtotal**: Rp 687,500
-   **Total Discount**: Rp 62,500
-   **Amount after Discount**: Rp 625,000
-   **VAT (11%)**: Rp 68,750
-   **Withholding Tax**: Rp 0
-   **Amount Due**: Rp 693,750

#### Step 4: Save and Post Invoice

1. Click "Save Invoice"
2. Click "Post Invoice"
3. Verify invoice status changes to "Posted"

### Expected Journal Entries

#### Journal Entry 1: Purchase Invoice Posting

```
Date: 2025-09-30
Description: Purchase Invoice PINV-202509-000001 - PT Office Supplies
Journal Number: JNL-202509-000001

Debit Entries:
- 5.1.1 Office Supplies Expense    Rp 625,000
- 2.1.1 PPN Masukan (Input Tax)    Rp 68,750

Credit Entries:
- 2.1.1 Accounts Payable           Rp 693,750

Total: Rp 693,750 (Balanced)
```

### Test Validation Points

-   [ ] Invoice number auto-generated correctly
-   [ ] Due date calculated automatically (30 days)
-   [ ] Discount calculations working properly
-   [ ] VAT calculated on discounted amount
-   [ ] Totals calculated correctly
-   [ ] Journal entry created with proper accounts
-   [ ] Double-entry bookkeeping maintained

---

## Scenario 2: Purchase Payment with Outstanding Invoices

### Story Context

PT Prasasta Education Center needs to make a payment to PT Office Supplies for outstanding invoices. The accountant will use the enhanced Purchase Payment feature to select outstanding invoices and make a cash payment.

### Test Steps

#### Step 1: Create Purchase Payment

1. **Navigate**: Accounting → Purchase → Purchase Payments → Create
2. **Fill Header Information**:
    - **Payment Number**: Auto-generated (PP-202509-000001)
    - **Date**: 2025-09-30
    - **Payment Method**: Cash
    - **Vendor**: PT Office Supplies
    - **Description**: Payment for outstanding invoices

#### Step 2: Load Outstanding Invoices

1. Click "Load Outstanding Invoices" button next to vendor dropdown
2. **Expected Modal Opens** with outstanding invoices:
    - **Invoice 1**: PINV-202509-000001
        - **PO No**: PO-202509-000001
        - **Invoice Date**: 2025-09-30
        - **Due Date**: 2025-10-30
        - **Original Amount**: Rp 693,750
        - **Outstanding**: Rp 693,750
        - **Days Past Due**: 0 (Current)
        - **Amount to Pay**: Rp 693,750

#### Step 3: Select Invoices for Payment

1. **Check the checkbox** for PINV-202509-000001
2. **Verify Amount to Pay** is set to Rp 693,750
3. **Click "Add Selected to Payment"**
4. **Modal closes** and payment lines are populated

#### Step 4: Review Payment Lines

-   **PO No**: PO-202509-000001
-   **Invoice No**: PINV-202509-000001
-   **Invoice Date**: 2025-09-30
-   **Due Date**: 2025-10-30
-   **Original Amount**: Rp 693,750
-   **Outstanding**: Rp 693,750
-   **Days Past Due**: Current
-   **Amount to Pay**: Rp 693,750
-   **Notes**: (Optional payment notes)

#### Step 5: Save and Post Payment

1. Click "Save Payment"
2. Click "Post Payment"
3. Verify payment status changes to "Posted"

### Expected Journal Entries

#### Journal Entry 2: Purchase Payment Posting

```
Date: 2025-09-30
Description: Purchase Payment PP-202509-000001 - PT Office Supplies
Journal Number: JNL-202509-000002

Debit Entries:
- 2.1.1 Accounts Payable           Rp 693,750

Credit Entries:
- 1.1.2.01 Cash in Hand            Rp 693,750

Total: Rp 693,750 (Balanced)
```

### Test Validation Points

-   [ ] Payment number auto-generated correctly
-   [ ] Outstanding invoices modal opens successfully
-   [ ] Invoice selection works properly
-   [ ] Payment lines populated correctly
-   [ ] Total payment amount calculated correctly
-   [ ] Journal entry created with proper accounts
-   [ ] Accounts Payable reduced by payment amount
-   [ ] Cash account reduced by payment amount

---

## Scenario 3: Partial Payment with Multiple Invoices

### Story Context

PT Prasasta Education Center has multiple outstanding invoices from PT Office Supplies and wants to make a partial payment covering some invoices.

### Test Steps

#### Step 1: Create Purchase Payment

1. **Navigate**: Accounting → Purchase → Purchase Payments → Create
2. **Fill Header Information**:
    - **Payment Number**: Auto-generated (PP-202509-000002)
    - **Date**: 2025-09-30
    - **Payment Method**: Bank Transfer
    - **Bank Account**: 1.1.2.01 - Bank BCA
    - **Reference Number**: TRF-2025-001
    - **Vendor**: PT Office Supplies
    - **Description**: Partial payment for multiple invoices

#### Step 2: Load Outstanding Invoices

1. Click "Load Outstanding Invoices" button
2. **Expected Modal Opens** with multiple outstanding invoices:
    - **Invoice 1**: PINV-202509-000001 (Rp 693,750 outstanding)
    - **Invoice 2**: PINV-202509-000002 (Rp 1,200,000 outstanding)
    - **Invoice 3**: PINV-202509-000003 (Rp 850,000 outstanding)

#### Step 3: Select Multiple Invoices with Partial Amounts

1. **Select Invoice 1**: Full payment (Rp 693,750)
2. **Select Invoice 2**: Partial payment (Rp 600,000)
3. **Leave Invoice 3**: Unselected
4. **Total Selected**: Rp 1,293,750
5. Click "Add Selected to Payment"

#### Step 4: Review Payment Lines

-   **Line 1**: PINV-202509-000001 (Full payment: Rp 693,750)
-   **Line 2**: PINV-202509-000002 (Partial payment: Rp 600,000)
-   **Total Payment**: Rp 1,293,750

#### Step 5: Save and Post Payment

1. Click "Save Payment"
2. Click "Post Payment"

### Expected Journal Entries

#### Journal Entry 3: Partial Payment Posting

```
Date: 2025-09-30
Description: Purchase Payment PP-202509-000002 - PT Office Supplies
Journal Number: JNL-202509-000003

Debit Entries:
- 2.1.1 Accounts Payable           Rp 1,293,750

Credit Entries:
- 1.1.2.01 Bank BCA                Rp 1,293,750

Total: Rp 1,293,750 (Balanced)
```

### Test Validation Points

-   [ ] Multiple invoice selection works
-   [ ] Partial payment amounts accepted
-   [ ] Total payment calculated correctly
-   [ ] Bank account field shown for bank transfer
-   [ ] Reference number field shown for bank transfer
-   [ ] Journal entry created with proper accounts

---

## Scenario 4: Check Payment Method

### Story Context

PT Prasasta Education Center needs to make a payment using a check to PT Office Supplies.

### Test Steps

#### Step 1: Create Purchase Payment

1. **Navigate**: Accounting → Purchase → Purchase Payments → Create
2. **Fill Header Information**:
    - **Payment Number**: Auto-generated (PP-202509-000003)
    - **Date**: 2025-09-30
    - **Payment Method**: Check
    - **Bank Account**: 1.1.2.01 - Bank BCA
    - **Check Number**: CHK-2025-001
    - **Vendor**: PT Office Supplies
    - **Description**: Check payment for outstanding invoice

#### Step 2: Load and Select Outstanding Invoice

1. Click "Load Outstanding Invoices"
2. Select remaining outstanding invoice
3. Add to payment

#### Step 3: Save and Post Payment

1. Click "Save Payment"
2. Click "Post Payment"

### Expected Journal Entries

#### Journal Entry 4: Check Payment Posting

```
Date: 2025-09-30
Description: Purchase Payment PP-202509-000003 - PT Office Supplies
Journal Number: JNL-202509-000004

Debit Entries:
- 2.1.1 Accounts Payable           Rp 850,000

Credit Entries:
- 1.1.2.01 Bank BCA                Rp 850,000

Total: Rp 850,000 (Balanced)
```

### Test Validation Points

-   [ ] Check number field shown for check payment
-   [ ] Bank account field shown for check payment
-   [ ] Journal entry created with proper accounts

---

## Scenario 5: Integration Testing - Complete Workflow

### Story Context

Complete end-to-end testing of Purchase Invoice creation, posting, and payment processing with accounting integration.

### Test Steps

#### Step 1: Create and Post Purchase Invoice

1. Create purchase invoice as in Scenario 1
2. Post the invoice
3. **Verify**: Accounts Payable increased by Rp 693,750

#### Step 2: Create and Post Purchase Payment

1. Create purchase payment as in Scenario 2
2. Post the payment
3. **Verify**: Accounts Payable reduced by Rp 693,750

#### Step 3: Verify Accounting Integration

1. **Navigate**: Accounting → Reports → Trial Balance
2. **Check Accounts**:
    - **2.1.1 Accounts Payable**: Should show net zero (if all invoices paid)
    - **1.1.2.01 Cash in Hand**: Should show reduction
    - **5.1.1 Office Supplies Expense**: Should show expense
    - **2.1.1 PPN Masukan**: Should show input tax

#### Step 4: Verify GL Detail Report

1. **Navigate**: Accounting → Reports → GL Detail
2. **Filter by Date**: 2025-09-30
3. **Verify Journal Entries**:
    - Purchase Invoice posting
    - Purchase Payment posting
    - Proper double-entry bookkeeping

### Expected Final Account Balances

```
Account Code    Account Name              Debit        Credit
1.1.2.01       Cash in Hand              Rp 693,750
5.1.1          Office Supplies Expense   Rp 625,000
2.1.1          PPN Masukan               Rp 68,750
2.1.1          Accounts Payable                          Rp 0
```

### Test Validation Points

-   [ ] Complete workflow works end-to-end
-   [ ] All journal entries created correctly
-   [ ] Trial Balance shows correct balances
-   [ ] GL Detail shows all transactions
-   [ ] Double-entry bookkeeping maintained
-   [ ] Accounts Payable properly managed

---

## Scenario 6: Error Handling and Validation

### Story Context

Testing error handling, validation, and edge cases in the Purchase Invoice and Payment workflows.

### Test Steps

#### Step 1: Test Required Field Validation

1. Try to save purchase invoice without required fields
2. **Expected**: Validation errors displayed
3. **Required Fields**: Date, Vendor, at least one line item

#### Step 2: Test Payment Method Validation

1. Select "Check" payment method
2. **Expected**: Check Number field appears
3. Try to save without check number
4. **Expected**: Validation error

#### Step 3: Test Outstanding Invoice Selection

1. Create payment without selecting vendor
2. Click "Load Outstanding Invoices"
3. **Expected**: Warning message "Please select a vendor first"

#### Step 4: Test Payment Amount Validation

1. Select outstanding invoice
2. Try to enter payment amount greater than outstanding
3. **Expected**: Warning message "Payment amount cannot exceed outstanding amount"

#### Step 5: Test Empty Outstanding Invoices

1. Select vendor with no outstanding invoices
2. Click "Load Outstanding Invoices"
3. **Expected**: Modal shows "No outstanding invoices found for this vendor"

### Test Validation Points

-   [ ] Required field validation works
-   [ ] Payment method validation works
-   [ ] Outstanding invoice validation works
-   [ ] Payment amount validation works
-   [ ] Empty state handling works
-   [ ] Error messages are user-friendly

---

## Scenario 7: Performance and Usability Testing

### Story Context

Testing performance, usability, and user experience aspects of the enhanced features.

### Test Steps

#### Step 1: Test Modal Performance

1. Select vendor with many outstanding invoices
2. Click "Load Outstanding Invoices"
3. **Expected**: Modal opens quickly (< 2 seconds)
4. **Expected**: All invoices displayed properly

#### Step 2: Test Real-time Calculations

1. Change payment amounts in modal
2. **Expected**: Total selected amount updates immediately
3. **Expected**: Indonesian Rupiah formatting applied

#### Step 3: Test Responsive Design

1. Test on different screen sizes
2. **Expected**: Modal and forms adapt properly
3. **Expected**: All functionality works on mobile

#### Step 4: Test Keyboard Navigation

1. Use Tab key to navigate through form
2. **Expected**: Proper tab order
3. **Expected**: All fields accessible via keyboard

### Test Validation Points

-   [ ] Modal performance is acceptable
-   [ ] Real-time calculations work smoothly
-   [ ] Responsive design works properly
-   [ ] Keyboard navigation works
-   [ ] User experience is intuitive

---

## Summary of Expected Journal Entries

### Complete Workflow Journal Entries

#### 1. Purchase Invoice Posting

```
Debit:  5.1.1 Office Supplies Expense    Rp 625,000
Debit:  2.1.1 PPN Masukan                Rp 68,750
Credit: 2.1.1 Accounts Payable           Rp 693,750
```

#### 2. Purchase Payment Posting (Cash)

```
Debit:  2.1.1 Accounts Payable           Rp 693,750
Credit: 1.1.2.01 Cash in Hand            Rp 693,750
```

#### 3. Purchase Payment Posting (Bank Transfer)

```
Debit:  2.1.1 Accounts Payable           Rp 1,293,750
Credit: 1.1.2.01 Bank BCA                Rp 1,293,750
```

#### 4. Purchase Payment Posting (Check)

```
Debit:  2.1.1 Accounts Payable           Rp 850,000
Credit: 1.1.2.01 Bank BCA                Rp 850,000
```

### Account Impact Summary

-   **Office Supplies Expense**: Increased by Rp 625,000
-   **PPN Masukan**: Increased by Rp 68,750
-   **Accounts Payable**: Net zero (increased by Rp 693,750, decreased by Rp 693,750)
-   **Cash in Hand**: Decreased by Rp 693,750
-   **Bank BCA**: Decreased by Rp 2,143,750

### Key Integration Points

1. **Purchase Invoice** → **Accounts Payable** (Credit)
2. **Purchase Payment** → **Accounts Payable** (Debit)
3. **Purchase Payment** → **Cash/Bank** (Credit)
4. **Expense Recognition** → **Office Supplies Expense** (Debit)
5. **Tax Recognition** → **PPN Masukan** (Debit)

---

## Test Execution Checklist

### Pre-Test Setup

-   [ ] Ensure test data is available
-   [ ] Verify user permissions
-   [ ] Check system date and time
-   [ ] Verify chart of accounts setup

### Test Execution

-   [ ] Execute Scenario 1: Purchase Invoice Workflow
-   [ ] Execute Scenario 2: Purchase Payment with Outstanding Invoices
-   [ ] Execute Scenario 3: Partial Payment with Multiple Invoices
-   [ ] Execute Scenario 4: Check Payment Method
-   [ ] Execute Scenario 5: Complete Integration Testing
-   [ ] Execute Scenario 6: Error Handling and Validation
-   [ ] Execute Scenario 7: Performance and Usability Testing

### Post-Test Validation

-   [ ] Verify all journal entries created correctly
-   [ ] Check trial balance for accuracy
-   [ ] Validate GL detail report
-   [ ] Confirm accounts payable reconciliation
-   [ ] Test report generation and export

### Success Criteria

-   [ ] All scenarios execute without errors
-   [ ] Journal entries are properly balanced
-   [ ] Accounting integration works correctly
-   [ ] User interface is intuitive and responsive
-   [ ] Error handling is comprehensive
-   [ ] Performance meets requirements

---

## Notes for Testers

1. **Data Requirements**: Ensure test vendors and items exist in the system
2. **Permissions**: Verify user has appropriate permissions for invoice and payment creation
3. **Date Settings**: Use consistent dates throughout testing
4. **Currency**: All amounts in Indonesian Rupiah with proper formatting
5. **Tax Compliance**: Follow Indonesian PPN (11%) tax requirements
6. **Documentation**: Record any issues or observations during testing

This comprehensive test scenario document ensures thorough validation of the enhanced Purchase Invoice and Purchase Payment features, including their integration with the accounting system and proper journal entry generation.

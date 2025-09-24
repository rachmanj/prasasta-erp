# Inventory Module Integration Test Scenarios

## Overview

This document provides comprehensive test scenarios to validate the integration of the Inventory module with Purchase and Sales workflows, including journal entries, quantity tracking, and inventory valuation.

## Test Environment Setup

### Prerequisites

1. **User Roles Setup:**

    - Accountant: Can create transactions and journals
    - Approver: Can approve transactions and post journals
    - Cashier: Can process payments and receipts

2. **Master Data:**

    - Inventory Categories: Office Supplies, Training Materials, Equipment Parts
    - Inventory Items: Both "Item" type (affects stock) and "Service" type (non-stock)
    - Suppliers: PT Komputer Maju, PT Office Supplies
    - Customers: PT Mandiri Sejahtera, Andi Pratama

3. **Chart of Accounts:**
    - Inventory Accounts: 1.1.10 (Raw Materials), 1.1.11 (Finished Goods), 1.1.12 (Supplies)
    - Cost of Goods Sold: 5.1.5
    - Revenue Accounts: 4.1.x series
    - AP/AR Accounts: 2.1.x series

---

## Scenario 1: Purchase Workflow with Inventory Items

### 1.1 Create Purchase Order (PO) with Mixed Item Types

**Objective:** Test PO creation with both inventory items and services

**Steps:**

1. Login as Accountant
2. Navigate to Purchase → Purchase Orders → Create
3. Create PO with following details:

    ```
    Supplier: PT Komputer Maju
    Date: 2025-01-25
    Reference: PO-TEST-001

    Line Items:
    Line 1: Laptop Dell (Item Type) - Qty: 5 - Price: Rp 15,000,000 each
    Line 2: Laptop Setup Service (Service Type) - Qty: 5 - Price: Rp 500,000 each
    Line 3: Office Chair (Item Type) - Qty: 10 - Price: Rp 2,500,000 each
    ```

**Expected Results:**

-   PO created successfully with mixed item types
-   Items show in PO lines with proper categorization
-   Services show as non-inventory items
-   Total PO value: Rp 95,000,000

### 1.2 Receive Goods (GR) - Partial Receipt

**Objective:** Test goods receipt with partial quantities

**Steps:**

1. Navigate to Purchase → Goods Receipts → Create
2. Reference the PO created in 1.1
3. Receive partial quantities:
    ```
    Laptop Dell: Received 3 out of 5
    Office Chair: Received 8 out of 10
    Laptop Setup Service: Received 3 out of 5
    ```

**Expected Results:**

-   GR created with partial quantities
-   Inventory quantities updated for Item types only
-   Service quantities tracked but don't affect inventory
-   Stock layers created with FIFO costing

### 1.3 Create Purchase Invoice (PINV)

**Objective:** Test purchase invoice creation and journal posting

**Steps:**

1. Navigate to Purchase → Purchase Invoices → Create
2. Reference the GR from 1.2
3. Create invoice with received quantities:
    ```
    Laptop Dell: 3 units × Rp 15,000,000 = Rp 45,000,000
    Office Chair: 8 units × Rp 2,500,000 = Rp 20,000,000
    Laptop Setup Service: 3 units × Rp 500,000 = Rp 1,500,000
    PPN 11%: Rp 7,315,000
    Total: Rp 73,815,000
    ```

**Expected Journal Entries (when approved):**

```
Dr. Inventory - Finished Goods (1.1.11)    Rp 45,000,000
Dr. Inventory - Supplies (1.1.12)          Rp 20,000,000
Dr. Service Expenses (6.1.1)               Rp 1,500,000
Dr. PPN Masukan (2.1.1)                    Rp 7,315,000
    Cr. Accounts Payable (2.1.3)                       Rp 73,815,000
```

### 1.4 Process Purchase Payment

**Objective:** Test payment processing and journal entries

**Steps:**

1. Navigate to Purchase → Purchase Payments → Create
2. Reference the PINV from 1.3
3. Create payment for full amount: Rp 73,815,000

**Expected Journal Entries:**

```
Dr. Accounts Payable (2.1.3)    Rp 73,815,000
    Cr. Cash/Bank (1.1.1)                  Rp 73,815,000
```

---

## Scenario 2: Sales Workflow with Inventory Items

### 2.1 Create Sales Order (SO) with Mixed Item Types

**Objective:** Test SO creation with inventory and service items

**Steps:**

1. Login as Accountant
2. Navigate to Sales → Sales Orders → Create
3. Create SO with following details:

    ```
    Customer: PT Mandiri Sejahtera
    Date: 2025-01-26
    Reference: SO-TEST-001

    Line Items:
    Line 1: Laptop Dell (Item Type) - Qty: 2 - Price: Rp 18,000,000 each
    Line 2: Laptop Setup Service (Service Type) - Qty: 2 - Price: Rp 750,000 each
    Line 3: Office Chair (Item Type) - Qty: 5 - Price: Rp 3,000,000 each
    ```

**Expected Results:**

-   SO created successfully
-   Available stock displayed for Item types
-   Service items show without stock information
-   Total SO value: Rp 46,500,000

### 2.2 Create Sales Invoice (SINV)

**Objective:** Test sales invoice creation and journal posting

**Steps:**

1. Navigate to Sales → Sales Invoices → Create
2. Reference the SO from 2.1
3. Create invoice:
    ```
    Laptop Dell: 2 units × Rp 18,000,000 = Rp 36,000,000
    Office Chair: 5 units × Rp 3,000,000 = Rp 15,000,000
    Laptop Setup Service: 2 units × Rp 750,000 = Rp 1,500,000
    PPN 11%: Rp 5,775,000
    Total: Rp 58,275,000
    ```

**Expected Journal Entries (when approved):**

```
Dr. Accounts Receivable (1.1.4)           Rp 58,275,000
    Cr. Revenue - Equipment Sales (4.1.1)            Rp 36,000,000
    Cr. Revenue - Furniture Sales (4.1.2)            Rp 15,000,000
    Cr. Revenue - Services (4.1.3)                   Rp 1,500,000
    Cr. PPN Keluaran (2.1.2)                         Rp 5,775,000

Dr. Cost of Goods Sold (5.1.5)            Rp 30,000,000
    Cr. Inventory - Finished Goods (1.1.11)          Rp 30,000,000
```

### 2.3 Process Sales Receipt

**Objective:** Test receipt processing

**Steps:**

1. Navigate to Sales → Sales Receipts → Create
2. Reference the SINV from 2.2
3. Create receipt for full amount: Rp 58,275,000

**Expected Journal Entries:**

```
Dr. Cash/Bank (1.1.1)           Rp 58,275,000
    Cr. Accounts Receivable (1.1.4)                 Rp 58,275,000
```

---

## Scenario 3: Inventory Valuation and FIFO Testing

### 3.1 Multiple Purchase Orders with Different Prices

**Objective:** Test FIFO costing with multiple purchase layers

**Steps:**

1. Create PO-001 for Laptop Dell: 5 units @ Rp 15,000,000
2. Create PO-002 for Laptop Dell: 3 units @ Rp 16,000,000
3. Receive both POs completely
4. Create sales for 6 units

**Expected Results:**

-   First 5 units sold @ Rp 15,000,000 (COGS: Rp 75,000,000)
-   Next 1 unit sold @ Rp 16,000,000 (COGS: Rp 16,000,000)
-   Remaining 2 units in inventory @ Rp 16,000,000 each
-   Total COGS: Rp 91,000,000
-   Remaining inventory value: Rp 32,000,000

### 3.2 Stock Adjustment Testing

**Objective:** Test stock adjustment impact on valuation

**Steps:**

1. Create stock adjustment for Laptop Dell:
    ```
    Reason: Physical count discrepancy
    Current stock: 2 units
    Adjusted stock: 1 unit
    Unit cost: Rp 16,000,000
    ```
2. Approve the adjustment

**Expected Results:**

-   Stock quantity reduced by 1 unit
-   Journal entry for inventory write-down:
    ```
    Dr. Inventory Adjustment Loss (6.2.1)    Rp 16,000,000
        Cr. Inventory - Finished Goods (1.1.11)         Rp 16,000,000
    ```

---

## Scenario 4: Service Items Integration Testing

### 4.1 Service-Only Purchase Order

**Objective:** Test service items don't affect inventory

**Steps:**

1. Create PO with only service items:
    ```
    Training Services: 10 hours @ Rp 500,000/hour
    Consulting Services: 5 hours @ Rp 1,000,000/hour
    ```
2. Create purchase invoice for services
3. Process payment

**Expected Results:**

-   No inventory quantities affected
-   Services tracked in separate accounts
-   Journal entries:
    ```
    Dr. Training Expenses (6.1.2)           Rp 5,000,000
    Dr. Consulting Expenses (6.1.3)         Rp 5,000,000
    Dr. PPN Masukan (2.1.1)                 Rp 1,100,000
        Cr. Accounts Payable (2.1.3)                   Rp 11,100,000
    ```

### 4.2 Service-Only Sales Order

**Objective:** Test service sales integration

**Steps:**

1. Create SO with only services:
    ```
    Training Services: 8 hours @ Rp 750,000/hour
    Consulting Services: 4 hours @ Rp 1,500,000/hour
    ```
2. Create sales invoice
3. Process receipt

**Expected Results:**

-   No inventory impact
-   Revenue recognized for services
-   Journal entries:
    ```
    Dr. Accounts Receivable (1.1.4)         Rp 12,100,000
        Cr. Revenue - Training (4.1.4)                 Rp 6,000,000
        Cr. Revenue - Consulting (4.1.5)               Rp 6,000,000
        Cr. PPN Keluaran (2.1.2)                       Rp 1,100,000
    ```

---

## Scenario 5: Complete Business Cycle Testing

### 5.1 Full Purchase-to-Sales Cycle

**Objective:** Test complete business cycle with inventory tracking

**Data Setup:**

```
Purchase: 10 units @ Rp 10,000,000 each = Rp 100,000,000
Sales: 7 units @ Rp 15,000,000 each = Rp 105,000,000
Remaining: 3 units in inventory
```

**Expected Results:**

-   Purchase COGS: Rp 70,000,000 (7 units sold)
-   Remaining inventory value: Rp 30,000,000 (3 units)
-   Gross profit: Rp 35,000,000 (105,000,000 - 70,000,000)
-   Inventory turnover tracking
-   FIFO layer management

---

## Validation Checklist

### Journal Entries Validation

-   [ ] All inventory purchases create proper inventory account debits
-   [ ] Service purchases create expense account debits
-   [ ] Sales create revenue credits and COGS debits
-   [ ] PPN calculations are correct (11%)
-   [ ] Inventory adjustments create proper journal entries
-   [ ] Payments and receipts create cash/bank journal entries

### Quantity Tracking Validation

-   [ ] Item types update inventory quantities
-   [ ] Service types don't affect inventory
-   [ ] FIFO layers are created correctly
-   [ ] Stock adjustments update quantities properly
-   [ ] Stock movements are tracked accurately

### Inventory Valuation Validation

-   [ ] FIFO costing calculates COGS correctly
-   [ ] Average cost is calculated properly
-   [ ] Inventory valuation matches stock layers
-   [ ] Stock adjustments affect valuation
-   [ ] Valuation reports show correct values

### Integration Validation

-   [ ] PO lines link to inventory items
-   [ ] SO lines show available stock
-   [ ] GR updates inventory quantities
-   [ ] PINV/SINV create proper journals
-   [ ] Payments/receipts complete the cycle

---

## Test Execution Notes

1. **User Roles:** Use different user roles to test permission-based access
2. **Data Verification:** Check database tables after each transaction
3. **Report Validation:** Generate inventory reports to verify data accuracy
4. **Error Handling:** Test with invalid data to ensure proper error handling
5. **Performance:** Monitor system performance with large quantities

## Expected Database Impact

### Tables to Monitor:

-   `items` - Inventory quantities and values
-   `stock_layers` - FIFO costing layers
-   `stock_movements` - Movement tracking
-   `journals` & `journal_lines` - Journal entries
-   `purchase_order_lines` - PO integration
-   `sales_order_lines` - SO integration
-   `purchase_invoice_lines` - PINV integration
-   `sales_invoice_lines` - SINV integration

This comprehensive testing ensures the Inventory module integrates seamlessly with Purchase and Sales workflows while maintaining accurate financial records and inventory tracking.

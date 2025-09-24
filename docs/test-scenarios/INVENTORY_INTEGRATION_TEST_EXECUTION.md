# Inventory Integration Test Execution Guide

## Quick Setup for Testing

### Step 1: Verify Test Data Exists

**Check Inventory Items:**

```sql
-- Verify we have both Item and Service types
SELECT id, code, name, type, category_id, current_stock_quantity, current_stock_value
FROM items
WHERE is_active = 1
ORDER BY type, name;
```

**Check Categories:**

```sql
-- Verify inventory categories
SELECT id, code, name FROM inventory_categories WHERE is_active = 1;
```

**Check Suppliers/Customers:**

```sql
-- Verify suppliers
SELECT id, code, name FROM vendors WHERE is_active = 1 LIMIT 5;

-- Verify customers
SELECT id, code, name FROM customers WHERE is_active = 1 LIMIT 5;
```

### Step 2: Create Test Data (if needed)

**Create Test Items:**

1. Navigate to Inventory → Items → Create
2. Create these test items:

    ```
    Item 1: LAPTOP-DELL-001
    - Name: Laptop Dell Inspiron
    - Type: Item (affects stock)
    - Category: Equipment Parts
    - Unit: pcs
    - Min Stock: 2
    - Last Cost: 15,000,000

    Item 2: CHAIR-OFFICE-001
    - Name: Office Chair Ergonomic
    - Type: Item (affects stock)
    - Category: Office Supplies
    - Unit: pcs
    - Min Stock: 5
    - Last Cost: 2,500,000

    Item 3: SERVICE-SETUP-001
    - Name: Laptop Setup Service
    - Type: Service (non-stock)
    - Category: Training Materials
    - Unit: hours
    - Last Cost: 500,000
    ```

---

## Test Scenario 1: Purchase Workflow Integration

### 1.1 Create Purchase Order

**Steps:**

1. Login as Accountant (budi@prasasta.com)
2. Navigate to Purchase → Purchase Orders → Create
3. Fill form:

    ```
    Supplier: PT Komputer Maju
    Date: Today's date
    Reference: PO-TEST-001

    Line Items:
    Line 1: LAPTOP-DELL-001 - Qty: 5 - Price: 15,000,000
    Line 2: CHAIR-OFFICE-001 - Qty: 10 - Price: 2,500,000
    Line 3: SERVICE-SETUP-001 - Qty: 5 - Price: 500,000
    ```

**Expected Results:**

-   PO total: Rp 95,000,000
-   Items show with proper categorization
-   Services show as non-inventory

**Validation Query:**

```sql
SELECT pol.*, i.name, i.type
FROM purchase_order_lines pol
JOIN items i ON pol.item_id = i.id
WHERE pol.purchase_order_id = [PO_ID];
```

### 1.2 Receive Goods (Partial)

**Steps:**

1. Navigate to Purchase → Goods Receipts → Create
2. Reference the PO from step 1.1
3. Receive partial quantities:
    ```
    LAPTOP-DELL-001: 3 out of 5
    CHAIR-OFFICE-001: 8 out of 10
    SERVICE-SETUP-001: 3 out of 5
    ```

**Expected Results:**

-   GR created with partial quantities
-   Stock layers created for Item types only
-   Services tracked but no inventory impact

**Validation Queries:**

```sql
-- Check inventory quantities updated
SELECT id, code, name, current_stock_quantity, current_stock_value
FROM items
WHERE code IN ('LAPTOP-DELL-001', 'CHAIR-OFFICE-001');

-- Check stock layers created
SELECT sl.*, i.name
FROM stock_layers sl
JOIN items i ON sl.item_id = i.id
WHERE sl.item_id IN (
    SELECT id FROM items WHERE code IN ('LAPTOP-DELL-001', 'CHAIR-OFFICE-001')
);

-- Check stock movements
SELECT sm.*, i.name
FROM stock_movements sm
JOIN items i ON sm.item_id = i.id
WHERE sm.movement_type = 'in' AND sm.item_id IN (
    SELECT id FROM items WHERE code IN ('LAPTOP-DELL-001', 'CHAIR-OFFICE-001')
);
```

### 1.3 Create Purchase Invoice

**Steps:**

1. Navigate to Purchase → Purchase Invoices → Create
2. Reference the GR from step 1.2
3. Create invoice with received quantities:
    ```
    LAPTOP-DELL-001: 3 × 15,000,000 = 45,000,000
    CHAIR-OFFICE-001: 8 × 2,500,000 = 20,000,000
    SERVICE-SETUP-001: 3 × 500,000 = 1,500,000
    PPN 11%: 7,315,000
    Total: 73,815,000
    ```

**Expected Results:**

-   Invoice created successfully
-   Ready for approval and journal posting

### 1.4 Approve Purchase Invoice (as Approver)

**Steps:**

1. Login as Approver (siti@prasasta.com)
2. Navigate to Purchase → Purchase Invoices
3. Find the invoice from step 1.3
4. Click "Approve" button

**Expected Journal Entries:**

```
Dr. Inventory - Finished Goods (1.1.11)    45,000,000
Dr. Inventory - Supplies (1.1.12)          20,000,000
Dr. Service Expenses (6.1.1)               1,500,000
Dr. PPN Masukan (2.1.1)                    7,315,000
    Cr. Accounts Payable (2.1.3)                      73,815,000
```

**Validation Query:**

```sql
-- Check journal entries created
SELECT j.journal_no, j.date, j.description,
       jl.description as line_desc, jl.debit, jl.credit, a.name as account_name
FROM journals j
JOIN journal_lines jl ON j.id = jl.journal_id
JOIN accounts a ON jl.account_id = a.id
WHERE j.reference_number = 'PINV-TEST-001'
ORDER BY j.id, jl.id;
```

### 1.5 Process Purchase Payment

**Steps:**

1. Navigate to Purchase → Purchase Payments → Create
2. Reference the approved PINV from step 1.4
3. Create payment for full amount: Rp 73,815,000

**Expected Journal Entries:**

```
Dr. Accounts Payable (2.1.3)    73,815,000
    Cr. Cash/Bank (1.1.1)                  73,815,000
```

---

## Test Scenario 2: Sales Workflow Integration

### 2.1 Create Sales Order

**Steps:**

1. Login as Accountant
2. Navigate to Sales → Sales Orders → Create
3. Fill form:

    ```
    Customer: PT Mandiri Sejahtera
    Date: Today's date
    Reference: SO-TEST-001

    Line Items:
    Line 1: LAPTOP-DELL-001 - Qty: 2 - Price: 18,000,000
    Line 2: CHAIR-OFFICE-001 - Qty: 5 - Price: 3,000,000
    Line 3: SERVICE-SETUP-001 - Qty: 2 - Price: 750,000
    ```

**Expected Results:**

-   SO total: Rp 46,500,000
-   Available stock shown for Item types
-   Services show without stock info

### 2.2 Create Sales Invoice

**Steps:**

1. Navigate to Sales → Sales Invoices → Create
2. Reference the SO from step 2.1
3. Create invoice:
    ```
    LAPTOP-DELL-001: 2 × 18,000,000 = 36,000,000
    CHAIR-OFFICE-001: 5 × 3,000,000 = 15,000,000
    SERVICE-SETUP-001: 2 × 750,000 = 1,500,000
    PPN 11%: 5,775,000
    Total: 58,275,000
    ```

### 2.3 Approve Sales Invoice (as Approver)

**Steps:**

1. Login as Approver
2. Navigate to Sales → Sales Invoices
3. Find the invoice from step 2.2
4. Click "Approve" button

**Expected Journal Entries:**

```
Dr. Accounts Receivable (1.1.4)           58,275,000
    Cr. Revenue - Equipment Sales (4.1.1)            36,000,000
    Cr. Revenue - Furniture Sales (4.1.2)            15,000,000
    Cr. Revenue - Services (4.1.3)                   1,500,000
    Cr. PPN Keluaran (2.1.2)                         5,775,000

Dr. Cost of Goods Sold (5.1.5)            30,000,000
    Cr. Inventory - Finished Goods (1.1.11)          30,000,000
```

**Validation Query:**

```sql
-- Check COGS calculation (should use FIFO)
SELECT j.journal_no, jl.description, jl.debit, jl.credit, a.name
FROM journals j
JOIN journal_lines jl ON j.id = jl.journal_id
JOIN accounts a ON jl.account_id = a.id
WHERE j.reference_number = 'SINV-TEST-001'
AND a.name LIKE '%Cost of Goods Sold%';
```

### 2.4 Process Sales Receipt

**Steps:**

1. Navigate to Sales → Sales Receipts → Create
2. Reference the approved SINV from step 2.3
3. Create receipt for full amount: Rp 58,275,000

---

## Test Scenario 3: FIFO Costing Validation

### 3.1 Create Second Purchase Order

**Steps:**

1. Create another PO for LAPTOP-DELL-001:
    ```
    Supplier: PT Komputer Maju
    LAPTOP-DELL-001: Qty: 3 - Price: 16,000,000 (higher price)
    ```
2. Receive all 3 units
3. Create and approve PINV

**Expected Results:**

-   Stock layers: 3 units @ 15,000,000 + 3 units @ 16,000,000
-   Total inventory: 6 units, value: Rp 93,000,000

### 3.2 Create Sales with FIFO

**Steps:**

1. Create SO for 4 units of LAPTOP-DELL-001 @ Rp 20,000,000 each
2. Create and approve SINV

**Expected Results:**

-   COGS: 3 units @ 15,000,000 + 1 unit @ 16,000,000 = Rp 61,000,000
-   Remaining: 2 units @ 16,000,000 = Rp 32,000,000
-   Gross profit: Rp 80,000,000 - Rp 61,000,000 = Rp 19,000,000

**Validation Query:**

```sql
-- Check FIFO layers after sales
SELECT sl.*, i.name
FROM stock_layers sl
JOIN items i ON sl.item_id = i.id
WHERE i.code = 'LAPTOP-DELL-001'
ORDER BY sl.purchase_date, sl.id;
```

---

## Test Scenario 4: Stock Adjustment Testing

### 4.1 Create Stock Adjustment

**Steps:**

1. Navigate to Inventory → Stock Adjustments → Create
2. Create adjustment:

    ```
    Date: Today
    Reason: Physical count discrepancy

    Items:
    CHAIR-OFFICE-001: Current: 3, Adjusted: 1, Unit Cost: 2,500,000
    ```

3. Approve the adjustment

**Expected Results:**

-   Stock reduced by 2 units
-   Journal entry for inventory write-down:
    ```
    Dr. Inventory Adjustment Loss (6.2.1)    5,000,000
        Cr. Inventory - Supplies (1.1.12)            5,000,000
    ```

**Validation Query:**

```sql
-- Check inventory quantities after adjustment
SELECT id, code, name, current_stock_quantity, current_stock_value
FROM items
WHERE code = 'CHAIR-OFFICE-001';

-- Check stock movements
SELECT sm.*, i.name
FROM stock_movements sm
JOIN items i ON sm.item_id = i.id
WHERE i.code = 'CHAIR-OFFICE-001'
ORDER BY sm.movement_date DESC;
```

---

## Test Scenario 5: Service Items Validation

### 5.1 Service-Only Purchase

**Steps:**

1. Create PO with only services:
    ```
    SERVICE-SETUP-001: 10 hours @ 500,000/hour
    ```
2. Receive, invoice, and pay

**Expected Results:**

-   No inventory impact
-   Service expenses recorded
-   No stock layers created

**Validation Query:**

```sql
-- Verify no inventory impact
SELECT id, code, name, current_stock_quantity
FROM items
WHERE code = 'SERVICE-SETUP-001';

-- Check service expenses in journals
SELECT j.journal_no, jl.description, jl.debit, a.name
FROM journals j
JOIN journal_lines jl ON j.id = jl.journal_id
JOIN accounts a ON jl.account_id = a.id
WHERE a.name LIKE '%Service%' AND jl.debit > 0;
```

---

## Reporting Validation

### Generate Reports to Verify Integration

1. **Inventory Dashboard:**

    - Navigate to Inventory → Reports → Dashboard
    - Verify total items, inventory value, low stock alerts

2. **Stock Status Report:**

    - Navigate to Inventory → Reports → Stock Status
    - Verify quantities and values match transactions

3. **Stock Movement Report:**

    - Navigate to Inventory → Reports → Stock Movement
    - Verify all movements are tracked correctly

4. **Inventory Valuation Report:**

    - Navigate to Inventory → Reports → Inventory Valuation
    - Verify FIFO costing and valuation accuracy

5. **Trial Balance:**
    - Navigate to Reports → Accounting → Trial Balance
    - Verify inventory and COGS balances

---

## Expected Database State After All Tests

### Inventory Quantities:

```
LAPTOP-DELL-001: 2 units @ Rp 16,000,000 = Rp 32,000,000
CHAIR-OFFICE-001: 1 unit @ Rp 2,500,000 = Rp 2,500,000
SERVICE-SETUP-001: 0 (service, no inventory)
```

### Key Journal Entry Balances:

```
Inventory - Finished Goods (1.1.11): Dr 32,000,000
Inventory - Supplies (1.1.12): Dr 2,500,000
Cost of Goods Sold (5.1.5): Dr 91,000,000
Revenue - Equipment Sales (4.1.1): Cr 80,000,000
Revenue - Furniture Sales (4.1.2): Cr 15,000,000
Revenue - Services (4.1.3): Cr 1,500,000
```

### Stock Layers:

```
LAPTOP-DELL-001: 2 units @ Rp 16,000,000 (remaining from second purchase)
CHAIR-OFFICE-001: 1 unit @ Rp 2,500,000 (after adjustment)
```

This comprehensive test validates the complete integration of the Inventory module with Purchase and Sales workflows, ensuring accurate journal entries, quantity tracking, and inventory valuation using FIFO costing.

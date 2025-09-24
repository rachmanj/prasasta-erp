# Inventory Integration Test - Quick Reference Card

## 🎯 Test Objectives

Validate complete integration of Inventory module with Purchase and Sales workflows, including journal entries, quantity tracking, and FIFO valuation.

## 📋 Pre-Test Checklist

-   [ ] Login as Accountant (budi@prasasta.com)
-   [ ] Verify inventory items exist (Item and Service types)
-   [ ] Check suppliers and customers are available
-   [ ] Ensure Chart of Accounts is properly configured

## 🔄 Test Scenarios Summary

### Scenario 1: Purchase Workflow

| Step | Action                     | Expected Result                          |
| ---- | -------------------------- | ---------------------------------------- |
| 1.1  | Create PO with mixed items | PO total: Rp 95,000,000                  |
| 1.2  | Receive goods (partial)    | Stock layers created for Item types only |
| 1.3  | Create PINV                | Ready for approval                       |
| 1.4  | Approve PINV               | Journal entries posted                   |
| 1.5  | Process payment            | Payment journal created                  |

### Scenario 2: Sales Workflow

| Step | Action                     | Expected Result                      |
| ---- | -------------------------- | ------------------------------------ |
| 2.1  | Create SO with mixed items | Available stock shown for Item types |
| 2.2  | Create SINV                | Revenue and COGS calculated          |
| 2.3  | Approve SINV               | Journal entries posted               |
| 2.4  | Process receipt            | Receipt journal created              |

### Scenario 3: FIFO Costing

| Step | Action                   | Expected Result              |
| ---- | ------------------------ | ---------------------------- |
| 3.1  | Second PO (higher price) | New stock layer created      |
| 3.2  | Sales with FIFO          | COGS uses oldest costs first |

### Scenario 4: Stock Adjustment

| Step | Action            | Expected Result              |
| ---- | ----------------- | ---------------------------- |
| 4.1  | Create adjustment | Inventory write-down journal |

### Scenario 5: Service Items

| Step | Action                | Expected Result       |
| ---- | --------------------- | --------------------- |
| 5.1  | Service-only purchase | No inventory impact   |
| 5.2  | Service-only sales    | Revenue only, no COGS |

## 📊 Key Journal Entries to Verify

### Purchase Invoice (PINV) Approval

```
Dr. Inventory - Finished Goods (1.1.11)    45,000,000
Dr. Inventory - Supplies (1.1.12)          20,000,000
Dr. Service Expenses (6.1.1)               1,500,000
Dr. PPN Masukan (2.1.1)                    7,315,000
    Cr. Accounts Payable (2.1.3)                      73,815,000
```

### Sales Invoice (SINV) Approval

```
Dr. Accounts Receivable (1.1.4)           58,275,000
    Cr. Revenue - Equipment (4.1.1)                   36,000,000
    Cr. Revenue - Furniture (4.1.2)                   15,000,000
    Cr. Revenue - Services (4.1.3)                    1,500,000
    Cr. PPN Keluaran (2.1.2)                         5,775,000

Dr. Cost of Goods Sold (5.1.5)            30,000,000
    Cr. Inventory - Finished Goods (1.1.11)          30,000,000
```

## 🔍 Validation Queries

### Check Inventory Quantities

```sql
SELECT id, code, name, current_stock_quantity, current_stock_value
FROM items
WHERE code IN ('LAPTOP-DELL-001', 'CHAIR-OFFICE-001');
```

### Check Stock Layers (FIFO)

```sql
SELECT sl.*, i.name
FROM stock_layers sl
JOIN items i ON sl.item_id = i.id
WHERE i.code = 'LAPTOP-DELL-001'
ORDER BY sl.purchase_date;
```

### Check Journal Entries

```sql
SELECT j.journal_no, jl.description, jl.debit, jl.credit, a.name
FROM journals j
JOIN journal_lines jl ON j.id = jl.journal_id
JOIN accounts a ON jl.account_id = a.id
WHERE j.reference_number LIKE '%TEST%'
ORDER BY j.id, jl.id;
```

### Check Stock Movements

```sql
SELECT sm.*, i.name
FROM stock_movements sm
JOIN items i ON sm.item_id = i.id
WHERE i.code IN ('LAPTOP-DELL-001', 'CHAIR-OFFICE-001')
ORDER BY sm.movement_date DESC;
```

## 📈 Expected Final State

### Inventory Quantities

-   **LAPTOP-DELL-001:** 2 units @ Rp 16,000,000 = Rp 32,000,000
-   **CHAIR-OFFICE-001:** 1 unit @ Rp 2,500,000 = Rp 2,500,000
-   **SERVICE-SETUP-001:** 0 (service, no inventory)

### Key Account Balances

-   **Inventory - Finished Goods:** Dr Rp 32,000,000
-   **Inventory - Supplies:** Dr Rp 2,500,000
-   **Cost of Goods Sold:** Dr Rp 91,000,000
-   **Revenue - Equipment:** Cr Rp 80,000,000
-   **Revenue - Furniture:** Cr Rp 15,000,000
-   **Revenue - Services:** Cr Rp 1,500,000

## 🚨 Common Issues to Check

### Data Issues

-   [ ] Items not linked to inventory accounts
-   [ ] Incorrect item types (Item vs Service)
-   [ ] Missing stock layers after GR
-   [ ] Wrong COGS calculation (not FIFO)

### Permission Issues

-   [ ] Accountant cannot create transactions
-   [ ] Approver cannot approve invoices
-   [ ] Missing inventory permissions

### Integration Issues

-   [ ] PO lines not linked to items
-   [ ] GR not updating inventory
-   [ ] SINV not calculating COGS
-   [ ] Missing journal entries

## 📋 Test Completion Checklist

### Purchase Workflow

-   [ ] PO created with mixed item types
-   [ ] GR received partial quantities
-   [ ] PINV approved with correct journals
-   [ ] Payment processed successfully
-   [ ] Stock layers created properly

### Sales Workflow

-   [ ] SO created with available stock check
-   [ ] SINV approved with revenue and COGS journals
-   [ ] Receipt processed successfully
-   [ ] FIFO costing applied correctly

### Inventory Management

-   [ ] Stock adjustments working
-   [ ] Service items don't affect inventory
-   [ ] Reports showing correct data
-   [ ] All movements tracked properly

### Financial Integration

-   [ ] All journal entries created correctly
-   [ ] PPN calculations accurate (11%)
-   [ ] Trial balance balanced
-   [ ] Inventory valuation accurate

## 🎯 Success Criteria

✅ All transactions process without errors
✅ Journal entries match expected calculations
✅ FIFO costing works correctly
✅ Service items don't affect inventory
✅ Stock movements tracked accurately
✅ Reports show correct data
✅ Integration with Purchase/Sales seamless

## 📞 Support

If issues are found during testing:

1. Check database queries for data validation
2. Verify user permissions and roles
3. Check Chart of Accounts configuration
4. Validate item types and inventory accounts
5. Review journal entry calculations

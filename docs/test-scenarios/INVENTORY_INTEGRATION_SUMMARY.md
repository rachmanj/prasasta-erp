# Inventory Module Integration Summary

## Overview

This document summarizes how the Inventory module integrates with Purchase and Sales workflows, including the journal entries created, quantity movement tracking, and inventory valuation methods.

## Item Types and Their Impact

### 1. Item Type (affects stock quantity)

-   **Purpose:** Physical inventory items that need stock tracking
-   **Examples:** Laptops, chairs, office supplies, equipment
-   **Impact on:**
    -   Inventory quantities (current_stock_quantity)
    -   Stock layers (FIFO costing)
    -   Stock movements tracking
    -   Inventory valuation

### 2. Service Type (non-stock)

-   **Purpose:** Services that don't require physical inventory tracking
-   **Examples:** Training, consulting, setup services, maintenance
-   **Impact on:**
    -   Revenue/expense recognition
    -   No inventory quantities affected
    -   No stock layers created
    -   Direct expense/revenue posting

## Purchase Workflow Integration

### Purchase Order (PO)

```
Input: Items and Services with quantities and prices
Output: Purchase order document with line items
Impact: No journal entries (document only)
```

### Goods Receipt (GR)

```
Input: PO reference, received quantities
Output: Inventory updates for Item types only
Impact:
- Creates stock layers with FIFO costing
- Updates current_stock_quantity and current_stock_value
- Records stock movements (movement_type: 'in')
- No journal entries (inventory update only)
```

### Purchase Invoice (PINV)

```
Input: GR reference, invoice amounts
Output: Journal entries for inventory and expenses
Impact: Creates journal entries when approved:

For Item Types:
Dr. Inventory Account (1.1.10/1.1.11/1.1.12)    [Item Cost]
Dr. PPN Masukan (2.1.1)                         [PPN Amount]
    Cr. Accounts Payable (2.1.3)                          [Total Amount]

For Service Types:
Dr. Service Expense Account (6.1.x)              [Service Cost]
Dr. PPN Masukan (2.1.1)                         [PPN Amount]
    Cr. Accounts Payable (2.1.3)                          [Total Amount]
```

### Purchase Payment (PP)

```
Input: PINV reference, payment amount
Output: Payment journal entries
Impact:
Dr. Accounts Payable (2.1.3)    [Payment Amount]
    Cr. Cash/Bank Account (1.1.1)               [Payment Amount]
```

## Sales Workflow Integration

### Sales Order (SO)

```
Input: Items and Services with quantities and prices
Output: Sales order document with line items
Impact: No journal entries (document only)
Stock Check: Shows available stock for Item types only
```

### Sales Invoice (SINV)

```
Input: SO reference, invoice amounts
Output: Journal entries for revenue and COGS
Impact: Creates journal entries when approved:

Revenue Recognition:
Dr. Accounts Receivable (1.1.4)                [Total Amount]
    Cr. Revenue Account (4.1.x)                          [Revenue Amount]
    Cr. PPN Keluaran (2.1.2)                           [PPN Amount]

Cost of Goods Sold (for Item types only):
Dr. Cost of Goods Sold (5.1.5)                 [COGS Amount]
    Cr. Inventory Account (1.1.10/1.1.11/1.1.12)       [COGS Amount]

For Service Types:
No COGS entry (services don't have inventory cost)
```

### Sales Receipt (SR)

```
Input: SINV reference, receipt amount
Output: Receipt journal entries
Impact:
Dr. Cash/Bank Account (1.1.1)    [Receipt Amount]
    Cr. Accounts Receivable (1.1.4)               [Receipt Amount]
```

## FIFO Costing Implementation

### Stock Layer Creation

```
When items are received (GR):
1. Create stock layer with:
   - Purchase date
   - Quantity received
   - Unit cost from purchase
   - Reference to GR/PINV
   - Remaining quantity (initially = received quantity)
```

### COGS Calculation (FIFO)

```
When items are sold (SINV):
1. Identify oldest stock layers first
2. Calculate COGS using FIFO method:
   - Take from oldest layers first
   - Use unit cost from stock layer
   - Update remaining quantities in layers
   - Create stock movement record (movement_type: 'out')
```

### Example FIFO Scenario

```
Purchase 1: 10 units @ Rp 100,000 = Rp 1,000,000
Purchase 2: 5 units @ Rp 120,000 = Rp 600,000
Total Inventory: 15 units, value = Rp 1,600,000

Sales: 8 units @ Rp 150,000 each = Rp 1,200,000
COGS Calculation (FIFO):
- Take 8 units from Purchase 1 @ Rp 100,000 = Rp 800,000
- Remaining: 2 units @ Rp 100,000 + 5 units @ Rp 120,000 = Rp 700,000
- Gross Profit: Rp 1,200,000 - Rp 800,000 = Rp 400,000
```

## Stock Movement Tracking

### Movement Types

1. **'in'** - Stock received (from GR)
2. **'out'** - Stock sold (from SINV)
3. **'adjustment'** - Stock adjustment (manual)
4. **'transfer'** - Stock transfer (future feature)

### Movement Records

```
Each movement creates a record with:
- Item reference
- Movement type
- Quantity (+ for in, - for out)
- Unit cost
- Total cost
- Reference document (GR, SINV, Adjustment)
- Movement date
- Created by user
- Notes (optional)
```

## Inventory Valuation Methods

### Current Implementation: FIFO

```
Advantages:
- Matches physical flow of goods
- Provides accurate cost tracking
- Minimizes tax liability in inflationary periods
- Industry standard for many businesses
```

### Valuation Calculation

```
Inventory Value = Sum of (Remaining Quantity × Unit Cost) for all stock layers

Example:
Layer 1: 2 units @ Rp 100,000 = Rp 200,000
Layer 2: 5 units @ Rp 120,000 = Rp 600,000
Total Inventory Value = Rp 800,000
```

## Chart of Accounts Integration

### Inventory Accounts

```
1.1.10 - Inventory - Raw Materials
1.1.11 - Inventory - Finished Goods
1.1.12 - Inventory - Supplies & Consumables
```

### Related Accounts

```
2.1.1 - PPN Masukan (Input VAT)
2.1.2 - PPN Keluaran (Output VAT)
2.1.3 - Accounts Payable
1.1.4 - Accounts Receivable
5.1.5 - Cost of Goods Sold
4.1.x - Revenue Accounts (various)
6.1.x - Service Expense Accounts
6.2.1 - Inventory Adjustment Loss
```

## Stock Adjustment Integration

### Adjustment Types

1. **Physical Count Discrepancy**
2. **Damage/Loss**
3. **Theft**
4. **Quality Issues**
5. **System Error Correction**

### Adjustment Process

```
1. Create stock adjustment with:
   - Current quantity (system)
   - Adjusted quantity (actual)
   - Unit cost for valuation
   - Reason for adjustment

2. Calculate variance:
   - Variance Quantity = Adjusted - Current
   - Variance Value = Variance Quantity × Unit Cost

3. Update inventory:
   - Update current_stock_quantity
   - Update current_stock_value
   - Create stock movement record

4. Create journal entry:
   Dr. Inventory Adjustment Loss (6.2.1)    [Variance Value]
       Cr. Inventory Account (1.1.x)               [Variance Value]
```

## Reporting Integration

### Inventory Reports

1. **Dashboard:** Key metrics and alerts
2. **Stock Status:** Current quantities and values
3. **Stock Movement:** Movement history and tracking
4. **Inventory Valuation:** FIFO layers and valuation
5. **Low Stock:** Reorder alerts and suggestions
6. **Stock Adjustments:** Adjustment history and analysis

### Financial Reports Impact

1. **Trial Balance:** Shows inventory and COGS balances
2. **Balance Sheet:** Inventory as current assets
3. **Income Statement:** COGS affects gross profit
4. **Cash Flow:** Inventory purchases affect operating cash flow

## Integration Benefits

### Operational Benefits

-   **Real-time Stock Tracking:** Always know current quantities
-   **FIFO Costing:** Accurate cost calculation and profit margins
-   **Movement History:** Complete audit trail of stock movements
-   **Low Stock Alerts:** Prevent stockouts and improve planning

### Financial Benefits

-   **Accurate COGS:** Proper cost allocation for profit analysis
-   **Inventory Valuation:** Current asset valuation for balance sheet
-   **Tax Compliance:** Proper PPN handling for Indonesian tax requirements
-   **Audit Trail:** Complete transaction history for auditing

### Business Intelligence

-   **Turnover Analysis:** Track inventory turnover rates
-   **Profitability Analysis:** Item-level profit margins
-   **Demand Forecasting:** Historical movement data for planning
-   **Cost Management:** Track cost changes over time

## Error Handling and Validation

### Data Validation

-   **Quantity Validation:** Cannot sell more than available stock
-   **Cost Validation:** Unit costs must be positive
-   **Reference Validation:** All transactions must reference valid documents
-   **Permission Validation:** Role-based access control

### Error Scenarios

1. **Insufficient Stock:** Prevent overselling
2. **Invalid References:** Ensure document linkages
3. **Permission Errors:** Enforce role-based access
4. **Calculation Errors:** Validate FIFO calculations

This integration provides a complete inventory management system that seamlessly connects with the existing Purchase and Sales workflows while maintaining accurate financial records and providing comprehensive business intelligence capabilities.

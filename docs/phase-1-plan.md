Title: Phase 1 Implementation Plan (Foundation)
Last Updated: [Pending Approval]

Objective

- Deliver a working double‑entry foundation with CoA, dimensions (project, fund, department), manual & system journals, and master data for AR/AP.

Scope (Phase 1)

- Core: Accounts, Periods, Journals, JournalLines with strict balancing.
- Dimensions: Projects, Funds, Departments.
- Masters: Customers, Vendors, TaxCodes, BankAccounts.
- Subledger scaffolding: SalesInvoices/Lines, PurchaseInvoices/Lines, SalesReceipts, PurchasePayments (CRUD + posting hooks).
- Seeders: Yayasan CoA, basic TaxCodes, sample Funds/Projects.
- RBAC: Spatie roles & permissions baseline; AdminLTE layout & navigation.

Deliverables

- Migrations and models for all Phase 1 tables.
- PostingService to generate balanced journals for AR/AP docs.
- Policies/permissions for create/view/post/close.
- PDF templates placeholders (invoice, receipt).
- Trial Balance & GL Detail reports (basic version).

Database (Initial Tables)

- accounts, periods, journals, journal_lines
- projects, funds, departments
- customers, vendors, tax_codes
- sales_invoices, sales_invoice_lines, sales_receipts
- purchase_invoices, purchase_invoice_lines, purchase_payments
- bank_accounts, bank_transactions (minimum for receipts/payments)

Key Laravel Components

- Services: PostingService, PeriodCloseService
- Policies: per module (Accounts, Journals, Invoices, Payments)
- FormRequests: validation for each document type
- Jobs: GeneratePdfJob (queued)
- Seeders: CoASeeder, TaxCodeSeeder, FundProjectSeeder, RolePermissionSeeder

Routes (web/api)

- /accounts, /journals, /projects, /funds, /departments
- /ar/invoices, /ar/receipts
- /ap/invoices, /ap/payments
- /reports/trial-balance, /reports/gl-detail

Posting Rules (Phase 1)

- Sales Invoice: Dr AR; Cr Revenue; Cr PPN Output (if any)
- Sales Receipt: Dr Cash/Bank; Cr AR
- Purchase Invoice: Dr Expense/Asset; Dr PPN Input; Cr AP
- Purchase Payment: Dr AP; Cr Cash/Bank

Security & Controls

- Roles: Admin, Accountant, Approver, Cashier, Auditor
- Permissions: view/create/update/approve/post/close per module
- Journals locked after posting; corrections via reversal entries
- Period close prevents new postings in closed periods

AdminLTE UI

- Sidebar modules, datatables, CRUD forms with line‑items and dimension pickers
- Print buttons for PDFs; status badges; approval/post controls

Reports (Phase 1)

- Trial Balance (by date/period)
- GL Detail (filters: account, project, fund, department)

Acceptance Criteria

- All journals balanced; posting is transactional and idempotent
- Basic CoA seeded; can create and post sample invoices/payments
- Trial Balance equals zero net (debits==credits) and ties to GL detail
- Role‑based access enforced

Risks & Mitigations

- Tax complexity → start with basic PPN/withholding; extend in Phase 2
- COA granularity → ship sane defaults; allow admin customization
- Reporting performance → add summary balances table in Phase 2 if needed

Timeline (Suggested)

- Week 1: DB + models + seeders + RBAC + AdminLTE shell
- Week 2: Posting engine + AR/AP docs + basic reports
- Week 3: QA, data import hooks, polish, documentation

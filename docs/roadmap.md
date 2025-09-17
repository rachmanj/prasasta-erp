Title: Phased Roadmap for Yayasan Bookkeeping Application
Last Updated: 2025-09-07

Overview

- This roadmap guides delivery across five phases, aligned with Indonesian non-profit (Yayasan) needs and double-entry accounting.

Phase 1 — Foundation (current)

- Deliverables
  - Core schema: accounts, journals, journal_lines, periods, projects, funds, departments, customers, vendors, tax_codes, bank_accounts, bank_transactions
  - Seeders: CoA, TaxCodes, Funds/Projects, Roles/Permissions
  - Auth (Breeze), RBAC (Spatie), AdminLTE nav
  - Reports API: Trial Balance, GL Detail
- Next actions
  - PostingService skeleton: post balanced journals, reversal policy
  - HTML Blade views for Trial Balance & GL Detail
  - Optional: add FK constraints in follow-up migration
- Acceptance
  - Migrations pass; seeders idempotent; reports return data; auth protects routes

Phase 2 — Sales & AR, Purchases & AP

- Deliverables
  - AR: SalesOrder → SalesInvoice(+lines) → SalesReceipt; posting rules
  - AP: PurchaseOrder → PurchaseInvoice(+lines) → PurchasePayment; posting rules
  - Taxes: PPN output/input on lines, basic withholding
  - PDFs: Invoice, Receipt, PO, Payment
  - AR/AP aging, document numbering
- Acceptance
  - Balanced journals; subledgers reconcile; PDFs downloadable

Phase 3 — Reporting

- Deliverables
  - Trial Balance, GL Summary/Detail
  - Laporan Aktivitas (P&L), Laporan Posisi Keuangan (BS), Laporan Arus Kas (indirect)
  - Project P&L, Fund activity, basic tax recaps
- Acceptance
  - Reports tie to GL; debits==credits; BS balances to net assets

Phase 4 — Controls & Operations

- Deliverables
  - Approvals, period close, audit trail
  - Bank reconciliation, budgets vs actuals
  - Summary balances table for performance
- Acceptance
  - Closed periods immutable; reconciliations tracked; budget alerts functional

Phase 5 — Enhancements

- Deliverables
  - Fixed assets & depreciation (PSAK), advanced tax, multi-currency (optional)
  - CALK generator, dashboards, performance tuning
- Acceptance
  - Depreciation auto-post; CALK sections render; core flows stable

Cross-cutting

- Quality: unit tests for posting rules, seeders, report math
- Docs: update architecture/decisions/todo after each milestone
- Security: route policies; validation on postings

Suggested Timeline

- Phase 1: 1–2 weeks (remaining 2–3 days)
- Phase 2: 2–3 weeks
- Phase 3: 1–2 weeks
- Phase 4: 2 weeks
- Phase 5: 2+ weeks

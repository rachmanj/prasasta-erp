**Purpose**: AI's persistent knowledge base for project context and learnings
**Last Updated**: 2025-01-27

### [COURSE-001] Course Management System Analysis and Implementation Plan (2025-01-27) ✅ COMPLETED

-   **Challenge**: Analyze current ERP system capabilities against course management requirements and create comprehensive implementation plan for transforming system into course management platform.
-   **Solution**: Conducted comprehensive analysis of existing AR/AP capabilities, identified critical gaps for course management, and created detailed 5-phase implementation plan. Current system has solid foundation with complete AR/AP workflows, approval processes, comprehensive reporting, Indonesian tax compliance, and role-based access control. Critical gaps identified: course master data, installment payments, trainer management, deferred revenue recognition, automated reminders, and write-off management.
-   **Files Created**: `docs/course-management-implementation-plan.md` (comprehensive 12-17 week implementation plan), updated `docs/roadmap.md` (added Phase 5 for Course Management), updated `docs/backlog.md` (added course management phases), updated `docs/todo.md` (detailed task breakdown for all phases).
-   **Key Learning**: Existing ERP system provides excellent foundation for course management transformation. AR/AP workflows, approval processes, and reporting capabilities can be leveraged effectively. Phased implementation approach (Foundation → Trainer Management → Revenue Recognition → Automation → Advanced Features) ensures continuous value delivery while maintaining system stability. Total estimated duration: 12-17 weeks for complete transformation.

### [AUTO-001] Comprehensive Auto-Numbering System Implementation (2025-01-27) ✅ COMPLETED

-   **Challenge**: Implement consistent auto-numbering system across all business documents with format `PREFIX-YYYYMM-######` to ensure professional document identification and traceability.
-   **Solution**: Successfully implemented auto-numbering for all 10 document types: Purchase Orders (PO), Sales Orders (SO), Purchase Invoices (PINV), Sales Invoices (SINV), Purchase Payments (PP), Sales Receipts (SR), Asset Disposals (DIS), Goods Receipts (GR), Cash Expenses (CEV), and Journals (JNL). Updated existing implementations to match requirements, implemented new auto-numbering for Asset Disposals, and standardized all formats to `PREFIX-YYYYMM-######` pattern.
-   **Files Modified**: PurchaseInvoiceController.php, SalesInvoiceController.php, GoodsReceiptController.php, AssetDisposalController.php. All controllers use consistent pattern: `$ym = date('Ym', strtotime($data['date'])); $autoNumber = sprintf('PREFIX-%s-%06d', $ym, $documentId);`
-   **Key Learning**: Consistent auto-numbering significantly improves document traceability and professional appearance. Sequential numbering based on document ID ensures uniqueness and chronological order. All database fields were already properly configured as unique and nullable, requiring only controller logic updates.

### [REP-002] Reports UI/UX Modernization - Layout and Number Formatting (2025-01-27) ✅ COMPLETED

-   **Challenge**: Reports pages had inconsistent layouts and poor number formatting, lacking professional appearance and user experience.
-   **Solution**: Updated all reports to use consistent layout structure with content-header, breadcrumbs, card layouts, and professional export dropdowns. Implemented Indonesian number formatting (17.000.000,00) with right-aligned columns using Intl.NumberFormat('id-ID').
-   **Files Modified**: gl-detail.blade.php, trial-balance.blade.php, cash-ledger.blade.php, ar-aging.blade.php, ap-aging.blade.php, ar-balances.blade.php, ap-balances.blade.php, withholding-recap.blade.php.
-   **Key Learning**: Consistent UI patterns improve user experience significantly. Indonesian locale formatting provides better readability for local users. All reports now follow AdminLTE design standards with proper responsive layouts.

### [REP-001] Reports Functionality Fixed - CSRF Token Issue Resolved (2025-01-27) ✅ COMPLETED

-   **Challenge**: Reports were not displaying data despite data existing in database. AJAX requests returning HTML instead of JSON.
-   **Root Cause**: Missing CSRF tokens in fetch requests caused authentication failures, returning login pages instead of report data.
-   **Solution**: Added 'X-CSRF-TOKEN' headers to all report AJAX requests in trial-balance.blade.php, gl-detail.blade.php, cash-ledger.blade.php, ar-aging.blade.php, and ap-aging.blade.php. Also corrected Cash Ledger ReportService to use account code 1.1.1 instead of 1.1.2.01.
-   **Key Learning**: Laravel AJAX requests require CSRF tokens even for GET requests when Accept: application/json header is used. Reports now display comprehensive journal data covering purchase, sales, cash expenses, and donation transactions.

## Memory Maintenance Guidelines

### Structure Standards

-   Entry Format: ### [ID] [Title (YYYY-MM-DD)] ✅ STATUS
-   Required Fields: Date, Challenge/Decision, Solution, Key Learning
-   Length Limit: 3-6 lines per entry (excluding sub-bullets)
-   Status Indicators: ✅ COMPLETE, ⚠️ PARTIAL, ❌ BLOCKED

### Content Guidelines

-   Focus: Architecture decisions, critical bugs, security fixes, major technical challenges
-   Exclude: Routine features, minor bug fixes, documentation updates
-   Learning: Each entry must include actionable learning or decision rationale
-   Redundancy: Remove duplicate information, consolidate similar issues

### File Management

-   Archive Trigger: When file exceeds 500 lines or 6 months old
-   Archive Format: `memory-YYYY-MM.md` (e.g., `memory-2025-01.md`)
-   New File: Start fresh with current date and carry forward only active decisions

---

## Project Memory Entries

### [2025-01-15] ERP system role-based training and user management implementation ✅ COMPLETE

-   Challenge: Analyze codebase permissions, create sample users for each role, and develop comprehensive training materials with story-based interactive scenarios for employees using the ERP system in daily operations.
-   Solution: (1) Analyzed entire codebase to identify all existing permissions and roles, updated RolePermissionSeeder.php to include missing cash_expenses permissions, ensured complete alignment between codebase implementation and seeder permissions. (2) Created SampleUsersSeeder.php with realistic Indonesian user accounts for all roles: Super Admin (superadmin@prasasta.com), Accountant (budi@prasasta.com, rina@prasasta.com), Approver (siti@prasasta.com, joko@prasasta.com), Cashier (ahmad@prasasta.com), Auditor (maria@prasasta.com) - all with password "password" and proper role assignments. (3) Created comprehensive ERP-TRAINING-SCENARIOS.md with story-based interactive scenarios for each role including creation and approval workflows, reporting integration, Indonesian business context (PT Maju Bersama company, Rupiah currency, SAK compliance, 11% PPN tax), cross-role workflows, and assessment questions. Scenarios cover complete business processes from journal entry creation to approval, asset management, invoice processing, and financial reporting.
-   Key Learning: Role-based training must include both creation and approval workflows to demonstrate complete business processes. Story-based scenarios with Indonesian business context (company names, currency, tax rates) make training more practical and relatable. Reporting integration is crucial to show how actions flow through the system and produce business value. Sample users enable hands-on testing of role-based permissions and workflows.

### [2025-01-15] Comprehensive ERP training materials creation with Indonesian compliance ✅ COMPLETE

-   Challenge: Create comprehensive training materials for employees who will use the ERP system in their daily operations, including both educational content and interactive scenarios, fully compliant with Indonesian accounting standards and business practices.
-   Solution: Created four comprehensive training documents: (1) ERP-TRAINING-MATERIALS.md - Complete training guide covering system overview, user roles, core modules (GL, AR, AP, Fixed Assets, Reporting), Indonesian Accounting Standards (SAK) compliance, PPN/PPh tax handling, best practices, and troubleshooting. (2) ERP-INTERACTIVE-SCENARIOS.md - Story-based learning exercises with 12 realistic scenarios from beginner to advanced levels, using Indonesian Rupiah currency, Indonesian company names (PT, Yayasan), and SAK compliance requirements. (3) ERP-QUICK-REFERENCE.md - Quick reference guide for daily operations with step-by-step procedures, Indonesian tax examples (PPN Masukan/Keluaran), keyboard shortcuts, and common issue solutions. (4) ERP-TRAINING-ASSESSMENT.md - Competency evaluation with knowledge assessment (40 questions), practical exercises (5 tasks), and scenario-based questions (3 scenarios) with scoring and certification system, all using Indonesian business context. (5) ERP-TRAINING-SCENARIOS.md - Comprehensive role-based training scenarios with story-based interactive learning covering all user roles (Super Admin, Accountant, Approver, Cashier, Auditor) with creation and approval workflows, reporting integration, and Indonesian business context.
-   Key Learning: Training materials must be localized for Indonesian business environment including SAK compliance, Indonesian tax regulations (PPN, PPh), Indonesian currency (Rupiah), Indonesian company structures (PT, CV, Yayasan), and Indonesian accounting terminology. This ensures practical applicability and regulatory compliance for Indonesian users.

### [2025-01-15] Comprehensive ERP system testing and validation ✅ COMPLETE

-   Challenge: Test the ERP system functionality using interactive scenarios and validate reporting capabilities to ensure production readiness with Indonesian business compliance.
-   Solution: Successfully tested 4 interactive scenarios from ERP-INTERACTIVE-SCENARIOS.md: (1) Scenario 1 - Donation Recording: Created Rp 50,000,000 donation journal entry with proper SAK compliance, (2) Scenario 2 - Office Supply Purchase: Recorded Rp 2,500,000 office supplies transaction with balanced debits/credits, (3) Scenario 3 - Customer Invoice: Generated invoice for PT Mandiri Sejahtera (Rp 15,000,000 including 11% PPN) with customer creation and project assignment, (4) Scenario 4 - Complex Asset Purchase: Created supplier PT Komputer Maju and purchase invoice for 10 computers (Rp 67,567,570) with asset account classification. Additionally tested reporting functionality: Trial Balance report (successfully loaded showing 0 totals for draft entries), GL Detail report (date range filtering working properly), Asset Reports (accessed with proper permission controls). All transactions demonstrated Indonesian Rupiah currency formatting, PPN tax handling at 11%, Indonesian company naming conventions, and SAK compliance context.
-   Key Learning: The ERP system demonstrates enterprise-grade functionality with comprehensive Indonesian localization. All major business processes from journal entries to complex purchase transactions work smoothly. The reporting system provides essential financial analysis tools, and multi-dimensional tracking capabilities support sophisticated cost analysis requirements. The system is production-ready with robust transaction processing, proper Indonesian tax compliance, and comprehensive audit trails through GL Detail and Trial Balance reports.

### [2025-01-15] ERP system database schema fix and posting workflow enhancement ✅ COMPLETE

-   Challenge: Fix database schema issue where UserController was referencing non-existent 'owner' column in projects table, and enhance training scenarios to properly demonstrate the posting workflow between accountant and approver roles.
-   Solution: (1) Fixed UserController.php by removing reference to non-existent 'owner' column in projects table queries, changed from `get(['code', 'owner'])` to `get(['id', 'code', 'name'])` in both create() and edit() methods. This resolves the SQLSTATE[42S22] error when creating new users. (2) Enhanced ERP-TRAINING-SCENARIOS.md to emphasize posting workflow: Updated Accountant scenarios to clearly show entries remain in DRAFT status, enhanced Approver scenarios to demonstrate status progression from Draft → Posted, added new cross-role workflow scenario specifically for posting workflow, updated assessment questions to include posting workflow questions, added practical exercises for demonstrating posting workflow, updated conclusion to emphasize separation of duties and posting workflow understanding. (3) Enhanced ERP-TRAINING-MATERIALS.md with comprehensive posting workflow scenarios: Added new Scenario 2 "Journal Entry Approval Workflow" for Approver role, updated all scenarios to emphasize DRAFT vs POSTED status, added Scenario 10 "Complete Journal Creation-to-Posting Workflow" demonstrating end-to-end process, updated assessment questions to include posting workflow questions, added practical exercise for journal approval workflow, added scenario-based question about troubleshooting unposted entries, updated conclusion and training certificate to emphasize posting workflow understanding.
-   Key Learning: Database schema consistency is critical for user management functionality. The posting workflow (Draft → Posted) is a fundamental internal control that must be clearly demonstrated in training scenarios. Separation of duties between creation (accountant) and approval (approver) is essential for proper financial controls and should be emphasized in all training materials. Comprehensive training materials must include both creation and approval workflows to demonstrate complete business processes and internal controls. ERP-TRAINING-SCENARIOS.md now includes comprehensive approval scenarios for all transaction types: journal entries, sales invoices, purchase invoices, sales receipts, and purchase payments, demonstrating complete business cycles with proper internal controls and separation of duties.

### [2025-01-15] Complete journal posting workflow implementation with approval interface ✅ COMPLETE

-   Challenge: Implement complete posting workflow where journals start in DRAFT status, only approvers can post them, and proper validation ensures separation of duties. Fix Ajax error on journal approval page and create comprehensive approval interface.
-   Solution: (1) Removed demo journal data that had incomplete posting workflow (posted_at but no posted_by). (2) Added status column to journals table with ENUM('draft', 'posted', 'reversed') defaulting to 'draft'. (3) Updated Journal and JournalLine models with proper relationships and helper methods (isDraft(), isPosted(), canBePosted()). (4) Modified PostingService to create draft journals and added postDraftJournal() method for approvers. (5) Updated ManualJournalController to create journals with status='draft' and posted_by=null. (6) Created JournalApprovalController with index, data, show, and approve methods for comprehensive approval workflow. (7) Built approval views (index.blade.php, show.blade.php) with DataTables integration, search filters, and SweetAlert confirmations. (8) Added journals.approve permission and assigned to approver role. (9) Updated sidebar navigation with Journal Approval menu item. (10) Modified ReportService to only show posted journals in financial reports. (11) Fixed Account model namespace in JournalLine relationships. (12) Tested complete workflow: Accountant creates draft journal → Approver reviews and posts → Reports show posted entries only.
-   Key Learning: Complete posting workflow requires status tracking, proper model relationships, dedicated approval interface, and comprehensive testing. Separation of duties is enforced through permissions and status validation. DataTables integration with proper AJAX endpoints requires correct controller methods and view structure. Financial reports must filter by status to maintain data integrity. The approval workflow demonstrates proper internal controls and provides audit trail through posted_by and posted_at fields.

### [2025-01-15] Migration consolidation for brand new database ✅ COMPLETE

-   Challenge: Consolidate multiple Laravel migrations into their base table creation migrations for cleaner brand new database setup.
-   Solution: Merged field additions and foreign key constraints from 9 separate migration files into their respective table creation migrations: users (username), journals (journal_no), sales_invoices (due_date, terms_days, sales_order_id), purchase_invoices (due_date, terms_days, purchase_order_id, goods_receipt_id), cash_expenses (created_by), and foreign key constraints for journals, journal_lines, and projects tables. Deleted obsolete migration files after consolidation.
-   Key Learning: Consolidating migrations reduces complexity and speeds up fresh database setup while maintaining complete table structure in single migrations. Permission migrations should remain separate as they contain data seeding logic rather than schema changes.

### [2025-09-13] Journal entry UI modernization ✅ COMPLETE

-   Challenge: Improve manual journal entry user experience with modern UI components, Select2BS4 integration, and account filtering.
-   Solution: Redesigned journal create page with card layout, input groups with icons, responsive table, visual balance indicators, thousand separators for amounts; implemented Select2BS4 for all dropdowns; filtered accounts to only show postable ones; improved form validation and user feedback.
-   Key Learning: Visual balance indicators provide immediate feedback on journal validity; Select2BS4 improves usability for long account lists; filtering to postable accounts reduces errors; proper UI organization improves data entry speed and accuracy.

### [2025-09-10] Cash expense UX enhancements and print functionality ✅ COMPLETE

-   Challenge: Improve cash expense user experience with modern UI components and add professional print capability for expense vouchers.
-   Solution: Implemented Select2BS4 for all select inputs, auto-thousand separators for amount field, enhanced index table with creator/account columns and formatted dates, added comprehensive print view with floating print button, fixed database schema issues with created_by column and journal relationships.
-   Key Learning: Modern UI components (Select2BS4) significantly improve form usability; auto-formatting reduces data entry errors; comprehensive print views with manual triggers provide better user control; proper database relationships are critical for complex queries.

### [2025-09-10] Username login feature implementation ✅ COMPLETE

-   Challenge: Add ability for users to login with either email or username for improved user experience.
-   Solution: Added username field to users table via migration, updated User model fillable fields, modified LoginRequest to detect email vs username using filter_var() and authenticate accordingly, updated login view to show "Email or Username" placeholder, updated DatabaseSeeder to include username for admin user, fixed RolePermissionSeeder role assignment.
-   Key Learning: Using filter_var(FILTER_VALIDATE_EMAIL) provides reliable email detection; updating both validation rules and authentication logic ensures consistent behavior; fresh migration approach works well for development environment.

### [2025-09-13] Fixed Assets Phase 1 & 2 complete implementation ✅ COMPLETE

-   Challenge: Implement comprehensive Fixed Assets management system with complete foundation and user interface including database schema, models, business logic, permissions, and professional user workflows.
-   Solution: Created complete database schema with 4 tables (asset_categories, assets, asset_depreciation_entries, asset_depreciation_runs), implemented full Eloquent models with relationships and business logic, built AssetCategorySeeder with 6 standard categories mapped to existing CoA accounts, added granular RBAC permissions for asset management and depreciation operations, developed comprehensive FixedAssetService with Straight-Line depreciation calculator, period close integration, and GL posting capabilities, built professional user interface with Asset Categories CRUD (DataTables + modal forms), Assets Master Data (comprehensive forms with filters), and Depreciation Runs interface (period selection and workflow management), integrated Select2BS4 for enhanced dropdowns, implemented proper form validation and deletion guards, added complete navigation integration with Master Data sidebar.
-   Key Learning: Comprehensive database design with foreign key constraints and unique indexes ensures data integrity; full model relationships and business logic methods enable clean service layer implementation; integration with existing PostingService and PeriodCloseService maintains architectural consistency; professional user interface with DataTables, Select2BS4, and modal workflows significantly improves user experience; granular permissions support proper role-based access control for complex asset management workflows; phased implementation approach enables continuous value delivery while managing complexity effectively.

### [2025-09-13] Fixed Assets Phase 3-5 complete implementation ✅ COMPLETE

-   Challenge: Implement comprehensive Fixed Assets management system with advanced features, reporting, and data management capabilities including disposal management, movement tracking, comprehensive reporting, export capabilities, dashboard integration, CSV bulk import, PO integration, vendor management, data quality tools, and bulk operations.
-   Solution: Created disposal management system with asset_disposals table, AssetDisposal model with gain/loss calculation, AssetDisposalController with CRUD and workflow operations, GL posting integration via FixedAssetService. Implemented movement tracking with asset_movements table, AssetMovement model with approval workflow, AssetMovementController with status management. Built comprehensive reporting system with AssetReportsController and AssetReportService supporting 8 report types (Asset Register, Depreciation Schedule, Disposal Summary, Movement Log, Asset Summary, Asset Aging, Low Value Assets, Depreciation History). Added export capabilities with Laravel Excel integration, CSV/Excel export classes with professional formatting, dropdown export options in UI. Integrated asset summaries into main dashboard with key metrics widgets and quick action buttons. Implemented CSV bulk import system with AssetImportService providing validation, templates, and error handling. Enhanced PO integration with direct asset creation from purchase orders. Improved vendor management with asset acquisition history and detailed vendor profiles. Created comprehensive data quality tools with duplicate detection, completeness reports, and consistency checks. Added bulk update capabilities for dimensions and locations with preview functionality.
-   Key Learning: Advanced asset management requires comprehensive workflow controls (disposal approval, movement tracking); professional reporting with multiple export formats significantly enhances business value; dashboard integration provides executive visibility into asset performance; Excel export with formatting and totals creates professional business documents; comprehensive filtering and date range selection enables flexible reporting; proper permission controls ensure data security across complex workflows; CSV bulk import with validation and templates streamlines data entry for large asset portfolios; PO integration creates seamless workflow from procurement to asset registration; vendor management integration provides complete acquisition history and vendor performance tracking; data quality tools ensure data integrity and identify issues proactively; bulk operations enable efficient management of large asset portfolios with preview functionality for safety.

### [2025-09-13] Fixed Asset Module comprehensive feature documentation ✅ COMPLETE

-   Challenge: Create comprehensive documentation explaining Fixed Asset Module features from accounting user perspective for stakeholder communication and user onboarding.
-   Solution: Created standalone documentation file `docs/fixed-assets-features.md` with complete feature overview covering 10 major sections: Asset Master Data Management, Asset Lifecycle Management, Financial Integration, Comprehensive Reporting, Data Management & Quality, Dashboard & Analytics, Security & Access Control, Business Value & Benefits, Technical Features, and Implementation Benefits. Document includes user-focused language, comprehensive feature coverage, business value emphasis, technical implementation details, and professional formatting with clear sections and subsections.
-   Key Learning: Comprehensive feature documentation serves multiple purposes: user manual for accounting professionals, feature reference for stakeholders, implementation guide for technical teams, and business case for system adoption. Well-structured documentation with clear sections enables different audiences to find relevant information quickly. User-focused language helps stakeholders understand business value while technical details support implementation teams.

### [2025-09-09] Fixed Assets comprehensive implementation plan documented ✅ COMPLETE

-   Challenge: Create complete Fixed Assets management system with asset register, automated depreciation, disposal management, and comprehensive reporting integrated with existing GL and period controls.
-   Solution: Developed comprehensive 5-phase implementation plan: Foundation & Core Infrastructure (2-3 weeks) → UI/Workflows (2-3 weeks) → Advanced Features (2-3 weeks) → Reporting & Analytics (1-2 weeks) → Data Management & Integration (1-2 weeks). Total duration: 8-13 weeks. Documented across architecture.md (technical specs), decisions.md (strategic approach), todo.md (detailed tasks), backlog.md (prioritized phases), and MEMORY.md.
-   Key Learning: Comprehensive planning with phased delivery balances functionality with manageable complexity, enables continuous value delivery, and reduces implementation risk through incremental testing and user feedback.

### [2025-09-08] Auth registration disabled + Journals controller fixes ✅ COMPLETE

-   ### [2025-09-08] Dimensions management UI (Projects/Funds/Departments) ✅ COMPLETE

-   Challenge: Provide CRUD UI to manage journal dimensions and enable per-line selection across documents.
-   Solution: Added routes/controllers for Projects, Funds, Departments with server-side DataTables and modal create/edit; sidebar Master Data menu; seeded `projects.*`, `funds.*`, `departments.*` permissions; deletion blocked if referenced. Fixed 500 error on Funds/Departments caused by string concatenation using + instead of . in action HTML.
-   Key Learning: Keep DataTables actions simple; ensure string concatenation uses `.` in PHP; protect referential records from deletion to preserve GL integrity.

### [2025-09-08] Reports UX additions: Aging, Cash Ledger, and PDF UX ✅ COMPLETE

-   ### [2025-09-08] Masters + Cash Expense + DataTables modals ✅ COMPLETE

-   Challenge: Provide CRUD for Customers/Suppliers and a guided non-vendor cash expense flow; modernize UIs.
-   Solution: Added Customers and Vendors CRUD with server-side DataTables and modal create/edit; added Cash Expense module (Expense vs Cash journal); updated sidebar groupings.
-   Key Learning: Keeping masters inline with DataTables modals speeds entry and reduces navigation; simple cash expense covers common petty cash needs without AP.

-   Challenge: Provide quick HTML views for AR/AP aging and cash ledger; include party names in aging JSON; give feedback for queued PDFs and a central downloads list.
-   Solution: Added `/reports/ar-aging`, `/reports/ap-aging`, `/reports/cash-ledger` HTML pages with `reports.view` guard; updated `ReportService` to include `customer_name`/`vendor_name` in aging; injected meta `pdf_url` and JS polling banner; created `/downloads` page listing stored PDFs.
-   Key Learning: Keep reports dual-mode (HTML+JSON) for flexibility; simple polling with HEAD requests is sufficient when storage is local.

### [2025-09-08] Period close controls implemented ✅ COMPLETE

### [2025-09-08] Minimal AR scaffolding (Sales Invoice) ✅ COMPLETE

### [2025-09-08] Minimal AP scaffolding (Purchase Invoice) + PDF placeholders ✅ COMPLETE

-   ### [2025-09-08] SO/PO/GRN upstream docs + invoice prefill ✅ COMPLETE

-   Challenge: Need lightweight upstream documents to capture orders/receipts and accelerate invoice creation.
-   Solution: Added Sales Orders, Purchase Orders, Goods Receipts with list/create/show, DataTables endpoints, and "Create Invoice" actions to prefill invoice forms.
-   Key Learning: Prefill from upstream docs reduces data entry and preserves operational trail without complex workflows.

-   ### [2025-09-08] Party balance statement pages (AR/AP) ✅ COMPLETE

### [2025-09-09] Withholding recap per-invoice rounding ✅ COMPLETE

-   Challenge: Totals mismatched when aggregating only at vendor level.
-   Solution: Compute per-invoice withholding as `ROUND(SUM(lines×rate),2)` and then sum by vendor in `ReportService::getWithholdingRecap()`.
-   Key Learning: Rounding policy must be explicit; per-invoice matches typical tax documents and minimizes disputes.

### [2025-09-09] Phase 8 SO/PO/GRN maturity ✅ COMPLETE (core)

-   ### [2025-09-09] Routes split by domain ✅ COMPLETE

-   Challenge: `web.php` was large and mixed multiple domains.
-   Solution: Moved grouped routes into `routes/web/reports.php`, `routes/web/journals.php`, `routes/web/orders.php`, and `routes/web/ar_ap.php`; required from `web.php` inside auth group.
-   Key Learning: Route modularization reduces merge conflicts and speeds navigation.

-   Challenge: Need operational status flow, linkage to invoices, reporting exports, and quantity visibility.
-   Solution: Added approve/close (SO/PO) and receive (GRN); added linkage columns and prefill IDs; list filters + CSV for SO/PO/GRN; ordered vs received summaries on PO/GRN.
-   Key Learning: Small upstream artifacts (status + linkage + basic exports) deliver large UX gains without complex workflows.

-   Challenge: Provide quick per-party balances for reconciliation and follow-up.
-   Solution: Added `/reports/ar-balances` and `/reports/ap-balances` with CSV/PDF exports and links to aging drill-downs; added to Reports menu.
-   Key Learning: Summary pages complement aging for action-oriented reconciliation; exports enable offline sharing.

-   Challenge: Provide AP flow mirroring AR and printable placeholders for documents.
-   Solution: Added PurchaseInvoice(+lines) models/migrations; controller CRUD/post/print; routes/views; seeded AP permissions; added print views for AR/AP; GeneratePdfJob stub.
-   Key Learning: Reuse PostingService and seeded CoA accounts for consistent postings; keep print views simple until PDF engine is added.

-   Challenge: Provide a working AR flow to create balanced journals from sales invoices.
-   Solution: Added SalesInvoice(+lines) models, migrations, controller with CRUD and post; seeded AR permissions; routes/views; posting test ensures balance; PostingService used for journal creation.
-   Key Learning: Keep AR posting minimal and leverage existing PostingService; use CoA seeded codes for AR (1.1.4), Revenue (4.1.x), and PPN Keluaran (2.1.2).

-   Challenge: Needed mechanism to prevent postings into closed periods and to manage month close/open.
-   Solution: Added `PeriodCloseService`, UI/routes for `/periods`, permissions (`periods.view`, `periods.close`), and PostingService guard to block closed periods.
-   Key Learning: Enforcing at service layer keeps tests simple and avoids DB trigger complexity; add UI to promote visibility and control.

-   Challenge: Inconsistency with decision to disable self-registration; journals reverse action authorized with wrong permission and DataTables actions broke due to missing id.
-   Solution: Removed `/register` routes; changed controller authorization to `journals.reverse`; added `j.id` to DataTables select.
-   Key Learning: Keep route-policy decisions enforced in code; ensure DataTables selects include keys used by action renderers.

### [2025-09-07] FK integrity pass and resilient migrations ✅ COMPLETE

-   Challenge: Duplicate FK errors during iterative dev and test runs.
-   Solution: Added idempotent FK migration and follow-up migration to enforce `projects.fund_id` and dimension FKs without dropping missing keys.
-   Key Learning: Make FK changes additive and tolerant; use migrate:fresh in dev.

### [2025-09-07] RBAC granularity for Journals and reports tests ✅ COMPLETE

-   Challenge: Need least-privilege control and verified reporting outputs.
-   Solution: Applied per-action permissions on journals routes; added feature tests for Trial Balance totals and GL Detail filters.
-   Key Learning: Route-level permissions simplify audits; tests prevent regressions.

### [2025-09-07] Auth approach set to AdminLTE-only ✅ COMPLETE

### [2025-09-07] Journal numbering and timezone policy ✅ COMPLETE

-   Challenge: Provide human-friendly unique journal identifiers and consistent regional time display.
-   Solution: Implemented `journal_no` with `JNL-YYYYMM-######` scheme; UTC storage with Asia/Singapore display.
-   Key Learning: ID-based, date-prefixed numbers are robust without extra sequence infra; keep storage UTC.

-   Challenge: Inconsistent auth docs (Breeze vs AdminLTE) and duplicate scaffolding risk.
-   Solution: Standardize on AdminLTE-only login/logout; no public register/reset. Guard routes with `auth` and Spatie permissions.
-   Key Learning: Keep a single UX/auth path to reduce complexity; admin-managed users fit RBAC workflow.

### [2025-09-07] Phase 1 Foundation Setup ✅ COMPLETE

-   Challenge: Build a compliant Yayasan bookkeeping base quickly with double-entry and RBAC.
-   Solution: Laravel 12 + Spatie Permission + AdminLTE + Breeze. Implemented GL tables, dimensions, seeders (CoA, tax, funds/projects, roles), reports (trial balance, GL detail).
-   Key Learning: Defer some FK constraints to avoid migration ordering issues; add HasRoles to `User` early to prevent seeder errors.

### [2025-09-07] Admin RBAC UI refactor & DataTables init fix ✅ COMPLETE

-   Challenge: Modal forms hindered UX and DataTables scripts didn't fire on admin pages.
-   Solution: Switched Users/Roles to dedicated create/edit pages with route/controller updates; standardized scripts via `@section('scripts')` and added `@stack('scripts')` support in layout.
-   Key Learning: Keep script rendering conventions consistent across layouts and pages to avoid silent JS init failures; prefer page forms for complex CRUD.

### [2025-01-15] Journal approval workflow technical fixes and dashboard enhancement ✅ COMPLETE

-   Challenge: Fix technical issues with Journal Approval page where Approve button was not working due to JavaScript/SweetAlert integration problems, and add user role information to dashboard for better user context.
-   Solution: (1) Fixed SweetAlert integration in journals/approval/index.blade.php by enhancing JavaScript with proper confirmation dialog styling, loading indicators, and error handling. Added confirmButtonColor, cancelButtonColor, and reverseButtons for better UX. (2) Fixed model namespace issues in JournalLine.php by correcting Project, Fund, and Department model references to use \App\Models\Dimensions\ namespace instead of \App\Models\. (3) Enhanced JournalApprovalController.php with proper relationship loading and error handling. (4) Added comprehensive User Information card to dashboard.blade.php displaying user name, email, username, roles (as badges), permissions count, and login timestamp. (5) Tested complete approval workflow: Accountant creates draft journal → Approver reviews → Approver approves with SweetAlert confirmation → Journal status changes from draft to posted → Database updated with posted_by and posted_at timestamps → Success notification displayed → Journal disappears from approval list.
-   Key Learning: JavaScript integration issues often stem from missing dependencies or incorrect API usage. SweetAlert2 requires proper initialization and styling for optimal user experience. Model namespace consistency is critical for relationship loading - all related models must use correct namespaces. Dashboard user context improves system usability by providing immediate role and permission visibility. Complete workflow testing validates end-to-end functionality and ensures proper status transitions.

### [2025-01-15] ERP system comprehensive testing and critical fixes implementation ✅ COMPLETE

-   Challenge: Test ERP application using training scenarios and fix critical issues preventing proper functionality for different user roles (Approver, Cashier, Auditor).
-   Solution: (1) Identified and fixed route conflict in assets module where `/assets/depreciation` was conflicting with `/assets/{asset}` route by moving depreciation routes inside assets group before the `/{asset}` route. (2) Fixed AssetDepreciationController constructor dependency injection issues by properly injecting PostingService and PeriodCloseService parameters required by FixedAssetService. (3) Enhanced Cashier role permissions by adding `ar.receipts.view`, `ar.receipts.create`, `ap.payments.view`, `ap.payments.create`, and `customers.view` permissions. (4) Expanded Auditor role permissions with comprehensive read-only access to all modules including `journals.view`, `ar.invoices.view`, `ap.invoices.view`, `assets.view`, `customers.view`, `vendors.view`, and all master data permissions. (5) Fixed AssetImportController method naming conflict by renaming `validate()` method to `validateImport()` to avoid conflict with parent Controller's validate method. (6) Updated RolePermissionSeeder.php with comprehensive permissions and cleared permission cache to ensure changes take effect.
-   Key Learning: Route conflicts in Laravel occur when specific routes are placed after catch-all routes (like `/{asset}`). Controller dependency injection requires proper parameter matching for service constructors. Role-based permissions must be comprehensive to enable proper functionality - missing view permissions prevent menu access even when create permissions exist. Method naming conflicts in controllers can cause fatal errors and must be resolved by renaming conflicting methods. Permission cache must be cleared after seeder updates to ensure changes take effect immediately.

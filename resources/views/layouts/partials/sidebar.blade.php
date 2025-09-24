<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/dashboard" class="brand-link">
        <img src="{{ asset('adminlte/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"><b>Prasasta</b> ERP</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Divider -->
                <li class="nav-header">MAIN</li>



                <!-- Sales Group -->
                @canany(['ar.invoices.view', 'ar.receipts.view', 'customers.view'])
                    @php
                        $salesActive =
                            request()->routeIs('sales-invoices.*') ||
                            request()->routeIs('sales-receipts.*') ||
                            request()->routeIs('sales-orders.*') ||
                            request()->routeIs('customers.*');
                    @endphp
                    <li class="nav-item {{ $salesActive ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $salesActive ? 'active' : '' }}">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>
                                Sales
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('customers.view')
                                <li class="nav-item">
                                    <a href="{{ route('customers.index') }}"
                                        class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Customers</p>
                                    </a>
                                </li>
                            @endcan
                            <li class="nav-item">
                                <a href="{{ route('sales-orders.index') }}"
                                    class="nav-link {{ request()->routeIs('sales-orders.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Sales Orders</p>
                                </a>
                            </li>
                            @can('ar.invoices.view')
                                <li class="nav-item">
                                    <a href="{{ route('sales-invoices.index') }}"
                                        class="nav-link {{ request()->routeIs('sales-invoices.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Sales Invoices</p>
                                    </a>
                                </li>
                            @endcan
                            @can('ar.receipts.view')
                                <li class="nav-item">
                                    <a href="{{ route('sales-receipts.index') }}"
                                        class="nav-link {{ request()->routeIs('sales-receipts.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Sales Receipts</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- Purchase Group -->
                @canany(['ap.invoices.view', 'ap.payments.view', 'vendors.view'])
                    @php
                        $purchaseActive =
                            request()->routeIs('purchase-invoices.*') ||
                            request()->routeIs('purchase-payments.*') ||
                            request()->routeIs('purchase-orders.*') ||
                            request()->routeIs('goods-receipts.*') ||
                            request()->routeIs('vendors.*');
                    @endphp
                    <li class="nav-item {{ $purchaseActive ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $purchaseActive ? 'active' : '' }}">
                            <i class="nav-icon fas fa-shopping-bag"></i>
                            <p>
                                Purchase
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('vendors.view')
                                <li class="nav-item">
                                    <a href="{{ route('vendors.index') }}"
                                        class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Suppliers</p>
                                    </a>
                                </li>
                            @endcan
                            <li class="nav-item">
                                <a href="{{ route('purchase-orders.index') }}"
                                    class="nav-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Purchase Orders</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('goods-receipts.index') }}"
                                    class="nav-link {{ request()->routeIs('goods-receipts.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Goods Receipts</p>
                                </a>
                            </li>
                            @can('ap.invoices.view')
                                <li class="nav-item">
                                    <a href="{{ route('purchase-invoices.index') }}"
                                        class="nav-link {{ request()->routeIs('purchase-invoices.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Purchase Invoices</p>
                                    </a>
                                </li>
                            @endcan
                            @can('ap.payments.view')
                                <li class="nav-item">
                                    <a href="{{ route('purchase-payments.index') }}"
                                        class="nav-link {{ request()->routeIs('purchase-payments.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Purchase Payments</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- Accounting Group (moved below Purchase) -->
                @can('journals.view')
                    @php
                        $acctActive =
                            request()->routeIs('journals.*') ||
                            request()->routeIs('accounts.*') ||
                            request()->routeIs('periods.*') ||
                            request()->routeIs('cash-expenses.*') ||
                            request()->routeIs('control-accounts.*');
                    @endphp
                    <li class="nav-item {{ $acctActive ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $acctActive ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calculator"></i>
                            <p>
                                Accounting
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('journals.index') }}"
                                    class="nav-link {{ request()->routeIs('journals.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Journals</p>
                                </a>
                            </li>
                            @can('journals.approve')
                                <li class="nav-item">
                                    <a href="{{ route('journals.approval.index') }}"
                                        class="nav-link {{ request()->routeIs('journals.approval.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Journal Approval</p>
                                    </a>
                                </li>
                            @endcan
                            <li class="nav-item">
                                <a href="{{ route('cash-expenses.index') }}"
                                    class="nav-link {{ request()->routeIs('cash-expenses.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Cash Expenses</p>
                                </a>
                            </li>
                            @can('accounts.view')
                                <li class="nav-item">
                                    <a href="{{ route('accounts.index') }}"
                                        class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Accounts</p>
                                    </a>
                                </li>
                            @endcan
                            @can('control_accounts.view')
                                <li class="nav-item">
                                    <a href="{{ route('control-accounts.index') }}"
                                        class="nav-link {{ request()->routeIs('control-accounts.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Control Accounts</p>
                                    </a>
                                </li>
                            @endcan
                            @can('periods.view')
                                <li class="nav-item">
                                    <a href="{{ route('periods.index') }}"
                                        class="nav-link {{ request()->routeIs('periods.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Periods</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                <!-- Banking Group -->
                @canany(['banking.view', 'banking.cash_out', 'banking.cash_in'])
                    @php
                        $bankingActive = request()->routeIs('banking.*');
                    @endphp
                    <li class="nav-item {{ $bankingActive ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $bankingActive ? 'active' : '' }}">
                            <i class="nav-icon fas fa-university"></i>
                            <p>
                                Banking
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('banking.view')
                                <li class="nav-item">
                                    <a href="{{ route('banking.dashboard.index') }}"
                                        class="nav-link {{ request()->routeIs('banking.dashboard.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Dashboard</p>
                                    </a>
                                </li>
                            @endcan
                            @can('banking.cash_out')
                                <li class="nav-item">
                                    <a href="{{ route('banking.cash-out.index') }}"
                                        class="nav-link {{ request()->routeIs('banking.cash-out.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Cash-Out</p>
                                    </a>
                                </li>
                            @endcan
                            @can('banking.cash_in')
                                <li class="nav-item">
                                    <a href="{{ route('banking.cash-in.index') }}"
                                        class="nav-link {{ request()->routeIs('banking.cash-in.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Cash-In</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- Fixed Assets Group (moved under MAIN) -->
                @canany(['assets.view', 'asset_categories.view', 'assets.depreciation.run', 'assets.disposal.view',
                    'assets.movement.view'])
                    @php
                        $assetsActive = request()->routeIs('assets.*') || request()->routeIs('asset-categories.*');
                    @endphp
                    <li class="nav-item {{ $assetsActive ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $assetsActive ? 'active' : '' }}">
                            <i class="nav-icon fas fa-building"></i>
                            <p>
                                Fixed Assets
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('asset_categories.view')
                                <li class="nav-item">
                                    <a href="{{ route('asset-categories.index') }}"
                                        class="nav-link {{ request()->routeIs('asset-categories.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Asset Categories</p>
                                    </a>
                                </li>
                            @endcan
                            @can('assets.view')
                                <li class="nav-item">
                                    <a href="{{ route('assets.index') }}"
                                        class="nav-link {{ request()->routeIs('assets.*') && !request()->routeIs('assets.depreciation.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Assets</p>
                                    </a>
                                </li>
                            @endcan
                            @can('assets.depreciation.run')
                                <li class="nav-item">
                                    <a href="{{ route('assets.depreciation.index') }}"
                                        class="nav-link {{ request()->routeIs('assets.depreciation.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Depreciation Runs</p>
                                    </a>
                                </li>
                            @endcan
                            @can('assets.disposal.view')
                                <li class="nav-item">
                                    <a href="{{ route('assets.disposals.index') }}"
                                        class="nav-link {{ request()->routeIs('assets.disposals.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Asset Disposals</p>
                                    </a>
                                </li>
                            @endcan
                            @can('assets.movement.view')
                                <li class="nav-item">
                                    <a href="{{ route('assets.movements.index') }}"
                                        class="nav-link {{ request()->routeIs('assets.movements.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Asset Movements</p>
                                    </a>
                                </li>
                            @endcan
                            @can('assets.create')
                                <li class="nav-item">
                                    <a href="{{ route('assets.import.index') }}"
                                        class="nav-link {{ request()->routeIs('assets.import.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Asset Import</p>
                                    </a>
                                </li>
                            @endcan
                            @can('assets.view')
                                <li class="nav-item">
                                    <a href="{{ route('assets.data-quality.index') }}"
                                        class="nav-link {{ request()->routeIs('assets.data-quality.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Data Quality</p>
                                    </a>
                                </li>
                            @endcan
                            @can('assets.update')
                                <li class="nav-item">
                                    <a href="{{ route('assets.bulk-operations.index') }}"
                                        class="nav-link {{ request()->routeIs('assets.bulk-operations.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Bulk Operations</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- Master Data Group (moved under MAIN, only Projects/Funds/Departments) -->
                @canany(['projects.view', 'funds.view', 'departments.view'])
                    @php
                        $masterActive =
                            request()->routeIs('projects.*') ||
                            request()->routeIs('funds.*') ||
                            request()->routeIs('departments.*');
                    @endphp
                    <li class="nav-item {{ $masterActive ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $masterActive ? 'active' : '' }}">
                            <i class="nav-icon fas fa-database"></i>
                            <p>
                                Master Data
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('projects.view')
                                <li class="nav-item">
                                    <a href="{{ route('projects.index') }}"
                                        class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Projects</p>
                                    </a>
                                </li>
                            @endcan
                            @can('funds.view')
                                <li class="nav-item">
                                    <a href="{{ route('funds.index') }}"
                                        class="nav-link {{ request()->routeIs('funds.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Funds</p>
                                    </a>
                                </li>
                            @endcan
                            @can('departments.view')
                                <li class="nav-item">
                                    <a href="{{ route('departments.index') }}"
                                        class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Departments</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- Inventory Group -->
                @canany(['inventory.categories.view', 'inventory.items.view', 'inventory.stock_adjustments.view',
                    'inventory.reports.view'])
                    @php
                        $inventoryActive =
                            request()->routeIs('inventory.*') ||
                            request()->routeIs('items.*') ||
                            request()->routeIs('stock-adjustments.*');
                    @endphp
                    <li class="nav-item {{ $inventoryActive ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $inventoryActive ? 'active' : '' }}">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>
                                Inventory
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('inventory.categories.view')
                                <li class="nav-item">
                                    <a href="{{ route('inventory.categories.index') }}"
                                        class="nav-link {{ request()->routeIs('inventory.categories.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Categories</p>
                                    </a>
                                </li>
                            @endcan
                            @can('inventory.items.view')
                                <li class="nav-item">
                                    <a href="{{ route('items.index') }}"
                                        class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Items</p>
                                    </a>
                                </li>
                            @endcan
                            @can('inventory.stock_adjustments.view')
                                <li class="nav-item">
                                    <a href="{{ route('stock-adjustments.index') }}"
                                        class="nav-link {{ request()->routeIs('stock-adjustments.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Stock Adjustments</p>
                                    </a>
                                </li>
                            @endcan
                            @can('inventory.reports.view')
                                <li class="nav-item">
                                    <a href="{{ route('inventory.reports.dashboard') }}"
                                        class="nav-link {{ request()->routeIs('inventory.reports.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Reports</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- Courses Group (moved under MAIN) -->
                @canany(['course_categories.view', 'courses.view', 'course_batches.view', 'enrollments.view'])
                    @php
                        $coursesActive =
                            request()->routeIs('course-categories.*') ||
                            request()->routeIs('courses.*') ||
                            request()->routeIs('course-batches.*') ||
                            request()->routeIs('enrollments.*');
                    @endphp
                    <li class="nav-item {{ $coursesActive ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $coursesActive ? 'active' : '' }}">
                            <i class="nav-icon fas fa-graduation-cap"></i>
                            <p>
                                Courses
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('course_categories.view')
                                <li class="nav-item">
                                    <a href="{{ route('course-categories.index') }}"
                                        class="nav-link {{ request()->routeIs('course-categories.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Course Categories</p>
                                    </a>
                                </li>
                            @endcan
                            @can('courses.view')
                                <li class="nav-item">
                                    <a href="{{ route('courses.index') }}"
                                        class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Courses</p>
                                    </a>
                                </li>
                            @endcan
                            @can('course_batches.view')
                                <li class="nav-item">
                                    <a href="{{ route('course-batches.index') }}"
                                        class="nav-link {{ request()->routeIs('course-batches.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Course Batches</p>
                                    </a>
                                </li>
                            @endcan
                            @can('enrollments.view')
                                <li class="nav-item">
                                    <a href="{{ route('enrollments.index') }}"
                                        class="nav-link {{ request()->routeIs('enrollments.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Enrollments</p>
                                    </a>
                                </li>
                            @endcan
                            @can('trainers.view')
                                <li class="nav-item">
                                    <a href="{{ route('trainers.index') }}"
                                        class="nav-link {{ request()->routeIs('trainers.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Trainers</p>
                                    </a>
                                </li>
                            @endcan
                            @can('payment_plans.view')
                                <li class="nav-item">
                                    <a href="{{ route('payment-plans.index') }}"
                                        class="nav-link {{ request()->routeIs('payment-plans.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Payment Plans</p>
                                    </a>
                                </li>
                            @endcan
                            @can('installment_payments.view')
                                <li class="nav-item">
                                    <a href="{{ route('installment-payments.index') }}"
                                        class="nav-link {{ request()->routeIs('installment-payments.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Installment Payments</p>
                                    </a>
                                </li>
                            @endcan
                            @can('revenue_recognition.view')
                                <li class="nav-item">
                                    <a href="{{ route('revenue-recognition.index') }}"
                                        class="nav-link {{ request()->routeIs('revenue-recognition.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Revenue Recognition</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- Dashboards Group -->
                @canany(['dashboard.executive.view', 'dashboard.financial.view', 'dashboard.operational.view',
                    'dashboard.performance.view'])
                    @php
                        $dashboardsActive =
                            request()->routeIs('dashboard.executive.*') ||
                            request()->routeIs('dashboard.financial.*') ||
                            request()->routeIs('dashboard.operational.*') ||
                            request()->routeIs('dashboard.performance.*');
                    @endphp
                    <li class="nav-item {{ $dashboardsActive ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $dashboardsActive ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>
                                Dashboards
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('dashboard.executive.view')
                                <li class="nav-item">
                                    <a href="{{ route('dashboard.executive.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.executive.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Executive Dashboard</p>
                                    </a>
                                </li>
                            @endcan
                            @can('dashboard.financial.view')
                                <li class="nav-item">
                                    <a href="{{ route('dashboard.financial.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.financial.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Financial Dashboard</p>
                                    </a>
                                </li>
                            @endcan
                            @can('dashboard.operational.view')
                                <li class="nav-item">
                                    <a href="{{ route('dashboard.operational.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.operational.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Operational Dashboard</p>
                                    </a>
                                </li>
                            @endcan
                            @can('dashboard.performance.view')
                                <li class="nav-item">
                                    <a href="{{ route('dashboard.performance.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.performance.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Performance Dashboard</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- Reports Section -->
                @include('layouts.partials.menu.reports')

                @can('view-admin')
                    @include('layouts.partials.menu.admin')
                @endcan


            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

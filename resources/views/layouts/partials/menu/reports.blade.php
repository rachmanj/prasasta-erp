@can('reports.view')
    @php
        $accountingActive =
            request()->routeIs('reports.trial-balance') ||
            request()->routeIs('reports.gl-detail') ||
            request()->routeIs('reports.cash-ledger') ||
            request()->routeIs('reports.withholding-recap');
        $salesActive = request()->routeIs('reports.ar-aging') || request()->routeIs('reports.ar-balances');
        $purchaseActive = request()->routeIs('reports.ap-aging') || request()->routeIs('reports.ap-balances');
        $coursesActive =
            request()->routeIs('reports.payment.*') ||
            request()->routeIs('reports.revenue.*') ||
            request()->routeIs('reports.course.*') ||
            request()->routeIs('reports.trainer.*');
        $assetsActive = request()->routeIs('reports.assets.*');
    @endphp
    <li class="nav-header">REPORTS</li>

    <!-- Accounting Reports -->
    <li class="nav-item {{ $accountingActive ? 'menu-is-opening menu-open' : '' }}">
        <a href="#" class="nav-link {{ $accountingActive ? 'active' : '' }}">
            <i class="nav-icon fas fa-calculator"></i>
            <p>
                Accounting
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('reports.trial-balance') }}"
                    class="nav-link {{ request()->routeIs('reports.trial-balance') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Trial Balance</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports.gl-detail') }}"
                    class="nav-link {{ request()->routeIs('reports.gl-detail') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>GL Detail</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports.cash-ledger') }}"
                    class="nav-link {{ request()->routeIs('reports.cash-ledger') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Cash Ledger</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports.withholding-recap') }}"
                    class="nav-link {{ request()->routeIs('reports.withholding-recap') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Withholding Recap</p>
                </a>
            </li>
        </ul>
    </li>

    <!-- Sales/Customer Reports -->
    <li class="nav-item {{ $salesActive ? 'menu-is-opening menu-open' : '' }}">
        <a href="#" class="nav-link {{ $salesActive ? 'active' : '' }}">
            <i class="nav-icon fas fa-shopping-cart"></i>
            <p>
                Sales
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('reports.ar-aging') }}"
                    class="nav-link {{ request()->routeIs('reports.ar-aging') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>AR Aging</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports.ar-balances') }}"
                    class="nav-link {{ request()->routeIs('reports.ar-balances') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>AR Party Balances</p>
                </a>
            </li>
        </ul>
    </li>

    <!-- Purchase/Vendor Reports -->
    <li class="nav-item {{ $purchaseActive ? 'menu-is-opening menu-open' : '' }}">
        <a href="#" class="nav-link {{ $purchaseActive ? 'active' : '' }}">
            <i class="nav-icon fas fa-shopping-bag"></i>
            <p>
                Purchase
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('reports.ap-aging') }}"
                    class="nav-link {{ request()->routeIs('reports.ap-aging') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>AP Aging</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports.ap-balances') }}"
                    class="nav-link {{ request()->routeIs('reports.ap-balances') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>AP Party Balances</p>
                </a>
            </li>
        </ul>
    </li>

    <!-- Course Management Reports -->
    @canany(['reports.payment.view', 'reports.revenue.view', 'reports.course.view', 'reports.trainer.view'])
        <li class="nav-item {{ $coursesActive ? 'menu-is-opening menu-open' : '' }}">
            <a href="#" class="nav-link {{ $coursesActive ? 'active' : '' }}">
                <i class="nav-icon fas fa-graduation-cap"></i>
                <p>
                    Courses
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                @can('reports.payment.view')
                    <li class="nav-item">
                        <a href="{{ route('reports.payment.index') }}"
                            class="nav-link {{ request()->routeIs('reports.payment.*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Payment Reports</p>
                        </a>
                    </li>
                @endcan
                @can('reports.revenue.view')
                    <li class="nav-item">
                        <a href="{{ route('reports.revenue.index') }}"
                            class="nav-link {{ request()->routeIs('reports.revenue.*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Revenue Reports</p>
                        </a>
                    </li>
                @endcan
                @can('reports.course.view')
                    <li class="nav-item">
                        <a href="{{ route('reports.course.index') }}"
                            class="nav-link {{ request()->routeIs('reports.course.*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Course Reports</p>
                        </a>
                    </li>
                @endcan
                @can('reports.trainer.view')
                    <li class="nav-item">
                        <a href="{{ route('reports.trainer.index') }}"
                            class="nav-link {{ request()->routeIs('reports.trainer.*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Trainer Reports</p>
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
    @endcanany

    <!-- Asset Reports -->
    @canany(['assets.view', 'assets.disposal.view', 'assets.movement.view'])
        <li class="nav-item {{ $assetsActive ? 'menu-is-opening menu-open' : '' }}">
            <a href="#" class="nav-link {{ $assetsActive ? 'active' : '' }}">
                <i class="nav-icon fas fa-building"></i>
                <p>
                    Assets
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('reports.assets.index') }}"
                        class="nav-link {{ request()->routeIs('reports.assets.*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Asset Reports</p>
                    </a>
                </li>
            </ul>
        </li>
    @endcanany

    <!-- Downloads -->
    <li class="nav-item">
        <a href="{{ route('downloads.index') }}" class="nav-link {{ request()->routeIs('downloads.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-download"></i>
            <p>Downloads</p>
        </a>
    </li>
@endcan

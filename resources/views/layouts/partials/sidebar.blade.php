<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!-- Sidebar Brand -->
    <div class="sidebar-brand text-center">
        <!-- Brand Image / Logo -->
        <a href="{{ route('dashboard') }}" class="brand-link text-decoration-none">
            <span class="brand-text fw-bold fs-4">🕌 Masjid Register</span>
        </a>
    </div>

    <!-- Sidebar Wrapper -->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <!-- Sidebar Menu -->
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                
                <li class="nav-item {{ (Route::is('expenses.*') && !Route::is('expenses.report')) ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ (Route::is('expenses.*') && !Route::is('expenses.report')) ? 'active' : '' }}">
                        <i class="nav-icon bi bi-currency-dollar"></i>
                        <p>
                            Expenses
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('expenses.index') }}" class="nav-link {{ Route::is('expenses.index') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>All Expenses</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('expenses.create') }}" class="nav-link {{ Route::is('expenses.create') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Add Expense</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('expenses.report') }}" class="nav-link {{ Route::is('expenses.report') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-file-earmark-bar-graph"></i>
                        <p>Expense Report</p>
                    </a>
                </li>

                <li class="nav-item {{ Route::is('categories.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ Route::is('categories.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-gear"></i>
                        <p>
                            Settings
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('categories.index') }}" class="nav-link {{ Route::is('categories.index') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Categories</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>

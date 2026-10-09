<nav class="app-header navbar navbar-expand bg-body shadow-sm">
    <div class="container-fluid">
        <!-- Left navbar links -->
        <ul class="navbar-nav align-items-center">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-inline-block">
                <span class="navbar-text fw-bold text-dark fs-5 ps-2">
                    Masjid Construction Expense Register
                </span>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ms-auto">
            <!-- User Menu -->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5 me-1"></i>
                    <span class="d-none d-md-inline fw-semibold">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow">
                    <!-- User image -->
                    <li class="user-header bg-primary text-white p-3 text-center">
                        <i class="bi bi-person-circle fs-1 d-block mb-2"></i>
                        <span class="d-block fw-bold">{{ Auth::user()->name }}</span>
                        <small class="d-block text-white-50">Administrator</small>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer d-flex justify-content-between p-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-default btn-flat border">Profile</a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-default btn-flat border float-end">
                                Sign Out
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

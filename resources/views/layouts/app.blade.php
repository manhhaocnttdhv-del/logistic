<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Hệ thống Quản lý Kho')</title>
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.css') }}">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- OverlayScrollbars -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css">
    <!-- Font Awesome (nếu cần) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .sidebar-brand {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem;
            text-align: center;
        }
        .brand-link {
            color: white !important;
            font-weight: bold;
            font-size: 1.2rem;
            text-decoration: none;
        }
        .brand-link:hover {
            color: #f0f0f0 !important;
        }
        .nav-sidebar .nav-link {
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
        }
        .nav-sidebar .nav-link .nav-icon {
            width: 24px;
            text-align: center;
            margin-right: 0.75rem;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .nav-sidebar .nav-link p {
            margin: 0;
            flex: 1;
            line-height: 1.5;
        }
        .nav-sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .nav-sidebar .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: none;
            margin-bottom: 1.5rem;
        }
        .card-header {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px 12px 0 0 !important;
            border-bottom: 2px solid #e0e0e0;
            font-weight: 600;
        }
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .table thead th {
            border: none;
            font-weight: 600;
            padding: 1rem;
        }
        .table tbody tr {
            transition: all 0.2s;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
            border-radius: 6px;
        }
        .info-box {
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .info-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .small-box {
            border-radius: 12px;
            transition: all 0.3s;
            overflow: hidden;
        }
        .small-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .small-box .icon {
            opacity: 0.3;
            font-size: 4rem;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar .nav-link {
            color: white !important;
        }
        .navbar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            border-radius: 6px;
        }
        .app-footer {
            background: #f8f9fa;
            border-top: 2px solid #e0e0e0;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .breadcrumb {
            background: transparent;
            padding: 0;
        }
        .content-header h1 {
            color: #333;
            font-weight: 700;
        }
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }
        .table-bordered {
            border-radius: 8px;
            overflow: hidden;
        }
        .table-bordered td, .table-bordered th {
            border: 1px solid #e0e0e0;
            padding: 0.75rem;
        }
        .table-bordered th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        .text-danger {
            color: #dc3545 !important;
        }
    </style>
    
    @stack('styles')
</head>
<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <!-- Header -->
        <nav class="app-header navbar navbar-expand">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list fs-4"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a href="{{ route('dashboard') }}" class="nav-link">
                            <i class="bi bi-house-door me-1"></i> Trang chủ
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown" href="#">
                            <i class="bi bi-person-circle me-2 fs-5"></i>
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            <span class="badge bg-light text-dark ms-2">{{ auth()->user()->isAdmin() ? 'Admin' : 'Staff' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Hồ sơ</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Sidebar -->
        <aside class="app-sidebar bg-body" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="{{ route('dashboard') }}" class="brand-link">
                    <i class="bi bi-box-seam me-2"></i>
                    <span class="brand-text">Logistics Nội Bộ</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-box-seam"></i>
                                    <p>Quản lý Hàng hóa</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.suppliers.index') }}" class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-truck"></i>
                                    <p>Nhà cung cấp</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.import-orders.index') }}" class="nav-link {{ request()->routeIs('admin.import-orders.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-box-arrow-in-down"></i>
                                    <p>Phiếu Nhập</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.export-orders.index') }}" class="nav-link {{ request()->routeIs('admin.export-orders.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-box-arrow-up"></i>
                                    <p>Phiếu Xuất</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.material-requests.index') }}" class="nav-link {{ request()->routeIs('admin.material-requests.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-clipboard-check"></i>
                                    <p>Yêu cầu Vật tư</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.employees.index') }}" class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-people"></i>
                                    <p>Quản lý Nhân viên</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.tasks.index') }}" class="nav-link {{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-list-check"></i>
                                    <p>Phân công Công việc</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-graph-up"></i>
                                    <p>Báo cáo</p>
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('staff.tasks.index') }}" class="nav-link {{ request()->routeIs('staff.tasks.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-list-check"></i>
                                    <p>Công việc của tôi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('staff.import-orders.index') }}" class="nav-link {{ request()->routeIs('staff.import-orders.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-box-arrow-in-down"></i>
                                    <p>Xử lý Phiếu Nhập</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('staff.export-orders.index') }}" class="nav-link {{ request()->routeIs('staff.export-orders.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-box-arrow-up"></i>
                                    <p>Xử lý Phiếu Xuất</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('staff.inventory.index') }}" class="nav-link {{ request()->routeIs('staff.inventory.*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-clipboard-check"></i>
                                    <p>Kiểm kê</p>
                                </a>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Có lỗi xảy ra:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="app-footer">
            <div class="float-end d-none d-sm-inline">
                <strong>Hệ thống Quản lý Kho v1.0</strong>
            </div>
            <strong>Copyright &copy; 2025</strong> - Đồ án Tốt nghiệp
        </footer>
    </div>

    <!-- AdminLTE JS -->
    <script src="{{ asset('adminlte/js/adminlte.js') }}"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- OverlayScrollbars -->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"></script>
    
    @stack('scripts')
</body>
</html>

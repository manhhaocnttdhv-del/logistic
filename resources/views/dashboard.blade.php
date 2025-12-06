@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item active">Trang chủ</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            @if(auth()->user()->isAdmin())
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ \App\Models\Product::count() }}</h3>
                            <p><i class="bi bi-box-seam me-1"></i>Tổng số hàng hóa</p>
                        </div>
                        <div class="icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <a href="{{ route('admin.products.index') }}" class="small-box-footer">
                            Xem chi tiết <i class="bi bi-arrow-right-circle ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ \App\Models\ImportOrder::where('status', 'pending')->count() }}</h3>
                            <p><i class="bi bi-box-arrow-in-down me-1"></i>Phiếu nhập chờ xử lý</p>
                        </div>
                        <div class="icon">
                            <i class="bi bi-box-arrow-in-down"></i>
                        </div>
                        <a href="{{ route('admin.import-orders.index') }}" class="small-box-footer">
                            Xem chi tiết <i class="bi bi-arrow-right-circle ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ \App\Models\ExportOrder::where('status', 'pending')->count() }}</h3>
                            <p><i class="bi bi-box-arrow-up me-1"></i>Phiếu xuất chờ xử lý</p>
                        </div>
                        <div class="icon">
                            <i class="bi bi-box-arrow-up"></i>
                        </div>
                        <a href="{{ route('admin.export-orders.index') }}" class="small-box-footer">
                            Xem chi tiết <i class="bi bi-arrow-right-circle ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ \App\Models\Task::where('status', 'pending')->count() }}</h3>
                            <p><i class="bi bi-list-check me-1"></i>Công việc chờ xử lý</p>
                        </div>
                        <div class="icon">
                            <i class="bi bi-list-check"></i>
                        </div>
                        <a href="{{ route('admin.tasks.index') }}" class="small-box-footer">
                            Xem chi tiết <i class="bi bi-arrow-right-circle ms-1"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Additional Stats -->
                <div class="col-lg-3 col-6">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-primary"><i class="bi bi-truck"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Nhà cung cấp</span>
                            <span class="info-box-number">{{ \App\Models\Supplier::where('is_active', true)->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-success"><i class="bi bi-people"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Nhân viên</span>
                            <span class="info-box-number">{{ \App\Models\User::where('role', 'staff')->where('is_active', true)->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-warning"><i class="bi bi-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Phiếu nhập hoàn thành</span>
                            <span class="info-box-number">{{ \App\Models\ImportOrder::where('status', 'completed')->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-info"><i class="bi bi-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Phiếu xuất hoàn thành</span>
                            <span class="info-box-number">{{ \App\Models\ExportOrder::where('status', 'completed')->count() }}</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ auth()->user()->tasksAssigned()->where('status', 'pending')->count() }}</h3>
                            <p><i class="bi bi-list-check me-1"></i>Công việc chờ xử lý</p>
                        </div>
                        <div class="icon">
                            <i class="bi bi-list-check"></i>
                        </div>
                        <a href="{{ route('staff.tasks.index') }}" class="small-box-footer">
                            Xem chi tiết <i class="bi bi-arrow-right-circle ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ auth()->user()->importOrdersAssigned()->where('status', 'processing')->count() }}</h3>
                            <p><i class="bi bi-box-arrow-in-down me-1"></i>Phiếu nhập đang xử lý</p>
                        </div>
                        <div class="icon">
                            <i class="bi bi-box-arrow-in-down"></i>
                        </div>
                        <a href="{{ route('staff.import-orders.index') }}" class="small-box-footer">
                            Xem chi tiết <i class="bi bi-arrow-right-circle ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ auth()->user()->exportOrdersAssigned()->where('status', 'processing')->count() }}</h3>
                            <p><i class="bi bi-box-arrow-up me-1"></i>Phiếu xuất đang xử lý</p>
                        </div>
                        <div class="icon">
                            <i class="bi bi-box-arrow-up"></i>
                        </div>
                        <a href="{{ route('staff.export-orders.index') }}" class="small-box-footer">
                            Xem chi tiết <i class="bi bi-arrow-right-circle ms-1"></i>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

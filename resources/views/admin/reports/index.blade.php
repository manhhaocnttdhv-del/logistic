@extends('layouts.app')

@section('title', 'Báo cáo')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="bi bi-graph-up me-2"></i>Báo cáo & Thống kê</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Quick Statistics -->
        <div class="row mb-4">
            <div class="col-lg-3 col-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-info"><i class="bi bi-box-seam"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Tổng sản phẩm</span>
                        <span class="info-box-number">{{ number_format($totalProducts ?? 0) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-success"><i class="bi bi-currency-dollar"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Giá trị tồn kho</span>
                        <span class="info-box-number">{{ number_format($totalInventoryValue ?? 0, 0, ',', '.') }} đ</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-warning"><i class="bi bi-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Sản phẩm sắp hết</span>
                        <span class="info-box-number">{{ number_format($lowStockProducts ?? 0) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-primary"><i class="bi bi-people"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Nhân viên hoạt động</span>
                        <span class="info-box-number">{{ number_format($activeStaff ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Today's Activity -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-gradient-info text-white">
                        <h5 class="card-title mb-0"><i class="bi bi-calendar-day me-2"></i>Hoạt động hôm nay</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="bi bi-box-arrow-in-down text-info fs-1 d-block mb-2"></i>
                                    <h4 class="mb-0">{{ number_format($todayImports ?? 0) }}</h4>
                                    <small class="text-muted">Phiếu nhập</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="bi bi-box-arrow-up text-success fs-1 d-block mb-2"></i>
                                    <h4 class="mb-0">{{ number_format($todayExports ?? 0) }}</h4>
                                    <small class="text-muted">Phiếu xuất</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-gradient-success text-white">
                        <h5 class="card-title mb-0"><i class="bi bi-calendar-month me-2"></i>Tháng này</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="bi bi-arrow-down-circle text-info fs-1 d-block mb-2"></i>
                                    <h5 class="mb-0">{{ number_format($monthImports ?? 0, 0, ',', '.') }} đ</h5>
                                    <small class="text-muted">Tổng nhập</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="bi bi-arrow-up-circle text-success fs-1 d-block mb-2"></i>
                                    <h5 class="mb-0">{{ number_format($monthExports ?? 0, 0, ',', '.') }} đ</h5>
                                    <small class="text-muted">Tổng xuất</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Report Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="card-title mb-1">Báo cáo Tồn kho</h5>
                                <p class="mb-0 small opacity-75">Xem chi tiết tồn kho</p>
                            </div>
                            <i class="bi bi-box-seam fs-1 opacity-50"></i>
                        </div>
                        <a href="{{ route('admin.reports.inventory') }}" class="btn btn-light btn-sm w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i> Xem báo cáo
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="card-title mb-1">Nhập - Xuất - Tồn</h5>
                                <p class="mb-0 small opacity-75">Báo cáo NXT chi tiết</p>
                            </div>
                            <i class="bi bi-arrow-left-right fs-1 opacity-50"></i>
                        </div>
                        <a href="{{ route('admin.reports.import-export') }}" class="btn btn-light btn-sm w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i> Xem báo cáo
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="card-title mb-1">Hiệu suất Nhân viên</h5>
                                <p class="mb-0 small opacity-75">Đánh giá hiệu suất</p>
                            </div>
                            <i class="bi bi-people fs-1 opacity-50"></i>
                        </div>
                        <a href="{{ route('admin.reports.employee-performance') }}" class="btn btn-light btn-sm w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i> Xem báo cáo
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="card-title mb-1">Sắp Hết Hàng</h5>
                                <p class="mb-0 small opacity-75">
                                    @if(($lowStockProducts ?? 0) > 0)
                                        <span class="badge bg-danger">{{ $lowStockProducts }} sản phẩm</span>
                                    @else
                                        Tất cả đều ổn
                                    @endif
                                </p>
                            </div>
                            <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                        </div>
                        <a href="{{ route('admin.reports.low-stock') }}" class="btn btn-light btn-sm w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i> Xem báo cáo
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tasks Overview -->
        @if(($pendingTasks ?? 0) > 0 || ($overdueTasks ?? 0) > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-gradient-warning text-dark">
                        <h5 class="card-title mb-0"><i class="bi bi-list-check me-2"></i>Công việc đang chờ</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="alert alert-warning mb-0">
                                    <i class="bi bi-clock-history me-2"></i>
                                    <strong>{{ number_format($pendingTasks ?? 0) }}</strong> công việc đang chờ xử lý
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-danger mb-0">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>{{ number_format($overdueTasks ?? 0) }}</strong> công việc quá hạn
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.tasks.index') }}" class="btn btn-primary">
                                <i class="bi bi-list-check me-1"></i> Xem danh sách công việc
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
@endsection


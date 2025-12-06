@extends('layouts.app')

@section('title', 'Báo cáo Tồn kho')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Báo cáo Tồn kho</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="card-title mb-0 text-white">
                            <i class="bi bi-box-seam me-2"></i>Báo cáo Tồn kho
                        </h3>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('admin.reports.inventory') }}" class="d-flex gap-2">
                            <select name="period" class="form-select" style="width: auto;">
                                <option value="all" {{ $period == 'all' ? 'selected' : '' }}>Tất cả</option>
                                <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Hôm nay</option>
                                <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Tuần này</option>
                                <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Tháng này</option>
                                <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>Quý này</option>
                                <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Năm nay</option>
                            </select>
                            <button type="submit" class="btn btn-light btn-sm"><i class="bi bi-search me-1"></i>Lọc</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-info"><i class="bi bi-box-seam"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Tổng sản phẩm</span>
                                <span class="info-box-number">{{ number_format($totalProducts ?? 0) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-success"><i class="bi bi-currency-dollar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Tổng giá trị</span>
                                <span class="info-box-number">{{ number_format($totalValue ?? 0, 0, ',', '.') }} đ</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-warning"><i class="bi bi-exclamation-triangle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Sắp hết hàng</span>
                                <span class="info-box-number">{{ number_format($lowStockCount ?? 0) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-danger"><i class="bi bi-x-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Hết hàng</span>
                                <span class="info-box-number">{{ number_format($outOfStockCount ?? 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Danh sách tồn kho</h5>
                    <a href="{{ route('admin.reports.export-excel', ['type' => 'inventory', 'period' => $period]) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Mã sản phẩm</th>
                            <th>Tên sản phẩm</th>
                            <th>Nhà cung cấp</th>
                            <th>Số lượng tồn</th>
                            <th>Giá trị</th>
                            <th>Đơn vị</th>
                            <th>Ngày cập nhật</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventories as $inventory)
                            @php
                                $productValue = $inventory->quantity * ($inventory->product->price ?? 0);
                            @endphp
                            <tr>
                                <td><strong>{{ $inventory->product->code }}</strong></td>
                                <td>{{ $inventory->product->name }}</td>
                                <td>{{ $inventory->product->supplier->name ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $inventory->product->isLowStock() ? 'bg-danger' : ($inventory->quantity <= 0 ? 'bg-dark' : 'bg-success') }}">
                                        {{ number_format($inventory->quantity, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>{{ number_format($productValue, 0, ',', '.') }} đ</td>
                                <td>{{ $inventory->product->unit }}</td>
                                <td>{{ $inventory->last_updated_date ? $inventory->last_updated_date->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                    <span class="text-muted">Không có dữ liệu</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


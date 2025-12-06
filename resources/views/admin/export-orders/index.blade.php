@extends('layouts.app')

@section('title', 'Quản lý Phiếu Xuất')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Quản lý Phiếu Xuất</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Phiếu Xuất</li>
                </ol>
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
                            <i class="bi bi-box-arrow-up me-2"></i>Danh sách Phiếu Xuất
                        </h3>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="btn-group">
                            <a href="{{ route('admin.export-orders.create') }}" class="btn btn-light btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Tạo phiếu xuất
                            </a>
                            <a href="{{ route('admin.export-orders.export') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.export-orders.index') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="🔍 Mã phiếu..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <button type="submit" class="btn btn-info"><i class="bi bi-search me-1"></i>Tìm kiếm</button>
                            <a href="{{ route('admin.export-orders.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-clockwise me-1"></i>Làm mới</a>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Mã phiếu</th>
                            <th>Ngày xuất</th>
                            <th>Người nhận</th>
                            <th>Lý do</th>
                            <th>Người tạo</th>
                            <th>Người xử lý</th>
                            <th>Tổng SL</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->code }}</td>
                                <td>{{ $order->export_date->format('d/m/Y') }}</td>
                                <td>{{ $order->recipient ?? '-' }}</td>
                                <td>
                                    @if($order->reason == 'sale')
                                        <span class="badge bg-primary">Bán hàng</span>
                                    @elseif($order->reason == 'transfer')
                                        <span class="badge bg-info">Luân chuyển</span>
                                    @elseif($order->reason == 'return')
                                        <span class="badge bg-warning">Trả hàng</span>
                                    @else
                                        <span class="badge bg-secondary">Khác</span>
                                    @endif
                                </td>
                                <td>{{ $order->creator->name }}</td>
                                <td>{{ $order->assignedStaff->name ?? '-' }}</td>
                                <td>{{ $order->total_quantity }}</td>
                                <td>
                                    @if($order->status == 'pending')
                                        <span class="badge bg-warning">Chờ xử lý</span>
                                    @elseif($order->status == 'processing')
                                        <span class="badge bg-info">Đang xử lý</span>
                                    @elseif($order->status == 'completed')
                                        <span class="badge bg-success">Hoàn thành</span>
                                    @else
                                        <span class="badge bg-secondary">Đã hủy</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.export-orders.show', $order) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($order->status == 'pending')
                                        <a href="{{ route('admin.export-orders.edit', $order) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                    <span class="text-muted">Không có dữ liệu</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
            @if($orders->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Hiển thị {{ $orders->firstItem() }} - {{ $orders->lastItem() }} / {{ $orders->total() }} kết quả
                    </div>
                    {{ $orders->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection


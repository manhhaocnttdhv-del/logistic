@extends('layouts.app')

@section('title', 'Phiếu Xuất của tôi')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Phiếu Xuất của tôi</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('staff.export-orders.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Tạo phiếu xuất
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary">
                <h3 class="card-title mb-0 text-white">
                    <i class="bi bi-box-arrow-up me-2"></i>Danh sách Phiếu Xuất
                </h3>
            </div>
            <div class="card-body p-3">
                <form method="GET" action="{{ route('staff.export-orders.index') }}" class="mb-3">
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
                            </select>
                        </div>
                        <div class="col-md-5">
                            <button type="submit" class="btn btn-info"><i class="bi bi-search me-1"></i>Tìm kiếm</button>
                            <a href="{{ route('staff.export-orders.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-clockwise me-1"></i>Làm mới</a>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Mã phiếu</th>
                                <th>Người nhận</th>
                                <th>Ngày xuất</th>
                                <th>Lý do</th>
                                <th>Tổng số lượng</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->code }}</td>
                                    <td>{{ $order->recipient ?? '-' }}</td>
                                    <td>{{ $order->export_date->format('d/m/Y') }}</td>
                                    <td>
                                        @php
                                            $reasons = ['sale' => 'Bán hàng', 'transfer' => 'Luân chuyển', 'return' => 'Trả hàng', 'other' => 'Khác'];
                                        @endphp
                                        {{ $reasons[$order->reason] ?? $order->reason }}
                                    </td>
                                    <td>{{ $order->total_quantity }}</td>
                                    <td>
                                        @if($order->status == 'pending')
                                            <span class="badge bg-warning">Chờ xử lý</span>
                                        @elseif($order->status == 'processing')
                                            <span class="badge bg-info">Đang xử lý</span>
                                        @else
                                            <span class="badge bg-success">Hoàn thành</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('staff.export-orders.show', $order) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                        <span class="text-muted">Không có phiếu xuất nào</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($orders->hasPages())
            <div class="card-footer bg-light">
                {{ $orders->links() }}
            </div>
            @endif
        </div>
    </div>
</section>
@endsection


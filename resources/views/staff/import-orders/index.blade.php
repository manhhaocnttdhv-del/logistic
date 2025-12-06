@extends('layouts.app')

@section('title', 'Phiếu Nhập của tôi')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Phiếu Nhập của tôi</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('staff.import-orders.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Tạo phiếu nhập
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
                    <i class="bi bi-box-arrow-in-down me-2"></i>Danh sách Phiếu Nhập
                </h3>
            </div>
            <div class="card-body p-3">
                <form method="GET" action="{{ route('staff.import-orders.index') }}" class="mb-3">
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
                            <a href="{{ route('staff.import-orders.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-clockwise me-1"></i>Làm mới</a>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Mã phiếu</th>
                                <th>Nhà cung cấp</th>
                                <th>Ngày nhập</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->code }}</td>
                                    <td>{{ $order->supplier->name ?? '-' }}</td>
                                    <td>{{ $order->import_date ? $order->import_date->format('d/m/Y') : '-' }}</td>
                                    <td>{{ number_format($order->total_amount, 0, ',', '.') }} đ</td>
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
                                        <a href="{{ route('staff.import-orders.show', $order) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                        <span class="text-muted">Không có phiếu nhập nào</span>
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


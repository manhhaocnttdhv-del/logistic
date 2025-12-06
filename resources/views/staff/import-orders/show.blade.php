@extends('layouts.app')

@section('title', 'Chi tiết Phiếu Nhập')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chi tiết Phiếu Nhập: {{ $order->code }}</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary">
                <h3 class="card-title mb-0 text-white">
                    <i class="bi bi-box-arrow-in-down me-2"></i>Thông tin phiếu nhập
                </h3>
            </div>
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Mã phiếu:</strong> {{ $order->code }}
                    </div>
                    <div class="col-md-6">
                        <strong>Nhà cung cấp:</strong> {{ $order->supplier->name ?? '-' }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Ngày nhập:</strong> {{ $order->import_date->format('d/m/Y') }}
                    </div>
                    <div class="col-md-6">
                        <strong>Trạng thái:</strong>
                        @if($order->status == 'pending')
                            <span class="badge bg-warning">Chờ xử lý</span>
                        @elseif($order->status == 'processing')
                            <span class="badge bg-info">Đang xử lý</span>
                        @else
                            <span class="badge bg-success">Hoàn thành</span>
                        @endif
                    </div>
                </div>
                @if($order->notes)
                <div class="mb-3">
                    <strong>Ghi chú:</strong> {{ $order->notes }}
                </div>
                @endif
            </div>
        </div>

        <div class="card shadow-sm mt-3">
            <div class="card-header bg-gradient-primary">
                <h3 class="card-title mb-0 text-white">
                    <i class="bi bi-list-ul me-2"></i>Chi tiết hàng hóa
                </h3>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                                <th>Tồn kho hiện tại</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->product->name }} ({{ $item->product->code }})</td>
                                    <td><strong>{{ $item->quantity }} {{ $item->product->unit }}</strong></td>
                                    <td>{{ number_format($item->unit_price, 0, ',', '.') }} đ</td>
                                    <td>{{ number_format($item->total_price, 0, ',', '.') }} đ</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $item->product->current_stock }} {{ $item->product->unit }}
                                        </span>
                                        @if($order->status == 'completed')
                                            <br><small class="text-success">
                                                → {{ $item->product->current_stock }} {{ $item->product->unit }}
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Tổng cộng:</th>
                                <th>{{ number_format($order->total_amount, 0, ',', '.') }} đ</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        @if($order->status == 'pending' || $order->status == 'processing')
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-gradient-primary">
                <h3 class="card-title mb-0 text-white">
                    <i class="bi bi-gear me-2"></i>Xử lý phiếu nhập
                </h3>
            </div>
            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('staff.import-orders.process', $order->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Ghi chú (tùy chọn)</label>
                        <textarea name="staff_notes" class="form-control" rows="3" placeholder="Nhập ghi chú...">{{ $order->staff_notes }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        @if($order->status == 'pending')
                            <input type="hidden" name="action" value="start">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-play-fill me-1"></i>Bắt đầu xử lý
                            </button>
                        @elseif($order->status == 'processing')
                            <input type="hidden" name="action" value="complete">
                            <button type="submit" class="btn btn-success" onclick="return confirm('Xác nhận hoàn thành phiếu nhập? Tồn kho sẽ được cập nhật.');">
                                <i class="bi bi-check-circle me-1"></i>Hoàn thành
                            </button>
                        @endif
                        <a href="{{ route('staff.import-orders.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="card-footer bg-light mt-3">
            <a href="{{ route('staff.import-orders.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Quay lại
            </a>
        </div>
        @endif
    </div>
</section>
@endsection


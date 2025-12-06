@extends('layouts.app')

@section('title', 'Chi tiết Phiếu Nhập')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chi tiết Phiếu Nhập: {{ $importOrder->code }}</h1>
            </div>
            <div class="col-sm-6">
                <a href="{{ route('admin.import-orders.index') }}" class="btn btn-secondary float-end">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title mb-0 text-white">
                            <i class="bi bi-info-circle me-2"></i>Thông tin phiếu nhập
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Mã phiếu</th>
                                <td>{{ $importOrder->code }}</td>
                            </tr>
                            <tr>
                                <th>Nhà cung cấp</th>
                                <td>{{ $importOrder->supplier->name }}</td>
                            </tr>
                            <tr>
                                <th>Ngày nhập</th>
                                <td>{{ $importOrder->import_date->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Người tạo</th>
                                <td>{{ $importOrder->creator->name }}</td>
                            </tr>
                            <tr>
                                <th>Người xử lý</th>
                                <td>{{ $importOrder->assignedStaff->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    @if($importOrder->status == 'pending')
                                        <span class="badge bg-warning">Chờ xử lý</span>
                                    @elseif($importOrder->status == 'processing')
                                        <span class="badge bg-info">Đang xử lý</span>
                                    @elseif($importOrder->status == 'completed')
                                        <span class="badge bg-success">Hoàn thành</span>
                                    @else
                                        <span class="badge bg-secondary">Đã hủy</span>
                                    @endif
                                </td>
                            </tr>
                            @if($importOrder->confirmed_at)
                                <tr>
                                    <th>Người xác nhận</th>
                                    <td>{{ $importOrder->confirmer->name }} - {{ $importOrder->confirmed_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>Ghi chú</th>
                                <td>{{ $importOrder->notes ?? '-' }}</td>
                            </tr>
                            @if($importOrder->staff_notes)
                                <tr>
                                    <th>Ghi chú nhân viên</th>
                                    <td>{{ $importOrder->staff_notes }}</td>
                                </tr>
                            @endif
                        </table>
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
                            <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($importOrder->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->product->name }} ({{ $item->product->code }})</td>
                                        <td>{{ $item->quantity }} {{ $item->product->unit }}</td>
                                        <td>{{ number_format($item->unit_price, 0, ',', '.') }} đ</td>
                                        <td>{{ number_format($item->total_price, 0, ',', '.') }} đ</td>
                                        <td>{{ $item->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Tổng cộng:</th>
                                    <th>{{ number_format($importOrder->total_amount, 0, ',', '.') }} đ</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title mb-0 text-white">
                            <i class="bi bi-gear me-2"></i>Thao tác
                        </h3>
                    </div>
                    <div class="card-body p-3">
                        @if($importOrder->status == 'pending')
                            <a href="{{ route('admin.import-orders.edit', $importOrder) }}" class="btn btn-warning btn-block mb-2">
                                <i class="bi bi-pencil"></i> Chỉnh sửa
                            </a>
                        @endif
                        @if($importOrder->status == 'processing')
                            <form action="{{ route('admin.import-orders.confirm', $importOrder) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Xác nhận phiếu nhập này?');">
                                    <i class="bi bi-check-circle"></i> Xác nhận hoàn thành
                                </button>
                            </form>
                        @endif
                        @if($importOrder->status == 'completed')
                            <a href="{{ route('admin.import-orders.pdf', $importOrder) }}" class="btn btn-danger btn-block" target="_blank">
                                <i class="bi bi-file-pdf"></i> Xuất PDF
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


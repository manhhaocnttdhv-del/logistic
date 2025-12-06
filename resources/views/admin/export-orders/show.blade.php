@extends('layouts.app')

@section('title', 'Chi tiết Phiếu Xuất')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chi tiết Phiếu Xuất: {{ $exportOrder->code }}</h1>
            </div>
            <div class="col-sm-6">
                <a href="{{ route('admin.export-orders.index') }}" class="btn btn-secondary float-end">
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
                            <i class="bi bi-info-circle me-2"></i>Thông tin phiếu xuất
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Mã phiếu</th>
                                <td>{{ $exportOrder->code }}</td>
                            </tr>
                            <tr>
                                <th>Ngày xuất</th>
                                <td>{{ $exportOrder->export_date->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Người nhận</th>
                                <td>{{ $exportOrder->recipient ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Lý do xuất</th>
                                <td>
                                    @if($exportOrder->reason == 'sale')
                                        <span class="badge bg-primary">Bán hàng</span>
                                    @elseif($exportOrder->reason == 'transfer')
                                        <span class="badge bg-info">Luân chuyển</span>
                                    @elseif($exportOrder->reason == 'return')
                                        <span class="badge bg-warning">Trả hàng</span>
                                    @else
                                        <span class="badge bg-secondary">Khác</span>
                                    @endif
                                    @if($exportOrder->reason_detail)
                                        - {{ $exportOrder->reason_detail }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Người tạo</th>
                                <td>{{ $exportOrder->creator->name }}</td>
                            </tr>
                            <tr>
                                <th>Người xử lý</th>
                                <td>{{ $exportOrder->assignedStaff->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    @if($exportOrder->status == 'pending')
                                        <span class="badge bg-warning">Chờ xử lý</span>
                                    @elseif($exportOrder->status == 'processing')
                                        <span class="badge bg-info">Đang xử lý</span>
                                    @elseif($exportOrder->status == 'completed')
                                        <span class="badge bg-success">Hoàn thành</span>
                                    @else
                                        <span class="badge bg-secondary">Đã hủy</span>
                                    @endif
                                </td>
                            </tr>
                            @if($exportOrder->confirmed_at)
                                <tr>
                                    <th>Người xác nhận</th>
                                    <td>{{ $exportOrder->confirmer->name }} - {{ $exportOrder->confirmed_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>Ghi chú</th>
                                <td>{{ $exportOrder->notes ?? '-' }}</td>
                            </tr>
                            @if($exportOrder->staff_notes)
                                <tr>
                                    <th>Ghi chú nhân viên</th>
                                    <td>{{ $exportOrder->staff_notes }}</td>
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
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exportOrder->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->product->name }} ({{ $item->product->code }})</td>
                                        <td>{{ $item->quantity }} {{ $item->product->unit }}</td>
                                        <td>{{ $item->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Tổng cộng:</th>
                                    <th>{{ $exportOrder->total_quantity }}</th>
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
                        @if($exportOrder->status == 'pending')
                            <a href="{{ route('admin.export-orders.edit', $exportOrder) }}" class="btn btn-warning btn-block mb-2">
                                <i class="bi bi-pencil"></i> Chỉnh sửa
                            </a>
                        @endif
                        @if($exportOrder->status == 'processing')
                            <form action="{{ route('admin.export-orders.confirm', $exportOrder) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Xác nhận phiếu xuất này? Tồn kho sẽ được giảm.');">
                                    <i class="bi bi-check-circle"></i> Xác nhận hoàn thành
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


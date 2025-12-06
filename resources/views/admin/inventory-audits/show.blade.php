@extends('layouts.app')

@section('title', 'Chi tiết Phiếu Kiểm toán')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chi tiết Phiếu Kiểm toán: {{ $inventoryAudit->code }}</h1>
            </div>
            <div class="col-sm-6">
                <a href="{{ route('admin.inventory-audits.index') }}" class="btn btn-secondary float-end">
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
                            <i class="bi bi-info-circle me-2"></i>Thông tin phiếu kiểm toán
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Mã phiếu</th>
                                <td><strong>{{ $inventoryAudit->code }}</strong></td>
                            </tr>
                            <tr>
                                <th>Kho kiểm toán</th>
                                <td>{{ $inventoryAudit->warehouse->name ?? '-' }} @if($inventoryAudit->warehouse) ({{ $inventoryAudit->warehouse->code }}) @endif</td>
                            </tr>
                            <tr>
                                <th>Ngày kiểm toán</th>
                                <td>{{ $inventoryAudit->audit_date->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Loại kiểm toán</th>
                                <td>
                                    @if($inventoryAudit->type == 'full')
                                        <span class="badge bg-primary">Kiểm toán toàn bộ kho</span>
                                    @elseif($inventoryAudit->type == 'partial')
                                        <span class="badge bg-info">Kiểm toán một phần</span>
                                    @else
                                        <span class="badge bg-warning">Kiểm toán đột xuất</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Người tạo</th>
                                <td>{{ $inventoryAudit->creator->name }}</td>
                            </tr>
                            <tr>
                                <th>Người được giao</th>
                                <td>{{ $inventoryAudit->assignedStaff->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    @if($inventoryAudit->status == 'pending')
                                        <span class="badge bg-warning">Chờ xử lý</span>
                                    @elseif($inventoryAudit->status == 'in_progress')
                                        <span class="badge bg-info">Đang kiểm toán</span>
                                    @elseif($inventoryAudit->status == 'completed')
                                        <span class="badge bg-success">Hoàn thành</span>
                                    @else
                                        <span class="badge bg-secondary">Đã hủy</span>
                                    @endif
                                </td>
                            </tr>
                            @if($inventoryAudit->confirmed_at)
                                <tr>
                                    <th>Người xác nhận</th>
                                    <td>{{ $inventoryAudit->confirmer->name }} - {{ $inventoryAudit->confirmed_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>Ghi chú</th>
                                <td>{{ $inventoryAudit->notes ?? '-' }}</td>
                            </tr>
                            @if($inventoryAudit->staff_notes)
                                <tr>
                                    <th>Ghi chú nhân viên</th>
                                    <td>{{ $inventoryAudit->staff_notes }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title mb-0 text-white">
                            <i class="bi bi-list-ul me-2"></i>Chi tiết kiểm toán
                        </h3>
                    </div>
                    <div class="card-body p-3">
                        @if($inventoryAudit->items->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Sản phẩm</th>
                                            <th>Số lượng hệ thống</th>
                                            <th>Số lượng thực tế</th>
                                            <th>Chênh lệch</th>
                                            <th>Ghi chú</th>
                                            <th>Đã điều chỉnh</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inventoryAudit->items as $index => $item)
                                            <tr class="{{ $item->difference != 0 ? 'table-warning' : '' }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $item->product->name }}</strong><br>
                                                    <small class="text-muted">{{ $item->product->code }} - {{ $item->product->unit }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $item->system_quantity }} {{ $item->product->unit }}</span>
                                                </td>
                                                <td>
                                                    @if($inventoryAudit->status == 'completed')
                                                        <span class="badge bg-info">{{ $item->actual_quantity }} {{ $item->product->unit }}</span>
                                                    @else
                                                        <span class="text-muted">Chưa nhập</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->difference > 0)
                                                        <span class="badge bg-success">+{{ $item->difference }} {{ $item->product->unit }}</span>
                                                    @elseif($item->difference < 0)
                                                        <span class="badge bg-danger">{{ $item->difference }} {{ $item->product->unit }}</span>
                                                    @else
                                                        <span class="badge bg-success">Khớp</span>
                                                    @endif
                                                </td>
                                                <td><small>{{ $item->notes ?? '-' }}</small></td>
                                                <td>
                                                    @if($item->is_adjusted)
                                                        <span class="badge bg-success">Đã điều chỉnh</span>
                                                    @else
                                                        <span class="badge bg-secondary">Chưa điều chỉnh</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-info">
                                            <th colspan="2">Tổng kết:</th>
                                            <th>Tổng: {{ $inventoryAudit->total_items }}</th>
                                            <th></th>
                                            <th>
                                                Khớp: <span class="text-success">{{ $inventoryAudit->matched_items }}</span><br>
                                                Chênh: <span class="text-danger">{{ $inventoryAudit->mismatched_items }}</span>
                                            </th>
                                            <th colspan="2"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center py-3">Chưa có sản phẩm nào trong phiếu kiểm toán</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-info">
                        <h3 class="card-title mb-0 text-white">
                            <i class="bi bi-bar-chart me-2"></i>Thống kê
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Tổng số sản phẩm:</strong>
                            <span class="badge bg-primary float-end">{{ $inventoryAudit->total_items }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Sản phẩm khớp:</strong>
                            <span class="badge bg-success float-end">{{ $inventoryAudit->matched_items }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Sản phẩm chênh lệch:</strong>
                            <span class="badge bg-danger float-end">{{ $inventoryAudit->mismatched_items }}</span>
                        </div>
                        @if($inventoryAudit->total_items > 0)
                            <div class="progress mb-3" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($inventoryAudit->matched_items / $inventoryAudit->total_items) * 100 }}%">
                                    {{ round(($inventoryAudit->matched_items / $inventoryAudit->total_items) * 100, 1) }}%
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title mb-0 text-white">
                            <i class="bi bi-gear me-2"></i>Thao tác
                        </h3>
                    </div>
                    <div class="card-body p-3">
                        @if($inventoryAudit->status == 'pending')
                            <a href="{{ route('admin.inventory-audits.edit', $inventoryAudit) }}" class="btn btn-warning btn-block mb-2">
                                <i class="bi bi-pencil"></i> Chỉnh sửa
                            </a>
                        @endif
                        @if($inventoryAudit->status == 'completed' && !$inventoryAudit->confirmed_by)
                            <form action="{{ route('admin.inventory-audits.confirm', $inventoryAudit) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Xác nhận phiếu kiểm toán này?');">
                                    <i class="bi bi-check-circle"></i> Xác nhận
                                </button>
                            </form>
                        @endif
                        @if($inventoryAudit->status == 'completed' && $inventoryAudit->confirmed_by && $inventoryAudit->mismatched_items > 0)
                            @php
                                $hasUnadjusted = $inventoryAudit->items->where('difference', '!=', 0)->where('is_adjusted', false)->count() > 0;
                            @endphp
                            @if($hasUnadjusted)
                                <form action="{{ route('admin.inventory-audits.adjust', $inventoryAudit) }}" method="POST" class="mb-2">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-block" onclick="return confirm('Điều chỉnh tồn kho theo số lượng thực tế? Hành động này không thể hoàn tác.');">
                                        <i class="bi bi-arrow-repeat"></i> Điều chỉnh tồn kho
                                    </button>
                                </form>
                            @else
                                <p class="text-success text-center mb-0">
                                    <i class="bi bi-check-circle"></i> Đã điều chỉnh tồn kho
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


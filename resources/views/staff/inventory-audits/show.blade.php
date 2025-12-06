@extends('layouts.app')

@section('title', 'Chi tiết Phiếu Kiểm toán')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chi tiết Phiếu Kiểm toán: {{ $audit->code }}</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary">
                <h3 class="card-title mb-0 text-white">
                    <i class="bi bi-info-circle me-2"></i>Thông tin phiếu kiểm toán
                </h3>
            </div>
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Mã phiếu:</strong> {{ $audit->code }}
                    </div>
                    <div class="col-md-6">
                        <strong>Kho:</strong> {{ $audit->warehouse->name ?? '-' }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Ngày kiểm toán:</strong> {{ $audit->audit_date ? $audit->audit_date->format('d/m/Y') : '-' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Trạng thái:</strong>
                        @if($audit->status == 'pending')
                            <span class="badge bg-warning">Chờ xử lý</span>
                        @elseif($audit->status == 'in_progress')
                            <span class="badge bg-info">Đang kiểm toán</span>
                        @else
                            <span class="badge bg-success">Hoàn thành</span>
                        @endif
                    </div>
                </div>
                @if($audit->notes)
                <div class="mb-3">
                    <strong>Ghi chú:</strong> {{ $audit->notes }}
                </div>
                @endif
            </div>
        </div>

        <div class="card shadow-sm mt-3">
            <div class="card-header bg-gradient-primary">
                <h3 class="card-title mb-0 text-white">
                    <i class="bi bi-list-ul me-2"></i>Chi tiết kiểm toán
                </h3>
            </div>
            <div class="card-body p-3">
                @if($audit->items->count() > 0)
                    @if($audit->status == 'pending' || $audit->status == 'in_progress')
                        <form action="{{ route('staff.inventory-audits.update-items', $audit->id) }}" method="POST" id="auditItemsForm">
                            @csrf
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($audit->items as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $item->product->name }}</strong><br>
                                                    <small class="text-muted">{{ $item->product->code }} - {{ $item->product->unit }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $item->system_quantity }} {{ $item->product->unit }}</span>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                    <input type="number" 
                                                           name="items[{{ $index }}][actual_quantity]" 
                                                           class="form-control actual-quantity-input" 
                                                           value="{{ $item->actual_quantity }}" 
                                                           min="0" 
                                                           data-system-qty="{{ $item->system_quantity }}"
                                                           onchange="calculateDifference(this, {{ $index }})"
                                                           style="width: 100px;">
                                                </td>
                                                <td>
                                                    <span id="difference-{{ $index }}" class="badge">
                                                        @if($item->actual_quantity > 0)
                                                            @php
                                                                $diff = $item->actual_quantity - $item->system_quantity;
                                                            @endphp
                                                            @if($diff > 0)
                                                                <span class="badge bg-success">+{{ $diff }}</span>
                                                            @elseif($diff < 0)
                                                                <span class="badge bg-danger">{{ $diff }}</span>
                                                            @else
                                                                <span class="badge bg-success">Khớp</span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <input type="text" 
                                                           name="items[{{ $index }}][notes]" 
                                                           class="form-control form-control-sm" 
                                                           value="{{ $item->notes }}" 
                                                           placeholder="Lý do chênh lệch...">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>Lưu số lượng thực tế
                                </button>
                            </div>
                        </form>
                    @else
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($audit->items as $index => $item)
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
                                                <span class="badge bg-info">{{ $item->actual_quantity }} {{ $item->product->unit }}</span>
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
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-info">
                                        <th colspan="2">Tổng kết:</th>
                                        <th>Tổng: {{ $audit->total_items }}</th>
                                        <th></th>
                                        <th>
                                            Khớp: <span class="text-success">{{ $audit->matched_items }}</span><br>
                                            Chênh: <span class="text-danger">{{ $audit->mismatched_items }}</span>
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                @else
                    <p class="text-muted text-center py-3">Chưa có sản phẩm nào trong phiếu kiểm toán</p>
                @endif
            </div>
        </div>

        @if($audit->status == 'pending' || $audit->status == 'in_progress')
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-gradient-primary">
                <h3 class="card-title mb-0 text-white">
                    <i class="bi bi-gear me-2"></i>Xử lý phiếu kiểm toán
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
                <form action="{{ route('staff.inventory-audits.process', $audit->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Ghi chú (tùy chọn)</label>
                        <textarea name="staff_notes" class="form-control" rows="3" placeholder="Nhập ghi chú...">{{ $audit->staff_notes }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        @if($audit->status == 'pending')
                            <input type="hidden" name="action" value="start">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-play-fill me-1"></i>Bắt đầu kiểm toán
                            </button>
                        @elseif($audit->status == 'in_progress')
                            <input type="hidden" name="action" value="complete">
                            <button type="submit" class="btn btn-success" onclick="return confirm('Xác nhận hoàn thành kiểm toán? Hệ thống sẽ tính thống kê.');">
                                <i class="bi bi-check-circle me-1"></i>Hoàn thành kiểm toán
                            </button>
                        @endif
                        <a href="{{ route('staff.inventory-audits.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="card-footer bg-light mt-3">
            <a href="{{ route('staff.inventory-audits.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Quay lại
            </a>
        </div>
        @endif
    </div>
</section>

<script>
function calculateDifference(input, index) {
    const systemQty = parseInt(input.dataset.systemQty || 0);
    const actualQty = parseInt(input.value || 0);
    const difference = actualQty - systemQty;
    const diffElement = document.getElementById('difference-' + index);
    
    if (actualQty === 0) {
        diffElement.innerHTML = '<span class="text-muted">-</span>';
    } else if (difference > 0) {
        diffElement.innerHTML = '<span class="badge bg-success">+' + difference + '</span>';
    } else if (difference < 0) {
        diffElement.innerHTML = '<span class="badge bg-danger">' + difference + '</span>';
    } else {
        diffElement.innerHTML = '<span class="badge bg-success">Khớp</span>';
    }
}
</script>
@endsection


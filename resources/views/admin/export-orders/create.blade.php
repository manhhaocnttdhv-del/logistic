@extends('layouts.app')

@section('title', 'Tạo Phiếu Xuất')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Tạo Phiếu Xuất</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <form action="{{ route('admin.export-orders.store') }}" method="POST" id="exportOrderForm">
            @csrf
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px 12px 0 0;">
                    <h3 class="card-title mb-0 fw-bold">
                        <i class="bi bi-info-circle-fill me-2"></i>Thông tin phiếu xuất
                    </h3>
                </div>
                <div class="card-body p-4 bg-white">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event me-1 text-primary"></i>Ngày xuất <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="export_date" class="form-control shadow-sm @error('export_date') is-invalid @enderror" value="{{ old('export_date', date('Y-m-d')) }}" required style="border-radius: 8px;">
                                @error('export_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person-check me-1 text-primary"></i>Phân công theo chức vụ
                                </label>
                                <select name="position" id="position" class="form-select shadow-sm @error('position') is-invalid @enderror" style="border-radius: 8px;">
                                    <option value="">Tự động phân công (khuyến nghị)</option>
                                    @foreach($positions as $key => $label)
                                        <option value="{{ $key }}" {{ old('position') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1">Hệ thống sẽ tự động chọn nhân viên xuất kho phù hợp</small>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person me-1 text-secondary"></i>Hoặc chọn nhân viên cụ thể
                                </label>
                                <select name="assigned_to" id="assigned_to" class="form-select shadow-sm @error('assigned_to') is-invalid @enderror" style="border-radius: 8px;">
                                    <option value="">Chọn nhân viên (tùy chọn)</option>
                                    @foreach($staff as $s)
                                        <option value="{{ $s->id }}" {{ old('assigned_to') == $s->id ? 'selected' : '' }}>
                                            {{ $s->name }} @if($s->position) ({{ $positions[$s->position] ?? '' }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1">Nếu chọn nhân viên cụ thể, hệ thống sẽ bỏ qua phân công tự động</small>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person me-1 text-primary"></i>Người nhận
                                </label>
                                <input type="text" name="recipient" class="form-control shadow-sm @error('recipient') is-invalid @enderror" value="{{ old('recipient') }}" placeholder="Nhập tên người nhận..." style="border-radius: 8px;">
                                @error('recipient')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-question-circle me-1 text-primary"></i>Lý do xuất <span class="text-danger">*</span>
                                </label>
                                <select name="reason" class="form-select shadow-sm @error('reason') is-invalid @enderror" required style="border-radius: 8px;">
                                    <option value="internal" {{ old('reason') == 'internal' ? 'selected' : '' }}>Sử dụng nội bộ</option>
                                    <option value="sale" {{ old('reason') == 'sale' ? 'selected' : '' }}>Bán hàng</option>
                                    <option value="transfer" {{ old('reason') == 'transfer' ? 'selected' : '' }}>Điều chuyển</option>
                                    <option value="return_supplier" {{ old('reason') == 'return_supplier' ? 'selected' : '' }}>Trả nhà cung cấp</option>
                                    <option value="return" {{ old('reason') == 'return' ? 'selected' : '' }}>Trả hàng</option>
                                    <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>Khác</option>
                                </select>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-building me-1 text-primary"></i>Phòng ban/Bộ phận
                                </label>
                                <input type="text" name="department" class="form-control shadow-sm @error('department') is-invalid @enderror" value="{{ old('department') }}" placeholder="Nhập phòng ban..." style="border-radius: 8px;">
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-folder me-1 text-primary"></i>Dự án/Chương trình
                                </label>
                                <input type="text" name="project" class="form-control shadow-sm @error('project') is-invalid @enderror" value="{{ old('project') }}" placeholder="Nhập dự án..." style="border-radius: 8px;">
                                @error('project')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-file-text me-1 text-primary"></i>Chi tiết lý do
                        </label>
                        <textarea name="reason_detail" class="form-control shadow-sm @error('reason_detail') is-invalid @enderror" rows="2" placeholder="Nhập chi tiết lý do xuất..." style="border-radius: 8px;">{{ old('reason_detail') }}</textarea>
                        @error('reason_detail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-pencil-square me-1 text-primary"></i>Ghi chú
                        </label>
                        <textarea name="notes" class="form-control shadow-sm @error('notes') is-invalid @enderror" rows="2" placeholder="Nhập ghi chú..." style="border-radius: 8px;">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card shadow-lg border-0">
                <div class="card-header text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px 12px 0 0;">
                    <h3 class="card-title mb-0 fw-bold">
                        <i class="bi bi-box-seam me-2"></i>Chi tiết hàng hóa
                    </h3>
                    <button type="button" class="btn btn-light btn-sm fw-semibold shadow-sm" onclick="addItem()" style="border-radius: 8px;">
                        <i class="bi bi-plus-circle-fill me-1"></i> Thêm hàng
                    </button>
                </div>
                <div class="card-body p-4" style="background: #f8f9fa;">
                    <div id="items-container">
                        <div class="item-row mb-4 p-3 bg-white rounded shadow-sm border" style="border-left: 4px solid #667eea !important;">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold mb-2">
                                        <i class="bi bi-box me-1 text-primary"></i>Sản phẩm <span class="text-danger">*</span>
                                    </label>
                                    <select name="items[0][product_id]" class="form-select product-select shadow-sm" required onchange="updateStock(this)" style="border-radius: 8px;">
                                        <option value="">Chọn sản phẩm</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-stock="{{ $product->current_stock }}">
                                                {{ $product->name }} ({{ $product->code }}) - Tồn: {{ $product->current_stock }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold mb-2">
                                        <i class="bi bi-123 me-1 text-primary"></i>Số lượng <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="items[0][quantity]" class="form-control quantity-input shadow-sm" min="1" value="1" required onchange="checkStock(this)" style="border-radius: 8px;">
                                    <small class="text-muted stock-info d-block mt-1">
                                        <i class="bi bi-inbox me-1"></i>Tồn kho: -
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold mb-2">
                                        <i class="bi bi-pencil-square me-1 text-primary"></i>Ghi chú
                                    </label>
                                    <input type="text" name="items[0][notes]" class="form-control shadow-sm" placeholder="Nhập ghi chú..." style="border-radius: 8px;">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label fw-semibold mb-2 d-block" style="visibility: hidden;">&nbsp;</label>
                                    <button type="button" class="btn btn-danger shadow-sm w-100" onclick="removeItem(this)" style="border-radius: 8px;" title="Xóa dòng này">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top" style="border-radius: 0 0 12px 12px;">
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <div>
                            <strong class="fs-5 text-dark">
                                <i class="bi bi-calculator me-2 text-primary"></i>Tổng số lượng: 
                                <span id="total-quantity" class="text-primary fw-bold" style="font-size: 1.5rem;">0</span>
                            </strong>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm fw-semibold" style="border-radius: 8px;">
                                <i class="bi bi-check-circle-fill me-1"></i>Lưu phiếu xuất
                            </button>
                            <a href="{{ route('admin.export-orders.index') }}" class="btn btn-secondary px-4 py-2 shadow-sm fw-semibold" style="border-radius: 8px;">
                                <i class="bi bi-x-circle-fill me-1"></i>Hủy
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@push('styles')
<style>
    .item-row {
        transition: all 0.3s ease;
    }
    .item-row:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15) !important;
        border-left-color: #764ba2 !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #5568d3 0%, #653a8f 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4) !important;
    }
    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3) !important;
    }
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4) !important;
    }
    .btn-light:hover {
        background-color: #fff !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    }
    .item-row .form-label {
        margin-bottom: 0.5rem;
    }
    .item-row .row {
        align-items: flex-end;
    }
</style>
@endpush

@push('scripts')
<script>
let itemIndex = 1;

function addItem() {
    const container = document.getElementById('items-container');
    const newItem = document.createElement('div');
    newItem.className = 'item-row mb-4 p-3 bg-white rounded shadow-sm border';
    newItem.style.borderLeft = '4px solid #667eea';
    newItem.innerHTML = `
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold mb-2">
                    <i class="bi bi-box me-1 text-primary"></i>Sản phẩm <span class="text-danger">*</span>
                </label>
                <select name="items[${itemIndex}][product_id]" class="form-select product-select shadow-sm" required onchange="updateStock(this)" style="border-radius: 8px;">
                    <option value="">Chọn sản phẩm</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-stock="{{ $product->current_stock }}">
                            {{ $product->name }} ({{ $product->code }}) - Tồn: {{ $product->current_stock }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold mb-2">
                    <i class="bi bi-123 me-1 text-primary"></i>Số lượng <span class="text-danger">*</span>
                </label>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity-input shadow-sm" min="1" value="1" required onchange="checkStock(this)" style="border-radius: 8px;">
                <small class="text-muted stock-info d-block mt-1">
                    <i class="bi bi-inbox me-1"></i>Tồn kho: -
                </small>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold mb-2">
                    <i class="bi bi-pencil-square me-1 text-primary"></i>Ghi chú
                </label>
                <input type="text" name="items[${itemIndex}][notes]" class="form-control shadow-sm" placeholder="Nhập ghi chú..." style="border-radius: 8px;">
            </div>
            <div class="col-md-1">
                <label class="form-label fw-semibold mb-2 d-block" style="visibility: hidden;">&nbsp;</label>
                <button type="button" class="btn btn-danger shadow-sm w-100" onclick="removeItem(this)" style="border-radius: 8px;" title="Xóa dòng này">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newItem);
    itemIndex++;
    calculateTotal();
}

function removeItem(btn) {
    if (document.querySelectorAll('.item-row').length <= 1) {
        alert('Phải có ít nhất một sản phẩm');
        return;
    }
    btn.closest('.item-row').remove();
    calculateTotal();
}

function updateStock(select) {
    const row = select.closest('.item-row');
    const stock = select.options[select.selectedIndex].dataset.stock || 0;
    row.querySelector('.stock-info').innerHTML = `<i class="bi bi-inbox me-1"></i>Tồn kho: ${stock}`;
    checkStock(row.querySelector('.quantity-input'));
}

function checkStock(input) {
    const row = input.closest('.item-row');
    const select = row.querySelector('.product-select');
    const stock = parseInt(select.options[select.selectedIndex]?.dataset.stock || 0);
    const quantity = parseInt(input.value || 0);
    
    if (quantity > stock) {
        input.classList.add('is-invalid');
        row.querySelector('.stock-info').innerHTML = `<span class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle-fill me-1"></i>Tồn kho: ${stock} (Không đủ!)</span>`;
    } else {
        input.classList.remove('is-invalid');
        row.querySelector('.stock-info').innerHTML = `<i class="bi bi-inbox me-1"></i>Tồn kho: ${stock}`;
    }
    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
        total += quantity;
    });
    document.getElementById('total-quantity').textContent = total;
}

// Initialize
document.querySelectorAll('.product-select').forEach(select => {
    select.addEventListener('change', function() { updateStock(this); });
});
document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('input', function() { checkStock(this); calculateTotal(); });
});
calculateTotal();
</script>
@endpush
@endsection


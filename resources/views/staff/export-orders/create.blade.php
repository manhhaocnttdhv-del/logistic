@extends('layouts.app')

@section('title', 'Tạo Phiếu Xuất')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Tạo Phiếu Xuất</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('staff.export-orders.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <form action="{{ route('staff.export-orders.store') }}" method="POST" id="exportOrderForm">
            @csrf
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary">
                    <h3 class="card-title mb-0 text-white">
                        <i class="bi bi-info-circle me-2"></i>Thông tin phiếu xuất
                    </h3>
                </div>
                <div class="card-body p-4">
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
                                    <i class="bi bi-person me-1 text-primary"></i>Người nhận
                                </label>
                                <input type="text" name="recipient" class="form-control shadow-sm @error('recipient') is-invalid @enderror" value="{{ old('recipient') }}" placeholder="Nhập tên người nhận..." style="border-radius: 8px;">
                                @error('recipient')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-file-text me-1 text-primary"></i>Chi tiết lý do
                                </label>
                                <input type="text" name="reason_detail" class="form-control shadow-sm @error('reason_detail') is-invalid @enderror" value="{{ old('reason_detail') }}" placeholder="Nhập chi tiết lý do..." style="border-radius: 8px;">
                                @error('reason_detail')
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
                            <i class="bi bi-pencil-square me-1 text-primary"></i>Ghi chú
                        </label>
                        <textarea name="notes" class="form-control shadow-sm @error('notes') is-invalid @enderror" rows="2" placeholder="Nhập ghi chú..." style="border-radius: 8px;">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0 text-white">
                        <i class="bi bi-list-ul me-2"></i>Chi tiết hàng hóa
                    </h3>
                    <button type="button" class="btn btn-light btn-sm" onclick="addItem()">
                        <i class="bi bi-plus-circle me-1"></i> Thêm hàng
                    </button>
                </div>
                <div class="card-body p-4">
                    <div id="items-container">
                        <div class="item-row row mb-3">
                            <div class="col-md-5">
                                <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                                <select name="items[0][product_id]" class="form-select product-select" required onchange="updateStock(this)">
                                    <option value="">Chọn sản phẩm</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-stock="{{ $product->current_stock }}">
                                            {{ $product->name }} ({{ $product->code }}) - Tồn: {{ $product->current_stock }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                                <input type="number" name="items[0][quantity]" class="form-control quantity-input" min="1" value="1" required onchange="checkStock(this)">
                                <small class="text-muted stock-info d-block mt-1">
                                    <i class="bi bi-inbox me-1"></i>Tồn kho: -
                                </small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ghi chú</label>
                                <input type="text" name="items[0][notes]" class="form-control" placeholder="Nhập ghi chú...">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-block" onclick="removeItem(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="fs-5">Tổng số lượng: <span id="total-quantity" class="text-primary">0</span></strong>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-1"></i>Lưu phiếu xuất
                            </button>
                            <a href="{{ route('staff.export-orders.index') }}" class="btn btn-secondary px-4">
                                <i class="bi bi-x-circle me-1"></i>Hủy
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
let itemIndex = 1;

function addItem() {
    const container = document.getElementById('items-container');
    const newItem = document.createElement('div');
    newItem.className = 'item-row row mb-3';
    newItem.innerHTML = `
        <div class="col-md-5">
            <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
            <select name="items[${itemIndex}][product_id]" class="form-select product-select" required onchange="updateStock(this)">
                <option value="">Chọn sản phẩm</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-stock="{{ $product->current_stock }}">
                        {{ $product->name }} ({{ $product->code }}) - Tồn: {{ $product->current_stock }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Số lượng <span class="text-danger">*</span></label>
            <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity-input" min="1" value="1" required onchange="checkStock(this)">
            <small class="text-muted stock-info d-block mt-1">
                <i class="bi bi-inbox me-1"></i>Tồn kho: -
            </small>
        </div>
        <div class="col-md-3">
            <label class="form-label">Ghi chú</label>
            <input type="text" name="items[${itemIndex}][notes]" class="form-control" placeholder="Nhập ghi chú...">
        </div>
        <div class="col-md-1">
            <label class="form-label">&nbsp;</label>
            <button type="button" class="btn btn-danger btn-block" onclick="removeItem(this)">
                <i class="bi bi-trash"></i>
            </button>
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
calculateTotal();
</script>
@endpush
@endsection


@extends('layouts.app')

@section('title', 'Tạo Phiếu Nhập')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Tạo Phiếu Nhập</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('staff.import-orders.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <form action="{{ route('staff.import-orders.store') }}" method="POST" id="importOrderForm">
            @csrf
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary">
                    <h3 class="card-title mb-0 text-white">
                        <i class="bi bi-info-circle me-2"></i>Thông tin phiếu nhập
                    </h3>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-arrow-down-circle me-1 text-primary"></i>Nguồn nhập <span class="text-danger">*</span>
                                </label>
                                <select name="source" id="source" class="form-select shadow-sm @error('source') is-invalid @enderror" required style="border-radius: 8px;">
                                    <option value="supplier" {{ old('source') == 'supplier' ? 'selected' : '' }}>Từ nhà cung cấp</option>
                                    <option value="transfer" {{ old('source') == 'transfer' ? 'selected' : '' }}>Điều chuyển từ kho khác</option>
                                    <option value="return" {{ old('source') == 'return' ? 'selected' : '' }}>Trả hàng</option>
                                    <option value="other" {{ old('source') == 'other' ? 'selected' : '' }}>Khác</option>
                                </select>
                                @error('source')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3" id="supplier-field">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-truck me-1 text-primary"></i>Nhà cung cấp <span class="text-danger">*</span>
                                </label>
                                <select name="supplier_id" class="form-select shadow-sm @error('supplier_id') is-invalid @enderror" style="border-radius: 8px;">
                                    <option value="">Chọn nhà cung cấp</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3" id="warehouse-field" style="display: none;">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-box-seam me-1 text-primary"></i>Kho điều chuyển từ
                                </label>
                                <input type="text" name="from_warehouse" class="form-control shadow-sm @error('from_warehouse') is-invalid @enderror" value="{{ old('from_warehouse') }}" placeholder="Nhập tên kho..." style="border-radius: 8px;">
                                @error('from_warehouse')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event me-1 text-primary"></i>Ngày nhập <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="import_date" class="form-control shadow-sm @error('import_date') is-invalid @enderror" value="{{ old('import_date', date('Y-m-d')) }}" required style="border-radius: 8px;">
                                @error('import_date')
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
                            <div class="col-md-4">
                                <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                                <select name="items[0][product_id]" class="form-select product-select" required>
                                    <option value="">Chọn sản phẩm</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                            {{ $product->name }} ({{ $product->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                                <input type="number" name="items[0][quantity]" class="form-control quantity-input" min="1" value="1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Đơn giá <span class="text-danger">*</span></label>
                                <input type="number" name="items[0][unit_price]" class="form-control price-input" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Thành tiền</label>
                                <input type="text" class="form-control total-input" readonly value="0">
                            </div>
                            <div class="col-md-2">
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
                            <strong class="fs-5">Tổng tiền: <span id="grand-total" class="text-primary">0</span> đ</strong>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-1"></i>Lưu phiếu nhập
                            </button>
                            <a href="{{ route('staff.import-orders.index') }}" class="btn btn-secondary px-4">
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
        <div class="col-md-4">
            <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
            <select name="items[${itemIndex}][product_id]" class="form-select product-select" required>
                <option value="">Chọn sản phẩm</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                        {{ $product->name }} ({{ $product->code }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Số lượng <span class="text-danger">*</span></label>
            <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity-input" min="1" value="1" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Đơn giá <span class="text-danger">*</span></label>
            <input type="number" name="items[${itemIndex}][unit_price]" class="form-control price-input" min="0" step="0.01" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Thành tiền</label>
            <input type="text" class="form-control total-input" readonly value="0">
        </div>
        <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <button type="button" class="btn btn-danger btn-block" onclick="removeItem(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newItem);
    itemIndex++;
    attachEventListeners(newItem);
}

function removeItem(btn) {
    if (document.querySelectorAll('.item-row').length <= 1) {
        alert('Phải có ít nhất một sản phẩm');
        return;
    }
    btn.closest('.item-row').remove();
    calculateTotal();
}

function attachEventListeners(item) {
    const productSelect = item.querySelector('.product-select');
    const quantityInput = item.querySelector('.quantity-input');
    const priceInput = item.querySelector('.price-input');
    
    productSelect.addEventListener('change', function() {
        const price = this.options[this.selectedIndex].dataset.price;
        if (price) {
            priceInput.value = price;
            calculateItemTotal(item);
        }
    });
    
    quantityInput.addEventListener('input', () => calculateItemTotal(item));
    priceInput.addEventListener('input', () => calculateItemTotal(item));
}

function calculateItemTotal(item) {
    const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
    const price = parseFloat(item.querySelector('.price-input').value) || 0;
    const total = quantity * price;
    item.querySelector('.total-input').value = total.toLocaleString('vi-VN');
    calculateTotal();
}

function calculateTotal() {
    let grandTotal = 0;
    document.querySelectorAll('.item-row').forEach(item => {
        const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(item.querySelector('.price-input').value) || 0;
        grandTotal += quantity * price;
    });
    document.getElementById('grand-total').textContent = grandTotal.toLocaleString('vi-VN');
}

// Attach listeners to initial items
document.querySelectorAll('.item-row').forEach(item => attachEventListeners(item));

// Handle source change
document.getElementById('source').addEventListener('change', function() {
    const source = this.value;
    const supplierField = document.getElementById('supplier-field');
    const warehouseField = document.getElementById('warehouse-field');
    const supplierSelect = supplierField.querySelector('select');
    
    if (source === 'supplier') {
        supplierField.style.display = 'block';
        supplierSelect.required = true;
        warehouseField.style.display = 'none';
    } else if (source === 'transfer') {
        supplierField.style.display = 'none';
        supplierSelect.required = false;
        supplierSelect.value = '';
        warehouseField.style.display = 'block';
    } else {
        supplierField.style.display = 'none';
        supplierSelect.required = false;
        supplierSelect.value = '';
        warehouseField.style.display = 'none';
    }
});

// Initialize on page load
document.getElementById('source').dispatchEvent(new Event('change'));
</script>
@endpush
@endsection


@extends('layouts.app')

@section('title', 'Chỉnh sửa Phiếu Xuất')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chỉnh sửa Phiếu Xuất</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <form action="{{ route('admin.export-orders.update', $exportOrder) }}" method="POST" id="exportOrderForm">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thông tin phiếu xuất</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày xuất <span class="text-danger">*</span></label>
                                <input type="date" name="export_date" class="form-control @error('export_date') is-invalid @enderror" value="{{ old('export_date', $exportOrder->export_date ? $exportOrder->export_date->format('Y-m-d') : '') }}" required>
                                @error('export_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Giao cho nhân viên</label>
                                <select name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror">
                                    <option value="">Chọn nhân viên</option>
                                    @foreach($staff as $s)
                                        <option value="{{ $s->id }}" {{ old('assigned_to', $exportOrder->assigned_to) == $s->id ? 'selected' : '' }}>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Người nhận</label>
                                <input type="text" name="recipient" class="form-control @error('recipient') is-invalid @enderror" value="{{ old('recipient', $exportOrder->recipient) }}">
                                @error('recipient')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Lý do xuất <span class="text-danger">*</span></label>
                                <select name="reason" class="form-select @error('reason') is-invalid @enderror" required>
                                    <option value="sale" {{ old('reason', $exportOrder->reason) == 'sale' ? 'selected' : '' }}>Bán hàng</option>
                                    <option value="transfer" {{ old('reason', $exportOrder->reason) == 'transfer' ? 'selected' : '' }}>Luân chuyển</option>
                                    <option value="return" {{ old('reason', $exportOrder->reason) == 'return' ? 'selected' : '' }}>Trả hàng</option>
                                    <option value="other" {{ old('reason', $exportOrder->reason) == 'other' ? 'selected' : '' }}>Khác</option>
                                </select>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chi tiết lý do</label>
                        <textarea name="reason_detail" class="form-control @error('reason_detail') is-invalid @enderror" rows="2">{{ old('reason_detail', $exportOrder->reason_detail) }}</textarea>
                        @error('reason_detail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes', $exportOrder->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Chi tiết hàng hóa</h3>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addItem()">
                        <i class="bi bi-plus-circle"></i> Thêm hàng
                    </button>
                </div>
                <div class="card-body">
                    <div id="items-container">
                        @foreach($exportOrder->items as $index => $item)
                            <div class="item-row row mb-3">
                                <div class="col-md-5">
                                    <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                                    <select name="items[{{ $index }}][product_id]" class="form-select product-select" required onchange="updateStock(this)">
                                        <option value="">Chọn sản phẩm</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-stock="{{ $product->current_stock }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} ({{ $product->code }}) - Tồn: {{ $product->current_stock }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity-input" min="1" value="{{ $item->quantity }}" required onchange="checkStock(this)">
                                    <small class="text-muted stock-info">Tồn kho: -</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ghi chú</label>
                                    <input type="text" name="items[{{ $index }}][notes]" class="form-control" value="{{ $item->notes }}" placeholder="Ghi chú">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-block" onclick="removeItem(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>Tổng số lượng: <span id="total-quantity">0</span></strong>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                            <a href="{{ route('admin.export-orders.index') }}" class="btn btn-secondary">Hủy</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
let itemIndex = {{ $exportOrder->items->count() }};

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
            <small class="text-muted stock-info">Tồn kho: -</small>
        </div>
        <div class="col-md-3">
            <label class="form-label">Ghi chú</label>
            <input type="text" name="items[${itemIndex}][notes]" class="form-control" placeholder="Ghi chú">
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
    row.querySelector('.stock-info').textContent = `Tồn kho: ${stock}`;
    checkStock(row.querySelector('.quantity-input'));
}

function checkStock(input) {
    const row = input.closest('.item-row');
    const select = row.querySelector('.product-select');
    const stock = parseInt(select.options[select.selectedIndex]?.dataset.stock || 0);
    const quantity = parseInt(input.value || 0);
    
    if (quantity > stock) {
        input.classList.add('is-invalid');
        row.querySelector('.stock-info').innerHTML = `<span class="text-danger">Tồn kho: ${stock} (Không đủ!)</span>`;
    } else {
        input.classList.remove('is-invalid');
        row.querySelector('.stock-info').textContent = `Tồn kho: ${stock}`;
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
    if (select.value) updateStock(select);
});
document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('input', function() { checkStock(this); calculateTotal(); });
});
calculateTotal();
</script>
@endpush
@endsection


@extends('layouts.app')

@section('title', 'Chỉnh sửa Yêu cầu Vật tư')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chỉnh sửa Yêu cầu Vật tư: {{ $materialRequest->code }}</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <form action="{{ route('admin.material-requests.update', $materialRequest) }}" method="POST" id="materialRequestForm">
            @csrf
            @method('PUT')
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary">
                    <h3 class="card-title mb-0 text-white">
                        <i class="bi bi-info-circle me-2"></i>Thông tin yêu cầu
                    </h3>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person me-1 text-primary"></i>Người yêu cầu <span class="text-danger">*</span>
                                </label>
                                <select name="requested_by" class="form-select shadow-sm @error('requested_by') is-invalid @enderror" required style="border-radius: 8px;">
                                    <option value="">Chọn người yêu cầu</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('requested_by', $materialRequest->requested_by) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('requested_by')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-building me-1 text-primary"></i>Phòng ban
                                </label>
                                <input type="text" name="department" class="form-control shadow-sm @error('department') is-invalid @enderror" value="{{ old('department', $materialRequest->department) }}" placeholder="Nhập phòng ban..." style="border-radius: 8px;">
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-folder me-1 text-primary"></i>Dự án/Chương trình
                                </label>
                                <input type="text" name="project" class="form-control shadow-sm @error('project') is-invalid @enderror" value="{{ old('project', $materialRequest->project) }}" placeholder="Nhập dự án/chương trình..." style="border-radius: 8px;">
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
                        <textarea name="notes" class="form-control shadow-sm @error('notes') is-invalid @enderror" rows="2" placeholder="Nhập ghi chú..." style="border-radius: 8px;">{{ old('notes', $materialRequest->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0 text-white">
                        <i class="bi bi-list-ul me-2"></i>Chi tiết vật tư yêu cầu
                    </h3>
                    <button type="button" class="btn btn-light btn-sm" onclick="addItem()">
                        <i class="bi bi-plus-circle me-1"></i> Thêm vật tư
                    </button>
                </div>
                <div class="card-body p-4">
                    <div id="items-container">
                        @foreach($materialRequest->items as $index => $item)
                            <div class="item-row row mb-3 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                                    <select name="items[{{ $index }}][product_id]" class="form-select product-select" required>
                                        <option value="">Chọn sản phẩm</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-stock="{{ $product->current_stock }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} ({{ $product->code }}) - Tồn: {{ $product->current_stock }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity-input" min="1" value="{{ $item->quantity }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ghi chú</label>
                                    <input type="text" name="items[{{ $index }}][notes]" class="form-control" value="{{ $item->notes }}" placeholder="Ghi chú...">
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
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="fs-5">Tổng số lượng: <span id="total-quantity" class="text-primary">0</span></strong>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-1"></i>Cập nhật yêu cầu
                            </button>
                            <a href="{{ route('admin.material-requests.show', $materialRequest) }}" class="btn btn-secondary px-4">
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
let itemIndex = {{ $materialRequest->items->count() }};

function addItem() {
    const container = document.getElementById('items-container');
    const newItem = document.createElement('div');
    newItem.className = 'item-row row mb-3 align-items-end';
    newItem.innerHTML = `
        <div class="col-md-5">
            <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
            <select name="items[${itemIndex}][product_id]" class="form-select product-select" required>
                <option value="">Chọn sản phẩm</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-stock="{{ $product->current_stock }}">
                        {{ $product->name }} ({{ $product->code }}) - Tồn: {{ $product->current_stock }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Số lượng <span class="text-danger">*</span></label>
            <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity-input" min="1" value="1" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Ghi chú</label>
            <input type="text" name="items[${itemIndex}][notes]" class="form-control" placeholder="Ghi chú...">
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
    attachEventListeners(newItem);
}

function removeItem(btn) {
    if (document.querySelectorAll('.item-row').length <= 1) {
        alert('Phải có ít nhất một vật tư');
        return;
    }
    btn.closest('.item-row').remove();
    calculateTotal();
}

function attachEventListeners(item) {
    const quantityInput = item.querySelector('.quantity-input');
    quantityInput.addEventListener('input', () => {
        validateQuantity(item);
        calculateTotal();
    });
    
    const productSelect = item.querySelector('.product-select');
    productSelect.addEventListener('change', () => {
        validateQuantity(item);
    });
}

function validateQuantity(item) {
    const productSelect = item.querySelector('.product-select');
    const quantityInput = item.querySelector('.quantity-input');
    
    if (productSelect.value) {
        const stock = parseInt(productSelect.options[productSelect.selectedIndex].dataset.stock) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        
        if (quantity > stock) {
            quantityInput.setCustomValidity(`Số lượng yêu cầu (${quantity}) vượt quá tồn kho (${stock})`);
            quantityInput.reportValidity();
        } else {
            quantityInput.setCustomValidity('');
        }
    }
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(item => {
        const quantity = parseInt(item.querySelector('.quantity-input').value) || 0;
        total += quantity;
    });
    document.getElementById('total-quantity').textContent = total;
}

// Attach listeners to initial items
document.querySelectorAll('.item-row').forEach(item => attachEventListeners(item));

// Calculate initial total
calculateTotal();
</script>
@endpush
@endsection


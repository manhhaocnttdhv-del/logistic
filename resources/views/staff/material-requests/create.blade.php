@extends('layouts.app')

@section('title', 'Tạo Yêu cầu Vật tư')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Tạo Yêu cầu Vật tư</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('staff.material-requests.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <form action="{{ route('staff.material-requests.store') }}" method="POST" id="materialRequestForm">
            @csrf
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
                                    <i class="bi bi-building me-1 text-primary"></i>Phòng ban
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
                                <input type="text" name="project" class="form-control shadow-sm @error('project') is-invalid @enderror" value="{{ old('project') }}" placeholder="Nhập dự án/chương trình..." style="border-radius: 8px;">
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
                        <i class="bi bi-list-ul me-2"></i>Chi tiết vật tư yêu cầu
                    </h3>
                    <button type="button" class="btn btn-light btn-sm" onclick="addItem()">
                        <i class="bi bi-plus-circle me-1"></i> Thêm vật tư
                    </button>
                </div>
                <div class="card-body p-4">
                    <div id="items-container">
                        <div class="item-row row mb-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                                <select name="items[0][product_id]" class="form-select product-select" required>
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
                                <input type="number" name="items[0][quantity]" class="form-control quantity-input" min="1" value="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Ghi chú</label>
                                <input type="text" name="items[0][notes]" class="form-control" placeholder="Ghi chú...">
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
                                <i class="bi bi-check-circle me-1"></i>Tạo yêu cầu
                            </button>
                            <a href="{{ route('staff.material-requests.index') }}" class="btn btn-secondary px-4">
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

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
        total += quantity;
    });
    document.getElementById('total-quantity').textContent = total;
}

// Initialize
document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('input', calculateTotal);
});
calculateTotal();
</script>
@endpush
@endsection


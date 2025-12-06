@extends('layouts.app')

@section('title', 'Tạo Phiếu Kiểm toán')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Tạo Phiếu Kiểm toán</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <form action="{{ route('admin.inventory-audits.store') }}" method="POST" id="auditForm">
            @csrf
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary">
                    <h3 class="card-title mb-0 text-white">
                        <i class="bi bi-clipboard-check me-2"></i>Thông tin phiếu kiểm toán
                    </h3>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-box-seam me-1 text-primary"></i>Kho kiểm toán
                                </label>
                                <select name="warehouse_id" id="warehouse_id" class="form-select shadow-sm @error('warehouse_id') is-invalid @enderror" style="border-radius: 8px;">
                                    <option value="">Chọn kho (tùy chọn)</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }} ({{ $warehouse->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event me-1 text-primary"></i>Ngày kiểm toán <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="audit_date" class="form-control shadow-sm @error('audit_date') is-invalid @enderror" value="{{ old('audit_date', date('Y-m-d')) }}" required style="border-radius: 8px;">
                                @error('audit_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-list-check me-1 text-primary"></i>Loại kiểm toán <span class="text-danger">*</span>
                                </label>
                                <select name="type" id="audit_type" class="form-select shadow-sm @error('type') is-invalid @enderror" required style="border-radius: 8px;">
                                    <option value="full" {{ old('type', 'full') == 'full' ? 'selected' : '' }}>Kiểm toán toàn bộ kho</option>
                                    <option value="partial" {{ old('type') == 'partial' ? 'selected' : '' }}>Kiểm toán một phần</option>
                                    <option value="spot" {{ old('type') == 'spot' ? 'selected' : '' }}>Kiểm toán đột xuất</option>
                                </select>
                                <small class="text-muted">
                                    <span id="type-help-full" style="display: none;">Kiểm toán tất cả sản phẩm trong kho</span>
                                    <span id="type-help-partial" style="display: none;">Kiểm toán một số sản phẩm được chọn</span>
                                    <span id="type-help-spot" style="display: none;">Kiểm toán đột xuất một số sản phẩm</span>
                                </small>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person me-1 text-primary"></i>Phân công nhân viên
                                </label>
                                <select name="position" id="position" class="form-select shadow-sm @error('position') is-invalid @enderror" style="border-radius: 8px;">
                                    <option value="">Tự động phân công (khuyến nghị)</option>
                                    @foreach($positions as $key => $label)
                                        <option value="{{ $key }}" {{ old('position') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hệ thống sẽ tự động chọn nhân viên kiểm toán phù hợp</small>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Hoặc chọn nhân viên cụ thể</label>
                                <select name="assigned_to" id="assigned_to" class="form-select shadow-sm @error('assigned_to') is-invalid @enderror" style="border-radius: 8px;">
                                    <option value="">Chọn nhân viên (tùy chọn)</option>
                                    @foreach($staff as $s)
                                        <option value="{{ $s->id }}" {{ old('assigned_to') == $s->id ? 'selected' : '' }}>
                                            {{ $s->name }} @if($s->position) ({{ $positions[$s->position] ?? '' }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div id="products-selection" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-box-seam me-1 text-primary"></i>Chọn sản phẩm cần kiểm toán <span class="text-danger">*</span>
                            </label>
                            <select name="products[]" id="products" class="form-select shadow-sm" multiple size="10" style="border-radius: 8px;">
                                @php
                                    $allProducts = \App\Models\Product::where('is_active', true)->get();
                                @endphp
                                @foreach($allProducts as $product)
                                    <option value="{{ $product->id }}" {{ in_array($product->id, old('products', [])) ? 'selected' : '' }}>
                                        {{ $product->code }} - {{ $product->name }} ({{ $product->unit }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều sản phẩm</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-file-text me-1 text-primary"></i>Ghi chú
                        </label>
                        <textarea name="notes" class="form-control shadow-sm @error('notes') is-invalid @enderror" rows="3" style="border-radius: 8px;" placeholder="Mục đích, lý do kiểm toán...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-1"></i>Tạo phiếu
                        </button>
                        <a href="{{ route('admin.inventory-audits.index') }}" class="btn btn-secondary px-4">
                            <i class="bi bi-x-circle me-1"></i>Hủy
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
document.getElementById('audit_type').addEventListener('change', function() {
    const type = this.value;
    const productsSelection = document.getElementById('products-selection');
    const productsSelect = document.getElementById('products');
    
    // Hide/show help text
    document.querySelectorAll('[id^="type-help-"]').forEach(el => el.style.display = 'none');
    document.getElementById('type-help-' + type).style.display = 'inline';
    
    // Show products selection for partial and spot
    if (type === 'partial' || type === 'spot') {
        productsSelection.style.display = 'block';
        productsSelect.setAttribute('required', 'required');
    } else {
        productsSelection.style.display = 'none';
        productsSelect.removeAttribute('required');
    }
});

// Trigger on page load
document.getElementById('audit_type').dispatchEvent(new Event('change'));
</script>
@endsection


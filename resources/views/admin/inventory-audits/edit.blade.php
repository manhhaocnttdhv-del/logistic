@extends('layouts.app')

@section('title', 'Chỉnh sửa Phiếu Kiểm toán')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chỉnh sửa Phiếu Kiểm toán</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary">
                <h3 class="card-title mb-0 text-white">
                    <i class="bi bi-pencil me-2"></i>Thông tin phiếu kiểm toán
                </h3>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.inventory-audits.update', $inventoryAudit) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-box-seam me-1 text-primary"></i>Kho kiểm toán
                                </label>
                                <select name="warehouse_id" class="form-select shadow-sm @error('warehouse_id') is-invalid @enderror" style="border-radius: 8px;">
                                    <option value="">Chọn kho (tùy chọn)</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $inventoryAudit->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
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
                                <input type="date" name="audit_date" class="form-control shadow-sm @error('audit_date') is-invalid @enderror" value="{{ old('audit_date', $inventoryAudit->audit_date ? $inventoryAudit->audit_date->format('Y-m-d') : '') }}" required style="border-radius: 8px;">
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
                                <select name="type" class="form-select shadow-sm @error('type') is-invalid @enderror" required style="border-radius: 8px;">
                                    <option value="full" {{ old('type', $inventoryAudit->type) == 'full' ? 'selected' : '' }}>Kiểm toán toàn bộ kho</option>
                                    <option value="partial" {{ old('type', $inventoryAudit->type) == 'partial' ? 'selected' : '' }}>Kiểm toán một phần</option>
                                    <option value="spot" {{ old('type', $inventoryAudit->type) == 'spot' ? 'selected' : '' }}>Kiểm toán đột xuất</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person me-1 text-primary"></i>Người được giao
                                </label>
                                <select name="assigned_to" class="form-select shadow-sm @error('assigned_to') is-invalid @enderror" style="border-radius: 8px;">
                                    <option value="">Chọn nhân viên (tùy chọn)</option>
                                    @foreach($staff as $s)
                                        <option value="{{ $s->id }}" {{ old('assigned_to', $inventoryAudit->assigned_to) == $s->id ? 'selected' : '' }}>
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
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-file-text me-1 text-primary"></i>Ghi chú
                        </label>
                        <textarea name="notes" class="form-control shadow-sm @error('notes') is-invalid @enderror" rows="3" style="border-radius: 8px;">{{ old('notes', $inventoryAudit->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-1"></i>Cập nhật
                        </button>
                        <a href="{{ route('admin.inventory-audits.show', $inventoryAudit) }}" class="btn btn-secondary px-4">
                            <i class="bi bi-x-circle me-1"></i>Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection


@extends('layouts.app')

@section('title', 'Chỉnh sửa Kho')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chỉnh sửa Kho</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary">
                <h3 class="card-title mb-0 text-white">
                    <i class="bi bi-pencil me-2"></i>Thông tin Kho
                </h3>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.warehouses.update', $warehouse) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-hash me-1 text-primary"></i>Mã kho <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="code" class="form-control shadow-sm @error('code') is-invalid @enderror" value="{{ old('code', $warehouse->code) }}" required style="border-radius: 8px;">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-building me-1 text-primary"></i>Tên kho <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" class="form-control shadow-sm @error('name') is-invalid @enderror" value="{{ old('name', $warehouse->name) }}" required style="border-radius: 8px;">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt me-1 text-primary"></i>Địa chỉ
                                </label>
                                <input type="text" name="address" class="form-control shadow-sm @error('address') is-invalid @enderror" value="{{ old('address', $warehouse->address) }}" style="border-radius: 8px;">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person me-1 text-primary"></i>Người quản lý
                                </label>
                                <input type="text" name="manager_name" class="form-control shadow-sm @error('manager_name') is-invalid @enderror" value="{{ old('manager_name', $warehouse->manager_name) }}" style="border-radius: 8px;">
                                @error('manager_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-telephone me-1 text-primary"></i>Số điện thoại
                                </label>
                                <input type="text" name="phone" class="form-control shadow-sm @error('phone') is-invalid @enderror" value="{{ old('phone', $warehouse->phone) }}" style="border-radius: 8px;">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-toggle-on me-1 text-primary"></i>Trạng thái
                                </label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ old('is_active', $warehouse->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label">Đang hoạt động</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-file-text me-1 text-primary"></i>Mô tả
                        </label>
                        <textarea name="description" class="form-control shadow-sm @error('description') is-invalid @enderror" rows="3" style="border-radius: 8px;">{{ old('description', $warehouse->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-1"></i>Cập nhật
                        </button>
                        <a href="{{ route('admin.warehouses.show', $warehouse) }}" class="btn btn-secondary px-4">
                            <i class="bi bi-x-circle me-1"></i>Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection


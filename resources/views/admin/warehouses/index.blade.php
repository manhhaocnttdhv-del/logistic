@extends('layouts.app')

@section('title', 'Quản lý Kho')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Quản lý Kho</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Kho</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="card-title mb-0 text-white">
                            <i class="bi bi-box-seam me-2"></i>Danh sách Kho
                        </h3>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('admin.warehouses.create') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Thêm mới
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.warehouses.index') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-8">
                            <input type="text" name="search" class="form-control" placeholder="🔍 Tìm kiếm..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-info"><i class="bi bi-search me-1"></i>Tìm kiếm</button>
                            <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-clockwise me-1"></i>Làm mới</a>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Mã kho</th>
                                <th>Tên kho</th>
                                <th>Địa chỉ</th>
                                <th>Người quản lý</th>
                                <th>Điện thoại</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($warehouses as $warehouse)
                                <tr>
                                    <td><strong>{{ $warehouse->code }}</strong></td>
                                    <td>{{ $warehouse->name }}</td>
                                    <td>{{ $warehouse->address ?? '-' }}</td>
                                    <td>{{ $warehouse->manager_name ?? '-' }}</td>
                                    <td>{{ $warehouse->phone ?? '-' }}</td>
                                    <td>
                                        @if($warehouse->is_active)
                                            <span class="badge bg-success">Đang hoạt động</span>
                                        @else
                                            <span class="badge bg-secondary">Ngừng hoạt động</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.warehouses.show', $warehouse) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.warehouses.edit', $warehouse) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.warehouses.destroy', $warehouse) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                        <span class="text-muted">Chưa có kho nào</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($warehouses->hasPages())
            <div class="card-footer bg-light">
                {{ $warehouses->links() }}
            </div>
            @endif
        </div>
    </div>
</section>
@endsection


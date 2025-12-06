@extends('layouts.app')

@section('title', 'Quản lý Nhà cung cấp')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Quản lý Nhà cung cấp</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Nhà cung cấp</li>
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
                            <i class="bi bi-truck me-2"></i>Danh sách Nhà cung cấp
                        </h3>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="btn-group">
                            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-light btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Thêm mới
                            </a>
                            <a href="{{ route('admin.suppliers.export') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel
                            </a>
                            <a href="{{ route('admin.suppliers.template') }}" class="btn btn-info btn-sm">
                                <i class="bi bi-download me-1"></i> Tải mẫu
                            </a>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="bi bi-upload me-1"></i> Nhập Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.suppliers.index') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-8">
                            <input type="text" name="search" class="form-control" placeholder="🔍 Tìm kiếm..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-info"><i class="bi bi-search me-1"></i>Tìm kiếm</button>
                            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-clockwise me-1"></i>Làm mới</a>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Tên nhà cung cấp</th>
                            <th>Người liên hệ</th>
                            <th>Điện thoại</th>
                            <th>Email</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td>{{ $supplier->code }}</td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->contact_person ?? '-' }}</td>
                                <td>{{ $supplier->phone ?? '-' }}</td>
                                <td>{{ $supplier->email ?? '-' }}</td>
                                <td>
                                    @if($supplier->is_active)
                                        <span class="badge bg-success">Đang hoạt động</span>
                                    @else
                                        <span class="badge bg-secondary">Ngừng hoạt động</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
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
                                    <span class="text-muted">Không có dữ liệu</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
            @if($suppliers->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Hiển thị {{ $suppliers->firstItem() }} - {{ $suppliers->lastItem() }} / {{ $suppliers->total() }} kết quả
                    </div>
                    {{ $suppliers->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white" id="importModalLabel">
                    <i class="bi bi-upload me-2"></i>Nhập dữ liệu từ Excel
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.suppliers.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label fw-semibold">
                            <i class="bi bi-file-earmark-excel me-1 text-primary"></i>Chọn file Excel
                        </label>
                        <input type="file" class="form-control shadow-sm" id="file" name="file" accept=".xlsx,.xls" required style="border-radius: 8px;">
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>Chỉ chấp nhận file .xlsx hoặc .xls (tối đa 10MB)
                        </small>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-lightbulb me-2"></i>
                        <strong>Lưu ý:</strong> Vui lòng tải mẫu Excel trước để đảm bảo định dạng đúng. File phải có header ở dòng đầu tiên.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i>Nhập dữ liệu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


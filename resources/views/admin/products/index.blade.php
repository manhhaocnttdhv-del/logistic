@extends('layouts.app')

@section('title', 'Quản lý Hàng hóa')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Quản lý Hàng hóa</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Hàng hóa</li>
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
                            <i class="bi bi-box-seam me-2"></i>Danh sách Hàng hóa
                        </h3>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="btn-group">
                            <a href="{{ route('admin.products.create') }}" class="btn btn-light btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Thêm mới
                            </a>
                            <a href="{{ route('admin.products.export') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel
                            </a>
                            <a href="{{ route('admin.products.template') }}" class="btn btn-info btn-sm">
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
                <form method="GET" action="{{ route('admin.products.index') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="🔍 Tìm kiếm..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="supplier_id" class="form-select">
                                <option value="">Tất cả nhà cung cấp</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Ngừng kinh doanh</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-info"><i class="bi bi-search me-1"></i>Tìm kiếm</button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-clockwise me-1"></i>Làm mới</a>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Mã hàng</th>
                            <th>Tên hàng</th>
                            <th>Đơn vị</th>
                            <th>Nhà cung cấp</th>
                            <th>Giá</th>
                            <th>Tồn kho</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->code }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->unit }}</td>
                                <td>{{ $product->supplier->name ?? '-' }}</td>
                                <td>{{ number_format($product->price, 0, ',', '.') }} đ</td>
                                <td>
                                    <span class="badge {{ $product->isLowStock() ? 'bg-danger' : 'bg-success' }}">
                                        {{ $product->current_stock }}
                                    </span>
                                </td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge bg-success">Đang hoạt động</span>
                                    @else
                                        <span class="badge bg-secondary">Ngừng kinh doanh</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                    <span class="text-muted">Không có dữ liệu</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
            @if($products->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Hiển thị {{ $products->firstItem() }} - {{ $products->lastItem() }} / {{ $products->total() }} kết quả
                    </div>
                    {{ $products->links() }}
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
            <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
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


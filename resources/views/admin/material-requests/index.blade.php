@extends('layouts.app')

@section('title', 'Quản lý Yêu cầu Vật tư')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Quản lý Yêu cầu Vật tư</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Yêu cầu Vật tư</li>
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
                            <i class="bi bi-clipboard-check me-2"></i>Danh sách Yêu cầu Vật tư
                        </h3>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('admin.material-requests.create') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Tạo yêu cầu
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.material-requests.index') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="🔍 Mã yêu cầu, phòng ban, dự án..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                                <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Đã chuyển phiếu xuất</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <button type="submit" class="btn btn-info"><i class="bi bi-search me-1"></i>Tìm kiếm</button>
                            <a href="{{ route('admin.material-requests.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-clockwise me-1"></i>Làm mới</a>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Mã yêu cầu</th>
                            <th>Người yêu cầu</th>
                            <th>Phòng ban</th>
                            <th>Dự án</th>
                            <th>Tổng SL</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            <tr>
                                <td>{{ $request->code }}</td>
                                <td>{{ $request->requester->name }}</td>
                                <td>{{ $request->department ?? '-' }}</td>
                                <td>{{ $request->project ?? '-' }}</td>
                                <td>{{ $request->total_quantity }}</td>
                                <td>
                                    @if($request->status == 'pending')
                                        <span class="badge bg-warning">Chờ duyệt</span>
                                    @elseif($request->status == 'approved')
                                        <span class="badge bg-info">Đã duyệt</span>
                                    @elseif($request->status == 'rejected')
                                        <span class="badge bg-danger">Đã từ chối</span>
                                    @elseif($request->status == 'converted')
                                        <span class="badge bg-success">Đã chuyển</span>
                                    @endif
                                </td>
                                <td>{{ $request->created_at ? $request->created_at->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.material-requests.show', $request) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($request->status == 'pending')
                                        <a href="{{ route('admin.material-requests.edit', $request) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
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
            @if($requests->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Hiển thị {{ $requests->firstItem() }} - {{ $requests->lastItem() }} / {{ $requests->total() }} kết quả
                    </div>
                    {{ $requests->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection


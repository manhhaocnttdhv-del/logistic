@extends('layouts.app')

@section('title', 'Kiểm toán Kho')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Kiểm toán Kho</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Kiểm toán</li>
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
                            <i class="bi bi-clipboard-check me-2"></i>Danh sách Phiếu Kiểm toán
                        </h3>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('admin.inventory-audits.create') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Tạo phiếu mới
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.inventory-audits.index') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="🔍 Mã phiếu..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Đang kiểm toán</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="warehouse_id" class="form-select">
                                <option value="">Tất cả kho</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="assigned_to" class="form-select">
                                <option value="">Tất cả nhân viên</option>
                                @foreach($staff as $s)
                                    <option value="{{ $s->id }}" {{ request('assigned_to') == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-info"><i class="bi bi-search me-1"></i>Tìm kiếm</button>
                            <a href="{{ route('admin.inventory-audits.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-clockwise me-1"></i>Làm mới</a>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Mã phiếu</th>
                                <th>Kho</th>
                                <th>Ngày kiểm toán</th>
                                <th>Loại</th>
                                <th>Người được giao</th>
                                <th>Thống kê</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($audits as $audit)
                                <tr>
                                    <td><strong>{{ $audit->code }}</strong></td>
                                    <td>{{ $audit->warehouse->name ?? '-' }}</td>
                                    <td>{{ $audit->audit_date ? $audit->audit_date->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if($audit->type == 'full')
                                            <span class="badge bg-primary">Toàn bộ</span>
                                        @elseif($audit->type == 'partial')
                                            <span class="badge bg-info">Một phần</span>
                                        @else
                                            <span class="badge bg-warning">Đột xuất</span>
                                        @endif
                                    </td>
                                    <td>{{ $audit->assignedStaff->name ?? '-' }}</td>
                                    <td>
                                        <small>
                                            Tổng: <strong>{{ $audit->total_items }}</strong><br>
                                            Khớp: <span class="text-success">{{ $audit->matched_items }}</span> | 
                                            Chênh: <span class="text-danger">{{ $audit->mismatched_items }}</span>
                                        </small>
                                    </td>
                                    <td>
                                        @if($audit->status == 'pending')
                                            <span class="badge bg-warning">Chờ xử lý</span>
                                        @elseif($audit->status == 'in_progress')
                                            <span class="badge bg-info">Đang kiểm toán</span>
                                        @elseif($audit->status == 'completed')
                                            <span class="badge bg-success">Hoàn thành</span>
                                        @else
                                            <span class="badge bg-secondary">Đã hủy</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.inventory-audits.show', $audit) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($audit->status == 'pending')
                                            <a href="{{ route('admin.inventory-audits.edit', $audit) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                        <span class="text-muted">Chưa có phiếu kiểm toán nào</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($audits->hasPages())
            <div class="card-footer bg-light">
                {{ $audits->links() }}
            </div>
            @endif
        </div>
    </div>
</section>
@endsection


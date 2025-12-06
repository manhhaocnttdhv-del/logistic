@extends('layouts.app')

@section('title', 'Phân công Công việc')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Phân công Công việc</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Công việc</li>
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
                            <i class="bi bi-list-check me-2"></i>Phân công Công việc
                        </h3>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('admin.tasks.create') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Phân công mới
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <!-- Workload Statistics -->
                <div class="row mb-4">
                    @foreach($staff as $s)
                        @php
                            $stats = $workloadStats[$s->id] ?? ['pending' => 0, 'in_progress' => 0, 'urgent' => 0, 'overdue' => 0];
                        @endphp
                        <div class="col-md-3 mb-3">
                            <div class="card border shadow-sm">
                                <div class="card-body p-3">
                                    <h6 class="mb-2 fw-bold">{{ $s->name }}</h6>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted small">Chờ xử lý:</span>
                                        <span class="badge bg-warning">{{ $stats['pending'] }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted small">Đang làm:</span>
                                        <span class="badge bg-info">{{ $stats['in_progress'] }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted small">Khẩn cấp:</span>
                                        <span class="badge bg-danger">{{ $stats['urgent'] }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted small">Quá hạn:</span>
                                        <span class="badge bg-dark">{{ $stats['overdue'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <form method="GET" action="{{ route('admin.tasks.index') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Đang làm</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="type" class="form-select">
                                <option value="">Tất cả loại</option>
                                <option value="import" {{ request('type') == 'import' ? 'selected' : '' }}>Nhập kho</option>
                                <option value="export" {{ request('type') == 'export' ? 'selected' : '' }}>Xuất kho</option>
                                <option value="inventory_check" {{ request('type') == 'inventory_check' ? 'selected' : '' }}>Kiểm kê</option>
                                <option value="picking" {{ request('type') == 'picking' ? 'selected' : '' }}>Soạn hàng</option>
                                <option value="stock_report" {{ request('type') == 'stock_report' ? 'selected' : '' }}>Báo cáo tồn</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="priority" class="form-select">
                                <option value="">Tất cả độ ưu tiên</option>
                                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Khẩn cấp</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Cao</option>
                                <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Bình thường</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Thấp</option>
                            </select>
                        </div>
                        <div class="col-md-3">
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
                            <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-clockwise me-1"></i>Làm mới</a>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Độ ưu tiên</th>
                            <th>Tiêu đề</th>
                            <th>Loại</th>
                            <th>Người được giao</th>
                            <th>Người giao</th>
                            <th>Hạn hoàn thành</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            @php
                                $isOverdue = $task->due_date && $task->due_date->isPast() && !in_array($task->status, ['completed', 'cancelled']);
                                $isDueSoon = $task->due_date && $task->due_date->isToday();
                            @endphp
                            <tr class="{{ $isOverdue ? 'table-danger' : ($isDueSoon ? 'table-warning' : '') }}">
                                <td>
                                    @if($task->priority == 'urgent')
                                        <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill"></i> Khẩn cấp</span>
                                    @elseif($task->priority == 'high')
                                        <span class="badge bg-warning"><i class="bi bi-arrow-up-circle-fill"></i> Cao</span>
                                    @elseif($task->priority == 'normal')
                                        <span class="badge bg-primary">Bình thường</span>
                                    @else
                                        <span class="badge bg-secondary">Thấp</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $task->title }}</div>
                                    @if($task->related_order_id && $task->related_order_type)
                                        <small class="text-muted">
                                            @if($task->related_order_type == 'import_order')
                                                <a href="{{ route('admin.import-orders.show', $task->related_order_id) }}" class="text-primary">
                                                    <i class="bi bi-link-45deg"></i> Phiếu nhập #{{ $task->related_order_id }}
                                                </a>
                                            @else
                                                <a href="{{ route('admin.export-orders.show', $task->related_order_id) }}" class="text-primary">
                                                    <i class="bi bi-link-45deg"></i> Phiếu xuất #{{ $task->related_order_id }}
                                                </a>
                                            @endif
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if($task->type == 'import')
                                        <span class="badge bg-primary">Nhập kho</span>
                                    @elseif($task->type == 'export')
                                        <span class="badge bg-success">Xuất kho</span>
                                    @elseif($task->type == 'inventory_check')
                                        <span class="badge bg-info">Kiểm kê</span>
                                    @elseif($task->type == 'picking')
                                        <span class="badge bg-warning">Soạn hàng</span>
                                    @else
                                        <span class="badge bg-secondary">Báo cáo tồn</span>
                                    @endif
                                </td>
                                <td>{{ $task->assignedTo->name }}</td>
                                <td>{{ $task->assignedBy->name }}</td>
                                <td>
                                    @if($task->due_date)
                                        <div class="{{ $isOverdue ? 'text-danger fw-bold' : ($isDueSoon ? 'text-warning fw-semibold' : '') }}">
                                            {{ $task->due_date ? $task->due_date->format('d/m/Y') : '-' }}
                                            @if($isOverdue)
                                                <br><small class="badge bg-danger">Quá hạn</small>
                                            @elseif($isDueSoon)
                                                <br><small class="badge bg-warning">Hôm nay</small>
                                            @endif
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($task->status == 'pending')
                                        <span class="badge bg-warning">Chờ xử lý</span>
                                    @elseif($task->status == 'in_progress')
                                        <span class="badge bg-info">Đang làm</span>
                                    @elseif($task->status == 'completed')
                                        <span class="badge bg-success">Hoàn thành</span>
                                    @else
                                        <span class="badge bg-secondary">Đã hủy</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($task->status == 'pending')
                                        <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-sm btn-warning" title="Chỉnh sửa">
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
            @if($tasks->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Hiển thị {{ $tasks->firstItem() }} - {{ $tasks->lastItem() }} / {{ $tasks->total() }} kết quả
                    </div>
                    {{ $tasks->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection


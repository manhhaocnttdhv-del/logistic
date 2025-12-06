@extends('layouts.app')

@section('title', 'Công việc của tôi')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Công việc của tôi</h1>
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
                            <i class="bi bi-list-task me-2"></i>Danh sách Công việc
                        </h3>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <form method="GET" action="{{ route('staff.tasks.index') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Đang thực hiện</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="type" class="form-select">
                                <option value="">Tất cả loại</option>
                                <option value="import" {{ request('type') == 'import' ? 'selected' : '' }}>Nhập hàng</option>
                                <option value="export" {{ request('type') == 'export' ? 'selected' : '' }}>Xuất hàng</option>
                                <option value="inventory" {{ request('type') == 'inventory' ? 'selected' : '' }}>Kiểm kê</option>
                                <option value="picking" {{ request('type') == 'picking' ? 'selected' : '' }}>Lấy hàng</option>
                                <option value="stock_report" {{ request('type') == 'stock_report' ? 'selected' : '' }}>Báo cáo tồn kho</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-info"><i class="bi bi-search me-1"></i>Tìm kiếm</button>
                            <a href="{{ route('staff.tasks.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-clockwise me-1"></i>Làm mới</a>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Độ ưu tiên</th>
                                <th>Loại</th>
                                <th>Tiêu đề</th>
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
                                        @php
                                            $types = [
                                                'import' => ['text' => 'Nhập hàng', 'badge' => 'info'],
                                                'export' => ['text' => 'Xuất hàng', 'badge' => 'warning'],
                                                'inventory_check' => ['text' => 'Kiểm kê', 'badge' => 'primary'],
                                                'picking' => ['text' => 'Lấy hàng', 'badge' => 'success'],
                                                'stock_report' => ['text' => 'Báo cáo', 'badge' => 'secondary'],
                                            ];
                                            $type = $types[$task->type] ?? ['text' => $task->type, 'badge' => 'secondary'];
                                        @endphp
                                        <span class="badge bg-{{ $type['badge'] }}">{{ $type['text'] }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $task->title }}</div>
                                        @if($task->related_order_id && $task->related_order_type)
                                            <small class="text-muted">
                                                @if($task->related_order_type == 'import_order')
                                                    <i class="bi bi-link-45deg"></i> Phiếu nhập #{{ $task->related_order_id }}
                                                @else
                                                    <i class="bi bi-link-45deg"></i> Phiếu xuất #{{ $task->related_order_id }}
                                                @endif
                                            </small>
                                        @endif
                                    </td>
                                    <td>{{ $task->assignedBy->name ?? '-' }}</td>
                                    <td>
                                        @if($task->due_date)
                                            <div class="{{ $isOverdue ? 'text-danger fw-bold' : ($isDueSoon ? 'text-warning fw-semibold' : '') }}">
                                                {{ $task->due_date->format('d/m/Y') }}
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
                                            <span class="badge bg-info">Đang thực hiện</span>
                                        @else
                                            <span class="badge bg-success">Hoàn thành</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('staff.tasks.show', $task) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                        <span class="text-muted">Không có công việc nào</span>
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


@extends('layouts.app')

@section('title', 'Chi tiết Công việc')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chi tiết Công việc</h1>
            </div>
            <div class="col-sm-6">
                <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary float-end">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary">
                <h3 class="card-title mb-0 text-white">
                    <i class="bi bi-info-circle me-2"></i>Chi tiết Công việc
                </h3>
            </div>
            <div class="card-body p-4">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Tiêu đề</th>
                        <td>{{ $task->title }}</td>
                    </tr>
                    <tr>
                        <th>Loại công việc</th>
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
                    </tr>
                    <tr>
                        <th>Độ ưu tiên</th>
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
                    </tr>
                    @if($task->related_order_id && $task->related_order_type)
                        <tr>
                            <th>Phiếu liên quan</th>
                            <td>
                                @if($task->related_order_type == 'import_order')
                                    <a href="{{ route('admin.import-orders.show', $task->related_order_id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-link-45deg"></i> Xem phiếu nhập #{{ $task->related_order_id }}
                                    </a>
                                @else
                                    <a href="{{ route('admin.export-orders.show', $task->related_order_id) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-link-45deg"></i> Xem phiếu xuất #{{ $task->related_order_id }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <th>Người được giao</th>
                        <td>{{ $task->assignedTo->name }}</td>
                    </tr>
                    <tr>
                        <th>Người giao</th>
                        <td>{{ $task->assignedBy->name }}</td>
                    </tr>
                    <tr>
                        <th>Hạn hoàn thành</th>
                        <td>
                            @if($task->due_date)
                                @php
                                    $isOverdue = $task->due_date->isPast() && !in_array($task->status, ['completed', 'cancelled']);
                                    $isDueSoon = $task->due_date->isToday();
                                @endphp
                                <div class="{{ $isOverdue ? 'text-danger fw-bold' : ($isDueSoon ? 'text-warning fw-semibold' : '') }}">
                                    {{ $task->due_date ? $task->due_date->format('d/m/Y') : '-' }}
                                    @if($isOverdue)
                                        <span class="badge bg-danger ms-2">Quá hạn</span>
                                    @elseif($isDueSoon)
                                        <span class="badge bg-warning ms-2">Hôm nay</span>
                                    @endif
                                </div>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Trạng thái</th>
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
                    </tr>
                    @if($task->started_at)
                        <tr>
                            <th>Bắt đầu lúc</th>
                            <td>{{ $task->started_at ? $task->started_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                    @endif
                    @if($task->completed_at)
                        <tr>
                            <th>Hoàn thành lúc</th>
                            <td>{{ $task->completed_at ? $task->completed_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th>Mô tả</th>
                        <td>{{ $task->description ?? '-' }}</td>
                    </tr>
                    @if($task->completion_notes)
                        <tr>
                            <th>Ghi chú hoàn thành</th>
                            <td>{{ $task->completion_notes }}</td>
                        </tr>
                    @endif
                </table>
            </div>
            <div class="card-footer bg-light">
                @if($task->status == 'pending')
                    <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i> Chỉnh sửa
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection


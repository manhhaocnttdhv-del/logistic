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
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('staff.tasks.index') }}">Công việc</a></li>
                    <li class="breadcrumb-item active">Chi tiết</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary">
                <h3 class="card-title mb-0 text-white">
                    <i class="bi bi-list-task me-2"></i>{{ $task->title }}
                </h3>
            </div>
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong><i class="bi bi-tag me-1"></i>Loại công việc:</strong>
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
                        <span class="badge bg-{{ $type['badge'] }} ms-2">{{ $type['text'] }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong><i class="bi bi-person me-1"></i>Người giao:</strong>
                        <span class="ms-2">{{ $task->assignedBy->name ?? '-' }}</span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong><i class="bi bi-flag me-1"></i>Độ ưu tiên:</strong>
                        @if($task->priority == 'urgent')
                            <span class="badge bg-danger ms-2"><i class="bi bi-exclamation-triangle-fill"></i> Khẩn cấp</span>
                        @elseif($task->priority == 'high')
                            <span class="badge bg-warning ms-2"><i class="bi bi-arrow-up-circle-fill"></i> Cao</span>
                        @elseif($task->priority == 'normal')
                            <span class="badge bg-primary ms-2">Bình thường</span>
                        @else
                            <span class="badge bg-secondary ms-2">Thấp</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong><i class="bi bi-info-circle me-1"></i>Trạng thái:</strong>
                        @if($task->status == 'pending')
                            <span class="badge bg-warning ms-2">Chờ xử lý</span>
                        @elseif($task->status == 'in_progress')
                            <span class="badge bg-info ms-2">Đang thực hiện</span>
                        @else
                            <span class="badge bg-success ms-2">Hoàn thành</span>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong><i class="bi bi-calendar-event me-1"></i>Hạn hoàn thành:</strong>
                        @if($task->due_date)
                            @php
                                $isOverdue = $task->due_date->isPast() && !in_array($task->status, ['completed', 'cancelled']);
                                $isDueSoon = $task->due_date->isToday();
                            @endphp
                            <div class="ms-2 d-inline-block {{ $isOverdue ? 'text-danger fw-bold' : ($isDueSoon ? 'text-warning fw-semibold' : '') }}">
                                {{ $task->due_date ? $task->due_date->format('d/m/Y') : '-' }}
                                @if($isOverdue)
                                    <span class="badge bg-danger ms-1">Quá hạn</span>
                                @elseif($isDueSoon)
                                    <span class="badge bg-warning ms-1">Hôm nay</span>
                                @endif
                            </div>
                        @else
                            <span class="ms-2">-</span>
                        @endif
                    </div>
                    @if($task->related_order_id && $task->related_order_type)
                        <div class="col-md-6">
                            <strong><i class="bi bi-link-45deg me-1"></i>Phiếu liên quan:</strong>
                            <div class="ms-2 d-inline-block">
                                @if($task->related_order_type == 'import_order')
                                    <a href="{{ route('staff.import-orders.show', $task->related_order_id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-box-arrow-in-right"></i> Xem & Xử lý phiếu nhập
                                    </a>
                                @else
                                    <a href="{{ route('staff.export-orders.show', $task->related_order_id) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-box-arrow-up-right"></i> Xem & Xử lý phiếu xuất
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                
                @if($task->description)
                <div class="mb-3">
                    <strong><i class="bi bi-file-text me-1"></i>Mô tả:</strong>
                    <p class="mt-2">{{ $task->description }}</p>
                </div>
                @endif
                
                @if($task->started_at)
                <div class="mb-3">
                    <strong><i class="bi bi-play-circle me-1"></i>Bắt đầu lúc:</strong>
                    <span class="ms-2">{{ $task->started_at ? $task->started_at->format('d/m/Y H:i') : '-' }}</span>
                </div>
                @endif
                
                @if($task->completed_at)
                <div class="mb-3">
                    <strong><i class="bi bi-check-circle me-1"></i>Hoàn thành lúc:</strong>
                    <span class="ms-2">{{ $task->completed_at ? $task->completed_at->format('d/m/Y H:i') : '-' }}</span>
                </div>
                @endif
                
                @if($task->completion_notes)
                <div class="mb-3">
                    <strong><i class="bi bi-chat-left-text me-1"></i>Ghi chú hoàn thành:</strong>
                    <p class="mt-2">{{ $task->completion_notes }}</p>
                </div>
                @endif
            </div>
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('staff.tasks.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Quay lại
                    </a>
                    <div class="d-flex gap-2">
                        @if($task->related_order_id && $task->related_order_type && $task->status != 'completed')
                            @if($task->related_order_type == 'import_order')
                                <a href="{{ route('staff.import-orders.show', $task->related_order_id) }}" class="btn btn-info">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Xử lý phiếu nhập
                                </a>
                            @else
                                <a href="{{ route('staff.export-orders.show', $task->related_order_id) }}" class="btn btn-info">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Xử lý phiếu xuất
                                </a>
                            @endif
                        @endif
                        @if($task->status == 'pending')
                            <form action="{{ route('staff.tasks.start', $task) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-play-fill me-1"></i>Bắt đầu
                                </button>
                            </form>
                        @elseif($task->status == 'in_progress')
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completeModal">
                                <i class="bi bi-check-circle me-1"></i>Hoàn thành
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Complete Modal -->
@if($task->status == 'in_progress')
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white">Hoàn thành công việc</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('staff.tasks.complete', $task) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ghi chú hoàn thành (tùy chọn)</label>
                        <textarea name="completion_notes" class="form-control" rows="3" placeholder="Nhập ghi chú..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Xác nhận hoàn thành</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection


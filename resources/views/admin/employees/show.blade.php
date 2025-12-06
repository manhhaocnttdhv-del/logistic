@extends('layouts.app')

@section('title', 'Chi tiết Nhân viên')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chi tiết Nhân viên</h1>
            </div>
            <div class="col-sm-6">
                <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary float-end">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title mb-0 text-white">
                            <i class="bi bi-info-circle me-2"></i>Thông tin nhân viên
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Họ tên</th>
                                <td>{{ $employee->name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $employee->email }}</td>
                            </tr>
                            <tr>
                                <th>Điện thoại</th>
                                <td>{{ $employee->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    @if($employee->is_active)
                                        <span class="badge bg-success">Đang hoạt động</span>
                                    @else
                                        <span class="badge bg-secondary">Đã khóa</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Ngày tạo</th>
                                <td>{{ $employee->created_at ? $employee->created_at->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title mb-0 text-white">
                            <i class="bi bi-graph-up me-2"></i>Thống kê năng suất
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="bi bi-box-arrow-in-down"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Phiếu nhập</span>
                                        <span class="info-box-number">{{ $stats['import_orders'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="bi bi-box-arrow-up"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Phiếu xuất</span>
                                        <span class="info-box-number">{{ $stats['export_orders'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="bi bi-list-check"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Công việc hoàn thành</span>
                                        <span class="info-box-number">{{ $stats['tasks_completed'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger"><i class="bi bi-clock-history"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Công việc chờ xử lý</span>
                                        <span class="info-box-number">{{ $stats['tasks_pending'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="bi bi-exclamation-triangle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Công việc quá hạn</span>
                                        <span class="info-box-number">{{ $stats['tasks_overdue'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Tasks -->
                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-gradient-primary">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0 text-white">
                                <i class="bi bi-list-check me-2"></i>Công việc gần đây
                            </h3>
                            <a href="{{ route('admin.tasks.create', ['assigned_to' => $employee->id]) }}" class="btn btn-light btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Phân công mới
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($recentTasks->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Tiêu đề</th>
                                            <th>Loại</th>
                                            <th>Ưu tiên</th>
                                            <th>Trạng thái</th>
                                            <th>Hạn</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentTasks as $task)
                                            <tr>
                                                <td>{{ $task->title }}</td>
                                                <td>
                                                    @if($task->type == 'import')
                                                        <span class="badge bg-info">Nhập kho</span>
                                                    @elseif($task->type == 'export')
                                                        <span class="badge bg-success">Xuất kho</span>
                                                    @elseif($task->type == 'inventory_check')
                                                        <span class="badge bg-warning">Kiểm kê</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $task->type }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($task->priority == 'urgent')
                                                        <span class="badge bg-danger">Khẩn cấp</span>
                                                    @elseif($task->priority == 'high')
                                                        <span class="badge bg-warning">Cao</span>
                                                    @elseif($task->priority == 'normal')
                                                        <span class="badge bg-info">Bình thường</span>
                                                    @else
                                                        <span class="badge bg-secondary">Thấp</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($task->status == 'completed')
                                                        <span class="badge bg-success">Hoàn thành</span>
                                                    @elseif($task->status == 'in_progress')
                                                        <span class="badge bg-primary">Đang làm</span>
                                                    @else
                                                        <span class="badge bg-warning">Chờ xử lý</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($task->due_date)
                                                        @if($task->due_date < now()->toDateString() && $task->status != 'completed')
                                                            <span class="text-danger">{{ $task->due_date ? $task->due_date->format('d/m/Y') : '-' }}</span>
                                                        @else
                                                            {{ $task->due_date ? $task->due_date->format('d/m/Y') : '-' }}
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                <p>Chưa có công việc nào</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Import Orders -->
                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title mb-0 text-white">
                            <i class="bi bi-box-arrow-in-down me-2"></i>Phiếu nhập gần đây
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        @if($recentImports->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Mã phiếu</th>
                                            <th>Nhà cung cấp</th>
                                            <th>Ngày nhập</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentImports as $order)
                                            <tr>
                                                <td>{{ $order->code }}</td>
                                                <td>{{ $order->supplier->name ?? '-' }}</td>
                                                <td>{{ $order->import_date ? $order->import_date->format('d/m/Y') : '-' }}</td>
                                                <td>
                                                    @if($order->status == 'completed')
                                                        <span class="badge bg-success">Hoàn thành</span>
                                                    @elseif($order->status == 'processing')
                                                        <span class="badge bg-primary">Đang xử lý</span>
                                                    @elseif($order->status == 'pending')
                                                        <span class="badge bg-warning">Chờ xử lý</span>
                                                    @else
                                                        <span class="badge bg-secondary">Đã hủy</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.import-orders.show', $order) }}" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                <p>Chưa có phiếu nhập nào</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Export Orders -->
                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title mb-0 text-white">
                            <i class="bi bi-box-arrow-up me-2"></i>Phiếu xuất gần đây
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        @if($recentExports->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Mã phiếu</th>
                                            <th>Người nhận</th>
                                            <th>Lý do</th>
                                            <th>Ngày xuất</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentExports as $order)
                                            <tr>
                                                <td>{{ $order->code }}</td>
                                                <td>{{ $order->recipient ?? '-' }}</td>
                                                <td>
                                                    @if($order->reason == 'sale')
                                                        <span class="badge bg-success">Bán hàng</span>
                                                    @elseif($order->reason == 'transfer')
                                                        <span class="badge bg-info">Điều chuyển</span>
                                                    @elseif($order->reason == 'return')
                                                        <span class="badge bg-warning">Trả hàng</span>
                                                    @elseif($order->reason == 'internal')
                                                        <span class="badge bg-primary">Nội bộ</span>
                                                    @else
                                                        <span class="badge bg-secondary">Khác</span>
                                                    @endif
                                                </td>
                                                <td>{{ $order->export_date ? $order->export_date->format('d/m/Y') : '-' }}</td>
                                                <td>
                                                    @if($order->status == 'completed')
                                                        <span class="badge bg-success">Hoàn thành</span>
                                                    @elseif($order->status == 'processing')
                                                        <span class="badge bg-primary">Đang xử lý</span>
                                                    @elseif($order->status == 'pending')
                                                        <span class="badge bg-warning">Chờ xử lý</span>
                                                    @else
                                                        <span class="badge bg-secondary">Đã hủy</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.export-orders.show', $order) }}" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                <p>Chưa có phiếu xuất nào</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title mb-0 text-white">
                            <i class="bi bi-gear me-2"></i>Thao tác
                        </h3>
                    </div>
                    <div class="card-body p-3">
                        <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-warning btn-block mb-2">
                            <i class="bi bi-pencil"></i> Chỉnh sửa
                        </a>
                        <form action="{{ route('admin.employees.toggle-status', $employee) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn {{ $employee->is_active ? 'btn-secondary' : 'btn-success' }} btn-block">
                                <i class="bi bi-{{ $employee->is_active ? 'lock' : 'unlock' }}"></i> 
                                {{ $employee->is_active ? 'Khóa tài khoản' : 'Kích hoạt tài khoản' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


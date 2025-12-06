@extends('layouts.app')

@section('title', 'Báo cáo Hiệu suất Nhân viên')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Báo cáo Hiệu suất Nhân viên</h1>
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
                            <i class="bi bi-people me-2"></i>Báo cáo Hiệu suất Nhân viên
                        </h3>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('admin.reports.employee-performance') }}" class="d-flex gap-2">
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" style="width: auto;">
                            <span class="align-self-center text-white">đến</span>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" style="width: auto;">
                            <button type="submit" class="btn btn-light btn-sm"><i class="bi bi-search me-1"></i>Lọc</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('admin.reports.export-excel', ['type' => 'employee-performance', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Nhân viên</th>
                            <th class="text-center">Phiếu nhập</th>
                            <th class="text-center">Phiếu xuất</th>
                            <th class="text-center">Công việc hoàn thành</th>
                            <th class="text-center">Công việc đang làm</th>
                            <th class="text-center">Công việc quá hạn</th>
                            <th class="text-end">Tổng cộng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($performance as $index => $perf)
                            <tr>
                                <td>
                                    @if($index < 3)
                                        <span class="badge bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'info') }}">
                                            {{ $index + 1 }}
                                        </span>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $perf['user']->name }}</strong>
                                    <br><small class="text-muted">{{ $perf['user']->email }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $perf['import_count'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $perf['export_count'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $perf['task_count'] }}</span>
                                </td>
                                <td class="text-center">
                                    @if($perf['pending_tasks'] > 0)
                                        <span class="badge bg-warning">{{ $perf['pending_tasks'] }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($perf['overdue_tasks'] > 0)
                                        <span class="badge bg-danger">{{ $perf['overdue_tasks'] }}</span>
                                    @else
                                        <span class="text-success">✓</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong class="text-primary">{{ $perf['total'] }}</strong>
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
        </div>
    </div>
</section>
@endsection


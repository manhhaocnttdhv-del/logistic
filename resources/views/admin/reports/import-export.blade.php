@extends('layouts.app')

@section('title', 'Báo cáo Nhập - Xuất - Tồn')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Báo cáo Nhập - Xuất - Tồn</h1>
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
                            <i class="bi bi-arrow-left-right me-2"></i>Báo cáo Nhập - Xuất - Tồn
                        </h3>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('admin.reports.import-export') }}" class="d-flex gap-2">
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" style="width: auto;">
                            <span class="align-self-center text-white">đến</span>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" style="width: auto;">
                            <button type="submit" class="btn btn-light btn-sm"><i class="bi bi-search me-1"></i>Lọc</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Summary Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-info"><i class="bi bi-box-arrow-in-down"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Tổng nhập</span>
                                <span class="info-box-number">{{ number_format($importTotal ?? 0, 0, ',', '.') }} đ</span>
                                <small class="text-muted">{{ number_format($importQuantity ?? 0, 0, ',', '.') }} sản phẩm</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-success"><i class="bi bi-box-arrow-up"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Tổng xuất</span>
                                <span class="info-box-number">{{ number_format($exportTotal ?? 0, 0, ',', '.') }} đ</span>
                                <small class="text-muted">{{ number_format($exportQuantity ?? 0, 0, ',', '.') }} sản phẩm</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-primary"><i class="bi bi-file-earmark-text"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Số phiếu nhập</span>
                                <span class="info-box-number">{{ $imports->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box shadow-sm">
                            <span class="info-box-icon bg-warning"><i class="bi bi-file-earmark-text"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Số phiếu xuất</span>
                                <span class="info-box-number">{{ $exports->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('admin.reports.export-excel', ['type' => 'import-export', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel
                    </a>
                </div>
                
                <h4>Phiếu nhập ({{ $imports->count() }})</h4>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Mã phiếu</th>
                                <th>Ngày</th>
                                <th>Nhà cung cấp</th>
                                <th>Nguồn</th>
                                <th class="text-end">Tổng tiền</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($imports as $import)
                                <tr>
                                    <td><a href="{{ route('admin.import-orders.show', $import) }}" class="text-primary">{{ $import->code }}</a></td>
                                    <td>{{ $import->import_date->format('d/m/Y') }}</td>
                                    <td>{{ $import->supplier->name ?? ($import->from_warehouse ?? '-') }}</td>
                                    <td>
                                        @if($import->source == 'supplier')
                                            <span class="badge bg-primary">Nhà cung cấp</span>
                                        @elseif($import->source == 'transfer')
                                            <span class="badge bg-info">Điều chuyển</span>
                                        @elseif($import->source == 'return')
                                            <span class="badge bg-warning">Trả hàng</span>
                                        @else
                                            <span class="badge bg-secondary">Khác</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format($import->total_amount ?? 0, 0, ',', '.') }} đ</td>
                                    <td>
                                        @if($import->status == 'completed')
                                            <span class="badge bg-success">Hoàn thành</span>
                                        @elseif($import->status == 'pending')
                                            <span class="badge bg-warning">Chờ xử lý</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $import->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <h4 class="mt-4">Phiếu xuất ({{ $exports->count() }})</h4>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Mã phiếu</th>
                                <th>Ngày</th>
                                <th>Người nhận</th>
                                <th>Lý do</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exports as $export)
                                <tr>
                                    <td><a href="{{ route('admin.export-orders.show', $export) }}" class="text-primary">{{ $export->code }}</a></td>
                                    <td>{{ $export->export_date->format('d/m/Y') }}</td>
                                    <td>{{ $export->recipient ?? '-' }}</td>
                                    <td>
                                        @if($export->reason == 'sale')
                                            <span class="badge bg-primary">Bán hàng</span>
                                        @elseif($export->reason == 'internal')
                                            <span class="badge bg-info">Nội bộ</span>
                                        @elseif($export->reason == 'transfer')
                                            <span class="badge bg-warning">Điều chuyển</span>
                                        @elseif($export->reason == 'return_supplier')
                                            <span class="badge bg-danger">Trả NCC</span>
                                        @else
                                            <span class="badge bg-secondary">Khác</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($export->status == 'completed')
                                            <span class="badge bg-success">Hoàn thành</span>
                                        @elseif($export->status == 'pending')
                                            <span class="badge bg-warning">Chờ xử lý</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $export->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if(isset($productSummary) && count($productSummary) > 0)
                <h4 class="mt-4">Tổng hợp theo sản phẩm</h4>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Mã SP</th>
                                <th>Tên sản phẩm</th>
                                <th class="text-end">SL Nhập</th>
                                <th class="text-end">Giá trị Nhập</th>
                                <th class="text-end">SL Xuất</th>
                                <th class="text-end">Giá trị Xuất</th>
                                <th class="text-end">Tồn cuối kỳ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productSummary as $summary)
                                @php
                                    $endingStock = $summary['import_quantity'] - $summary['export_quantity'];
                                @endphp
                                <tr>
                                    <td><strong>{{ $summary['product']->code }}</strong></td>
                                    <td>{{ $summary['product']->name }}</td>
                                    <td class="text-end">{{ number_format($summary['import_quantity'], 0, ',', '.') }} {{ $summary['product']->unit }}</td>
                                    <td class="text-end">{{ number_format($summary['import_value'], 0, ',', '.') }} đ</td>
                                    <td class="text-end">{{ number_format($summary['export_quantity'], 0, ',', '.') }} {{ $summary['product']->unit }}</td>
                                    <td class="text-end">{{ number_format($summary['export_value'], 0, ',', '.') }} đ</td>
                                    <td class="text-end">
                                        <span class="badge {{ $endingStock < 0 ? 'bg-danger' : ($summary['product']->isLowStock() ? 'bg-warning' : 'bg-success') }}">
                                            {{ number_format($endingStock, 0, ',', '.') }} {{ $summary['product']->unit }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection


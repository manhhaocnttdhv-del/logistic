@extends('layouts.app')

@section('title', 'Chi tiết Kho')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chi tiết Kho</h1>
            </div>
            <div class="col-sm-6">
                <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary float-end">
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
                            <i class="bi bi-info-circle me-2"></i>Thông tin Kho
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Mã kho</th>
                                <td><strong>{{ $warehouse->code }}</strong></td>
                            </tr>
                            <tr>
                                <th>Tên kho</th>
                                <td>{{ $warehouse->name }}</td>
                            </tr>
                            <tr>
                                <th>Địa chỉ</th>
                                <td>{{ $warehouse->address ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Người quản lý</th>
                                <td>{{ $warehouse->manager_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Số điện thoại</th>
                                <td>{{ $warehouse->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Mô tả</th>
                                <td>{{ $warehouse->description ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    @if($warehouse->is_active)
                                        <span class="badge bg-success">Đang hoạt động</span>
                                    @else
                                        <span class="badge bg-secondary">Ngừng hoạt động</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Tổng số sản phẩm</th>
                                <td>{{ $warehouse->inventories->count() }} sản phẩm</td>
                            </tr>
                            <tr>
                                <th>Giá trị tồn kho</th>
                                <td><strong>{{ number_format($warehouse->total_value, 0, ',', '.') }} đ</strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer bg-light">
                        <a href="{{ route('admin.warehouses.edit', $warehouse) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i> Chỉnh sửa
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-info">
                        <h3 class="card-title mb-0 text-white">
                            <i class="bi bi-box-seam me-2"></i>Sản phẩm trong kho
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($warehouse->inventories->count() > 0)
                            <div class="list-group">
                                @foreach($warehouse->inventories->take(10) as $inventory)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span>{{ $inventory->product->name }}</span>
                                            <span class="badge bg-primary">{{ $inventory->quantity }} {{ $inventory->product->unit }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($warehouse->inventories->count() > 10)
                                <p class="text-muted mt-2">Và {{ $warehouse->inventories->count() - 10 }} sản phẩm khác...</p>
                            @endif
                        @else
                            <p class="text-muted">Chưa có sản phẩm trong kho</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


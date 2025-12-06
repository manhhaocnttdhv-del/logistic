@extends('layouts.app')

@section('title', 'Chi tiết Nhà cung cấp')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chi tiết Nhà cung cấp</h1>
            </div>
            <div class="col-sm-6">
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary float-end">
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
                            <i class="bi bi-info-circle me-2"></i>Thông tin nhà cung cấp
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Mã</th>
                                <td>{{ $supplier->code }}</td>
                            </tr>
                            <tr>
                                <th>Tên nhà cung cấp</th>
                                <td>{{ $supplier->name }}</td>
                            </tr>
                            <tr>
                                <th>Người liên hệ</th>
                                <td>{{ $supplier->contact_person ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Điện thoại</th>
                                <td>{{ $supplier->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $supplier->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Địa chỉ</th>
                                <td>{{ $supplier->address ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Ghi chú</th>
                                <td>{{ $supplier->notes ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    @if($supplier->is_active)
                                        <span class="badge bg-success">Đang hoạt động</span>
                                    @else
                                        <span class="badge bg-secondary">Ngừng hoạt động</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Số sản phẩm</th>
                                <td>{{ $supplier->products->count() }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer bg-light">
                        <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i> Chỉnh sửa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


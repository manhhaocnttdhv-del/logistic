@extends('layouts.app')

@section('title', 'Chi tiết Hàng hóa')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chi tiết Hàng hóa</h1>
            </div>
            <div class="col-sm-6">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary float-end">
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
                            <i class="bi bi-info-circle me-2"></i>Thông tin hàng hóa
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Mã hàng</th>
                                <td>{{ $product->code }}</td>
                            </tr>
                            <tr>
                                <th>Tên hàng</th>
                                <td>{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <th>Đơn vị tính</th>
                                <td>{{ $product->unit }}</td>
                            </tr>
                            <tr>
                                <th>Nhà cung cấp</th>
                                <td>{{ $product->supplier->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Giá</th>
                                <td>{{ number_format($product->price, 0, ',', '.') }} đ</td>
                            </tr>
                            <tr>
                                <th>Tồn kho hiện tại</th>
                                <td>
                                    <span class="badge {{ $product->isLowStock() ? 'bg-danger' : 'bg-success' }}">
                                        {{ $product->current_stock }} {{ $product->unit }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Tồn kho tối thiểu</th>
                                <td>{{ $product->min_stock }} {{ $product->unit }}</td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge bg-success">Đang hoạt động</span>
                                    @else
                                        <span class="badge bg-secondary">Ngừng kinh doanh</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Mô tả</th>
                                <td>{{ $product->description ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer bg-light">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i> Chỉnh sửa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


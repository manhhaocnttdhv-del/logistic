@extends('layouts.app')

@section('title', 'Báo cáo Sản phẩm Sắp Hết Hàng')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Báo cáo Sản phẩm Sắp Hết Hàng</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary">
                <h3 class="card-title mb-0 text-white">
                    <i class="bi bi-exclamation-triangle me-2"></i>Sản phẩm Sắp Hết Hàng
                </h3>
            </div>
            <div class="card-body p-3">
                <div class="alert alert-warning mb-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Cảnh báo:</strong> Có <strong>{{ $products->count() }}</strong> sản phẩm đang ở mức tồn kho thấp, cần nhập thêm hàng.
                </div>
                
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('admin.reports.export-excel', ['type' => 'low-stock']) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Mã sản phẩm</th>
                            <th>Tên sản phẩm</th>
                            <th>Nhà cung cấp</th>
                            <th>Tồn kho hiện tại</th>
                            <th>Tồn kho tối thiểu</th>
                            <th>Chênh lệch</th>
                            <th>Đơn vị</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            @php
                                $difference = $product->current_stock - $product->min_stock;
                                $percentage = $product->min_stock > 0 ? ($product->current_stock / $product->min_stock) * 100 : 0;
                            @endphp
                            <tr class="{{ $product->current_stock <= 0 ? 'table-danger' : '' }}">
                                <td><strong>{{ $product->code }}</strong></td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->supplier->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-danger fs-6">{{ number_format($product->current_stock, 0, ',', '.') }}</span>
                                </td>
                                <td>{{ number_format($product->min_stock, 0, ',', '.') }}</td>
                                <td>
                                    @if($difference < 0)
                                        <span class="badge bg-danger">Thiếu {{ number_format(abs($difference), 0, ',', '.') }}</span>
                                    @elseif($difference == 0)
                                        <span class="badge bg-warning">Đúng mức</span>
                                    @else
                                        <span class="badge bg-success">+{{ number_format($difference, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td>{{ $product->unit }}</td>
                                <td>
                                    @if($product->current_stock <= 0)
                                        <span class="badge bg-danger">Hết hàng</span>
                                    @elseif($percentage < 50)
                                        <span class="badge bg-danger">Rất thấp</span>
                                    @elseif($percentage < 100)
                                        <span class="badge bg-warning">Thấp</span>
                                    @else
                                        <span class="badge bg-info">Cần theo dõi</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                                    <span class="text-success fw-bold">Tuyệt vời!</span>
                                    <br><span class="text-muted">Không có sản phẩm nào sắp hết hàng</span>
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


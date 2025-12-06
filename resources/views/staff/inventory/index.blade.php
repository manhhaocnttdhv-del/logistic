@extends('layouts.app')

@section('title', 'Kiểm kê Kho')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Kiểm kê Kho</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary">
                <h3 class="card-title mb-0 text-white">
                    <i class="bi bi-box-seam me-2"></i>Danh sách Tồn kho
                </h3>
            </div>
            <div class="card-body p-3">
                <form method="GET" action="{{ route('staff.inventory.index') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="🔍 Tìm kiếm sản phẩm..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="low_stock" value="1" id="lowStock" {{ request('low_stock') ? 'checked' : '' }}>
                                <label class="form-check-label" for="lowStock">
                                    Sắp hết hàng
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-info"><i class="bi bi-search me-1"></i>Tìm kiếm</button>
                            <a href="{{ route('staff.inventory.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-clockwise me-1"></i>Làm mới</a>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Mã sản phẩm</th>
                                <th>Tên sản phẩm</th>
                                <th>Nhà cung cấp</th>
                                <th>Tồn kho</th>
                                <th>Tồn kho tối thiểu</th>
                                <th>Cập nhật lần cuối</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventories as $inventory)
                                <tr>
                                    <td>{{ $inventory->product->code }}</td>
                                    <td>{{ $inventory->product->name }}</td>
                                    <td>{{ $inventory->product->supplier->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $inventory->quantity <= $inventory->product->min_stock ? 'bg-danger' : 'bg-success' }}">
                                            {{ $inventory->quantity }} {{ $inventory->product->unit }}
                                        </span>
                                    </td>
                                    <td>{{ $inventory->product->min_stock }} {{ $inventory->product->unit }}</td>
                                    <td>{{ $inventory->last_updated_date->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#checkModal{{ $inventory->id }}">
                                            <i class="bi bi-pencil"></i> Cập nhật
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Check Modal -->
                                <div class="modal fade" id="checkModal{{ $inventory->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                <h5 class="modal-title text-white">Cập nhật tồn kho</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('staff.inventory.check') }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Sản phẩm</label>
                                                        <input type="text" class="form-control" value="{{ $inventory->product->name }} ({{ $inventory->product->code }})" readonly>
                                                        <input type="hidden" name="product_id" value="{{ $inventory->product_id }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Số lượng tồn kho <span class="text-danger">*</span></label>
                                                        <input type="number" name="quantity" class="form-control" value="{{ $inventory->quantity }}" min="0" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Ghi chú</label>
                                                        <textarea name="notes" class="form-control" rows="2" placeholder="Nhập ghi chú...">{{ $inventory->notes }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                        <span class="text-muted">Không có dữ liệu</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($inventories->hasPages())
            <div class="card-footer bg-light">
                {{ $inventories->links() }}
            </div>
            @endif
        </div>
    </div>
</section>
@endsection


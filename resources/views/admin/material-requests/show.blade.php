@extends('layouts.app')

@section('title', 'Chi tiết Yêu cầu Vật tư')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chi tiết Yêu cầu Vật tư: {{ $materialRequest->code }}</h1>
            </div>
            <div class="col-sm-6">
                <a href="{{ route('admin.material-requests.index') }}" class="btn btn-secondary float-end">
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
                            <i class="bi bi-info-circle me-2"></i>Thông tin yêu cầu
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Mã yêu cầu</th>
                                <td>{{ $materialRequest->code }}</td>
                            </tr>
                            <tr>
                                <th>Người yêu cầu</th>
                                <td>{{ $materialRequest->requester->name }} ({{ $materialRequest->requester->email }})</td>
                            </tr>
                            <tr>
                                <th>Phòng ban</th>
                                <td>{{ $materialRequest->department ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Dự án/Chương trình</th>
                                <td>{{ $materialRequest->project ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    @if($materialRequest->status == 'pending')
                                        <span class="badge bg-warning">Chờ duyệt</span>
                                    @elseif($materialRequest->status == 'approved')
                                        <span class="badge bg-info">Đã duyệt</span>
                                    @elseif($materialRequest->status == 'rejected')
                                        <span class="badge bg-danger">Đã từ chối</span>
                                    @elseif($materialRequest->status == 'converted')
                                        <span class="badge bg-success">Đã chuyển phiếu xuất</span>
                                    @endif
                                </td>
                            </tr>
                            @if($materialRequest->approved_by)
                                <tr>
                                    <th>Người duyệt</th>
                                    <td>{{ $materialRequest->approver->name }} - {{ $materialRequest->approved_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endif
                            @if($materialRequest->rejection_reason)
                                <tr>
                                    <th>Lý do từ chối</th>
                                    <td class="text-danger">{{ $materialRequest->rejection_reason }}</td>
                                </tr>
                            @endif
                            @if($materialRequest->exportOrder)
                                <tr>
                                    <th>Phiếu xuất</th>
                                    <td>
                                        <a href="{{ route('admin.export-orders.show', $materialRequest->exportOrder) }}" class="btn btn-sm btn-info">
                                            {{ $materialRequest->exportOrder->code }}
                                        </a>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <th>Ghi chú</th>
                                <td>{{ $materialRequest->notes ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Ngày tạo</th>
                                <td>{{ $materialRequest->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title mb-0 text-white">
                            <i class="bi bi-list-ul me-2"></i>Chi tiết vật tư yêu cầu
                        </h3>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Tồn kho</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($materialRequest->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->product->name }} ({{ $item->product->code }})</td>
                                        <td>{{ $item->quantity }} {{ $item->product->unit }}</td>
                                        <td>
                                            @if($item->product->current_stock >= $item->quantity)
                                                <span class="badge bg-success">{{ $item->product->current_stock }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ $item->product->current_stock }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Tổng số lượng:</th>
                                    <th>{{ $materialRequest->total_quantity }}</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
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
                        @if($materialRequest->status == 'pending')
                            <a href="{{ route('admin.material-requests.edit', $materialRequest) }}" class="btn btn-warning btn-block mb-2">
                                <i class="bi bi-pencil"></i> Chỉnh sửa
                            </a>
                            <form action="{{ route('admin.material-requests.approve', $materialRequest) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Duyệt yêu cầu này và tạo phiếu xuất tự động?');">
                                    <i class="bi bi-check-circle"></i> Duyệt yêu cầu
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger btn-block" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle"></i> Từ chối
                            </button>
                        @endif
                        @if($materialRequest->exportOrder)
                            <a href="{{ route('admin.export-orders.show', $materialRequest->exportOrder) }}" class="btn btn-info btn-block mt-2">
                                <i class="bi bi-file-earmark-text"></i> Xem phiếu xuất
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.material-requests.reject', $materialRequest) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Từ chối Yêu cầu Vật tư</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Lý do từ chối <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4" required placeholder="Nhập lý do từ chối..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


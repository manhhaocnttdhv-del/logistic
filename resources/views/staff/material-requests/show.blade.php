@extends('layouts.app')

@section('title', 'Chi tiết Yêu cầu Vật tư')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chi tiết Yêu cầu Vật tư: {{ $request->code }}</h1>
            </div>
            <div class="col-sm-6">
                <a href="{{ route('staff.material-requests.index') }}" class="btn btn-secondary float-end">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
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
                                <td>{{ $request->code }}</td>
                            </tr>
                            <tr>
                                <th>Người yêu cầu</th>
                                <td>{{ $request->requester->name }} ({{ $request->requester->email }})</td>
                            </tr>
                            <tr>
                                <th>Phòng ban</th>
                                <td>{{ $request->department ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Dự án/Chương trình</th>
                                <td>{{ $request->project ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    @if($request->status == 'pending')
                                        <span class="badge bg-warning">Chờ duyệt</span>
                                    @elseif($request->status == 'approved')
                                        <span class="badge bg-info">Đã duyệt</span>
                                    @elseif($request->status == 'rejected')
                                        <span class="badge bg-danger">Đã từ chối</span>
                                    @elseif($request->status == 'converted')
                                        <span class="badge bg-success">Đã chuyển phiếu xuất</span>
                                    @endif
                                </td>
                            </tr>
                            @if($request->approved_by)
                                <tr>
                                    <th>Người duyệt</th>
                                    <td>{{ $request->approver->name ?? '-' }} - {{ $request->approved_at ? $request->approved_at->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                            @endif
                            @if($request->rejection_reason)
                                <tr>
                                    <th>Lý do từ chối</th>
                                    <td class="text-danger">{{ $request->rejection_reason }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>Ghi chú</th>
                                <td>{{ $request->notes ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Ngày tạo</th>
                                <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
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
                                @foreach($request->items as $index => $item)
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
                                    <th>{{ $request->total_quantity }}</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


@extends('layouts.app')

@section('title', 'Chỉnh sửa Công việc')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Chỉnh sửa Công việc</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary">
                <h3 class="card-title mb-0 text-white">
                    <i class="bi bi-pencil me-2"></i>Chỉnh sửa Công việc
                </h3>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.tasks.update', $task) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Người được giao <span class="text-danger">*</span></label>
                                <select name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror" required>
                                    <option value="">Chọn nhân viên</option>
                                    @foreach($staff as $s)
                                        <option value="{{ $s->id }}" {{ old('assigned_to', $task->assigned_to) == $s->id ? 'selected' : '' }}>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Loại công việc <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="import" {{ old('type', $task->type) == 'import' ? 'selected' : '' }}>Nhập kho</option>
                                    <option value="export" {{ old('type', $task->type) == 'export' ? 'selected' : '' }}>Xuất kho</option>
                                    <option value="inventory_check" {{ old('type', $task->type) == 'inventory_check' ? 'selected' : '' }}>Kiểm kê</option>
                                    <option value="picking" {{ old('type', $task->type) == 'picking' ? 'selected' : '' }}>Soạn hàng</option>
                                    <option value="stock_report" {{ old('type', $task->type) == 'stock_report' ? 'selected' : '' }}>Báo cáo tồn</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Độ ưu tiên</label>
                                <select name="priority" class="form-select @error('priority') is-invalid @enderror">
                                    <option value="normal" {{ old('priority', $task->priority ?? 'normal') == 'normal' ? 'selected' : '' }}>Bình thường</option>
                                    <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Thấp</option>
                                    <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>Cao</option>
                                    <option value="urgent" {{ old('priority', $task->priority) == 'urgent' ? 'selected' : '' }}>Khẩn cấp</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $task->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            Mô tả / Nhiệm vụ
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="fillDefaultDescription()">
                                <i class="bi bi-magic"></i> Điền mặc định
                            </button>
                        </label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Nhập mô tả công việc...">{{ old('description', $task->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Loại phiếu liên quan</label>
                                <select name="related_order_type" class="form-select @error('related_order_type') is-invalid @enderror">
                                    <option value="">Không có</option>
                                    <option value="import_order" {{ old('related_order_type', $task->related_order_type) == 'import_order' ? 'selected' : '' }}>Phiếu nhập</option>
                                    <option value="export_order" {{ old('related_order_type', $task->related_order_type) == 'export_order' ? 'selected' : '' }}>Phiếu xuất</option>
                                </select>
                                @error('related_order_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">ID phiếu liên quan</label>
                                <input type="number" name="related_order_id" class="form-control @error('related_order_id') is-invalid @enderror" value="{{ old('related_order_id', $task->related_order_id) }}" placeholder="Nhập ID phiếu...">
                                @error('related_order_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Hạn hoàn thành</label>
                                <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-1"></i>Cập nhật
                        </button>
                        <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary px-4">
                            <i class="bi bi-x-circle me-1"></i>Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
const defaultDescriptions = {
    'import': 'Xử lý phiếu nhập hàng: Kiểm tra số lượng, chất lượng hàng hóa, nhập vào kho và cập nhật tồn kho.',
    'export': 'Xử lý phiếu xuất hàng: Soạn hàng theo phiếu, kiểm tra số lượng, xuất kho và cập nhật tồn kho.',
    'inventory_check': 'Kiểm kê kho: Đối chiếu số lượng thực tế với sổ sách, phát hiện chênh lệch và báo cáo.',
    'picking': 'Soạn hàng: Lấy hàng theo đơn hàng, đóng gói và chuẩn bị xuất kho.',
    'stock_report': 'Báo cáo tồn kho: Tổng hợp số liệu tồn kho, báo cáo hàng tồn kho thấp và đề xuất nhập hàng.'
};

function fillDefaultDescription() {
    const type = document.querySelector('select[name="type"]').value;
    const descriptionField = document.getElementById('description');
    
    if (type && defaultDescriptions[type]) {
        descriptionField.value = defaultDescriptions[type];
    } else {
        alert('Vui lòng chọn loại công việc trước.');
    }
}
</script>
@endpush
@endsection


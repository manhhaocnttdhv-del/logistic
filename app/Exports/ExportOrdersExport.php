<?php

namespace App\Exports;

use App\Models\ExportOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportOrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ExportOrder::with('creator', 'assignedStaff', 'items.product')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Mã phiếu',
            'Người tạo',
            'Người được giao',
            'Ngày xuất',
            'Người nhận',
            'Lý do xuất',
            'Phòng ban',
            'Dự án',
            'Trạng thái',
            'Tổng số lượng',
            'Số sản phẩm',
            'Ghi chú',
        ];
    }

    /**
     * @param ExportOrder $order
     * @return array
     */
    public function map($order): array
    {
        return [
            $order->code,
            $order->creator ? $order->creator->name : '',
            $order->assignedStaff ? $order->assignedStaff->name : '',
            $order->export_date,
            $order->recipient ?? '',
            $this->getReasonText($order->reason),
            $order->department ?? '',
            $order->project ?? '',
            $this->getStatusText($order->status),
            $order->total_quantity,
            $order->items->count(),
            $order->notes ?? '',
        ];
    }

    private function getReasonText($reason)
    {
        return match($reason) {
            'sale' => 'Bán hàng',
            'internal' => 'Sử dụng nội bộ',
            'transfer' => 'Điều chuyển',
            'return_supplier' => 'Trả nhà cung cấp',
            'return' => 'Trả hàng',
            'other' => 'Khác',
            default => $reason,
        };
    }

    private function getStatusText($status)
    {
        return match($status) {
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            default => $status,
        };
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']]],
        ];
    }
}

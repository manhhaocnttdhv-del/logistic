<?php

namespace App\Exports;

use App\Models\ImportOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ImportOrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ImportOrder::with('supplier', 'creator', 'assignedStaff', 'items.product')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Mã phiếu',
            'Nguồn nhập',
            'Nhà cung cấp',
            'Kho nguồn',
            'Người tạo',
            'Người được giao',
            'Ngày nhập',
            'Trạng thái',
            'Tổng tiền',
            'Số sản phẩm',
            'Ghi chú',
        ];
    }

    /**
     * @param ImportOrder $order
     * @return array
     */
    public function map($order): array
    {
        return [
            $order->code,
            $this->getSourceText($order->source),
            $order->supplier ? $order->supplier->name : '',
            $order->from_warehouse ?? '',
            $order->creator ? $order->creator->name : '',
            $order->assignedStaff ? $order->assignedStaff->name : '',
            $order->import_date,
            $this->getStatusText($order->status),
            number_format($order->total_amount, 0, ',', '.') . ' đ',
            $order->items->count(),
            $order->notes ?? '',
        ];
    }

    private function getSourceText($source)
    {
        return match($source) {
            'supplier' => 'Nhà cung cấp',
            'transfer' => 'Điều chuyển',
            'return' => 'Trả hàng',
            'other' => 'Khác',
            default => $source ?? '',
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

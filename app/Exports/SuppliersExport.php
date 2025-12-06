<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuppliersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Supplier::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Mã nhà cung cấp',
            'Tên nhà cung cấp',
            'Người liên hệ',
            'Số điện thoại',
            'Email',
            'Địa chỉ',
            'Ghi chú',
            'Trạng thái',
        ];
    }

    /**
     * @param Supplier $supplier
     * @return array
     */
    public function map($supplier): array
    {
        return [
            $supplier->code,
            $supplier->name,
            $supplier->contact_person ?? '',
            $supplier->phone ?? '',
            $supplier->email ?? '',
            $supplier->address ?? '',
            $supplier->notes ?? '',
            $supplier->is_active ? 'Hoạt động' : 'Ngừng hoạt động',
        ];
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

<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Product::with('supplier', 'inventory')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Mã sản phẩm',
            'Tên sản phẩm',
            'Đơn vị tính',
            'Nhà cung cấp',
            'Mô tả',
            'Giá',
            'Tồn kho tối thiểu',
            'Tồn kho hiện tại',
            'Trạng thái',
        ];
    }

    /**
     * @param Product $product
     * @return array
     */
    public function map($product): array
    {
        return [
            $product->code,
            $product->name,
            $product->unit,
            $product->supplier ? $product->supplier->name : '',
            $product->description ?? '',
            $product->price ?? 0,
            $product->min_stock ?? 0,
            $product->inventory ? $product->inventory->quantity : 0,
            $product->is_active ? 'Hoạt động' : 'Ngừng hoạt động',
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

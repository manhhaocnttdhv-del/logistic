<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProductsImport implements ToModel, WithHeadingRow, SkipsEmptyRows, SkipsOnError, SkipsOnFailure
{
    private $errors = [];
    private $failures = [];

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Log raw row first to see what we're getting
            Log::info('Raw product import row:', $row);
            
            // Normalize column names
            $row = $this->normalizeRow($row);
            
            // Debug: Log the normalized row
            Log::info('Product import row normalized:', $row);
            
            // Get product name - try multiple possible keys
            $name = null;
            $possibleKeys = [
                'ten_san_pham',
                'tên sản phẩm',
                'tensanpham',
                'Tên sản phẩm',
                'TEN_SAN_PHAM',
            ];
            
            foreach ($possibleKeys as $key) {
                if (!empty($row[$key]) && trim($row[$key]) !== '') {
                    $name = trim($row[$key]);
                    break;
                }
            }
            
            // Also check original keys from raw row (before normalization)
            if (empty($name)) {
                $originalRow = func_get_arg(0) ?? $row;
                foreach ($originalRow as $key => $value) {
                    $lowerKey = mb_strtolower(trim($key));
                    // Check if key contains "ten" and "san" and "pham"
                    if (str_contains($lowerKey, 'ten') && str_contains($lowerKey, 'san') && str_contains($lowerKey, 'pham')) {
                        if (!empty($value) && trim($value) !== '') {
                            $name = trim($value);
                            Log::info('Found product name from original key:', ['key' => $key, 'name' => $name]);
                            break;
                        }
                    }
                }
            }
            
            // Last resort: check all values in row for non-empty string
            if (empty($name)) {
                foreach ($row as $key => $value) {
                    if (!empty($value) && is_string($value) && trim($value) !== '' && 
                        !str_contains(strtolower($key), 'ma') && 
                        !str_contains(strtolower($key), 'don') &&
                        !str_contains(strtolower($key), 'gia') &&
                        !str_contains(strtolower($key), 'ton') &&
                        !str_contains(strtolower($key), 'trang') &&
                        !str_contains(strtolower($key), 'nha') &&
                        !str_contains(strtolower($key), 'mo')) {
                        $name = trim($value);
                        Log::info('Found product name as fallback:', ['key' => $key, 'name' => $name]);
                        break;
                    }
                }
            }
            
            // Skip empty rows or rows without required data
            if (empty($name) || $name === '') {
                Log::info('Skipping empty row - no product name found', ['row_keys' => array_keys($row)]);
                return null;
            }
            
            // Skip rows that look like file paths or invalid data
            if (str_contains($name, '.xlsx') || str_contains($name, '.xls') || str_contains($name, 'Downloads/') || str_contains($name, '\\')) {
                Log::info('Skipping invalid row:', ['name' => $name]);
                return null;
            }
            
            // Get code
            $code = null;
            $possibleCodeKeys = [
                'ma_san_pham',
                'mã sản phẩm',
                'masanpham',
                'Mã sản phẩm',
                'MA_SAN_PHAM',
            ];
            foreach ($possibleCodeKeys as $key) {
                if (!empty($row[$key]) && trim($row[$key]) !== '') {
                    $code = trim($row[$key]);
                    break;
                }
            }
            
            // Generate code if not provided
            if (empty($code)) {
                $code = $this->generateCode();
            }
            
            // Validate code doesn't contain invalid characters
            if (str_contains($code, '.xlsx') || str_contains($code, '.xls') || str_contains($code, 'Downloads/')) {
                $code = $this->generateCode();
            }
            
            // Find supplier by name or code
            $supplier = null;
            $supplierKeys = ['nha_cung_cap', 'nhà cung cấp', 'nhacungcap', 'Nhà cung cấp'];
            $supplierValue = null;
            foreach ($supplierKeys as $key) {
                if (!empty($row[$key]) && trim($row[$key]) !== '') {
                    $supplierValue = trim($row[$key]);
                    break;
                }
            }
            
            if ($supplierValue) {
                $supplier = Supplier::where('name', $supplierValue)
                    ->orWhere('code', $supplierValue)
                    ->first();
            }
            
            // Check if product with this code already exists, update instead of create
            $product = Product::where('code', $code)->first();
            
            if ($product) {
                // Update existing product
                $product->update([
                    'name' => $name,
                    'unit' => $this->getValue($row, ['don_vi_tinh', 'đơn vị tính', 'donvitinh'], 'cái'),
                    'supplier_id' => $supplier?->id,
                    'description' => $this->getValue($row, ['mo_ta', 'mô tả', 'mota']),
                    'price' => $this->parseNumber($this->getValue($row, ['gia', 'giá', 'price'], 0)),
                    'min_stock' => $this->parseNumber($this->getValue($row, ['ton_kho_toi_thieu', 'tồn kho tối thiểu', 'tonkhoitoithieu'], 0)),
                    'is_active' => $this->parseBoolean($this->getValue($row, ['trang_thai', 'trạng thái', 'trangthai'], 'hoạt động')),
                ]);
                Log::info('Updated product:', ['code' => $code, 'name' => $name]);
                return null; // Don't create new model
            }

            // Create new product
            $newProduct = new Product([
                'code' => $code,
                'name' => $name,
                'unit' => $this->getValue($row, ['don_vi_tinh', 'đơn vị tính', 'donvitinh'], 'cái'),
                'supplier_id' => $supplier?->id,
                'description' => $this->getValue($row, ['mo_ta', 'mô tả', 'mota']),
                'price' => $this->parseNumber($this->getValue($row, ['gia', 'giá', 'price'], 0)),
                'min_stock' => $this->parseNumber($this->getValue($row, ['ton_kho_toi_thieu', 'tồn kho tối thiểu', 'tonkhoitoithieu'], 0)),
                'is_active' => $this->parseBoolean($this->getValue($row, ['trang_thai', 'trạng thái', 'trangthai'], 'hoạt động')),
            ]);
            
            Log::info('Creating new product:', ['code' => $code, 'name' => $name]);
            return $newProduct;
        } catch (\Exception $e) {
            Log::error('Product import error: ' . $e->getMessage(), ['row' => $row, 'trace' => $e->getTraceAsString()]);
            $this->errors[] = $e->getMessage();
            return null;
        }
    }
    
    /**
     * Get value from row using multiple possible keys
     */
    private function getValue(array $row, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            if (!empty($row[$key]) && trim($row[$key]) !== '') {
                return trim($row[$key]);
            }
        }
        return $default;
    }

    /**
     * Normalize row keys to handle different column name formats
     */
    private function normalizeRow(array $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            // Keep original key
            $normalized[$key] = $value;
            
            // Also add normalized versions
            $normalizedKey = mb_strtolower(trim(str_replace([' ', '_'], '', $key)));
            
            // Map common variations
            $mapping = [
                'masanpham' => 'ma_san_pham',
                'tensanpham' => 'ten_san_pham',
                'donvitinh' => 'don_vi_tinh',
                'nhacungcap' => 'nha_cung_cap',
                'mota' => 'mo_ta',
                'tonkhoitoithieu' => 'ton_kho_toi_thieu',
                'trangthai' => 'trang_thai',
            ];
            
            if (isset($mapping[$normalizedKey])) {
                $normalized[$mapping[$normalizedKey]] = $value;
            }
            
            // Also add Vietnamese variations
            $vietnameseMappings = [
                'mã sản phẩm' => 'ma_san_pham',
                'tên sản phẩm' => 'ten_san_pham',
                'đơn vị tính' => 'don_vi_tinh',
                'nhà cung cấp' => 'nha_cung_cap',
                'mô tả' => 'mo_ta',
                'tồn kho tối thiểu' => 'ton_kho_toi_thieu',
                'trạng thái' => 'trang_thai',
                'giá' => 'gia',
            ];
            
            $originalKey = trim($key);
            if (isset($vietnameseMappings[$originalKey])) {
                $normalized[$vietnameseMappings[$originalKey]] = $value;
            }
        }
        return $normalized;
    }

    /**
     * Parse number from string
     */
    private function parseNumber($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        // Remove commas and spaces
        $cleaned = str_replace([',', ' '], '', $value);
        return is_numeric($cleaned) ? (float) $cleaned : 0;
    }

    /**
     * Parse boolean from string
     */
    private function parseBoolean($value)
    {
        if (is_bool($value)) {
            return $value;
        }
        $value = mb_strtolower(trim($value));
        return in_array($value, ['1', 'true', 'hoạt động', 'active', 'yes', 'có']);
    }

    /**
     * @param \Throwable $e
     */
    public function onError(\Throwable $e)
    {
        Log::error('Product import error: ' . $e->getMessage());
        $this->errors[] = $e->getMessage();
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failures[] = $failure;
        }
    }

    /**
     * Get errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get failures
     */
    public function getFailures(): array
    {
        return $this->failures;
    }

    private function generateCode(): string
    {
        $lastProduct = Product::latest()->first();
        $number = $lastProduct ? (int)substr($lastProduct->code, -6) + 1 : 1;
        return 'SP' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}

<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;

class SuppliersImport implements ToModel, WithHeadingRow, SkipsEmptyRows, SkipsOnError, SkipsOnFailure
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
            // Store original row before normalization
            $originalRow = $row;
            
            // Log raw row first to see what we're getting
            Log::info('Raw import row:', $row);
            
            // Normalize column names
            $row = $this->normalizeRow($row);
            
            // Debug: Log the normalized row to see what we're getting
            Log::info('Import row normalized:', $row);
            
            // Get supplier name - try multiple possible keys (check all variations)
            $name = null;
            $possibleKeys = [
                'ten_nha_cung_cap',
                'tên nhà cung cấp',
                'tennhacungcap',
                'Tên nhà cung cấp',
                'TEN_NHA_CUNG_CAP',
            ];
            
            foreach ($possibleKeys as $key) {
                if (!empty($row[$key]) && trim($row[$key]) !== '') {
                    $name = trim($row[$key]);
                    break;
                }
            }
            
            // Also check original keys from raw row (before normalization)
            if (empty($name)) {
                foreach ($originalRow as $key => $value) {
                    $lowerKey = mb_strtolower(trim($key));
                    // Check if key contains "ten" and "nha" and "cung" and "cap"
                    if (str_contains($lowerKey, 'ten') && str_contains($lowerKey, 'nha') && str_contains($lowerKey, 'cung') && str_contains($lowerKey, 'cap')) {
                        if (!empty($value) && trim($value) !== '') {
                            $name = trim($value);
                            Log::info('Found name from original key:', ['key' => $key, 'name' => $name]);
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
                        !str_contains(strtolower($key), 'email') &&
                        !str_contains(strtolower($key), 'phone') &&
                        !str_contains(strtolower($key), 'dia') &&
                        !str_contains(strtolower($key), 'ghi') &&
                        !str_contains(strtolower($key), 'trang')) {
                        $name = trim($value);
                        Log::info('Found name as fallback:', ['key' => $key, 'name' => $name]);
                        break;
                    }
                }
            }
            
            // Skip empty rows or rows without required data
            if (empty($name) || $name === '') {
                Log::info('Skipping empty row - no name found', ['row_keys' => array_keys($row)]);
                return null;
            }
            
            // Skip rows that look like file paths or invalid data
            if (str_contains($name, '.xlsx') || str_contains($name, '.xls') || str_contains($name, 'Downloads/') || str_contains($name, '\\')) {
                Log::info('Skipping invalid row:', ['name' => $name]);
                return null;
            }
            
            // Get code
            $code = null;
            if (!empty($row['ma_nha_cung_cap'])) {
                $code = trim($row['ma_nha_cung_cap']);
            } elseif (!empty($row['mã nhà cung cấp'])) {
                $code = trim($row['mã nhà cung cấp']);
            }
            
            // Generate code if not provided
            if (empty($code)) {
                $code = $this->generateCode();
            }
            
            // Validate code doesn't contain invalid characters
            if (str_contains($code, '.xlsx') || str_contains($code, '.xls') || str_contains($code, 'Downloads/')) {
                $code = $this->generateCode();
            }
            
            // Check if supplier with this code already exists, update instead of create
            $supplier = Supplier::where('code', $code)->first();
            
            if ($supplier) {
                // Update existing supplier
                $supplier->update([
                    'name' => $name,
                    'contact_person' => !empty($row['nguoi_lien_he']) ? trim($row['nguoi_lien_he']) : null,
                    'phone' => !empty($row['so_dien_thoai']) ? trim($row['so_dien_thoai']) : null,
                    'email' => !empty($row['email']) ? trim($row['email']) : null,
                    'address' => !empty($row['dia_chi']) ? trim($row['dia_chi']) : null,
                    'notes' => !empty($row['ghi_chu']) ? trim($row['ghi_chu']) : null,
                    'is_active' => $this->parseBoolean($row['trang_thai'] ?? 'hoạt động'),
                ]);
                Log::info('Updated supplier:', ['code' => $code, 'name' => $name]);
                return null; // Don't create new model
            }

            // Create new supplier
            $newSupplier = new Supplier([
                'code' => $code,
                'name' => $name,
                'contact_person' => !empty($row['nguoi_lien_he']) ? trim($row['nguoi_lien_he']) : null,
                'phone' => !empty($row['so_dien_thoai']) ? trim($row['so_dien_thoai']) : null,
                'email' => !empty($row['email']) ? trim($row['email']) : null,
                'address' => !empty($row['dia_chi']) ? trim($row['dia_chi']) : null,
                'notes' => !empty($row['ghi_chu']) ? trim($row['ghi_chu']) : null,
                'is_active' => $this->parseBoolean($row['trang_thai'] ?? 'hoạt động'),
            ]);
            
            Log::info('Creating new supplier:', ['code' => $code, 'name' => $name]);
            return $newSupplier;
        } catch (\Exception $e) {
            Log::error('Supplier import error: ' . $e->getMessage(), ['row' => $row, 'trace' => $e->getTraceAsString()]);
            $this->errors[] = $e->getMessage();
            return null;
        }
    }

    /**
     * Normalize row keys to handle different column name formats
     */
    private function normalizeRow(array $row): array
    {
        $normalized = [];
        
        // Direct mapping for common column name variations
        $columnMapping = [
            // Vietnamese with spaces
            'mã nhà cung cấp' => 'ma_nha_cung_cap',
            'tên nhà cung cấp' => 'ten_nha_cung_cap',
            'người liên hệ' => 'nguoi_lien_he',
            'số điện thoại' => 'so_dien_thoai',
            'địa chỉ' => 'dia_chi',
            'ghi chú' => 'ghi_chu',
            'trạng thái' => 'trang_thai',
            // Without spaces
            'manhacungcap' => 'ma_nha_cung_cap',
            'tennhacungcap' => 'ten_nha_cung_cap',
            'nguoilienhe' => 'nguoi_lien_he',
            'sodienthoai' => 'so_dien_thoai',
            'diachi' => 'dia_chi',
            'ghichu' => 'ghi_chu',
            'trangthai' => 'trang_thai',
            // With underscores
            'ma_nha_cung_cap' => 'ma_nha_cung_cap',
            'ten_nha_cung_cap' => 'ten_nha_cung_cap',
            'nguoi_lien_he' => 'nguoi_lien_he',
            'so_dien_thoai' => 'so_dien_thoai',
            'dia_chi' => 'dia_chi',
            'ghi_chu' => 'ghi_chu',
            'trang_thai' => 'trang_thai',
        ];
        
        foreach ($row as $key => $value) {
            // Remove extra spaces and convert to lowercase
            $cleanKey = mb_strtolower(trim($key));
            
            // Remove all spaces and special characters for matching
            $keyWithoutSpaces = mb_strtolower(trim(str_replace([' ', '_', '-'], '', $key)));
            
            // Try direct match first
            if (isset($columnMapping[$cleanKey])) {
                $normalized[$columnMapping[$cleanKey]] = $value;
            }
            // Try match without spaces
            elseif (isset($columnMapping[$keyWithoutSpaces])) {
                $normalized[$columnMapping[$keyWithoutSpaces]] = $value;
            }
            // Keep original key if no mapping found
            else {
                $normalized[$key] = $value;
            }
        }
        
        return $normalized;
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
        Log::error('Supplier import error: ' . $e->getMessage());
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
        $lastSupplier = Supplier::latest()->first();
        $number = $lastSupplier ? (int)substr($lastSupplier->code, -6) + 1 : 1;
        return 'NCC' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}

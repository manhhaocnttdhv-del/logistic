<!DOCTYPE html>
<html lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <title>Phiếu Nhập - {{ $importOrder->code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            margin: 20mm;
        }
        
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            color: #000;
            line-height: 1.6;
            padding: 0 15mm;
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 0 10mm;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #333;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .header .subtitle {
            font-size: 14px;
            color: #7f8c8d;
        }
        
        .info-section {
            margin-bottom: 25px;
        }
        
        .info-row {
            margin-bottom: 8px;
            overflow: hidden;
        }
        
        .info-label {
            float: left;
            width: 30%;
            font-weight: bold;
            color: #555;
        }
        
        .info-value {
            float: left;
            width: 70%;
        }
        
        .info-row::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .items-table th {
            background-color: #34495e;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #2c3e50;
        }
        
        .items-table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .total-section {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #333;
        }
        
        .total-row {
            margin-bottom: 8px;
            font-size: 13px;
            overflow: hidden;
        }
        
        .total-row .total-label {
            float: left;
        }
        
        .total-row span:last-child {
            float: right;
        }
        
        .total-row::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .total-label {
            font-weight: bold;
        }
        
        .total-amount {
            font-size: 16px;
            font-weight: bold;
            color: #e74c3c;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        
        .signature-section {
            width: 100%;
            margin-top: 30px;
            overflow: hidden;
        }
        
        .signature-box {
            float: left;
            width: 50%;
            text-align: center;
            padding: 10px;
        }
        
        .signature-section::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .status-completed {
            background-color: #27ae60;
            color: white;
        }
        
        .status-pending {
            background-color: #f39c12;
            color: white;
        }
        
        .status-processing {
            background-color: #3498db;
            color: white;
        }
        
        .status-cancelled {
            background-color: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
    <div class="header">
        <h1>PHIẾU NHẬP KHO</h1>
        <div class="subtitle">Mã phiếu: {{ $importOrder->code }}</div>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Ngày nhập:</div>
            <div class="info-value">{{ $importOrder->import_date ? $importOrder->import_date->format('d/m/Y') : '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nguồn nhập:</div>
            <div class="info-value">
                @if($importOrder->source == 'supplier')
                    Nhà cung cấp
                @elseif($importOrder->source == 'transfer')
                    Điều chuyển
                @elseif($importOrder->source == 'return')
                    Trả hàng
                @else
                    Khác
                @endif
            </div>
        </div>
        @if($importOrder->supplier)
        <div class="info-row">
            <div class="info-label">Nhà cung cấp:</div>
            <div class="info-value">{{ $importOrder->supplier->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Người liên hệ:</div>
            <div class="info-value">{{ $importOrder->supplier->contact_person }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Điện thoại:</div>
            <div class="info-value">{{ $importOrder->supplier->phone }}</div>
        </div>
        @endif
        @if($importOrder->from_warehouse)
        <div class="info-row">
            <div class="info-label">Kho nguồn:</div>
            <div class="info-value">{{ $importOrder->from_warehouse }}</div>
        </div>
        @endif
        @if($importOrder->creator)
        <div class="info-row">
            <div class="info-label">Người tạo:</div>
            <div class="info-value">{{ $importOrder->creator->name }}</div>
        </div>
        @endif
        @if($importOrder->assignedStaff)
        <div class="info-row">
            <div class="info-label">Người được giao:</div>
            <div class="info-value">{{ $importOrder->assignedStaff->name }}</div>
        </div>
        @endif
        <div class="info-row">
            <div class="info-label">Trạng thái:</div>
            <div class="info-value">
                @if($importOrder->status == 'completed')
                    <span class="status-badge status-completed">Hoàn thành</span>
                @elseif($importOrder->status == 'pending')
                    <span class="status-badge status-pending">Chờ xử lý</span>
                @elseif($importOrder->status == 'processing')
                    <span class="status-badge status-processing">Đang xử lý</span>
                @else
                    <span class="status-badge status-cancelled">Đã hủy</span>
                @endif
            </div>
        </div>
        @if($importOrder->confirmed_at)
        <div class="info-row">
            <div class="info-label">Ngày xác nhận:</div>
            <div class="info-value">{{ $importOrder->confirmed_at ? $importOrder->confirmed_at->format('d/m/Y H:i') : '-' }}</div>
        </div>
        @endif
        @if($importOrder->notes)
        <div class="info-row">
            <div class="info-label">Ghi chú:</div>
            <div class="info-value">{{ $importOrder->notes }}</div>
        </div>
        @endif
    </div>
    
    <h3 style="margin: 20px 0 10px 0; font-size: 14px;">Chi tiết sản phẩm:</h3>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">STT</th>
                <th style="width: 15%;">Mã SP</th>
                <th style="width: 30%;">Tên sản phẩm</th>
                <th style="width: 10%;">Đơn vị</th>
                <th style="width: 12%;" class="text-right">Số lượng</th>
                <th style="width: 13%;" class="text-right">Đơn giá</th>
                <th style="width: 15%;" class="text-right">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($importOrder->items as $index => $item)
                @if($item->product)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->product->code ?? '-' }}</td>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td class="text-center">{{ $item->product->unit ?? '-' }}</td>
                    <td class="text-right">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }} đ</td>
                    <td class="text-right">{{ number_format($item->total_price, 0, ',', '.') }} đ</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    
    <div class="total-section">
        <div class="total-row">
            <span class="total-label">Tổng số sản phẩm:</span>
            <span>{{ $importOrder->items->count() }} sản phẩm</span>
        </div>
        <div class="total-row">
            <span class="total-label">Tổng số lượng:</span>
            <span>{{ number_format($importOrder->items->sum('quantity'), 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span class="total-label">Tổng tiền:</span>
            <span class="total-amount">{{ number_format($importOrder->total_amount, 0, ',', '.') }} đ</span>
        </div>
    </div>
    
    <div class="footer">
        @if($importOrder->staff_notes)
        <div style="margin-bottom: 15px;">
            <strong>Ghi chú xử lý:</strong>
            <p style="margin-top: 5px;">{{ $importOrder->staff_notes }}</p>
        </div>
        @endif
        
        <div class="signature-section">
            @if($importOrder->creator)
            <div class="signature-box">
                <div class="signature-line">
                    <strong>Người tạo</strong><br>
                    {{ $importOrder->creator->name }}
                </div>
            </div>
            @endif
            @if($importOrder->confirmer)
            <div class="signature-box">
                <div class="signature-line">
                    <strong>Người xác nhận</strong><br>
                    {{ $importOrder->confirmer->name }}
                </div>
            </div>
            @endif
        </div>
        
        <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #7f8c8d;">
            <p>Phiếu được in vào: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
    </div>
</body>
</html>


# Hệ thống Quản lý Kho - Đồ án Tốt nghiệp

Hệ thống quản lý kho được xây dựng bằng Laravel và Bootstrap với template AdminLTE 4.0.

## Tính năng

### I. Nghiệp vụ của Admin

1. **Quản lý hàng hóa**
   - Thêm mới mặt hàng (tên hàng, mã hàng, đơn vị tính, nhà cung cấp…)
   - Cập nhật thông tin hàng hóa
   - Xóa / ngừng kinh doanh hàng hóa
   - Tra cứu danh sách hàng hóa

2. **Quản lý nhập kho**
   - Tạo phiếu nhập
   - Ghi nhận thông tin: Nhà cung cấp, Số lượng nhập, Đơn giá, Ngày nhập, Nhân viên thực hiện
   - Xác nhận phiếu nhập
   - In / xuất PDF phiếu nhập

3. **Quản lý xuất kho**
   - Tạo phiếu xuất
   - Ghi nhận thông tin: Mặt hàng, Số lượng xuất, Người nhận, Lý do xuất
   - Xác nhận phiếu xuất
   - Theo dõi tồn kho theo thời gian thực

4. **Quản lý nhân viên**
   - Thêm nhân viên kho
   - Chỉnh sửa thông tin nhân viên
   - Gán vai trò nhân viên
   - Khóa / kích hoạt tài khoản
   - Theo dõi năng suất nhân viên

5. **Phân công công việc**
   - Giao nhiệm vụ cho từng nhân viên
   - Theo dõi tiến độ hoàn thành nhiệm vụ
   - Xác nhận hoàn thành

6. **Quản lý báo cáo**
   - Báo cáo tồn kho theo ngày/tháng/quý
   - Báo cáo nhập – xuất – tồn
   - Báo cáo hiệu suất nhân viên
   - Báo cáo sản phẩm sắp hết hàng
   - Xuất Excel/PDF

### II. Nghiệp vụ của Nhân viên kho

1. **Xử lý phiếu nhập**
   - Nhận công việc được giao
   - Kiểm tra hàng thực tế
   - Nhập số lượng
   - Ghi chú tình trạng hàng
   - Gửi yêu cầu Admin xác nhận

2. **Xử lý phiếu xuất**
   - Nhận nhiệm vụ xuất hàng
   - Kiểm tra kho và soạn hàng
   - Xác nhận đã xuất đủ
   - Gửi báo cáo hoàn thành nhiệm vụ

3. **Kiểm kê**
   - Kiểm số lượng hàng trong kho
   - Gửi số lượng kiểm kê lên hệ thống
   - Ghi nhận chênh lệch (nếu có)

4. **Xem lịch phân công**
   - Xem danh sách công việc (hôm nay + tuần)
   - Nhận công việc hoặc đánh dấu hoàn thành

## Cài đặt

### Yêu cầu hệ thống
- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Node.js và NPM (nếu cần build assets)

### Các bước cài đặt

1. **Clone hoặc tải project về**

2. **Cài đặt dependencies**
```bash
composer install
```

3. **Cấu hình môi trường**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Cấu hình database trong file `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=warehouse_db
DB_USERNAME=root
DB_PASSWORD=
```

5. **Chạy migrations và seeders**
```bash
php artisan migrate
php artisan db:seed
```

6. **Chạy server**
```bash
php artisan serve
```

7. **Truy cập hệ thống**
- URL: http://localhost:8000
- Đăng nhập với tài khoản mặc định:
  - Admin: `admin@example.com` / `password`
  - Staff: `staff1@example.com` / `password`

## Cấu trúc Project

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Controllers cho Admin
│   │   └── Staff/           # Controllers cho Staff
│   └── Middleware/         # Middleware phân quyền
├── Models/                 # Models
database/
├── migrations/            # Database migrations
└── seeders/              # Database seeders
resources/
└── views/
    ├── layouts/           # Layout chính
    ├── admin/             # Views cho Admin
    └── staff/             # Views cho Staff
routes/
└── web.php               # Routes
public/
└── adminlte/            # AdminLTE assets
```

## Tài khoản mặc định

Sau khi chạy seeder, bạn có thể đăng nhập với:

**Admin:**
- Email: `admin@example.com`
- Password: `password`

**Staff:**
- Email: `staff1@example.com` hoặc `staff2@example.com`
- Password: `password`

## Phát triển tiếp

Để hoàn thiện hệ thống, bạn cần:

1. **Hoàn thiện các Controllers còn lại:**
   - SupplierController
   - ImportOrderController
   - ExportOrderController
   - EmployeeController
   - TaskController
   - ReportController
   - Staff controllers

2. **Tạo các Views tương ứng** cho từng controller

3. **Implement chức năng xuất PDF và Excel** cho báo cáo

4. **Thêm validation và xử lý lỗi** đầy đủ

5. **Tối ưu hiệu năng** và bảo mật

## Packages đã sử dụng

- **Laravel Framework** 12.x
- **AdminLTE 4.0** - Admin template
- **barryvdh/laravel-dompdf** - PDF generation
- **maatwebsite/excel** - Excel export/import

## License

MIT License

## Tác giả

Đồ án Tốt nghiệp - Hệ thống Quản lý Kho

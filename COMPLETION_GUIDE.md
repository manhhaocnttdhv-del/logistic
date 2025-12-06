# Hướng dẫn Hoàn thiện Hệ thống

## Tình trạng hiện tại

### ✅ Đã hoàn thành:
1. **Products Module** - Đầy đủ (index, create, edit, show)
2. **Suppliers Module** - Đầy đủ (index, create, edit, show)
3. **Import Orders Module** - Controller đầy đủ, views: index, create, show (còn thiếu edit)
4. **Layout & Authentication** - Hoàn chỉnh
5. **Database & Models** - Hoàn chỉnh
6. **Middleware & Routes** - Hoàn chỉnh

### ⚠️ Cần hoàn thiện:

#### 1. Import Orders
- [ ] View `edit.blade.php` (tương tự create nhưng có dữ liệu sẵn)
- [ ] View `pdf.blade.php` (template PDF cho phiếu nhập)

#### 2. Export Orders Module
- [ ] Controller đầy đủ chức năng
- [ ] Views: index, create, edit, show
- [ ] Chức năng xác nhận và cập nhật tồn kho

#### 3. Employees Module
- [ ] Controller đầy đủ chức năng
- [ ] Views: index, create, edit, show
- [ ] Chức năng toggle status, theo dõi năng suất

#### 4. Tasks Module (Admin)
- [ ] Controller đầy đủ chức năng
- [ ] Views: index, create, edit, show
- [ ] Phân công công việc cho nhân viên

#### 5. Reports Module
- [ ] Controller với các báo cáo:
  - Báo cáo tồn kho
  - Báo cáo nhập-xuất-tồn
  - Báo cáo hiệu suất nhân viên
  - Báo cáo sản phẩm sắp hết hàng
- [ ] Views cho từng loại báo cáo
- [ ] Chức năng xuất Excel/PDF

#### 6. Staff Modules
- [ ] Staff/TaskController - Xem và xử lý công việc
- [ ] Staff/ImportOrderController - Xử lý phiếu nhập
- [ ] Staff/ExportOrderController - Xử lý phiếu xuất
- [ ] Staff/InventoryController - Kiểm kê kho
- [ ] Views tương ứng cho từng controller

## Cách tiếp tục phát triển

### 1. Export Orders Controller
Tham khảo ImportOrderController, tạo tương tự nhưng:
- Generate code: 'PX' + số
- Không có supplier, có recipient và reason
- Khi xác nhận sẽ giảm tồn kho

### 2. Employee Controller
Tham khảo ProductController, thêm:
- Toggle status (active/inactive)
- Thống kê năng suất (số phiếu đã xử lý)

### 3. Task Controller
- Tạo task với type: import, export, inventory_check, picking, stock_report
- Gán cho nhân viên
- Theo dõi trạng thái: pending, in_progress, completed

### 4. Report Controller
- Sử dụng Query Builder để tính toán
- Export Excel: dùng Maatwebsite\Excel
- Export PDF: dùng DomPDF

### 5. Staff Controllers
- Đơn giản hơn Admin controllers
- Focus vào xử lý và cập nhật trạng thái
- Không có quyền xóa hoặc chỉnh sửa lớn

## Lưu ý quan trọng

1. **Cập nhật tồn kho**: 
   - Khi xác nhận Import Order → Tăng tồn kho
   - Khi xác nhận Export Order → Giảm tồn kho
   - Sử dụng Inventory model methods: increase(), decrease()

2. **Generate Code**:
   - Import: 'PN' + 6 số
   - Export: 'PX' + 6 số
   - Tự động tăng dần

3. **Validation**:
   - Kiểm tra tồn kho trước khi xuất
   - Không cho xóa khi đã có dữ liệu liên quan

4. **Permissions**:
   - Admin: Full access
   - Staff: Chỉ xử lý và cập nhật trạng thái

## File mẫu để tham khảo

- `app/Http/Controllers/Admin/ProductController.php` - CRUD đầy đủ
- `resources/views/admin/products/*.blade.php` - Views mẫu
- `app/Http/Controllers/Admin/ImportOrderController.php` - Controller phức tạp với items

## Các bước tiếp theo

1. Hoàn thiện Import Orders (edit view, PDF view)
2. Tạo Export Orders module hoàn chỉnh
3. Tạo Employees module
4. Tạo Tasks module
5. Tạo Reports module
6. Tạo tất cả Staff modules
7. Test toàn bộ hệ thống
8. Tối ưu và fix bugs


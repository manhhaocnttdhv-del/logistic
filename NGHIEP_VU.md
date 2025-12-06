# NGHIỆP VỤ HỆ THỐNG QUẢN LÝ LOGISTICS

## MỤC LỤC
1. [Tổng quan hệ thống](#tổng-quan-hệ-thống)
2. [Nghiệp vụ Admin](#nghiệp-vụ-admin)
3. [Nghiệp vụ Staff](#nghiệp-vụ-staff)
4. [Quy trình nghiệp vụ chi tiết](#quy-trình-nghiệp-vụ-chi-tiết)
5. [Phân công nhân viên](#phân-công-nhân-viên)
6. [Quản lý tồn kho](#quản-lý-tồn-kho)
7. [Trạng thái và luồng xử lý](#trạng-thái-và-luồng-xử-lý)
8. [Kiểm soát và bảo mật](#kiểm-soát-và-bảo-mật)

---

## TỔNG QUAN HỆ THỐNG

Hệ thống quản lý logistics nội bộ hỗ trợ toàn bộ quy trình từ yêu cầu vật tư, nhập kho, xuất kho, điều chuyển đến báo cáo và phân tích.

### Các module chính:
- **Quản lý Danh mục**: Sản phẩm, Nhà cung cấp, Nhân viên
- **Yêu cầu Vật tư**: Tạo, duyệt, chuyển thành phiếu xuất
- **Phiếu Nhập kho**: Nhập từ NCC, điều chuyển, trả hàng
- **Phiếu Xuất kho**: Xuất cho bộ phận, dự án, điều chuyển
- **Phân công Công việc**: Tạo và theo dõi nhiệm vụ
- **Báo cáo**: Tồn kho, nhập-xuất-tồn, hiệu suất

---

## NGHIỆP VỤ ADMIN

### 1. Quản lý Danh mục

#### 1.1. Quản lý Hàng hóa/Vật tư (Products)
- **Tạo mới**: Thêm sản phẩm với thông tin đầy đủ (tên, mã, đơn vị, giá, mô tả)
- **Chỉnh sửa**: Cập nhật thông tin sản phẩm
- **Xóa/Ẩn**: Vô hiệu hóa sản phẩm (soft delete)
- **Tìm kiếm**: Tìm theo tên, mã, danh mục
- **Import/Export Excel**: Nhập/xuất danh sách sản phẩm
- **Quản lý tồn kho**: Xem số lượng tồn kho hiện tại

#### 1.2. Quản lý Nhà cung cấp (Suppliers)
- **Tạo mới**: Thêm NCC với thông tin liên hệ, địa chỉ
- **Chỉnh sửa**: Cập nhật thông tin NCC
- **Xóa/Ẩn**: Vô hiệu hóa NCC
- **Lịch sử giao hàng**: Xem các phiếu nhập từ NCC
- **Đánh giá**: Theo dõi chất lượng dịch vụ NCC

#### 1.3. Quản lý Nhân viên (Employees)
- **Tạo tài khoản**: Tạo tài khoản nhân viên với chức vụ
- **Chỉnh sửa**: Cập nhật thông tin, chức vụ
- **Phân công chức vụ**: 
  - `import_staff`: Nhân viên nhập kho
  - `export_staff`: Nhân viên xuất kho
  - `inventory_staff`: Nhân viên kiểm kê
  - `warehouse_staff`: Nhân viên kho (nhiệm vụ khác)
  - `general_staff`: Nhân viên tổng hợp
- **Kích hoạt/Vô hiệu hóa**: Bật/tắt tài khoản
- **Theo dõi hiệu suất**: Xem số lượng công việc đã hoàn thành

### 2. Quản lý Yêu cầu Vật tư (Material Requests)

#### 2.1. Tạo yêu cầu
- **Thông tin yêu cầu**:
  - Phòng ban/Dự án
  - Người yêu cầu
  - Danh sách sản phẩm và số lượng
  - Ghi chú

#### 2.2. Duyệt yêu cầu
- **Phê duyệt**: Chuyển sang trạng thái `approved`
- **Từ chối**: Chuyển sang trạng thái `rejected` (có lý do)
- **Chuyển thành phiếu xuất**: Tự động tạo phiếu xuất khi duyệt

#### 2.3. Trạng thái yêu cầu
- `pending`: Chờ duyệt
- `approved`: Đã duyệt
- `converted`: Đã chuyển thành phiếu xuất
- `rejected`: Đã từ chối

### 3. Quản lý Nhập kho (Import Orders)

#### 3.1. Tạo phiếu nhập
- **Nguồn nhập**:
  - `supplier`: Từ nhà cung cấp (bắt buộc chọn NCC)
  - `transfer`: Điều chuyển từ kho khác
  - `return`: Trả hàng
  - `other`: Nguồn khác
- **Thông tin phiếu**:
  - Mã phiếu (tự động: PN000001, PN000002...)
  - Ngày nhập
  - Danh sách sản phẩm (mã, số lượng, đơn giá, thành tiền)
  - Ghi chú

#### 3.2. Phân công nhân viên
- **Tự động phân công** (khuyến nghị):
  - Ưu tiên: `import_staff` → `warehouse_staff` → `general_staff`
  - Tính workload để phân công công bằng
- **Phân công thủ công**: Chọn nhân viên cụ thể
- **Phân công theo chức vụ**: Chọn chức vụ, hệ thống tự tìm nhân viên phù hợp

#### 3.3. Xác nhận phiếu nhập
- Admin có thể xác nhận trực tiếp (nếu cần)
- Tự động cập nhật tồn kho khi xác nhận

#### 3.4. In phiếu nhập
- Xuất PDF
- Xuất Excel

### 4. Quản lý Xuất kho (Export Orders)

#### 4.1. Tạo phiếu xuất
- **Lý do xuất**:
  - `sale`: Bán hàng
  - `internal`: Nội bộ (phòng ban, dự án)
  - `transfer`: Điều chuyển sang kho khác
  - `return_supplier`: Trả hàng nhà cung cấp
  - `return`: Trả hàng khác
  - `other`: Lý do khác
- **Thông tin phiếu**:
  - Mã phiếu (tự động: PX000001, PX000002...)
  - Ngày xuất
  - Người nhận (nếu có)
  - Phòng ban/Dự án (nếu xuất nội bộ)
  - Danh sách sản phẩm (mã, số lượng)
  - Ghi chú
- **Kiểm tra tồn kho**: Tự động kiểm tra trước khi tạo phiếu
  - Không cho tạo nếu không đủ hàng
  - Hiển thị cảnh báo nếu tồn kho thấp

#### 4.2. Phân công nhân viên
- **Tự động phân công** (khuyến nghị):
  - Ưu tiên: `export_staff` → `warehouse_staff` → `general_staff`
  - Tính workload để phân công công bằng
- **Phân công thủ công**: Chọn nhân viên cụ thể
- **Phân công theo chức vụ**: Chọn chức vụ, hệ thống tự tìm nhân viên phù hợp

#### 4.3. Xác nhận phiếu xuất
- Admin có thể xác nhận trực tiếp (nếu cần)
- Tự động giảm tồn kho khi xác nhận (có kiểm tra lại)

#### 4.4. In phiếu xuất
- Xuất PDF
- Xuất Excel

### 5. Phân công Công việc (Tasks)

#### 5.1. Tạo nhiệm vụ
- **Loại nhiệm vụ**:
  - `import`: Nhập kho
  - `export`: Xuất kho
  - `inventory_check`: Kiểm kê
  - `picking`: Soạn hàng
  - `stock_report`: Báo cáo tồn kho
- **Thông tin nhiệm vụ**:
  - Tiêu đề
  - Mô tả
  - Người được giao
  - Độ ưu tiên (normal, high, urgent)
  - Hạn hoàn thành
  - Liên kết với phiếu (nếu có)

#### 5.2. Phân công
- **Phân công theo chức vụ**: Chọn chức vụ, hệ thống tự tìm nhân viên
- **Phân công thủ công**: Chọn nhân viên cụ thể

#### 5.3. Theo dõi
- Xem danh sách tất cả nhiệm vụ
- Lọc theo trạng thái, người được giao, độ ưu tiên
- Xem chi tiết và tiến độ

### 6. Quản lý Báo cáo

#### 6.1. Báo cáo Tồn kho
- Tồn kho hiện tại theo sản phẩm
- Sản phẩm sắp hết hàng
- Giá trị tồn kho

#### 6.2. Báo cáo Nhập-Xuất-Tồn (NXT)
- Tổng hợp theo kỳ (ngày, tuần, tháng, năm)
- So sánh nhập/xuất
- Biến động tồn kho

#### 6.3. Báo cáo Hiệu suất
- Hiệu suất nhân viên (số phiếu đã xử lý)
- Hiệu suất nhà cung cấp
- Thời gian xử lý trung bình

#### 6.4. Xuất báo cáo
- Excel
- PDF

---

## NGHIỆP VỤ STAFF

### 1. Xử lý Phiếu Nhập

#### 1.1. Xem danh sách phiếu được giao
- Chỉ xem phiếu được phân công cho mình
- Lọc theo trạng thái
- Tìm kiếm theo mã phiếu

#### 1.2. Xem chi tiết phiếu
- Thông tin phiếu (mã, ngày, nguồn, NCC)
- Danh sách sản phẩm (tên, số lượng, đơn giá)
- Ghi chú từ admin

#### 1.3. Xử lý phiếu
- **Bắt đầu** (`start`):
  - Chuyển trạng thái: `pending` → `processing`
  - Thêm ghi chú (nếu cần)
- **Hoàn thành** (`complete`):
  - Kiểm tra hàng hóa thực tế
  - Cập nhật số lượng (nếu có chênh lệch)
  - Thêm ghi chú
  - Tự động cập nhật tồn kho
  - Chuyển trạng thái: `processing` → `completed`
  - Tự động ghi nhận người xác nhận và thời gian

### 2. Xử lý Phiếu Xuất

#### 2.1. Xem danh sách phiếu được giao
- Chỉ xem phiếu được phân công cho mình
- Lọc theo trạng thái
- Tìm kiếm theo mã phiếu

#### 2.2. Xem chi tiết phiếu
- Thông tin phiếu (mã, ngày, người nhận, lý do)
- Danh sách sản phẩm (tên, số lượng)
- Tồn kho hiện tại của từng sản phẩm
- Ghi chú từ admin

#### 2.3. Xử lý phiếu
- **Bắt đầu** (`start`):
  - **Kiểm tra tồn kho**: Xác nhận đủ hàng trước khi bắt đầu
    - Nếu không đủ: Báo lỗi, không cho bắt đầu
  - Chuyển trạng thái: `pending` → `processing`
  - Thêm ghi chú (nếu cần)
- **Hoàn thành** (`complete`):
  - Soạn hàng từ kho
  - **Kiểm tra tồn kho lại**: Double check trước khi giảm tồn kho
    - Nếu không đủ: Báo lỗi, không cho hoàn thành
  - Thêm ghi chú
  - Tự động giảm tồn kho
  - Chuyển trạng thái: `processing` → `completed`
  - Tự động ghi nhận người xác nhận và thời gian

### 3. Kiểm kê Kho

#### 3.1. Xem tồn kho
- Danh sách sản phẩm và số lượng hiện tại
- Lọc theo danh mục
- Tìm kiếm sản phẩm

#### 3.2. Cập nhật tồn kho
- Điều chỉnh khi có chênh lệch
- Ghi chú lý do chênh lệch
- Lưu lịch sử thay đổi

### 4. Công việc được giao (Tasks)

#### 4.1. Xem danh sách
- Tất cả công việc được phân công
- Lọc theo trạng thái, độ ưu tiên
- Sắp xếp theo hạn hoàn thành

#### 4.2. Xử lý công việc
- **Bắt đầu**: Đánh dấu đã bắt đầu
- **Hoàn thành**: Cập nhật trạng thái và ghi chú

---

## QUY TRÌNH NGHIỆP VỤ CHI TIẾT

### Quy trình 1: Yêu cầu Vật tư → Xuất kho

```
1. Bộ phận tạo Yêu cầu vật tư
   └─> Trạng thái: pending

2. Admin duyệt yêu cầu
   ├─> Phê duyệt: approved
   └─> Từ chối: rejected (có lý do)

3. Hệ thống tự động tạo Phiếu xuất (khi duyệt)
   └─> Trạng thái: pending
   └─> Yêu cầu: converted

4. Admin gán nhân viên soạn hàng
   ├─> Tự động phân công: export_staff (ưu tiên)
   └─> Hoặc chọn nhân viên cụ thể

5. Nhân viên xử lý
   ├─> Start: Kiểm tra tồn kho → Trạng thái: processing
   └─> Complete: Soạn hàng → Giảm tồn kho → Trạng thái: completed

6. Tồn kho tự động giảm
```

### Quy trình 2: Nhập kho từ Nhà cung cấp

```
1. Admin tạo Phiếu nhập (từ NCC)
   └─> Trạng thái: pending
   └─> Nguồn: supplier

2. Admin gán nhân viên nhận hàng
   ├─> Tự động phân công: import_staff (ưu tiên)
   └─> Hoặc chọn nhân viên cụ thể

3. Nhân viên xử lý
   ├─> Start: Nhận hàng → Trạng thái: processing
   └─> Complete: Kiểm tra → Nhập kho → Tăng tồn kho → Trạng thái: completed

4. Tồn kho tự động tăng

5. Admin xác nhận (nếu cần)
   └─> Có thể xác nhận trực tiếp mà không cần nhân viên
```

### Quy trình 3: Điều chuyển giữa các kho

```
1. Admin tạo Phiếu điều chuyển
   ├─> Phiếu xuất từ kho A
   └─> Phiếu nhập vào kho B

2. Gán nhân viên xử lý
   ├─> Xuất: export_staff
   └─> Nhập: import_staff

3. Nhân viên thực hiện điều chuyển
   ├─> Xử lý phiếu xuất: Giảm tồn kho kho A
   └─> Xử lý phiếu nhập: Tăng tồn kho kho B

4. Cập nhật tồn kho cả 2 kho
```

### Quy trình 4: Kiểm kê định kỳ

```
1. Admin tạo nhiệm vụ Kiểm kê
   └─> Loại: inventory_check
   └─> Gán: inventory_staff

2. Nhân viên kiểm đếm thực tế
   └─> So sánh với tồn kho hệ thống

3. Cập nhật chênh lệch (nếu có)
   └─> Điều chỉnh tồn kho
   └─> Ghi chú lý do

4. Báo cáo kết quả
```

---

## PHÂN CÔNG NHÂN VIÊN

### Chức vụ và nhiệm vụ

#### 1. Nhân viên nhập kho (`import_staff`)
- **Nhiệm vụ chính**: Xử lý phiếu nhập
- **Ưu tiên phân công**: Phiếu nhập
- **Số lượng**: 2-3 người (tùy quy mô)

#### 2. Nhân viên xuất kho (`export_staff`)
- **Nhiệm vụ chính**: Xử lý phiếu xuất
- **Ưu tiên phân công**: Phiếu xuất
- **Số lượng**: 2-3 người (tùy quy mô)

#### 3. Nhân viên kiểm kê (`inventory_staff`)
- **Nhiệm vụ chính**: Kiểm kê kho, báo cáo tồn kho
- **Ưu tiên phân công**: Nhiệm vụ kiểm kê, báo cáo
- **Số lượng**: 1-2 người

#### 4. Nhân viên kho (`warehouse_staff`)
- **Nhiệm vụ**: Hỗ trợ, điều chuyển, nhiệm vụ khác
- **Phân công**: Khi không có nhân viên chuyên trách
- **Số lượng**: 2-3 người

#### 5. Nhân viên tổng hợp (`general_staff`)
- **Nhiệm vụ**: Linh hoạt, hỗ trợ các nhiệm vụ khác
- **Phân công**: Khi các chức vụ khác quá tải
- **Số lượng**: 1-2 người

### Logic phân công tự động

#### Phân công phiếu nhập
```
Ưu tiên 1: import_staff
    ↓ (nếu không có hoặc quá tải)
Ưu tiên 2: warehouse_staff
    ↓ (nếu không có hoặc quá tải)
Ưu tiên 3: general_staff
```

#### Phân công phiếu xuất
```
Ưu tiên 1: export_staff
    ↓ (nếu không có hoặc quá tải)
Ưu tiên 2: warehouse_staff
    ↓ (nếu không có hoặc quá tải)
Ưu tiên 3: general_staff
```

#### Tính workload
- Đếm số phiếu `pending` và `processing` của mỗi nhân viên
- Phân công cho nhân viên có workload thấp nhất
- Sử dụng **pessimistic locking** để tránh trùng lặp

### Phân công thủ công
- Admin có thể chọn nhân viên cụ thể
- Admin có thể chọn chức vụ, hệ thống tự tìm nhân viên phù hợp

---

## QUẢN LÝ TỒN KHO

### Cập nhật tồn kho

#### Khi nhập kho
- **Thời điểm**: Khi nhân viên hoàn thành phiếu nhập
- **Hành động**: Tăng tồn kho
- **Công thức**: `tồn kho mới = tồn kho cũ + số lượng nhập`
- **Lưu ý**: 
  - Sử dụng transaction để đảm bảo tính nhất quán
  - Lưu lịch sử thay đổi (audit trail)

#### Khi xuất kho
- **Thời điểm**: Khi nhân viên hoàn thành phiếu xuất
- **Hành động**: Giảm tồn kho
- **Công thức**: `tồn kho mới = tồn kho cũ - số lượng xuất`
- **Kiểm tra**:
  - Kiểm tra khi tạo phiếu xuất (Admin)
  - Kiểm tra khi nhân viên start (Staff)
  - Kiểm tra lại khi complete (Staff) - Double check
- **Lưu ý**:
  - Không cho xuất nếu không đủ hàng
  - Sử dụng transaction để đảm bảo tính nhất quán
  - Lưu lịch sử thay đổi (audit trail)

### Kiểm tra tồn kho

#### Khi tạo phiếu xuất (Admin)
- Kiểm tra tồn kho của từng sản phẩm
- Báo lỗi nếu không đủ hàng
- Hiển thị cảnh báo nếu tồn kho thấp

#### Khi nhân viên start xuất (Staff)
- Kiểm tra lại tồn kho (có thể đã thay đổi)
- Không cho start nếu không đủ hàng

#### Khi nhân viên complete xuất (Staff)
- Kiểm tra lại lần cuối (double check)
- Không cho complete nếu không đủ hàng

### Lịch sử thay đổi tồn kho
- Ghi nhận mọi thay đổi tồn kho
- Lưu thông tin: người thực hiện, thời gian, lý do, số lượng thay đổi
- Hỗ trợ truy vết và kiểm toán

---

## TRẠNG THÁI VÀ LUỒNG XỬ LÝ

### Trạng thái Phiếu Nhập/Xuất

#### `pending` (Chờ xử lý)
- **Mô tả**: Phiếu mới tạo, chưa được xử lý
- **Có thể**:
  - Chỉnh sửa (Admin)
  - Xóa (Admin)
  - Phân công nhân viên (Admin)
  - Nhân viên bắt đầu xử lý (Staff)
  - Admin xác nhận trực tiếp (Admin)
- **Không thể**:
  - Hoàn thành (chưa bắt đầu)

#### `processing` (Đang xử lý)
- **Mô tả**: Nhân viên đã bắt đầu xử lý
- **Có thể**:
  - Nhân viên hoàn thành (Staff)
  - Admin xác nhận trực tiếp (Admin)
- **Không thể**:
  - Chỉnh sửa (Admin)
  - Xóa (Admin)
  - Bắt đầu lại (đã bắt đầu rồi)

#### `completed` (Hoàn thành)
- **Mô tả**: Đã xử lý xong, tồn kho đã cập nhật
- **Có thể**:
  - Xem chi tiết
  - In phiếu
  - Xuất báo cáo
- **Không thể**:
  - Chỉnh sửa
  - Xóa
  - Xử lý lại

#### `cancelled` (Đã hủy)
- **Mô tả**: Phiếu đã bị hủy
- **Có thể**:
  - Xem chi tiết (để tham khảo)
- **Không thể**:
  - Chỉnh sửa
  - Xóa
  - Xử lý

### Luồng xử lý chuẩn

```
Tạo phiếu (Admin)
    ↓
pending
    ↓
Phân công nhân viên (Admin)
    ↓
Nhân viên bắt đầu (Staff)
    ↓
processing
    ↓
Nhân viên hoàn thành (Staff)
    ↓
completed
```

### Luồng xử lý nhanh (Admin xác nhận trực tiếp)

```
Tạo phiếu (Admin)
    ↓
pending
    ↓
Admin xác nhận trực tiếp (Admin)
    ↓
completed
```

---

## KIỂM SOÁT VÀ BẢO MẬT

### Phân quyền

#### Admin
- **Toàn quyền**:
  - Tạo, sửa, xóa tất cả dữ liệu
  - Phân công công việc
  - Xác nhận phiếu
  - Xem tất cả báo cáo
  - Quản lý nhân viên

#### Staff
- **Quyền hạn chế**:
  - Chỉ xem công việc được giao
  - Xử lý phiếu nhập/xuất được phân công
  - Cập nhật tồn kho (kiểm kê)
  - Xem tồn kho
- **Không thể**:
  - Tạo, sửa, xóa phiếu
  - Xem phiếu không được phân công
  - Phân công công việc
  - Xem báo cáo toàn hệ thống

### Kiểm soát dữ liệu

#### Validation
- Kiểm tra tồn kho trước khi xuất
- Kiểm tra số lượng hợp lệ (phải > 0)
- Kiểm tra trạng thái trước khi xử lý
- Kiểm tra quyền truy cập

#### Transaction
- Sử dụng database transaction cho các thao tác quan trọng
- Đảm bảo tính nhất quán dữ liệu
- Rollback nếu có lỗi

#### Locking
- Sử dụng pessimistic locking khi phân công nhân viên
- Tránh trùng lặp phân công
- Đảm bảo tính nhất quán

### Audit Trail
- Ghi nhận mọi thay đổi quan trọng
- Lưu thông tin: người thực hiện, thời gian, hành động
- Hỗ trợ truy vết và kiểm toán

---

## TÍNH NĂNG ĐẶC BIỆT

### 1. Tự động hóa
- ✅ Tự động tạo phiếu xuất từ yêu cầu vật tư
- ✅ Tự động phân công nhân viên theo chức vụ
- ✅ Tự động cập nhật tồn kho khi hoàn thành
- ✅ Tự động kiểm tra tồn kho trước khi xuất
- ✅ Tự động tính workload để phân công công bằng
- ✅ Tự động cảnh báo khi tồn kho thấp
- ✅ Tự động tính toán giá trị tồn kho

### 2. Kiểm soát
- ✅ Kiểm tra tồn kho trước khi xuất (3 lần: tạo, start, complete)
- ✅ Không cho xuất nếu không đủ hàng
- ✅ Kiểm tra trạng thái trước khi xử lý
- ✅ Kiểm tra quyền truy cập
- ✅ Theo dõi lịch sử thay đổi tồn kho
- ✅ Audit trail cho mọi thao tác

### 3. Báo cáo & Phân tích
- ✅ Dashboard tổng quan
- ✅ Báo cáo theo thời gian thực
- ✅ Phân tích xu hướng nhập/xuất
- ✅ Đánh giá hiệu suất nhân viên
- ✅ Xuất Excel/PDF

---

## LƯU Ý QUAN TRỌNG

### 1. Không tạo Task khi tạo phiếu nhập/xuất
- **Lý do**: Phiếu nhập/xuất đã là công việc rồi, không cần tạo task riêng
- **Task chỉ dùng cho**: Công việc độc lập (kiểm kê, báo cáo, v.v.)

### 2. Phân công tự động
- Hệ thống tự động phân công dựa trên chức vụ và workload
- Admin có thể override bằng cách chọn nhân viên cụ thể

### 3. Kiểm tra tồn kho
- Kiểm tra 3 lần khi xuất: tạo phiếu, start, complete
- Đảm bảo không xuất quá tồn kho

### 4. Transaction và Locking
- Sử dụng transaction cho các thao tác quan trọng
- Sử dụng pessimistic locking để tránh race condition

### 5. Audit Trail
- Mọi thay đổi quan trọng đều được ghi nhận
- Hỗ trợ truy vết và kiểm toán

---

## KIỂM TOÁN KHO (INVENTORY AUDIT)

### 1. Quản lý Kiểm toán (Admin)

#### 1.1. Tạo phiếu kiểm toán
- **Thông tin phiếu**:
  - Mã phiếu (tự động: KT000001, KT000002...)
  - Kho kiểm toán (chọn kho)
  - Ngày kiểm toán
  - Loại kiểm toán:
    - `full`: Kiểm toán toàn bộ kho
    - `partial`: Kiểm toán một phần (theo danh mục)
    - `spot`: Kiểm toán đột xuất (một số sản phẩm)
- **Phân công nhân viên**:
  - Tự động phân công: `inventory_staff` (ưu tiên)
  - Hoặc chọn nhân viên cụ thể
- **Ghi chú**: Mục đích, lý do kiểm toán

#### 1.2. Xem danh sách phiếu kiểm toán
- Lọc theo trạng thái, kho, người được giao
- Tìm kiếm theo mã phiếu
- Xem thống kê: tổng số sản phẩm, số khớp, số chênh lệch

#### 1.3. Xác nhận và điều chỉnh
- Xem kết quả kiểm toán từ nhân viên
- Xác nhận phiếu kiểm toán
- Tự động điều chỉnh tồn kho theo số lượng thực tế
- Lưu lý do chênh lệch

### 2. Xử lý Kiểm toán (Staff)

#### 2.1. Xem danh sách phiếu được giao
- Chỉ xem phiếu được phân công cho mình
- Lọc theo trạng thái

#### 2.2. Xử lý kiểm toán
- **Bắt đầu** (`start`):
  - Chuyển trạng thái: `pending` → `in_progress`
  - Xem danh sách sản phẩm cần kiểm toán
- **Nhập số lượng thực tế**:
  - Đối chiếu với số lượng hệ thống
  - Nhập số lượng thực tế đếm được
  - Hệ thống tự động tính chênh lệch
  - Ghi chú lý do chênh lệch (nếu có)
- **Hoàn thành** (`complete`):
  - Xác nhận đã kiểm toán xong
  - Chuyển trạng thái: `in_progress` → `completed`
  - Hệ thống tự động tính thống kê:
    - Tổng số sản phẩm
    - Số sản phẩm khớp (chênh lệch = 0)
    - Số sản phẩm chênh lệch

### 3. Quy trình Kiểm toán

```
1. Admin tạo phiếu kiểm toán
   └─> Trạng thái: pending
   └─> Phân công: inventory_staff

2. Nhân viên bắt đầu kiểm toán
   └─> Trạng thái: in_progress
   └─> Xem danh sách sản phẩm

3. Nhân viên nhập số lượng thực tế
   └─> Đối chiếu với hệ thống
   └─> Ghi chú chênh lệch

4. Nhân viên hoàn thành
   └─> Trạng thái: completed
   └─> Tính thống kê

5. Admin xác nhận và điều chỉnh
   └─> Xem kết quả
   └─> Điều chỉnh tồn kho (nếu cần)
```

### 4. Điều chỉnh Tồn kho

- **Tự động điều chỉnh**: Khi admin xác nhận phiếu kiểm toán
- **Cập nhật tồn kho**: Số lượng thực tế thay thế số lượng hệ thống
- **Lưu lịch sử**: Ghi nhận mọi điều chỉnh với lý do

---

## QUẢN LÝ KHO (WAREHOUSE MANAGEMENT)

### 1. Quản lý Danh sách Kho (Admin)

#### 1.1. Tạo kho mới
- **Thông tin kho**:
  - Mã kho (duy nhất)
  - Tên kho
  - Địa chỉ
  - Người quản lý
  - Số điện thoại
  - Mô tả
- **Trạng thái**: Kích hoạt/Vô hiệu hóa

#### 1.2. Chỉnh sửa kho
- Cập nhật thông tin kho
- Không thể xóa nếu còn tồn kho

#### 1.3. Xem chi tiết kho
- Thông tin kho
- Danh sách sản phẩm trong kho
- Tổng số sản phẩm
- Giá trị tồn kho
- Lịch sử kiểm toán

### 2. Tồn kho theo Kho

- Mỗi sản phẩm có thể có tồn kho ở nhiều kho
- Xem tồn kho theo từng kho
- Báo cáo tồn kho theo kho

### 3. Điều chuyển giữa các Kho

- Tạo phiếu xuất từ kho A
- Tạo phiếu nhập vào kho B
- Cập nhật tồn kho cả 2 kho

---

## KẾT LUẬN

Hệ thống quản lý logistics được thiết kế để:
- ✅ Tự động hóa quy trình
- ✅ Kiểm soát chặt chẽ
- ✅ Phân công công bằng
- ✅ Báo cáo nhanh chóng
- ✅ Giảm sai sót
- ✅ Tăng hiệu suất
- ✅ **Kiểm toán kho định kỳ**
- ✅ **Quản lý nhiều kho**

Tất cả nghiệp vụ đã được đồng bộ và đúng với thực tế logistics.


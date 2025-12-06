# HƯỚNG DẪN SỬ DỤNG HỆ THỐNG THEO TỪNG NHÂN VIÊN

## 🔐 ĐĂNG NHẬP

**URL:** `http://localhost/logistic/login`

---

## 👨‍💼 ADMIN - Quản trị viên

### Thông tin đăng nhập:
```
Email:    admin@example.com
Password: password
```

### Sau khi đăng nhập, Admin có thể:

#### 1. **Quản lý Kho (Warehouse)**
- Xem danh sách kho
- Tạo kho mới
- Chỉnh sửa thông tin kho
- Xem chi tiết kho (tổng sản phẩm, giá trị tồn kho)

#### 2. **Tạo và Quản lý Phiếu Nhập**
- Tạo phiếu nhập mới
- Phân công cho nhân viên nhập kho (tự động hoặc thủ công)
- Xem tất cả phiếu nhập
- Xác nhận phiếu nhập đã hoàn thành
- Xuất PDF phiếu nhập

#### 3. **Tạo và Quản lý Phiếu Xuất**
- Tạo phiếu xuất mới
- Phân công cho nhân viên xuất kho (tự động hoặc thủ công)
- Xem tất cả phiếu xuất
- Xác nhận phiếu xuất đã hoàn thành
- Tạo phiếu xuất từ Yêu cầu Vật tư

#### 4. **Quản lý Yêu cầu Vật tư**
- Xem tất cả yêu cầu vật tư
- Duyệt yêu cầu (tự động tạo phiếu xuất)
- Từ chối yêu cầu (có lý do)
- Tạo yêu cầu vật tư cho nhân viên khác

#### 5. **Quản lý Kiểm toán Kho**
- Tạo phiếu kiểm toán mới
- Phân công cho nhân viên kiểm kê
- Xem kết quả kiểm toán
- Xác nhận và điều chỉnh tồn kho

#### 6. **Phân công Công việc**
- Tạo công việc mới
- Phân công cho nhân viên theo chức vụ
- Xem tất cả công việc
- Theo dõi tiến độ

#### 7. **Quản lý Nhân viên**
- Xem danh sách nhân viên
- Tạo tài khoản nhân viên mới
- Chỉnh sửa thông tin nhân viên
- Gán chức vụ cho nhân viên

#### 8. **Xem Báo cáo**
- Báo cáo tồn kho
- Báo cáo nhập/xuất
- Báo cáo công việc

---

## 👷 NHÂN VIÊN NHẬP KHO (Import Staff)

### Thông tin đăng nhập:
```
Email:    staff1@example.com
Password: password
Tên:      Nguyễn Văn An
Chức vụ:  Nhân viên nhập kho
```

### Sau khi đăng nhập, Nhân viên Nhập kho có thể:

#### 1. **Xem Phiếu Nhập của tôi**
- Xem danh sách phiếu nhập được phân công
- Xem phiếu nhập tự tạo
- Lọc theo trạng thái: Chờ xử lý, Đang xử lý, Hoàn thành
- Tìm kiếm theo mã phiếu

#### 2. **Tạo Phiếu Nhập mới**
- Click nút **"Tạo phiếu nhập"** ở trang danh sách
- Điền thông tin:
  - Nguồn nhập: Từ nhà cung cấp / Điều chuyển / Trả hàng / Khác
  - Nhà cung cấp (nếu nguồn là từ nhà cung cấp)
  - Ngày nhập
  - Ghi chú
- Thêm sản phẩm:
  - Chọn sản phẩm
  - Nhập số lượng
  - Nhập đơn giá
  - Ghi chú (nếu có)
- Click **"Lưu phiếu nhập"**
- ✅ Phiếu được tạo và tự động gán cho chính nhân viên đó

#### 3. **Xử lý Phiếu Nhập**
- Vào **"Phiếu Nhập của tôi"** → Chọn một phiếu
- Click **"Xem"** để xem chi tiết
- Nếu phiếu ở trạng thái **"Chờ xử lý"**:
  - Click **"Bắt đầu xử lý"**
  - Trạng thái chuyển sang **"Đang xử lý"**
  - Nhập ghi chú (nếu có)
- Sau khi nhận hàng và kiểm tra xong:
  - Click **"Hoàn thành"**
  - ✅ Trạng thái chuyển sang **"Hoàn thành"**
  - ✅ Tồn kho tự động được cập nhật (tăng lên)

#### 4. **Xem Tồn kho**
- Vào menu **"Tồn kho"**
- Xem danh sách sản phẩm và số lượng tồn kho
- Tìm kiếm sản phẩm

#### 5. **Xem Công việc của tôi**
- Xem các công việc được phân công
- Bắt đầu công việc
- Hoàn thành công việc

#### 6. **Tạo Yêu cầu Vật tư**
- Vào menu **"Yêu cầu Vật tư của tôi"**
- Click **"Tạo yêu cầu"**
- Điền thông tin và gửi yêu cầu
- Chờ Admin duyệt

---

## 👷 NHÂN VIÊN XUẤT KHO (Export Staff)

### Thông tin đăng nhập:
```
Email:    staff2@example.com
Password: password
Tên:      Trần Thị Bình
Chức vụ:  Nhân viên xuất kho
```

### Sau khi đăng nhập, Nhân viên Xuất kho có thể:

#### 1. **Xem Phiếu Xuất của tôi**
- Xem danh sách phiếu xuất được phân công
- Xem phiếu xuất tự tạo
- Lọc theo trạng thái
- Tìm kiếm theo mã phiếu

#### 2. **Tạo Phiếu Xuất mới**
- Click nút **"Tạo phiếu xuất"** ở trang danh sách
- Điền thông tin:
  - Ngày xuất
  - Người nhận (tùy chọn)
  - Lý do xuất: Sử dụng nội bộ / Bán hàng / Điều chuyển / Trả nhà cung cấp / Trả hàng / Khác
  - Chi tiết lý do
  - Phòng ban / Dự án (tùy chọn)
  - Ghi chú
- Thêm sản phẩm:
  - Chọn sản phẩm (hệ thống hiển thị tồn kho)
  - Nhập số lượng (kiểm tra tồn kho có đủ không)
  - Ghi chú (nếu có)
- Click **"Lưu phiếu xuất"**
- ✅ Phiếu được tạo và tự động gán cho chính nhân viên đó

#### 3. **Xử lý Phiếu Xuất**
- Vào **"Phiếu Xuất của tôi"** → Chọn một phiếu
- Click **"Xem"** để xem chi tiết
- Nếu phiếu ở trạng thái **"Chờ xử lý"**:
  - Click **"Bắt đầu xử lý"**
  - Hệ thống kiểm tra tồn kho có đủ không
  - Nếu đủ: Trạng thái chuyển sang **"Đang xử lý"**
  - Nếu không đủ: Hiển thị lỗi, không cho phép xử lý
- Sau khi soạn hàng xong:
  - Click **"Hoàn thành"**
  - ✅ Trạng thái chuyển sang **"Hoàn thành"**
  - ✅ Tồn kho tự động được cập nhật (giảm đi)

#### 4. **Xem Tồn kho**
- Vào menu **"Tồn kho"**
- Xem danh sách sản phẩm và số lượng tồn kho
- Kiểm tra tồn kho trước khi tạo phiếu xuất

#### 5. **Xem Công việc của tôi**
- Xem các công việc được phân công
- Bắt đầu công việc
- Hoàn thành công việc

#### 6. **Tạo Yêu cầu Vật tư**
- Vào menu **"Yêu cầu Vật tư của tôi"**
- Click **"Tạo yêu cầu"**
- Điền thông tin và gửi yêu cầu
- Chờ Admin duyệt

---

## 👷 NHÂN VIÊN KIỂM KÊ (Inventory Staff)

### Thông tin đăng nhập:
```
Email:    staff3@example.com
Password: password
Tên:      Lê Văn Cường
Chức vụ:  Nhân viên kiểm kê
```

### Sau khi đăng nhập, Nhân viên Kiểm kê có thể:

#### 1. **Xem Phiếu Kiểm toán của tôi**
- Xem danh sách phiếu kiểm toán được phân công
- Lọc theo trạng thái: Chờ xử lý, Đang kiểm toán, Hoàn thành
- Tìm kiếm theo mã phiếu

#### 2. **Xử lý Phiếu Kiểm toán**
- Vào **"Phiếu Kiểm toán của tôi"** → Chọn một phiếu
- Click **"Xem"** để xem chi tiết
- Nếu phiếu ở trạng thái **"Chờ xử lý"**:
  - Click **"Bắt đầu kiểm toán"**
  - Trạng thái chuyển sang **"Đang kiểm toán"**
- Nhập số lượng thực tế:
  - Với mỗi sản phẩm, nhập số lượng thực tế đếm được
  - Hệ thống tự động tính chênh lệch:
    - Màu xanh = Khớp (số lượng thực tế = số lượng hệ thống)
    - Màu đỏ = Chênh lệch (số lượng thực tế ≠ số lượng hệ thống)
  - Nhập ghi chú nếu có chênh lệch
- Click **"Lưu số lượng thực tế"** để lưu
- Sau khi hoàn thành tất cả:
  - Click **"Hoàn thành kiểm toán"**
  - ✅ Trạng thái chuyển sang **"Hoàn thành"**
  - ✅ Hệ thống tính thống kê (tổng số, khớp, chênh lệch)

#### 3. **Xem Tồn kho**
- Vào menu **"Tồn kho"**
- Xem danh sách sản phẩm và số lượng tồn kho
- So sánh với số lượng thực tế khi kiểm toán

#### 4. **Xem Công việc của tôi**
- Xem các công việc được phân công
- Bắt đầu công việc
- Hoàn thành công việc

#### 5. **Tạo Yêu cầu Vật tư**
- Vào menu **"Yêu cầu Vật tư của tôi"**
- Click **"Tạo yêu cầu"**
- Điền thông tin và gửi yêu cầu
- Chờ Admin duyệt

---

## 👷 NHÂN VIÊN KHO (Warehouse Staff)

### Thông tin đăng nhập:
```
Email:    staff4@example.com
Password: password
Tên:      Phạm Thị Dung
Chức vụ:  Nhân viên kho
```

### Sau khi đăng nhập, Nhân viên Kho có thể:

#### 1. **Xem Công việc của tôi**
- Xem các công việc được phân công
- Bắt đầu công việc
- Hoàn thành công việc

#### 2. **Xem Tồn kho**
- Vào menu **"Tồn kho"**
- Xem danh sách sản phẩm và số lượng tồn kho
- Tìm kiếm sản phẩm

#### 3. **Tạo Yêu cầu Vật tư**
- Vào menu **"Yêu cầu Vật tư của tôi"**
- Click **"Tạo yêu cầu"**
- Điền thông tin và gửi yêu cầu
- Chờ Admin duyệt

#### 4. **Hỗ trợ các nhiệm vụ khác trong kho**
- Có thể được Admin phân công các công việc khác
- Xử lý các công việc được giao

---

## 👷 NHÂN VIÊN TỔNG HỢP (General Staff)

### Thông tin đăng nhập:
```
Email:    staff5@example.com
Password: password
Tên:      Hoàng Văn Em
Chức vụ:  Nhân viên tổng hợp
```

### Sau khi đăng nhập, Nhân viên Tổng hợp có thể:

#### 1. **Xem Công việc của tôi**
- Xem các công việc được phân công
- Bắt đầu công việc
- Hoàn thành công việc

#### 2. **Xem Tồn kho**
- Vào menu **"Tồn kho"**
- Xem danh sách sản phẩm và số lượng tồn kho
- Tìm kiếm sản phẩm

#### 3. **Tạo Yêu cầu Vật tư**
- Vào menu **"Yêu cầu Vật tư của tôi"**
- Click **"Tạo yêu cầu"**
- Điền thông tin và gửi yêu cầu
- Chờ Admin duyệt

#### 4. **Hỗ trợ các công việc tổng hợp**
- Có thể được Admin phân công các công việc khác
- Xử lý các công việc được giao

---

## 📋 QUY TRÌNH NGHIỆP VỤ THEO TỪNG NHÂN VIÊN

### Quy trình 1: Nhân viên Nhập kho tự tạo và xử lý phiếu nhập

1. **Login:** `staff1@example.com` / `password`
2. **Tạo phiếu nhập:**
   - Vào **"Phiếu Nhập của tôi"** → Click **"Tạo phiếu nhập"**
   - Chọn nguồn nhập, nhà cung cấp, ngày nhập
   - Thêm sản phẩm, số lượng, đơn giá
   - Click **"Lưu phiếu nhập"**
   - ✅ Phiếu được tạo và tự động gán cho chính nhân viên đó
3. **Xử lý phiếu nhập:**
   - Vào **"Phiếu Nhập của tôi"** → Chọn phiếu vừa tạo
   - Click **"Bắt đầu xử lý"**
   - Sau khi nhận hàng, click **"Hoàn thành"**
   - ✅ Tồn kho tự động tăng

### Quy trình 2: Nhân viên Xuất kho tự tạo và xử lý phiếu xuất

1. **Login:** `staff2@example.com` / `password`
2. **Tạo phiếu xuất:**
   - Vào **"Phiếu Xuất của tôi"** → Click **"Tạo phiếu xuất"**
   - Chọn ngày xuất, lý do xuất, người nhận
   - Thêm sản phẩm, số lượng (kiểm tra tồn kho)
   - Click **"Lưu phiếu xuất"**
   - ✅ Phiếu được tạo và tự động gán cho chính nhân viên đó
3. **Xử lý phiếu xuất:**
   - Vào **"Phiếu Xuất của tôi"** → Chọn phiếu vừa tạo
   - Click **"Bắt đầu xử lý"** (hệ thống kiểm tra tồn kho)
   - Sau khi soạn hàng, click **"Hoàn thành"**
   - ✅ Tồn kho tự động giảm

### Quy trình 3: Nhân viên Kiểm kê thực hiện kiểm toán

1. **Login:** `staff3@example.com` / `password`
2. **Xem phiếu kiểm toán:**
   - Vào **"Phiếu Kiểm toán của tôi"**
   - Chọn phiếu có trạng thái **"Chờ xử lý"**
3. **Thực hiện kiểm toán:**
   - Click **"Bắt đầu kiểm toán"**
   - Đếm số lượng thực tế và nhập vào hệ thống
   - Hệ thống tự động tính chênh lệch
   - Click **"Lưu số lượng thực tế"**
   - Click **"Hoàn thành kiểm toán"**
4. **Admin xác nhận:**
   - Admin login và xác nhận phiếu kiểm toán
   - Nếu có chênh lệch, Admin điều chỉnh tồn kho

### Quy trình 4: Nhân viên tạo Yêu cầu Vật tư

1. **Login:** Bất kỳ nhân viên nào (ví dụ: `staff1@example.com`)
2. **Tạo yêu cầu:**
   - Vào **"Yêu cầu Vật tư của tôi"** → Click **"Tạo yêu cầu"**
   - Điền phòng ban, dự án (nếu có)
   - Thêm sản phẩm, số lượng cần
   - Click **"Tạo yêu cầu"**
   - ✅ Yêu cầu được tạo với trạng thái **"Chờ duyệt"**
3. **Admin duyệt:**
   - Admin login và duyệt yêu cầu
   - Hệ thống tự động tạo phiếu xuất
   - Nhân viên xuất kho xử lý phiếu xuất

---

## ⚠️ LƯU Ý QUAN TRỌNG

1. **Mật khẩu mặc định:** Tất cả tài khoản mẫu đều có mật khẩu là `password`
2. **Tự động gán:** Khi nhân viên tự tạo phiếu nhập/xuất, phiếu tự động được gán cho chính nhân viên đó
3. **Tồn kho tự động:** Khi hoàn thành phiếu nhập/xuất, tồn kho tự động được cập nhật
4. **Kiểm tra tồn kho:** Khi tạo phiếu xuất, hệ thống kiểm tra tồn kho có đủ không
5. **Kiểm toán:** Chỉ có thể điều chỉnh tồn kho sau khi Admin xác nhận phiếu kiểm toán
6. **Yêu cầu Vật tư:** Chỉ Admin mới có thể duyệt và tạo phiếu xuất từ yêu cầu

---

## 🔄 MENU THEO VAI TRÒ

### Admin thấy:
- Quản lý Hàng hóa
- Quản lý Nhà cung cấp
- Phiếu Nhập
- Phiếu Xuất
- Yêu cầu Vật tư
- Quản lý Kho
- Kiểm toán Kho
- Phân công Công việc
- Nhân viên
- Báo cáo

### Staff thấy:
- Phiếu Nhập của tôi (có thể tạo mới)
- Phiếu Xuất của tôi (có thể tạo mới)
- Phiếu Kiểm toán của tôi (chỉ nhân viên kiểm kê)
- Yêu cầu Vật tư của tôi (có thể tạo mới)
- Công việc của tôi
- Tồn kho

---

## 🚀 CHẠY SEEDER

Để tạo lại dữ liệu mẫu:

```bash
php artisan db:seed
```

Hoặc reset toàn bộ database:

```bash
php artisan migrate:fresh --seed
```



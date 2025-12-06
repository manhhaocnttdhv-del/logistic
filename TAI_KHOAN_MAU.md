# TÀI KHOẢN MẪU VÀ HƯỚNG DẪN TEST NGHIỆP VỤ

## 🔐 TÀI KHOẢN MẪU

Sau khi chạy `php artisan db:seed`, các tài khoản sau sẽ được tạo:

### 👨‍💼 ADMIN (Quản trị viên)
```
Email: admin@example.com
Password: password
Chức năng: Quản lý toàn bộ hệ thống
```

### 👷 NHÂN VIÊN (Staff)

#### 1. Nhân viên Nhập kho (Import Staff)
```
Email: staff1@example.com
Password: password
Tên: Nguyễn Văn An
Chức vụ: Nhân viên nhập kho
Nhiệm vụ: Xử lý phiếu nhập, nhận hàng từ nhà cung cấp
```

#### 2. Nhân viên Xuất kho (Export Staff)
```
Email: staff2@example.com
Password: password
Tên: Trần Thị Bình
Chức vụ: Nhân viên xuất kho
Nhiệm vụ: Xử lý phiếu xuất, giao hàng
```

#### 3. Nhân viên Kiểm kê (Inventory Staff)
```
Email: staff3@example.com
Password: password
Tên: Lê Văn Cường
Chức vụ: Nhân viên kiểm kê
Nhiệm vụ: Kiểm toán kho, đếm số lượng thực tế
```

#### 4. Nhân viên Kho (Warehouse Staff)
```
Email: staff4@example.com
Password: password
Tên: Phạm Thị Dung
Chức vụ: Nhân viên kho
Nhiệm vụ: Các nhiệm vụ khác trong kho
```

#### 5. Nhân viên Tổng hợp (General Staff)
```
Email: staff5@example.com
Password: password
Tên: Hoàng Văn Em
Chức vụ: Nhân viên tổng hợp
Nhiệm vụ: Hỗ trợ các công việc tổng hợp
```

---

## 📋 HƯỚNG DẪN TEST NGHIỆP VỤ

### 1. ĐĂNG NHẬP ADMIN

**URL:** `http://localhost/logistic/login`

**Tài khoản:**
- Email: `admin@example.com`
- Password: `password`

**Chức năng Admin có thể làm:**

#### A. Quản lý Kho (Warehouse)
1. Vào menu **Quản lý Kho** → **Danh sách Kho**
2. Click **Thêm mới** để tạo kho mới
3. Điền thông tin:
   - Mã kho: `KHO001`
   - Tên kho: `Kho Chính`
   - Địa chỉ: `123 Đường ABC, Quận 1, TP.HCM`
   - Người quản lý: `Nguyễn Văn Quản lý`
   - Số điện thoại: `0901234567`
4. Click **Lưu**

#### B. Tạo Phiếu Nhập (Import Order)
1. Vào menu **Phiếu Nhập** → **Tạo phiếu nhập**
2. Chọn:
   - Nguồn nhập: `Từ nhà cung cấp`
   - Nhà cung cấp: Chọn một nhà cung cấp
   - Ngày nhập: Hôm nay
   - **Phân công theo chức vụ:** `Nhân viên nhập kho` (hoặc để trống để tự động)
3. Thêm sản phẩm:
   - Click **Thêm sản phẩm**
   - Chọn sản phẩm, nhập số lượng, đơn giá
4. Click **Tạo phiếu**
5. **Kết quả:** Hệ thống tự động phân công cho nhân viên nhập kho (staff1@example.com)

#### C. Tạo Phiếu Xuất (Export Order)

**Cách 1: Tạo trực tiếp**
1. Vào menu **Phiếu Xuất** → **Tạo phiếu xuất**
2. Chọn:
   - Ngày xuất: Hôm nay
   - **Phân công theo chức vụ:** `Nhân viên xuất kho` (hoặc để trống để tự động)
3. Thêm sản phẩm cần xuất
4. Click **Tạo phiếu**
5. **Kết quả:** Hệ thống tự động phân công cho nhân viên xuất kho (staff2@example.com)

**Cách 2: Tạo từ Yêu cầu Vật tư (Material Request)**
1. Vào menu **Yêu cầu Vật tư** → **Danh sách yêu cầu**
2. Tìm yêu cầu có trạng thái **Chờ duyệt**
3. Click **Xem** để xem chi tiết
4. Kiểm tra tồn kho có đủ không
5. Click **Duyệt và tạo phiếu xuất**
6. **Kết quả:** 
   - Hệ thống tự động tạo phiếu xuất từ yêu cầu
   - Tự động phân công cho nhân viên xuất kho
   - Cập nhật trạng thái yêu cầu thành **Đã chuyển đổi**
   - Phiếu xuất có ghi chú "Xuất từ yêu cầu vật tư YC..."

#### D. Tạo Phiếu Kiểm toán (Inventory Audit)
1. Vào menu **Kiểm toán Kho** → **Tạo phiếu mới**
2. Chọn:
   - Kho kiểm toán: Chọn kho (ví dụ: Kho Chính)
   - Ngày kiểm toán: Hôm nay
   - Loại kiểm toán:
     - **Toàn bộ:** Kiểm toán tất cả sản phẩm trong kho
     - **Một phần:** Chọn một số sản phẩm cụ thể
     - **Đột xuất:** Kiểm toán đột xuất một số sản phẩm
   - **Phân công theo chức vụ:** `Nhân viên kiểm kê` (hoặc để trống để tự động)
3. Nếu chọn "Một phần" hoặc "Đột xuất", chọn các sản phẩm cần kiểm toán
4. Click **Tạo phiếu**
5. **Kết quả:** Hệ thống tự động phân công cho nhân viên kiểm kê (staff3@example.com)

#### E. Phân công Công việc (Task)
1. Vào menu **Phân công Công việc** → **Phân công mới**
2. Chọn:
   - **Phân công theo chức vụ:** Chọn chức vụ phù hợp
   - Loại công việc: `Nhập kho`, `Xuất kho`, `Kiểm kê`, `Soạn hàng`, `Báo cáo tồn`
   - Tiêu đề: Mô tả công việc
   - Độ ưu tiên: `Bình thường`, `Cao`, `Khẩn cấp`
3. Click **Phân công**
4. **Kết quả:** Hệ thống tự động chọn nhân viên phù hợp dựa trên chức vụ và khối lượng công việc

---

### 2. ĐĂNG NHẬP NHÂN VIÊN NHẬP KHO

**Tài khoản:**
- Email: `staff1@example.com`
- Password: `password`

**Chức năng nhân viên nhập kho có thể làm:**

#### A. Xem Phiếu Nhập được giao
1. Vào menu **Phiếu Nhập của tôi**
2. Xem danh sách các phiếu nhập được phân công
3. Click vào một phiếu để xem chi tiết

#### B. Xử lý Phiếu Nhập
1. Vào **Phiếu Nhập của tôi** → Chọn một phiếu có trạng thái **Chờ xử lý**
2. Click **Xem** để xem chi tiết
3. Click **Bắt đầu xử lý** để chuyển trạng thái sang **Đang xử lý**
4. Sau khi nhận hàng và kiểm tra xong:
   - Nhập ghi chú (nếu có)
   - Click **Hoàn thành**
5. **Kết quả:** 
   - Trạng thái chuyển sang **Hoàn thành**
   - Tồn kho tự động được cập nhật

#### C. Xem Công việc được giao
1. Vào menu **Công việc của tôi**
2. Xem danh sách các công việc được phân công
3. Có thể:
   - **Bắt đầu** công việc
   - **Hoàn thành** công việc

---

### 3. ĐĂNG NHẬP NHÂN VIÊN XUẤT KHO

**Tài khoản:**
- Email: `staff2@example.com`
- Password: `password`

**Chức năng nhân viên xuất kho có thể làm:**

#### A. Xem Phiếu Xuất được giao
1. Vào menu **Phiếu Xuất của tôi**
2. Xem danh sách các phiếu xuất được phân công

#### B. Xử lý Phiếu Xuất
1. Vào **Phiếu Xuất của tôi** → Chọn một phiếu
2. Click **Bắt đầu xử lý** để bắt đầu soạn hàng
3. Sau khi soạn hàng xong:
   - Nhập ghi chú (nếu có)
   - Click **Hoàn thành**
4. **Kết quả:** Tồn kho tự động được trừ đi

---

### 4. ĐĂNG NHẬP NHÂN VIÊN KIỂM KÊ

**Tài khoản:**
- Email: `staff3@example.com`
- Password: `password`

**Chức năng nhân viên kiểm kê có thể làm:**

#### A. Xem Phiếu Kiểm toán được giao
1. Vào menu **Phiếu Kiểm toán của tôi**
2. Xem danh sách các phiếu kiểm toán được phân công

#### B. Xử lý Phiếu Kiểm toán
1. Vào **Phiếu Kiểm toán của tôi** → Chọn một phiếu có trạng thái **Chờ xử lý**
2. Click **Xem** để xem chi tiết
3. Click **Bắt đầu kiểm toán** để chuyển trạng thái sang **Đang kiểm toán**
4. Nhập số lượng thực tế:
   - Với mỗi sản phẩm, nhập số lượng thực tế đếm được
   - Hệ thống tự động tính chênh lệch (màu xanh = khớp, màu đỏ = chênh lệch)
   - Nhập ghi chú nếu có chênh lệch
5. Click **Lưu số lượng thực tế** để lưu
6. Sau khi hoàn thành tất cả:
   - Click **Hoàn thành kiểm toán**
7. **Kết quả:** 
   - Trạng thái chuyển sang **Hoàn thành**
   - Hệ thống tính thống kê (tổng số, khớp, chênh lệch)

#### C. Quay lại Admin để xác nhận
1. Admin vào **Kiểm toán Kho** → Xem phiếu vừa hoàn thành
2. Xem thống kê và chi tiết chênh lệch
3. Click **Xác nhận** để xác nhận phiếu kiểm toán
4. Nếu có chênh lệch, click **Điều chỉnh tồn kho** để cập nhật số lượng thực tế vào hệ thống

---

## 🔄 QUY TRÌNH NGHIỆP VỤ HOÀN CHỈNH

### Quy trình 1: Nhập hàng từ nhà cung cấp

1. **Admin tạo phiếu nhập:**
   - Vào **Phiếu Nhập** → **Tạo phiếu nhập**
   - Chọn nhà cung cấp, sản phẩm, số lượng
   - Phân công cho **Nhân viên nhập kho** (hoặc để tự động)
   - Click **Tạo phiếu**

2. **Nhân viên nhập kho xử lý:**
   - Login với `staff1@example.com`
   - Vào **Phiếu Nhập của tôi**
   - Click **Bắt đầu xử lý**
   - Sau khi nhận hàng, click **Hoàn thành**
   - **Kết quả:** Tồn kho tự động tăng

3. **Admin xác nhận (nếu cần):**
   - Vào **Phiếu Nhập** → Xem phiếu đã hoàn thành
   - Có thể xuất PDF

---

### Quy trình 2: Xuất hàng

**Cách 1: Admin tạo phiếu xuất trực tiếp**

1. **Admin tạo phiếu xuất:**
   - Vào **Phiếu Xuất** → **Tạo phiếu xuất**
   - Chọn sản phẩm, số lượng cần xuất
   - Phân công cho **Nhân viên xuất kho** (hoặc để tự động)
   - Click **Tạo phiếu**

2. **Nhân viên xuất kho xử lý:**
   - Login với `staff2@example.com`
   - Vào **Phiếu Xuất của tôi**
   - Click **Bắt đầu xử lý** để soạn hàng
   - Sau khi soạn xong, click **Hoàn thành**
   - **Kết quả:** Tồn kho tự động giảm

**Cách 2: Tạo phiếu xuất từ Yêu cầu Vật tư**

1. **Nhân viên tạo yêu cầu vật tư (tùy chọn):**
   - Login với tài khoản staff bất kỳ
   - Vào **Yêu cầu Vật tư** → **Tạo yêu cầu mới**
   - Chọn sản phẩm, số lượng cần
   - Gửi yêu cầu (trạng thái: **Chờ duyệt**)

2. **Admin duyệt và tạo phiếu xuất:**
   - Login với `admin@example.com`
   - Vào **Yêu cầu Vật tư** → Xem yêu cầu **Chờ duyệt**
   - Kiểm tra tồn kho có đủ không
   - Click **Duyệt và tạo phiếu xuất**
   - **Kết quả:** 
     - Hệ thống tự động tạo phiếu xuất
     - Tự động phân công cho nhân viên xuất kho
     - Yêu cầu chuyển sang trạng thái **Đã chuyển đổi**

3. **Nhân viên xuất kho xử lý:**
   - Login với `staff2@example.com`
   - Vào **Phiếu Xuất của tôi**
   - Xem phiếu xuất (có ghi chú "Xuất từ yêu cầu vật tư YC...")
   - Click **Bắt đầu xử lý** → **Hoàn thành**
   - **Kết quả:** Tồn kho tự động giảm

---

### Quy trình 3: Kiểm toán kho

1. **Admin tạo phiếu kiểm toán:**
   - Vào **Kiểm toán Kho** → **Tạo phiếu mới**
   - Chọn kho, loại kiểm toán (toàn bộ/một phần/đột xuất)
   - Phân công cho **Nhân viên kiểm kê** (hoặc để tự động)
   - Click **Tạo phiếu**

2. **Nhân viên kiểm kê thực hiện:**
   - Login với `staff3@example.com`
   - Vào **Phiếu Kiểm toán của tôi**
   - Click **Bắt đầu kiểm toán**
   - Đếm số lượng thực tế và nhập vào hệ thống
   - Hệ thống tự động tính chênh lệch
   - Click **Hoàn thành kiểm toán**

3. **Admin xác nhận và điều chỉnh:**
   - Login với `admin@example.com`
   - Vào **Kiểm toán Kho** → Xem phiếu đã hoàn thành
   - Xem thống kê và chi tiết chênh lệch
   - Click **Xác nhận** để xác nhận phiếu
   - Nếu có chênh lệch, click **Điều chỉnh tồn kho** để cập nhật số lượng thực tế

---

## 📊 MENU THEO VAI TRÒ

### Admin thấy:
- Quản lý Hàng hóa
- Quản lý Nhà cung cấp
- Phiếu Nhập
- Phiếu Xuất
- Yêu cầu Vật tư
- Quản lý Kho ⭐ (MỚI)
- Kiểm toán Kho ⭐ (MỚI)
- Phân công Công việc
- Nhân viên
- Báo cáo

### Staff thấy:
- Phiếu Nhập của tôi
- Phiếu Xuất của tôi
- Phiếu Kiểm toán của tôi ⭐ (MỚI)
- Công việc của tôi
- Tồn kho

---

## ⚠️ LƯU Ý

1. **Mật khẩu mặc định:** Tất cả tài khoản mẫu đều có mật khẩu là `password`
2. **Phân công tự động:** Nếu không chọn nhân viên cụ thể, hệ thống sẽ tự động chọn nhân viên phù hợp dựa trên:
   - Chức vụ
   - Khối lượng công việc hiện tại
3. **Tồn kho tự động:** Khi hoàn thành phiếu nhập/xuất, tồn kho tự động được cập nhật
4. **Kiểm toán:** Chỉ có thể điều chỉnh tồn kho sau khi Admin xác nhận phiếu kiểm toán

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

---

## 📝 GHI CHÚ

- Tất cả tài khoản mẫu đều có email theo pattern: `staff{number}@example.com`
- Mật khẩu mặc định: `password`
- Các tài khoản được phân bổ đều các chức vụ để test đầy đủ


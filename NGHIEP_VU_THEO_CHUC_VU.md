# NGHIỆP VỤ THEO CHỨC VỤ - KHI ĐĂNG NHẬP

## 🔍 NHÂN VIÊN KIỂM TOÁN (inventory_staff)

### Khi đăng nhập, nhân viên kiểm toán sẽ thấy:

#### 1. **Dashboard**
- Tổng số phiếu kiểm toán được giao (pending, in_progress)
- Tổng số công việc kiểm kê được giao
- Thống kê công việc đã hoàn thành

#### 2. **Menu chức năng**

##### 2.1. **Kiểm toán Kho** (Inventory Audit) - CHỨC NĂNG CHÍNH
- **Xem danh sách phiếu kiểm toán được giao**:
  - Chỉ thấy phiếu được Admin phân công cho mình
  - Lọc theo trạng thái: pending, in_progress, completed
  - Tìm kiếm theo mã phiếu

- **Xử lý phiếu kiểm toán**:
  - **Bắt đầu kiểm toán**:
    - Chuyển trạng thái: `pending` → `in_progress`
    - Xem danh sách sản phẩm cần kiểm toán (theo kho)
    - Xem số lượng trong hệ thống
  
  - **Nhập số lượng thực tế**:
    - Đối chiếu từng sản phẩm
    - Nhập số lượng thực tế đếm được
    - Hệ thống tự động tính chênh lệch:
      - Chênh lệch = Số lượng thực tế - Số lượng hệ thống
    - Ghi chú lý do chênh lệch (nếu có):
      - Hàng hỏng, mất mát
      - Nhập sai, xuất sai
      - Hàng hết hạn
      - Lý do khác
  
  - **Hoàn thành kiểm toán**:
    - Xác nhận đã kiểm toán xong
    - Chuyển trạng thái: `in_progress` → `completed`
    - Hệ thống tự động tính thống kê:
      - Tổng số sản phẩm đã kiểm toán
      - Số sản phẩm khớp (chênh lệch = 0)
      - Số sản phẩm chênh lệch (thừa/thiếu)
    - Gửi báo cáo cho Admin

##### 2.2. **Kiểm kê Kho** (Inventory Check) - CHỨC NĂNG PHỤ
- **Xem tồn kho**:
  - Danh sách tất cả sản phẩm và số lượng
  - Lọc theo kho (nếu có nhiều kho)
  - Tìm kiếm sản phẩm
  - Xem sản phẩm sắp hết hàng

- **Cập nhật tồn kho đơn giản**:
  - Điều chỉnh số lượng khi có chênh lệch nhỏ
  - Ghi chú lý do
  - (Khác với kiểm toán: không cần phiếu, cập nhật trực tiếp)

##### 2.3. **Công việc được giao** (Tasks)
- **Xem danh sách công việc**:
  - Chỉ thấy công việc được Admin phân công
  - Ưu tiên: `inventory_check`, `stock_report`
  - Lọc theo trạng thái, độ ưu tiên

- **Xử lý công việc**:
  - Bắt đầu công việc
  - Hoàn thành và ghi chú

#### 3. **Không được phép**
- ❌ Tạo phiếu nhập/xuất
- ❌ Xử lý phiếu nhập/xuất (trừ khi được phân công đặc biệt)
- ❌ Xem tất cả phiếu kiểm toán (chỉ thấy của mình)
- ❌ Xác nhận phiếu kiểm toán (chỉ Admin mới xác nhận)

---

## 📦 NHÂN VIÊN KHO (warehouse_staff)

### Khi đăng nhập, nhân viên kho sẽ thấy:

#### 1. **Dashboard**
- Tổng số phiếu nhập/xuất được giao (pending, processing)
- Tổng số công việc được giao
- Thống kê công việc đã hoàn thành

#### 2. **Menu chức năng**

##### 2.1. **Phiếu Nhập** (Import Orders) - HỖ TRỢ
- **Xem danh sách phiếu được giao**:
  - Chỉ thấy phiếu được Admin phân công cho mình
  - (Ưu tiên phân công cho `import_staff`, nhưng có thể giao cho `warehouse_staff` khi cần)

- **Xử lý phiếu nhập**:
  - Bắt đầu: Nhận hàng, kiểm tra
  - Hoàn thành: Nhập kho, cập nhật tồn kho

##### 2.2. **Phiếu Xuất** (Export Orders) - HỖ TRỢ
- **Xem danh sách phiếu được giao**:
  - Chỉ thấy phiếu được Admin phân công cho mình
  - (Ưu tiên phân công cho `export_staff`, nhưng có thể giao cho `warehouse_staff` khi cần)

- **Xử lý phiếu xuất**:
  - Bắt đầu: Kiểm tra tồn kho, soạn hàng
  - Hoàn thành: Xuất kho, giảm tồn kho

##### 2.3. **Công việc được giao** (Tasks)
- **Xem danh sách công việc**:
  - Chỉ thấy công việc được Admin phân công
  - Các loại công việc: import, export, inventory_check, picking, stock_report
  - (Hỗ trợ các công việc khác khi cần)

- **Xử lý công việc**:
  - Bắt đầu công việc
  - Hoàn thành và ghi chú

##### 2.4. **Kiểm kê Kho** (Inventory) - HỖ TRỢ
- Xem tồn kho
- Cập nhật tồn kho đơn giản (nếu được phân công)

#### 3. **Đặc điểm**
- ✅ **Linh hoạt**: Có thể làm nhiều loại công việc
- ✅ **Hỗ trợ**: Khi các chức vụ chuyên trách quá tải
- ⚠️ **Không chuyên trách**: Không ưu tiên phân công như các chức vụ khác

---

## 📋 SO SÁNH CÁC CHỨC VỤ

| Chức năng | inventory_staff | warehouse_staff | import_staff | export_staff |
|-----------|----------------|-----------------|--------------|--------------|
| **Kiểm toán Kho** | ✅ Chính | ❌ | ❌ | ❌ |
| **Kiểm kê đơn giản** | ✅ | ✅ | ❌ | ❌ |
| **Xử lý Phiếu Nhập** | ❌ | ✅ Hỗ trợ | ✅ Chính | ❌ |
| **Xử lý Phiếu Xuất** | ❌ | ✅ Hỗ trợ | ❌ | ✅ Chính |
| **Công việc khác** | ✅ Kiểm kê, Báo cáo | ✅ Tất cả | ❌ | ❌ |

---

## 🔐 PHÂN QUYỀN CHI TIẾT

### inventory_staff (Nhân viên kiểm toán)
```
✅ ĐƯỢC PHÉP:
- Xem phiếu kiểm toán được giao
- Xử lý phiếu kiểm toán (start, complete)
- Nhập số lượng thực tế
- Xem tồn kho
- Cập nhật tồn kho đơn giản
- Xem công việc được giao (inventory_check, stock_report)

❌ KHÔNG ĐƯỢC PHÉP:
- Tạo phiếu kiểm toán (chỉ Admin)
- Xác nhận phiếu kiểm toán (chỉ Admin)
- Xử lý phiếu nhập/xuất (trừ khi được phân công đặc biệt)
- Xem tất cả phiếu kiểm toán
```

### warehouse_staff (Nhân viên kho)
```
✅ ĐƯỢC PHÉP:
- Xem phiếu nhập/xuất được giao
- Xử lý phiếu nhập/xuất (khi được phân công)
- Xem tồn kho
- Cập nhật tồn kho đơn giản
- Xem và xử lý công việc được giao (tất cả loại)

❌ KHÔNG ĐƯỢC PHÉP:
- Tạo phiếu nhập/xuất (chỉ Admin)
- Xác nhận phiếu (chỉ Admin)
- Xem tất cả phiếu (chỉ thấy của mình)
```

---

## 📊 QUY TRÌNH LÀM VIỆC

### Quy trình của inventory_staff:

```
1. Đăng nhập
   ↓
2. Xem Dashboard
   - Số phiếu kiểm toán pending
   - Số công việc pending
   ↓
3. Chọn "Kiểm toán Kho"
   ↓
4. Xem danh sách phiếu được giao
   ↓
5. Chọn phiếu cần xử lý
   ↓
6. Bắt đầu kiểm toán
   - Xem danh sách sản phẩm
   - Xem số lượng hệ thống
   ↓
7. Đi kiểm đếm thực tế
   ↓
8. Nhập số lượng thực tế
   - Đối chiếu từng sản phẩm
   - Ghi chú chênh lệch
   ↓
9. Hoàn thành kiểm toán
   - Hệ thống tính thống kê
   - Gửi báo cáo cho Admin
   ↓
10. Admin xác nhận và điều chỉnh tồn kho
```

### Quy trình của warehouse_staff:

```
1. Đăng nhập
   ↓
2. Xem Dashboard
   - Số phiếu nhập/xuất pending
   - Số công việc pending
   ↓
3. Chọn công việc cần làm
   - Phiếu nhập (nếu được giao)
   - Phiếu xuất (nếu được giao)
   - Công việc khác (nếu được giao)
   ↓
4. Xử lý công việc
   - Bắt đầu
   - Hoàn thành
   ↓
5. Cập nhật tồn kho (tự động)
```

---

## 💡 LƯU Ý

1. **inventory_staff** chuyên về kiểm toán và kiểm kê
2. **warehouse_staff** linh hoạt, hỗ trợ nhiều loại công việc
3. Tất cả nhân viên chỉ thấy công việc được phân công cho mình
4. Admin có thể override phân công tự động bằng cách chọn nhân viên cụ thể
5. Phân công tự động dựa trên chức vụ và workload


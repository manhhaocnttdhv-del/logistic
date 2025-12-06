# Nghiệp vụ Hệ thống Quản lý Logistics Nội Bộ

## Tổng quan
Hệ thống quản lý toàn bộ quy trình logistics nội bộ của công ty, từ yêu cầu vật tư, nhập kho, xuất kho, điều chuyển đến báo cáo.

---

## I. NGHIỆP VỤ ADMIN (Quản lý Logistics)

### 1. Quản lý Danh mục
- **Quản lý Hàng hóa/Vật tư**: Thêm, sửa, xóa, tìm kiếm sản phẩm, import/export Excel
- **Quản lý Nhà cung cấp**: Thông tin NCC, lịch sử giao hàng, đánh giá
- **Quản lý Nhân viên**: Tài khoản, phân quyền, theo dõi hiệu suất

### 2. Quản lý Yêu cầu Vật tư (Material Requests)
- **Tạo yêu cầu**: Từ các bộ phận (Phòng ban, Dự án, Bộ phận sản xuất)
- **Duyệt yêu cầu**: Phê duyệt/từ chối yêu cầu
- **Chuyển thành phiếu xuất**: Tự động tạo phiếu xuất khi duyệt

### 3. Quản lý Nhập kho (Import Orders)
- **Tạo phiếu nhập**: Từ nhà cung cấp, điều chuyển từ kho khác, trả hàng
- **Gán nhân viên**: Phân công nhân viên xử lý
- **Xác nhận nhập**: Sau khi nhân viên kiểm tra xong
- **In phiếu nhập**: PDF, Excel

### 4. Quản lý Xuất kho (Export Orders)
- **Tạo phiếu xuất**: 
  - Từ yêu cầu vật tư (tự động)
  - Xuất cho bộ phận/dự án
  - Điều chuyển sang kho khác
  - Trả hàng nhà cung cấp
- **Kiểm tra tồn kho**: Tự động kiểm tra trước khi xuất
- **Gán nhân viên**: Phân công soạn hàng
- **Xác nhận xuất**: Sau khi nhân viên soạn xong

### 5. Quản lý Điều chuyển (Transfer Orders)
- **Điều chuyển giữa các kho**: Nếu có nhiều kho
- **Theo dõi vận chuyển**: Trạng thái vận chuyển nội bộ

### 6. Phân công Công việc
- **Tạo nhiệm vụ**: Nhập kho, xuất kho, kiểm kê, soạn hàng
- **Gán nhân viên**: Phân công cụ thể
- **Theo dõi tiến độ**: Xem trạng thái, thời gian hoàn thành

### 7. Quản lý Báo cáo
- **Báo cáo tồn kho**: Theo thời gian, theo sản phẩm
- **Báo cáo nhập-xuất-tồn**: Tổng hợp theo kỳ
- **Báo cáo hiệu suất**: Nhân viên, nhà cung cấp
- **Báo cáo sắp hết hàng**: Cảnh báo tồn kho thấp
- **Xuất Excel/PDF**: Tất cả báo cáo

---

## II. NGHIỆP VỤ STAFF (Nhân viên Kho)

### 1. Xử lý Phiếu Nhập
- **Nhận nhiệm vụ**: Xem phiếu được giao
- **Kiểm tra hàng**: Đối chiếu với phiếu nhập
- **Nhập kho**: Cập nhật số lượng thực tế, ghi chú tình trạng
- **Hoàn thành**: Tự động cập nhật tồn kho

### 2. Xử lý Phiếu Xuất
- **Nhận nhiệm vụ**: Xem phiếu được giao
- **Kiểm tra tồn kho**: Xác nhận đủ hàng
- **Soạn hàng**: Lấy hàng từ kho, đóng gói
- **Hoàn thành**: Tự động giảm tồn kho

### 3. Kiểm kê Kho
- **Xem tồn kho**: Danh sách sản phẩm và số lượng
- **Cập nhật tồn kho**: Điều chỉnh khi có chênh lệch
- **Báo cáo chênh lệch**: Ghi chú lý do

### 4. Công việc được giao
- **Xem danh sách**: Tất cả công việc được phân công
- **Bắt đầu**: Đánh dấu đã bắt đầu
- **Hoàn thành**: Cập nhật trạng thái và ghi chú

---

## III. QUY TRÌNH NGHIỆP VỤ CHI TIẾT

### Quy trình 1: Yêu cầu Vật tư → Xuất kho
```
1. Bộ phận tạo Yêu cầu vật tư
2. Admin duyệt yêu cầu
3. Hệ thống tự động tạo Phiếu xuất
4. Admin gán nhân viên soạn hàng
5. Nhân viên kiểm tra tồn kho → Soạn hàng → Hoàn thành
6. Tồn kho tự động giảm
```

### Quy trình 2: Nhập kho từ Nhà cung cấp
```
1. Admin tạo Phiếu nhập (từ NCC)
2. Admin gán nhân viên nhận hàng
3. Nhân viên nhận hàng → Kiểm tra → Nhập kho
4. Nhân viên hoàn thành → Tồn kho tự động tăng
5. Admin xác nhận (nếu cần)
```

### Quy trình 3: Điều chuyển giữa các kho
```
1. Admin tạo Phiếu điều chuyển
2. Xuất từ kho A → Nhập vào kho B
3. Gán nhân viên xử lý
4. Nhân viên thực hiện điều chuyển
5. Cập nhật tồn kho cả 2 kho
```

### Quy trình 4: Kiểm kê định kỳ
```
1. Admin tạo nhiệm vụ Kiểm kê
2. Gán nhân viên thực hiện
3. Nhân viên kiểm đếm thực tế
4. Cập nhật chênh lệch (nếu có)
5. Báo cáo kết quả
```

---

## IV. CÁC TRẠNG THÁI VÀ LUỒNG XỬ LÝ

### Trạng thái Phiếu Nhập/Xuất:
- **pending** (Chờ xử lý): Mới tạo, chưa gán hoặc đã gán chưa bắt đầu
- **processing** (Đang xử lý): Nhân viên đã bắt đầu làm việc
- **completed** (Hoàn thành): Đã xử lý xong, tồn kho đã cập nhật
- **cancelled** (Đã hủy): Hủy bỏ phiếu

### Trạng thái Công việc:
- **pending**: Chờ nhân viên bắt đầu
- **in_progress**: Đang thực hiện
- **completed**: Đã hoàn thành

---

## V. TÍNH NĂNG ĐẶC BIỆT

### 1. Tự động hóa
- Tự động tạo phiếu xuất từ yêu cầu vật tư
- Tự động cập nhật tồn kho khi hoàn thành
- Tự động cảnh báo khi tồn kho thấp
- Tự động tính toán giá trị tồn kho

### 2. Kiểm soát
- Kiểm tra tồn kho trước khi xuất
- Không cho xuất nếu không đủ hàng
- Theo dõi lịch sử thay đổi tồn kho
- Audit trail cho mọi thao tác

### 3. Báo cáo & Phân tích
- Dashboard tổng quan
- Báo cáo theo thời gian thực
- Phân tích xu hướng nhập/xuất
- Đánh giá hiệu suất nhân viên

---

## VI. MỞ RỘNG TƯƠNG LAI (Nếu cần)

1. **Quản lý nhiều kho**: Hỗ trợ nhiều kho hàng
2. **Quản lý vận chuyển**: Theo dõi vận chuyển nội bộ
3. **Tích hợp barcode/QR**: Quét mã để nhập/xuất nhanh
4. **Mobile App**: Ứng dụng di động cho nhân viên
5. **Thông báo tự động**: Email/SMS khi có yêu cầu mới
6. **Đánh giá nhà cung cấp**: Rating, review NCC

---

## VII. PHÂN QUYỀN

### Admin:
- Toàn quyền quản lý hệ thống
- Tạo, sửa, xóa tất cả dữ liệu
- Xem tất cả báo cáo
- Phân công công việc

### Staff:
- Chỉ xem công việc được giao
- Xử lý phiếu nhập/xuất được phân công
- Cập nhật tồn kho (kiểm kê)
- Không thể tạo, sửa, xóa phiếu

---

## VIII. LỢI ÍCH

1. **Tối ưu quy trình**: Tự động hóa, giảm thủ công
2. **Kiểm soát chặt chẽ**: Theo dõi mọi giao dịch
3. **Báo cáo nhanh**: Dữ liệu real-time
4. **Giảm sai sót**: Validation, kiểm tra tự động
5. **Tăng hiệu suất**: Phân công rõ ràng, theo dõi tiến độ


# Hướng dẫn Phân công Nhân viên

## 1. Khi tạo Phiếu Nhập (Import Order)

### Cách phân công:

**Bước 1: Chọn phương thức phân công**
- **Tự động phân công (Khuyến nghị)**: Hệ thống tự động chọn nhân viên phù hợp
- **Chọn nhân viên cụ thể**: Admin chọn trực tiếp nhân viên

**Bước 2: Logic tự động phân công**

Khi chọn "Tự động phân công", hệ thống sẽ:

1. **Ưu tiên chức vụ** (theo thứ tự):
   - `import_staff` (Nhân viên nhập kho) - Ưu tiên cao nhất
   - `warehouse_staff` (Nhân viên kho)
   - `general_staff` (Nhân viên tổng hợp)

2. **Tính workload** cho mỗi nhân viên:
   - Đếm số phiếu nhập đang ở trạng thái: `pending` hoặc `processing`
   - Nhân viên có workload thấp hơn sẽ được ưu tiên

3. **Chọn nhân viên**:
   - Tìm nhân viên có position phù hợp và workload thấp nhất
   - Nếu workload ≤ 2, dừng tìm kiếm và chọn nhân viên đó
   - Nếu không tìm được, tìm trong tất cả nhân viên có workload thấp nhất

4. **Tự động tạo Task**:
   - Sau khi phân công, hệ thống tự động tạo task cho nhân viên
   - Task type: `import`
   - Title: "Xử lý phiếu nhập {MÃ_PHIẾU}"
   - Description: "Nhập hàng từ {Nguồn}"

### Ví dụ:

```
Phiếu nhập: PN000001
Nguồn: Từ nhà cung cấp "Công ty ABC"

→ Hệ thống tìm:
  1. Nhân viên có position = "import_staff" và workload thấp nhất
  2. Nếu không có, tìm "warehouse_staff"
  3. Nếu không có, tìm "general_staff"
  
→ Giả sử tìm được: Nguyễn Văn A (import_staff, workload = 1)
→ Phân công: assigned_to = Nguyễn Văn A
→ Tạo task: "Xử lý phiếu nhập PN000001" cho Nguyễn Văn A
```

---

## 2. Khi tạo Phiếu Xuất (Export Order)

### Cách phân công:

**Bước 1: Chọn phương thức phân công**
- **Tự động phân công (Khuyến nghị)**: Hệ thống tự động chọn nhân viên phù hợp
- **Chọn nhân viên cụ thể**: Admin chọn trực tiếp nhân viên

**Bước 2: Logic tự động phân công**

Khi chọn "Tự động phân công", hệ thống sẽ:

1. **Ưu tiên chức vụ** (theo thứ tự):
   - `export_staff` (Nhân viên xuất kho) - Ưu tiên cao nhất
   - `picking_staff` (Nhân viên soạn hàng)
   - `warehouse_staff` (Nhân viên kho)
   - `general_staff` (Nhân viên tổng hợp)

2. **Tính workload** cho mỗi nhân viên:
   - Đếm số phiếu xuất đang ở trạng thái: `pending` hoặc `processing`
   - Nhân viên có workload thấp hơn sẽ được ưu tiên

3. **Chọn nhân viên**:
   - Tìm nhân viên có position phù hợp và workload thấp nhất
   - Nếu workload ≤ 2, dừng tìm kiếm và chọn nhân viên đó
   - Nếu không tìm được, tìm trong tất cả nhân viên có workload thấp nhất

4. **Tự động tạo Task**:
   - Sau khi phân công, hệ thống tự động tạo task cho nhân viên
   - Task type: `export`
   - Title: "Xử lý phiếu xuất {MÃ_PHIẾU}"
   - Description: "Xuất hàng cho {Người nhận} - Lý do: {Lý do}"

### Ví dụ:

```
Phiếu xuất: PX000001
Người nhận: Phòng Sản xuất
Lý do: Sử dụng nội bộ

→ Hệ thống tìm:
  1. Nhân viên có position = "export_staff" và workload thấp nhất
  2. Nếu không có, tìm "picking_staff"
  3. Nếu không có, tìm "warehouse_staff"
  4. Nếu không có, tìm "general_staff"
  
→ Giả sử tìm được: Trần Thị B (export_staff, workload = 0)
→ Phân công: assigned_to = Trần Thị B
→ Tạo task: "Xử lý phiếu xuất PX000001" cho Trần Thị B
```

---

## 3. Tính năng Bảo vệ (Race Condition Protection)

Hệ thống sử dụng **Database Transaction** và **Pessimistic Locking** để đảm bảo:

- ✅ Không phân công trùng khi có nhiều request đồng thời
- ✅ Workload luôn được tính chính xác
- ✅ Phân bổ công việc công bằng giữa các nhân viên

---

## 4. Các Chức vụ (Positions)

| Chức vụ | Mã | Mô tả |
|---------|-----|-------|
| Nhân viên kho | `warehouse_staff` | Xử lý cả nhập và xuất |
| Nhân viên nhập kho | `import_staff` | Chuyên xử lý phiếu nhập |
| Nhân viên xuất kho | `export_staff` | Chuyên xử lý phiếu xuất |
| Nhân viên kiểm kê | `inventory_staff` | Chuyên kiểm kê kho |
| Nhân viên soạn hàng | `picking_staff` | Chuyên soạn hàng xuất kho |
| Nhân viên tổng hợp | `general_staff` | Có thể làm nhiều việc |

---

## 5. Lưu ý

- Nếu nhân viên không có chức vụ (position = null), vẫn có thể được phân công nếu workload thấp
- Hệ thống ưu tiên phân bổ đều workload giữa các nhân viên
- Admin luôn có thể ghi đè bằng cách chọn nhân viên cụ thể


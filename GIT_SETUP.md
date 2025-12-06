# HƯỚNG DẪN PUSH CODE LÊN GITHUB

## Vấn đề: Repository not found

Lỗi này xảy ra khi:
- Repository chưa được tạo trên GitHub
- URL repository sai
- Chưa xác thực với GitHub

## Giải pháp:

### Cách 1: Tạo repository trên GitHub (Khuyến nghị)

1. **Tạo repository trên GitHub:**
   - Truy cập: https://github.com/new
   - Repository name: `logistic`
   - Chọn Private hoặc Public
   - **KHÔNG** tích "Initialize this repository with a README"
   - Click "Create repository"

2. **Push code lên GitHub:**
   ```powershell
   git add .
   git commit -m "Initial commit: Logistic management system"
   git push -u origin main
   ```

### Cách 2: Sử dụng Personal Access Token (Nếu cần xác thực)

1. **Tạo Personal Access Token:**
   - Truy cập: https://github.com/settings/tokens
   - Click "Generate new token" → "Generate new token (classic)"
   - Đặt tên token (ví dụ: "logistic-repo")
   - Chọn quyền: `repo` (Full control of private repositories)
   - Click "Generate token"
   - **LƯU LẠI TOKEN** (chỉ hiển thị 1 lần)

2. **Push với token:**
   ```powershell
   git push https://YOUR_TOKEN@github.com/manhhaocnttdhv-del/logistic.git main
   ```

   Hoặc cập nhật remote:
   ```powershell
   git remote set-url origin https://YOUR_TOKEN@github.com/manhhaocnttdhv-del/logistic.git
   git push -u origin main
   ```

### Cách 3: Sử dụng SSH (Nếu đã có SSH key)

1. **Kiểm tra SSH key:**
   ```powershell
   ls ~/.ssh
   ```

2. **Nếu chưa có SSH key, tạo mới:**
   ```powershell
   ssh-keygen -t ed25519 -C "haopm@sankei-bc.co.jp"
   ```

3. **Thêm SSH key vào GitHub:**
   - Copy nội dung file `~/.ssh/id_ed25519.pub`
   - Truy cập: https://github.com/settings/keys
   - Click "New SSH key"
   - Paste key và lưu

4. **Cập nhật remote sang SSH:**
   ```powershell
   git remote set-url origin git@github.com:manhhaocnttdhv-del/logistic.git
   git push -u origin main
   ```

## Lệnh nhanh để push code hiện tại:

```powershell
# Thêm tất cả file
git add .

# Commit
git commit -m "Add staff create import/export orders and material requests"

# Push (sau khi đã tạo repository trên GitHub)
git push -u origin main
```

## Lưu ý:

- Đảm bảo repository đã được tạo trên GitHub trước khi push
- Nếu repository là Private, cần xác thực với Personal Access Token hoặc SSH key
- Nếu repository là Public, có thể push trực tiếp (nhưng vẫn cần xác thực)


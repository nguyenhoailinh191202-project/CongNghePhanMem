# HỆ THỐNG ĐIỂM DANH SINH VIÊN & CẢNH BÁO CHUYÊN CẦN TRỰC TUYẾN

> **Đồ án môn học:** Công nghệ Phần mềm / Lập trình Web[cite: 1]  
> **Công nghệ phát triển:** Laravel 12 + Tailwind CSS (Vite) + MySQL  
> **Packages tích hợp:** `maatwebsite/excel`, `barryvdh/laravel-dompdf`, `chart.js`, `qrcode`

---

## 1. YÊU CẦU MÔI TRƯỜNG HỆ THỐNG

Trước khi tiến hành cài đặt, hãy đảm bảo máy tính đã cài đặt:
- **PHP:** >= 8.2 (Bắt buộc bật các extensions: `pdo_mysql`, `gd`, `zip`, `fileinfo`)
- **Composer:** >= 2.x
- **Node.js:** >= 18.x & **NPM** >= 9.x
- **Cơ sở dữ liệu:** MySQL >= 8.0 hoặc MariaDB >= 10.4 (Khuyến nghị XAMPP)
- **Git**

> **Lưu ý cấu hình PHP trên XAMPP (Windows):**  
> Mở file `xampp/php/php.ini` (hoặc `php --ini`), tìm và xóa dấu `;` ở đầu các dòng sau, sau đó lưu lại và restart Apache:
> ```ini
> extension=gd
> extension=zip
> extension=fileinfo
> ```

---

## 2. CÂY CẤU TRÚC THƯ MỤC DỰ ÁN TỪ A -> Z

Cấu trúc mã nguồn chi tiết phản ánh toàn bộ 22 tasks của hệ thống:

```text
attendance-system/
├── app/
│   ├── Exports/
│   │   └── AttendanceReportExport.php       # Xuất dữ liệu điểm danh ra file Excel (.xlsx)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── ClassSectionController.php # Tạo lớp HP, tự sinh 15 buổi học & ghi danh
│   │   │   │   ├── DashboardController.php    # Thống kê tổng quan & lọc sinh viên vắng > 20%
│   │   │   │   ├── RoomController.php         # Quản lý danh mục phòng học & IP Wifi[cite: 2, 3]
│   │   │   │   ├── SubjectController.php      # Quản lý danh mục môn học[cite: 2, 3]
│   │   │   │   └── UserController.php         # Quản lý tài khoản (CRUD) & Import Excel[cite: 2, 3]
│   │   │   ├── Student/
│   │   │   │   ├── LeaveRequestController.php # Nộp đơn xin nghỉ & theo dõi trạng thái[cite: 2, 3]
│   │   │   │   └── ScheduleController.php     # Tra cứu thời khóa biểu & báo cáo chuyên cần[cite: 2, 3]
│   │   │   ├── Teacher/
│   │   │   │   ├── AttendanceController.php   # Điểm danh lớp & sinh mã QR Code động[cite: 2, 3]
│   │   │   │   └── LeaveApprovalController.php# Duyệt đơn nghỉ & xuất báo cáo PDF/Excel[cite: 2, 3]
│   │   │   └── AuthController.php             # Xử lý Đăng nhập, Đăng xuất, Đổi mật khẩu[cite: 2, 3]
│   │   ├── Middleware/
│   │   │   └── CheckRole.php                  # Phân quyền truy cập theo vai trò (RBAC)[cite: 2, 3]
│   │   └── Requests/
│   │       └── StoreLeaveRequest.php          # Validate form nộp đơn & file minh chứng[cite: 2, 3]
│   ├── Imports/
│   │   └── UsersImport.php                    # Class đọc & import danh sách SV lớn từ Excel[cite: 2, 3]
│   ├── Models/
│   │   ├── BuoiHoc.php                        # Model buổi học[cite: 2, 3]
│   │   ├── ChiTietDiemDanh.php                # Model nhật ký điểm danh[cite: 2, 3]
│   │   ├── DanhSachLop.php                    # Model trung gian SV - Lớp HP[cite: 2, 3]
│   │   ├── DonXinPhep.php                     # Model đơn xin nghỉ học trực tuyến[cite: 2, 3]
│   │   ├── LopHocPhan.php                     # Model lớp học phần[cite: 2, 3]
│   │   ├── MonHoc.php                         # Model môn học[cite: 2, 3]
│   │   └── User.php                           # Model tài khoản người dùng[cite: 2, 3]
│   ├── Observers/
│   │   └── DonXinPhepObserver.php             # Tự động cập nhật điểm danh khi duyệt đơn[cite: 2, 3]
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/
│   ├── app.php
│   └── providers.php
├── config/
│   ├── app.php
│   ├── database.php
│   ├── dompdf.php                             # Cấu hình nhúng font UTF-8 xuất PDF[cite: 2, 3]
│   └── excel.php                              # Cấu hình Import/Export bảng tính
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── xxxx_create_users_and_academic_tables.php       # Bảng users, mon_hoc, lop_hoc_phan[cite: 2, 3]
│   │   └── xxxx_create_operational_attendance_tables.php   # Bảng buoi_hoc, diem_danh, don_xin_phep[cite: 2, 3]
│   └── seeders/
│       ├── AcademicSeeder.php                 # Tạo dữ liệu môn học, lớp học phần mẫu[cite: 2, 3]
│       ├── AttendanceSeeder.php               # Giả lập lịch sử điểm danh mẫu vẽ biểu đồ[cite: 2, 3]
│       ├── DatabaseSeeder.php                 # Seeder tổng điều phối[cite: 2, 3]
│       └── UserSeeder.php                     # Tạo tài khoản Admin, Giảng viên, Sinh viên[cite: 2, 3]
├── docs/
│   └── erd/
│       ├── attendance_system_erd.drawio       # File thiết kế CSDL nguồn Draw.io[cite: 2, 3]
│       └── attendance_system_erd.png          # Sơ đồ thực thể ERD chuẩn hóa[cite: 2, 3]
├── public/
│   ├── build/                                 # Chứa file CSS/JS tĩnh khi build Vite
│   ├── storage/                               # Symbolic link trỏ vào storage/app/public[cite: 2, 3]
│   └── index.php
├── resources/
│   ├── css/
│   │   └── app.css                            # Cấu hình import Tailwind CSS (@import "tailwindcss";)[cite: 2, 3]
│   ├── js/
│   │   ├── app.js
│   │   ├── attendance_qr.js                   # Xử lý QR Code động qua JavaScript[cite: 2, 3]
│   │   └── dashboard_chart.js                 # Vẽ biểu đồ thống kê bằng Chart.js[cite: 2, 3]
│   └── views/
│       ├── admin/                             # Giao diện dành cho Admin[cite: 2, 3]
│       ├── auth/
│       │   ├── change_password.blade.php      # Form đổi mật khẩu[cite: 2, 3]
│       │   └── login.blade.php                # Giao diện Đăng nhập[cite: 2, 3]
│       ├── exports/
│       │   └── attendance_pdf.blade.php       # Template xuất bảng điểm danh PDF[cite: 2, 3]
│       ├── layouts/
│       │   ├── app.blade.php                  # Master Layout chuẩn Responsive[cite: 2, 3]
│       │   ├── header.blade.php               # Topbar hiển thị User info[cite: 2, 3]
│       │   └── sidebar.blade.php              # Sidebar điều hướng theo vai trò[cite: 2, 3]
│       ├── student/                           # Giao diện dành cho Sinh viên[cite: 2, 3]
│       └── teacher/                           # Giao diện dành cho Giảng viên[cite: 2, 3]
├── routes/
│   ├── console.php
│   └── web.php                                # Khai báo hệ thống Routes & Middleware[cite: 2, 3]
├── storage/
│   ├── app/
│   │   └── public/
│   │       └── leave_proofs/                  # Thư mục lưu ảnh minh chứng đơn xin nghỉ[cite: 2, 3]
│   ├── framework/
│   ├── logs/
│   └── testing/
│       └── sample_1000_students.xlsx          # File test Import dữ liệu lớn[cite: 2, 3]
├── tests/
│   └── manual/
│       ├── E2E_Attendance_Flow_Scenario.xlsx  # Kịch bản kiểm thử End-to-End[cite: 2, 3]
│       └── Security_RBAC_Validation_Cases.docx# Kịch bản kiểm thử bảo mật & RBAC[cite: 2, 3]
├── .env.example                               # File mẫu cấu hình biến môi trường[cite: 2, 3]
├── .gitignore
├── composer.json                              # Quản lý dependencies PHP Backend[cite: 2, 3]
├── composer.lock
├── package.json                               # Quản lý dependencies Frontend[cite: 2, 3]
├── package-lock.json
├── README.md                                  # Hướng dẫn chi tiết dự án[cite: 2, 3]
└── vite.config.js                             # Cấu hình plugin Tailwind CSS Vite[cite: 2, 3]

3. HƯỚNG DẪN CÀI ĐẶT DỰ ÁN TỪ ĐẦU (KHI FORK / CLONE)Sau khi fork hoặc clone repository về máy, mở Terminal/CMD tại thư mục dự án và thực hiện các bước sau:Bước 1: Cài đặt thư viện Backend (Khi không có mục vendor/)Bashcomposer install
(Nếu gặp lỗi thiếu extension gd hoặc zip, kiểm tra lại Bước 1 để bật trong php.ini).Bước 2: Cài đặt thư viện Frontend (Khi không có hoặc đã xóa mục node_modules/)Thư mục node_modules/ không được đưa lên Git nhằm giảm dung lượng repository[cite: 2, 3]. Bạn không cần phải gõ lệnh cài đặt từng gói, chỉ cần chạy đúng 1 lệnh duy nhất:Bashnpm install
(Hoặc viết tắt là npm i). NPM sẽ tự động đọc file package.json và tải lại toàn bộ: tailwindcss, @tailwindcss/vite, chart.js, qrcode.Bước 3: Thiết lập cấu hình môi trường .envNhân bản cấu hình từ file mẫu .env.example:Bash# Trên Windows (Command Prompt):
copy .env.example .env

# Trên Git Bash / Linux / macOS:
cp .env.example .env
Sinh mã khóa bảo mật Application Key mới cho ứng dụng:Bashphp artisan key:generate
Bước 4: Tạo và kết nối Cơ sở dữ liệuMở phpMyAdmin (http://localhost/phpmyadmin), tạo một Database mới có tên: db_diem_danh (chọn bảng mã utf8mb4_unicode_ci).Mở file .env vừa tạo và chỉnh sửa thông số kết nối:Ini, TOMLDB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_diem_danh
DB_USERNAME=root
DB_PASSWORD=
Bước 5: Chạy Migration và Nạp dữ liệu mẫu (Seeders)Khởi tạo toàn bộ bảng CSDL và nạp sẵn dữ liệu mẫu cho Admin, Giảng viên, Sinh viên, Môn học[cite: 2, 3]:Bashphp artisan migrate:fresh --seed
Bước 6: Tạo liên kết Symbolic Link cho thư mục lưu trữ ảnhĐể hình ảnh minh chứng đơn xin nghỉ học hiển thị được trên trình duyệt[cite: 2, 3]:Bashphp artisan storage:link
4. HƯỚNG DẪN KHỞI CHẠY DỰ ÁNĐể chạy và thao tác thử nghiệm trên Localhost, mở 2 cửa sổ Terminal song song:Terminal 1: Khởi chạy Vite (Biên dịch Tailwind CSS và JavaScript động)Bashnpm run dev
(Nếu muốn build file tĩnh production mà không cần bật terminal dev, chạy: npm run build)[cite: 2, 3]Terminal 2: Khởi chạy máy chủ nội bộ LaravelBashphp artisan serve
Mở trình duyệt truy cập: http://127.0.0.1:80005. TÀI KHOẢN MẪU ĐĂNG NHẬP THỬ NGHIỆMSau khi chạy seeder, hệ thống có sẵn các tài khoản sau[cite: 2, 3]:Phân quyềnEmail đăng nhậpMật khẩu mặc địnhChức năng chínhQuản trị viên (Admin)admin@caothang.edu.vn12345678Dashboard, Lọc cảnh báo vắng > 20%, CRUD User/Môn/Lớp, Import Excel[cite: 2, 3]Giảng viêngiangvien1@caothang.edu.vn12345678Điểm danh lớp, Sinh QR Code động, Duyệt đơn nghỉ, Xuất Excel/PDF[cite: 2, 3]Sinh viênsinhvien1@caothang.edu.vn12345678Xem thời khóa biểu, Báo cáo chuyên cần cá nhân, Nộp đơn xin nghỉ[cite: 2, 3]6. XỬ LÝ SỰ CỐ PHỔ BIẾN (TROUBLESHOOTING)Lỗi giao diện bị vỡ hạt / Không nhận style Tailwind:Chưa bật tiến trình Vite. Chạy lệnh npm run dev (hoặc npm run build)[cite: 2, 3].Lỗi không xem được ảnh minh chứng đơn nghỉ:Chạy lệnh php artisan storage:link để tạo shortcut tượng trưng liên kết thư mục storage sang public[cite: 2, 3].Lỗi cấu hình .env không nhận thay đổi:Xóa cache cấu hình bằng lệnh: php artisan optimize:clear.Lỗi xuất file PDF bị vỡ font tiếng Việt có dấu:Kiểm tra cấu hình isRemoteEnabled => true trong config/dompdf.php và nhúng phông DejaVu Sans vào template Blade[cite: 2, 3].

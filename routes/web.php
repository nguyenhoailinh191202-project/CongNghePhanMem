<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Student\LeaveRequestController;
use App\Http\Controllers\Student\ScheduleController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\LeaveApprovalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Vào gốc domain (/) -> Luôn chuyển hướng ngay sang trang Đăng nhập (/login)
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes (Chưa đăng nhập)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes (Đã đăng nhập)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/doi-mat-khau', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
    Route::put('/doi-mat-khau', [AuthController::class, 'changePassword'])->name('password.update');

    // --- QUẢN TRỊ VIÊN (ADMIN) ---
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Dashboard & Cảnh báo vắng
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/warnings', [DashboardController::class, 'warnings'])->name('warnings.index');

        // Xem chi tiết điểm danh Lớp HP dành cho Admin
        Route::get('/attendance/class/{lopHocPhan}', [AdminAttendanceController::class, 'show'])->name('attendance.show');

        // Import Excel danh sách người dùng (Đặt trước Route::resource để không bị trùng URL)
        Route::get('/users/import', [UserController::class, 'showImportForm'])->name('users.import');
        Route::post('/users/import', [UserController::class, 'import'])->name('users.import.store');
        Route::get('/users/download-sample', [UserController::class, 'downloadSample'])->name('users.download-sample');

        // Thao tác Tài khoản nâng cao (Reset mật khẩu, Khóa/Mở khóa)
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Quản lý CRUD Tài khoản (Index, Create, Store, Edit, Update, Destroy)
        Route::resource('users', UserController::class);
    });

    // --- SINH VIÊN ---
    Route::middleware('role:sinh_vien')->prefix('sinh-vien')->name('student.')->group(function () {
        Route::get('/thoi-khoa-bieu', [ScheduleController::class, 'schedule'])->name('schedule.index');
        Route::get('/chuyen-can', [ScheduleController::class, 'attendanceReport'])->name('attendance.index');
        Route::get('/diem-danh/qr/{token}', [AttendanceController::class, 'scanQr'])->name('attendance.qr.scan');
        Route::resource('don-xin-nghi', LeaveRequestController::class)->only(['index', 'create', 'store'])->names('leave-requests');
    });

    // --- GIẢNG VIÊN ---
    Route::middleware('role:giang_vien')->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/{buoiHoc}', [AttendanceController::class, 'sheet'])->name('attendance.sheet');
        Route::post('/attendance/{buoiHoc}', [AttendanceController::class, 'save'])->name('attendance.save');
        Route::get('/attendance/{buoiHoc}/qr', [AttendanceController::class, 'qrCode'])->name('attendance.qr');
        Route::get('/attendance/{buoiHoc}/token', [AttendanceController::class, 'token'])->name('attendance.token');
        Route::get('/leave-requests', [LeaveApprovalController::class, 'index'])->name('leave-requests.index');
        Route::patch('/leave-requests/{donXinPhep}/approve', [LeaveApprovalController::class, 'approve'])->name('leave-requests.approve');
        Route::patch('/leave-requests/{donXinPhep}/reject', [LeaveApprovalController::class, 'reject'])->name('leave-requests.reject');
        Route::get('/export-attendance/{lopHocPhan}/excel', [LeaveApprovalController::class, 'exportExcel'])->name('export-attendance.excel');
        Route::get('/export-attendance/{lopHocPhan}/pdf', [LeaveApprovalController::class, 'exportPdf'])->name('export-attendance.pdf');
    });
});
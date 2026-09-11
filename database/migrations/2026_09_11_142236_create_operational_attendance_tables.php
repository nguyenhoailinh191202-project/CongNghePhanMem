<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Bảng Ghi danh sinh viên vào lớp học phần (danh_sach_lop)
        Schema::create('danh_sach_lop', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinh_vien_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lop_hoc_phan_id')->constrained('lop_hoc_phan')->onDelete('cascade');
            $table->timestamps();

            // Ràng buộc UNIQUE để một sinh viên không bị ghi danh 2 lần vào 1 lớp HP
            $table->unique(['sinh_vien_id', 'lop_hoc_phan_id']);
        });

        // 2. Bảng Quản lý lịch trình từng buổi học (buoi_hoc)
        Schema::create('buoi_hoc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lop_hoc_phan_id')->constrained('lop_hoc_phan')->onDelete('cascade');
            $table->dateTime('ngay_hoc');
            $table->enum('trang_thai', ['da_hoc', 'chua_hoc', 'huy'])->default('chua_hoc');
            $table->timestamps();
        });

        // 3. Bảng Nhật ký điểm danh từng sinh viên (chi_tiet_diem_danh)
        Schema::create('chi_tiet_diem_danh', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buoi_hoc_id')->constrained('buoi_hoc')->onDelete('cascade');
            $table->foreignId('sinh_vien_id')->constrained('users')->onDelete('cascade');
            $table->enum('trang_thai', ['co_mat', 'vang_mat', 'di_tre', 'vang_co_phep'])->default('co_mat');
            $table->timestamps();

            // Mỗi sinh viên chỉ có 1 bản ghi điểm danh trong 1 buổi học
            $table->unique(['buoi_hoc_id', 'sinh_vien_id']);
        });

        // 4. Bảng Đơn xin nghỉ phép trực tuyến (don_xin_phep)
        Schema::create('don_xin_phep', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinh_vien_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('buoi_hoc_id')->constrained('buoi_hoc')->onDelete('cascade');
            $table->text('ly_do');
            $table->string('hinh_anh_minh_chung', 255)->nullable();
            $table->enum('trang_thai', ['cho_duyet', 'da_duyet', 'tu_choi'])->default('cho_duyet');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('don_xin_phep');
        Schema::dropIfExists('chi_tiet_diem_danh');
        Schema::dropIfExists('buoi_hoc');
        Schema::dropIfExists('danh_sach_lop');
    }
};
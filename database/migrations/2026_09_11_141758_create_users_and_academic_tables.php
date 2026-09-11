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
        // 1. Cập nhật hoặc tạo mới bảng người dùng (users)
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('ho_ten', 150);
                $table->string('email', 150)->unique();
                $table->string('password', 255);
                $table->enum('vai_tro', ['admin', 'giang_vien', 'sinh_vien'])->default('sinh_vien');
                $table->rememberToken();
                $table->timestamps();
            });
        } else {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'ho_ten')) {
                    $table->string('ho_ten', 150)->after('id');
                }
                if (!Schema::hasColumn('users', 'vai_tro')) {
                    $table->enum('vai_tro', ['admin', 'giang_vien', 'sinh_vien'])->default('sinh_vien')->after('password');
                }
            });
        }

        // 2. Bảng Danh mục Môn học (mon_hoc)
        Schema::create('mon_hoc', function (Blueprint $table) {
            $table->id();
            $table->string('ma_mon', 20)->unique();
            $table->string('ten_mon', 200);
            $table->integer('so_tin_chi')->default(3);
            $table->timestamps();
        });

        // 3. Bảng Lớp học phần & Phân công giảng dạy (lop_hoc_phan)
        Schema::create('lop_hoc_phan', function (Blueprint $table) {
            $table->id();
            $table->string('ma_lhp', 50)->unique();
            $table->foreignId('mon_hoc_id')->constrained('mon_hoc')->onDelete('restrict');
            $table->foreignId('giang_vien_id')->constrained('users')->onDelete('restrict');
            $table->string('hoc_ky', 20);
            $table->string('nam_hoc', 20);
            $table->string('phong_hoc', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lop_hoc_phan');
        Schema::dropIfExists('mon_hoc');
        // Chỉ drop users nếu bạn tạo mới hoàn toàn
        // Schema::dropIfExists('users');
    }
};
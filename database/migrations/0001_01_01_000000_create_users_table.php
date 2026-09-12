<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('ma_so', 20)->unique();          // MSSV / MSGV, dùng để đăng nhập
            $table->string('ho_ten', 100);
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('vai_tro', ['admin', 'giang_vien', 'sinh_vien'])->default('sinh_vien');
            $table->boolean('trang_thai')->default(true);   // false = khoá tài khoản
            $table->boolean('doi_mat_khau_lan_dau')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};

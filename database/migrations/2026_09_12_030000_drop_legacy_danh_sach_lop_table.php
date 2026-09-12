<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('danh_sach_lop');
    }

    public function down(): void
    {
        Schema::create('danh_sach_lop', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sinh_vien_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lop_hoc_phan_id')->constrained('lop_hoc_phan')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['sinh_vien_id', 'lop_hoc_phan_id']);
        });
    }
};
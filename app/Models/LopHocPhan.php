<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LopHocPhan extends Model
{
    use HasFactory;

    protected $table = 'lop_hoc_phan';

    protected $fillable = [
        'ma_lhp',
        'mon_hoc_id',
        'giang_vien_id',
        'hoc_ky',
        'nam_hoc',
        'phong_hoc',
    ];

    // Lớp học phần thuộc về 1 môn học
    public function monHoc(): BelongsTo
    {
        return $this->belongsTo(MonHoc::class, 'mon_hoc_id');
    }

    // Lớp học phần được giảng dạy bởi 1 giảng viên
    public function giangVien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'giang_vien_id');
    }

    // Danh sách sinh viên thuộc lớp học phần
    public function sinhViens(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'lop_hoc_phan_sinh_vien', // Tên bảng trung gian (pivot table) trong CSDL
            'lop_hoc_phan_id',        // Khóa ngoại của lớp học phần trong bảng trung gian
            'sinh_vien_id'            // Khóa ngoại của sinh viên trong bảng trung gian
        );
    }

    public function buoiHocs(): HasMany
    {
        return $this->hasMany(BuoiHoc::class, 'lop_hoc_phan_id');
    }
}
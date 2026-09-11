<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
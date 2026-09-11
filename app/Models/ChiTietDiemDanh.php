<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietDiemDanh extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_diem_danh';

    protected $fillable = [
        'buoi_hoc_id',
        'sinh_vien_id',
        'trang_thai',
    ];

    public function buoiHoc(): BelongsTo
    {
        return $this->belongsTo(BuoiHoc::class, 'buoi_hoc_id');
    }

    public function sinhVien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sinh_vien_id');
    }
}
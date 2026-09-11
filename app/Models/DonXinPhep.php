<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonXinPhep extends Model
{
    use HasFactory;

    protected $table = 'don_xin_phep';

    protected $fillable = [
        'sinh_vien_id',
        'buoi_hoc_id',
        'ly_do',
        'hinh_anh_minh_chung',
        'trang_thai',
    ];

    public function sinhVien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sinh_vien_id');
    }

    public function buoiHoc(): BelongsTo
    {
        return $this->belongsTo(BuoiHoc::class, 'buoi_hoc_id');
    }
}
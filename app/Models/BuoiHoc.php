<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuoiHoc extends Model
{
    use HasFactory;

    protected $table = 'buoi_hoc';

    protected $fillable = [
        'lop_hoc_phan_id',
        'ngay_hoc',
        'trang_thai',
    ];

    protected $casts = [
        'ngay_hoc' => 'datetime',
    ];

    public function lopHocPhan(): BelongsTo
    {
        return $this->belongsTo(LopHocPhan::class, 'lop_hoc_phan_id');
    }

    public function chiTietDiemDanhs(): HasMany
    {
        return $this->hasMany(ChiTietDiemDanh::class, 'buoi_hoc_id');
    }

    public function donXinPheps(): HasMany
    {
        return $this->hasMany(DonXinPhep::class, 'buoi_hoc_id');
    }
}
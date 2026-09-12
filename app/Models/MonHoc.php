<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonHoc extends Model
{
    use HasFactory;

    protected $table = 'mon_hoc';

    protected $fillable = [
        'ma_mon',
        'ten_mon',
        'so_tin_chi',
        'so_tiet_ly_thuyet',
        'so_tiet_thuc_hanh',
    ];

    public function lopHocPhans(): HasMany
    {
        return $this->hasMany(LopHocPhan::class, 'mon_hoc_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DanhSachLop extends Model
{
    use HasFactory;

    protected $table = 'lop_hoc_phan_sinh_vien';

    protected $fillable = [
        'sinh_vien_id',
        'lop_hoc_phan_id',
    ];

    public function sinhVien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sinh_vien_id');
    }

    public function lopHocPhan(): BelongsTo
    {
        return $this->belongsTo(LopHocPhan::class, 'lop_hoc_phan_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

protected $fillable = [
    'name',
    'ho_ten',
    'email',
    'password',
    'vai_tro',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Một giảng viên có thể phụ trách nhiều lớp học phần
    public function lopHocPhans(): HasMany
    {
        return $this->hasMany(LopHocPhan::class, 'giang_vien_id');
    }
}
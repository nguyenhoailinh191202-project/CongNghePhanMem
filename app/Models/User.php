<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const VAI_TRO_ADMIN = 'admin';
    public const VAI_TRO_GIANG_VIEN = 'giang_vien';
    public const VAI_TRO_SINH_VIEN = 'sinh_vien';

    protected $fillable = [
        'ma_so',
        'ho_ten',
        'email',
        'password',
        'vai_tro',
        'trang_thai',
        'doi_mat_khau_lan_dau',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'             => 'hashed',
            'trang_thai'           => 'boolean',
            'doi_mat_khau_lan_dau' => 'boolean',
        ];
    }

    public function laVaiTro(string ...$vaiTro): bool
    {
        return in_array($this->vai_tro, $vaiTro, true);
    }
}

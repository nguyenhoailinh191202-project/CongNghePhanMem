<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo 1 Tài khoản Admin
        User::create([
            'ma_so'    => 'ADMIN001',
            'ho_ten'   => 'Quản Trị Viên Khoa',
            'email'    => 'admin@caothang.edu.vn',
            'password' => Hash::make('12345678'),
            'vai_tro'  => 'admin',
        ]);

        // 2. Tạo 5 Giảng viên mẫu
        for ($i = 1; $i <= 5; $i++) {
            $msgv = 'GV' . str_pad($i, 3, '0', STR_PAD_LEFT);
            User::create([
                'ma_so'    => $msgv,
                'ho_ten'   => "Giảng Viên $i",
                'email'    => "giangvien{$i}@caothang.edu.vn",
                'password' => Hash::make('12345678'),
                'vai_tro'  => 'giang_vien',
            ]);
        }

        // 3. Tạo 50 Sinh viên mẫu
        for ($i = 1; $i <= 50; $i++) {
            $mssv = '0306' . str_pad($i, 4, '0', STR_PAD_LEFT);
            User::create([
                'ma_so'    => $mssv,
                'ho_ten'   => "Sinh Viên $i",
                'email'    => "{$mssv}@caothang.edu.vn",
                'password' => Hash::make('12345678'),
                'vai_tro'  => 'sinh_vien',
            ]);
        }
    }
}
<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    /**
     * Chuyển đổi từng hàng dữ liệu Excel thành Model User
     */
    public function model(array $row)
    {
        // Loại bỏ hàng trống
        $email = trim($row['email'] ?? '');
        if (empty($email)) {
            return null;
        }

        $roleCol = Schema::hasColumn('users', 'role') ? 'role' : 'vai_tro';
        $codeCol = Schema::hasColumn('users', 'code') ? 'code' : (Schema::hasColumn('users', 'mssv') ? 'mssv' : 'ma_sv');
        $nameCol = Schema::hasColumn('users', 'name') ? 'name' : 'ho_ten';

        $codeValue = trim($row['mssv'] ?? $row['code'] ?? $row['ma_sv'] ?? $row['mgv'] ?? '');
        $nameValue = trim($row['ho_ten'] ?? $row['name'] ?? $row['ho_va_ten'] ?? 'Chưa đặt tên');
        $roleValue = strtolower(trim($row['role'] ?? $row['vai_tro'] ?? 'sinh_vien'));
        $passwordVal = trim($row['mat_khau'] ?? $row['password'] ?? '12345678');

        if (!in_array($roleValue, ['sinh_vien', 'giang_vien', 'admin'])) {
            $roleValue = 'sinh_vien';
        }

        $attributes = ['email' => $email];
        $values = [
            $nameCol   => $nameValue,
            $roleCol   => $roleValue,
            'password' => Hash::make($passwordVal),
        ];

        if (Schema::hasColumn('users', $codeCol) && !empty($codeValue)) {
            $values[$codeCol] = $codeValue;
        }

        if (Schema::hasColumn('users', 'is_active')) {
            $values['is_active'] = true;
        } elseif (Schema::hasColumn('users', 'trang_thai')) {
            $values['trang_thai'] = 'hoat_dong';
        }

        return User::updateOrCreate($attributes, $values);
    }

    /**
     * Quy tắc Validate dữ liệu từng dòng
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }
}
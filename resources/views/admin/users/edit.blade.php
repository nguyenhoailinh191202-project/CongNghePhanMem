@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Chỉnh sửa tài khoản</h1>
            <p class="text-sm text-slate-500 mt-1">Cập nhật thông tin thông tin cá nhân và vai trò.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
            &larr; Hủy
        </a>
    </div>

    @php
        $codeVal = $user->code ?? $user->mssv ?? '';
        $nameVal = $user->name ?? $user->ho_ten ?? '';
        $roleVal = $user->role ?? $user->vai_tro ?? 'sinh_vien';
    @endphp

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Mã SV / MGV</label>
            <input type="text" name="code" value="{{ old('code', $codeVal) }}" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
            @error('code') <span class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Họ và tên <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $nameVal) }}" required class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
            @error('name') <span class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Địa chỉ Email <span class="text-rose-500">*</span></label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
            @error('email') <span class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Vai trò <span class="text-rose-500">*</span></label>
            <select name="role" required class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="sinh_vien" {{ old('role', $roleVal) == 'sinh_vien' ? 'selected' : '' }}>Sinh viên</option>
                <option value="giang_vien" {{ old('role', $roleVal) == 'giang_vien' ? 'selected' : '' }}>Giảng viên</option>
                <option value="admin" {{ old('role', $roleVal) == 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
            </select>
            @error('role') <span class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Đổi mật khẩu mới (Nếu có)</label>
            <input type="password" name="password" placeholder="Để trống nếu giữ nguyên mật khẩu cũ" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
            @error('password') <span class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm rounded-xl transition-colors">Hủy</a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-colors shadow-sm">
                Cập nhật
            </button>
        </div>
    </form>
</div>
@endsection
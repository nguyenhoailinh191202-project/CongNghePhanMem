@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Thêm tài khoản mới</h1>
            <p class="text-sm text-slate-500 mt-1">Tạo thủ công tài khoản Quản trị viên, Giảng viên hoặc Sinh viên.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
            &larr; Hủy
        </a>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Mã SV / MGV (MSSV hoặc Mã Giảng viên)</label>
            <input type="text" name="code" value="{{ old('code') }}" placeholder="VD: SV2026001 hoặc GV202601" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
            @error('code') <span class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Họ và tên <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="VD: Nguyễn Văn An" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
            @error('name') <span class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Địa chỉ Email <span class="text-rose-500">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required placeholder="an.nguyen@student.edu.vn" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
            @error('email') <span class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Vai trò hệ thống <span class="text-rose-500">*</span></label>
            <select name="role" required class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="sinh_vien" {{ old('role') == 'sinh_vien' ? 'selected' : '' }}>Sinh viên</option>
                <option value="giang_vien" {{ old('role') == 'giang_vien' ? 'selected' : '' }}>Giảng viên</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
            </select>
            @error('role') <span class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Mật khẩu (Mặc định: 12345678)</label>
            <input type="password" name="password" placeholder="Để trống nếu muốn lấy mật khẩu mặc định 12345678" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
            @error('password') <span class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm rounded-xl transition-colors">Hủy</a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-colors shadow-sm">
                Lưu tài khoản
            </button>
        </div>
    </form>
</div>
@endsection
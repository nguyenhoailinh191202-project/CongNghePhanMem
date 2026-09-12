@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Quản lý Tài khoản Người dùng</h1>
            <p class="text-sm text-slate-500 mt-1">
                Hiển thị tổng cộng <strong class="text-slate-800">{{ $users->total() }}</strong> tài khoản trên hệ thống.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.import') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import Excel
            </a>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Thêm tài khoản
            </a>
        </div>
    </div>

    <!-- Thông báo Alert -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <!-- Form Tìm kiếm & Bộ lọc -->
    <form method="GET" action="{{ route('admin.users.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between">
        <div class="flex-1 flex flex-col sm:flex-row gap-3 w-full">
            <!-- Ô Tìm kiếm -->
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo Họ tên, Email, Mã số..." class="w-full pl-10 pr-4 py-2 rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            
            <!-- Lọc Vai trò -->
            <select name="role" class="rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500 min-w-[160px]" onchange="this.form.submit()">
                <option value="">-- Tất cả vai trò --</option>
                <option value="sinh_vien" {{ request('role') == 'sinh_vien' ? 'selected' : '' }}>Sinh viên</option>
                <option value="giang_vien" {{ request('role') == 'giang_vien' ? 'selected' : '' }}>Giảng viên</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
            </select>

            <!-- Chọn số lượng hiển thị -->
            <select name="per_page" class="rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500 min-w-[140px]" onchange="this.form.submit()">
                <option value="10" {{ request('per_page', 10) == '10' ? 'selected' : '' }}>10 / trang</option>
                <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 / trang</option>
                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 / trang</option>
                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 / trang</option>
                <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Xem tất cả</option>
            </select>
        </div>

        <div class="flex gap-2 w-full md:w-auto justify-end">
            <button type="submit" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm rounded-xl transition-colors">
                Lọc
            </button>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm rounded-xl transition-colors">
                Xóa lọc
            </a>
        </div>
    </form>

    <!-- Bảng danh sách tài khoản -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-xs uppercase">
                        <th class="py-3.5 px-4">STT</th>
                        <th class="py-3.5 px-4">Mã / MSSV</th>
                        <th class="py-3.5 px-4">Họ và tên</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4 text-center">Vai trò</th>
                        <th class="py-3.5 px-4 text-center">Trạng thái</th>
                        <th class="py-3.5 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $index => $u)
                        @php
                            // 1. Mã số
                            $codeVal = trim($u->ma_so ?? $u->code ?? $u->mssv ?? $u->ma_sv ?? '');
                            if (empty($codeVal)) {
                                $codeVal = 'SV' . str_pad($u->id, 4, '0', STR_PAD_LEFT);
                            }

                            // 2. Họ tên
                            $nameVal = $u->ho_ten ?? $u->name ?? $u->full_name ?? 'N/A';

                            // 3. Xử lý nhận diện vai trò đa năng
                            $roleRaw = strtolower(trim($u->role ?? $u->vai_tro ?? $u->chuc_vu ?? $u->type ?? ''));
                            if (empty($roleRaw) && method_exists($u, 'getRoleNames') && $u->getRoleNames()->isNotEmpty()) {
                                $roleRaw = strtolower($u->getRoleNames()->first());
                            }

                            // 4. Trạng thái
                            $isActive = $u->is_active ?? ($u->trang_thai === 'hoat_dong' || $u->trang_thai == 1 || $u->trang_thai === 'active');
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 text-slate-400 font-medium">{{ $users->firstItem() + $index }}</td>
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">{{ $codeVal }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $nameVal }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $u->email }}</td>
                            <td class="py-3.5 px-4 text-center">
                                @if(\Illuminate\Support\Str::contains($roleRaw, ['admin', 'quan_tri', 'quản trị', 'administrator', '1']))
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700">Admin</span>
                                @elseif(\Illuminate\Support\Str::contains($roleRaw, ['giang_vien', 'giảng viên', 'lecturer', 'teacher', 'gv', '2']))
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">Giảng viên</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">Sinh viên</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($isActive)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Hoạt động
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Đã khóa
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Reset Password -->
                                    <form action="{{ route('admin.users.reset-password', $u->id) }}" method="POST" class="inline-flex items-center" onsubmit="return confirm('Reset mật khẩu tài khoản này về 12345678?')">
                                        @csrf
                                        <button type="submit" class="p-2 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors inline-flex items-center justify-center" title="Reset mật khẩu">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z"></path></svg>
                                        </button>
                                    </form>

                                    <!-- Lock / Unlock -->
                                    <form action="{{ route('admin.users.toggle-status', $u->id) }}" method="POST" class="inline-flex items-center">
                                        @csrf
                                        <button type="submit" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors inline-flex items-center justify-center" title="{{ $isActive ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}">
                                            @if($isActive)
                                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                            @else
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                                            @endif
                                        </button>
                                    </form>

                                    <!-- Edit -->
                                    <a href="{{ route('admin.users.edit', $u->id) }}" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors inline-flex items-center justify-center" title="Chỉnh sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="inline-flex items-center" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors inline-flex items-center justify-center" title="Xóa tài khoản">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Không tìm thấy tài khoản nào phù hợp.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages() && request('per_page') !== 'all')
            <div class="p-4 bg-slate-50 border-t border-slate-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
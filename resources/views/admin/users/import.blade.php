@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Import danh sách từ file Excel</h1>
            <p class="text-sm text-slate-500 mt-1">Nạp nhanh hàng loạt tài khoản Sinh viên / Giảng viên đầu năm học.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
            &larr; Quay lại
        </a>
    </div>

    <!-- Thông báo lỗi nếu có -->
    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <!-- Khối tải file mẫu -->
    <div class="p-5 bg-blue-50/70 border border-blue-200 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-blue-600 text-white rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">Tải file Excel mẫu chuẩn</h3>
                <p class="text-xs text-slate-500">File đã được định dạng chuẩn tiêu đề tiêu chuẩn (mssv, ho_ten, email, role).</p>
            </div>
        </div>
        <a href="{{ route('admin.users.download-sample') }}" class="whitespace-nowrap px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-sm transition-colors">
            &darr; Tải mau_import_sinh_vien.xlsx
        </a>
    </div>

    <!-- Upload Form -->
    <form action="{{ route('admin.users.import.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
        @csrf

        <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-700">Chọn file Excel (.xlsx, .xls, .csv)</label>
            <input type="file" name="file" required accept=".xlsx,.xls,.csv" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-xl cursor-pointer">
            @error('file') <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Bảng Hướng dẫn cột chuẩn trong file Excel -->
        <div class="space-y-2">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Cấu trúc các cột trong file Excel:</h4>
            <div class="overflow-hidden border border-slate-200 rounded-xl text-xs">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Tên cột (Header)</th>
                            <th class="py-2.5 px-3">Mô tả</th>
                            <th class="py-2.5 px-3">Bắt buộc</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        <tr>
                            <td class="py-2 px-3 font-mono font-bold text-slate-800">mssv</td>
                            <td class="py-2 px-3">Mã SV hoặc Mã Giảng viên (VD: SV2026001)</td>
                            <td class="py-2 px-3 text-emerald-600 font-semibold">Nên có</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-mono font-bold text-slate-800">ho_ten</td>
                            <td class="py-2 px-3">Họ và tên đầy đủ</td>
                            <td class="py-2 px-3 text-rose-600 font-semibold">Có</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-mono font-bold text-slate-800">email</td>
                            <td class="py-2 px-3">Địa chỉ email (Duy nhất)</td>
                            <td class="py-2 px-3 text-rose-600 font-semibold">Có</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-mono font-bold text-slate-800">role</td>
                            <td class="py-2 px-3"><code class="bg-slate-100 px-1 rounded">sinh_vien</code>, <code class="bg-slate-100 px-1 rounded">giang_vien</code>, hoặc <code class="bg-slate-100 px-1 rounded">admin</code></td>
                            <td class="py-2 px-3 text-slate-400">Mặc định: sinh_vien</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-mono font-bold text-slate-800">mat_khau</td>
                            <td class="py-2 px-3">Mật khẩu ban đầu</td>
                            <td class="py-2 px-3 text-slate-400">Mặc định: 12345678</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm rounded-xl transition-colors">Hủy</a>
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl transition-colors shadow-sm">
                Bắt đầu Import dữ liệu
            </button>
        </div>
    </form>
</div>
@endsection
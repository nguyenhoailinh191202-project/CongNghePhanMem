@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Quản lý môn học</h1>
            <p class="mt-1 text-sm text-slate-500">Chuẩn hóa thông tin môn học dùng trong phân công giảng dạy.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.subjects.store') }}" class="grid gap-4 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm md:grid-cols-5">
        @csrf
        <div><label class="mb-1 block text-xs font-semibold text-slate-700">Mã môn học *</label><input name="ma_mon" value="{{ old('ma_mon') }}" required class="w-full rounded-xl border-slate-200 text-sm"></div>
        <div class="md:col-span-2"><label class="mb-1 block text-xs font-semibold text-slate-700">Tên môn học *</label><input name="ten_mon" value="{{ old('ten_mon') }}" required class="w-full rounded-xl border-slate-200 text-sm"></div>
        <div><label class="mb-1 block text-xs font-semibold text-slate-700">Tín chỉ *</label><input type="number" name="so_tin_chi" value="{{ old('so_tin_chi', 3) }}" min="0" required class="w-full rounded-xl border-slate-200 text-sm"></div>
        <div class="flex items-end"><button class="w-full rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Thêm môn học</button></div>
        <div><label class="mb-1 block text-xs font-semibold text-slate-700">Tiết lý thuyết *</label><input type="number" name="so_tiet_ly_thuyet" value="{{ old('so_tiet_ly_thuyet', 0) }}" min="0" required class="w-full rounded-xl border-slate-200 text-sm"></div>
        <div><label class="mb-1 block text-xs font-semibold text-slate-700">Tiết thực hành *</label><input type="number" name="so_tiet_thuc_hanh" value="{{ old('so_tiet_thuc_hanh', 0) }}" min="0" required class="w-full rounded-xl border-slate-200 text-sm"></div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase text-slate-600"><tr><th class="px-4 py-3">Mã môn</th><th class="px-4 py-3">Tên môn</th><th class="px-4 py-3 text-center">TC</th><th class="px-4 py-3 text-center">LT</th><th class="px-4 py-3 text-center">TH</th><th class="px-4 py-3 text-right">Thao tác</th></tr></thead><tbody class="divide-y divide-slate-100">
        @forelse($subjects as $subject)
            <tr class="hover:bg-slate-50"><td class="px-4 py-3 font-mono font-semibold">{{ $subject->ma_mon }}</td><td class="px-4 py-3 font-semibold">{{ $subject->ten_mon }}</td><td class="px-4 py-3 text-center">{{ $subject->so_tin_chi }}</td><td class="px-4 py-3 text-center">{{ $subject->so_tiet_ly_thuyet }}</td><td class="px-4 py-3 text-center">{{ $subject->so_tiet_thuc_hanh }}</td><td class="px-4 py-3 text-right"><details class="inline-block text-left"><summary class="cursor-pointer rounded-lg px-3 py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-50">Sửa</summary><form method="POST" action="{{ route('admin.subjects.update', $subject) }}" class="mt-2 w-72 space-y-2 rounded-xl border border-slate-200 bg-white p-3 shadow-lg">@csrf @method('PUT')<input name="ma_mon" value="{{ $subject->ma_mon }}" required class="w-full rounded-lg border-slate-200 text-sm"><input name="ten_mon" value="{{ $subject->ten_mon }}" required class="w-full rounded-lg border-slate-200 text-sm"><div class="grid grid-cols-3 gap-2"><input type="number" name="so_tin_chi" value="{{ $subject->so_tin_chi }}" min="0" required class="w-full rounded-lg border-slate-200 text-sm"><input type="number" name="so_tiet_ly_thuyet" value="{{ $subject->so_tiet_ly_thuyet }}" min="0" required class="w-full rounded-lg border-slate-200 text-sm"><input type="number" name="so_tiet_thuc_hanh" value="{{ $subject->so_tiet_thuc_hanh }}" min="0" required class="w-full rounded-lg border-slate-200 text-sm"></div><button class="w-full rounded-lg bg-blue-600 py-2 text-xs font-semibold text-white">Lưu thay đổi</button></form></details><form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}" class="ml-2 inline" onsubmit="return confirm('Xóa môn học này?')">@csrf @method('DELETE')<button class="text-xs font-semibold text-rose-600 hover:underline">Xóa</button></form></td></tr>
        @empty
            <tr><td colspan="6" class="px-4 py-10 text-center text-slate-400">Chưa có môn học.</td></tr>
        @endforelse
        </tbody></table></div>
        @if($subjects->hasPages())<div class="border-t border-slate-200 bg-slate-50 p-4">{{ $subjects->links() }}</div>@endif
    </div>
</div>
@endsection

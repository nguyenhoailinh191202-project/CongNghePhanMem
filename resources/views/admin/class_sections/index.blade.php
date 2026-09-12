@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div><h1 class="text-2xl font-bold text-slate-900">Quản lý lớp học phần</h1><p class="mt-1 text-sm text-slate-500">Phân công giảng viên và quản lý danh sách sinh viên theo từng học kỳ.</p></div>
        <a href="{{ route('admin.class-sections.create') }}" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">+ Tạo lớp học phần</a>
    </div>

    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-800">{{ session('error') }}</div>@endif

    <form method="GET" action="{{ route('admin.class-sections.index') }}" class="flex flex-col gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm sm:flex-row sm:items-end">
        <div><label class="mb-1 block text-xs font-semibold text-slate-700">Lọc theo học kỳ</label><select name="hoc_ky" class="min-w-48 rounded-xl border-slate-200 text-sm"><option value="">Tất cả học kỳ</option>@foreach($semesters as $semester)<option value="{{ $semester }}" @selected(request('hoc_ky') === $semester)>{{ $semester }}</option>@endforeach</select></div>
        <button class="rounded-xl bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-900">Lọc</button>
        <a href="{{ route('admin.class-sections.index') }}" class="rounded-xl bg-slate-100 px-4 py-2.5 text-center text-sm font-semibold text-slate-600 hover:bg-slate-200">Xóa lọc</a>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm"><div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase text-slate-600"><tr><th class="px-4 py-3">Mã lớp</th><th class="px-4 py-3">Môn học</th><th class="px-4 py-3">Học kỳ</th><th class="px-4 py-3">Giảng viên</th><th class="px-4 py-3">Phòng</th><th class="px-4 py-3 text-center">Buổi / SV</th><th class="px-4 py-3 text-right">Thao tác</th></tr></thead><tbody class="divide-y divide-slate-100">
        @forelse($classSections as $classSection)
            <tr class="hover:bg-slate-50"><td class="px-4 py-3 font-mono font-semibold text-slate-900">{{ $classSection->ma_lhp }}</td><td class="px-4 py-3"><span class="font-semibold">{{ $classSection->monHoc->ten_mon }}</span><span class="block text-xs text-slate-400">{{ $classSection->monHoc->ma_mon }}</span></td><td class="px-4 py-3">{{ $classSection->hoc_ky }}<span class="block text-xs text-slate-400">{{ $classSection->nam_hoc }}</span></td><td class="px-4 py-3">{{ $classSection->giangVien->ho_ten }}</td><td class="px-4 py-3">{{ $classSection->phong_hoc }}</td><td class="px-4 py-3 text-center">{{ $classSection->buoi_hocs_count }} / {{ $classSection->sinh_viens_count }}</td><td class="px-4 py-3 text-right"><a href="{{ route('admin.class-sections.enroll', $classSection) }}" class="text-xs font-semibold text-blue-600 hover:underline">Ghi danh</a><form method="POST" action="{{ route('admin.class-sections.destroy', $classSection) }}" class="ml-3 inline" onsubmit="return confirm('Xóa lớp học phần và các buổi học liên quan?')">@csrf @method('DELETE')<button class="text-xs font-semibold text-rose-600 hover:underline">Xóa</button></form></td></tr>
        @empty
            <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">Chưa có lớp học phần.</td></tr>
        @endforelse
        </tbody></table></div>@if($classSections->hasPages())<div class="border-t border-slate-200 bg-slate-50 p-4">{{ $classSections->links() }}</div>@endif</div>
</div>
@endsection

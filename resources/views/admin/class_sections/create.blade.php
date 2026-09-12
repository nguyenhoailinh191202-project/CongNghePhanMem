@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div><h1 class="text-2xl font-bold text-slate-900">Tạo lớp học phần</h1><p class="mt-1 text-sm text-slate-500">Chọn môn, giảng viên và lịch học để hệ thống tạo 15 buổi học.</p></div>
    @if($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"><ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <form method="POST" action="{{ route('admin.class-sections.store') }}" class="space-y-6 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
        @csrf
        <div class="grid gap-4 md:grid-cols-2">
            <div><label class="mb-1 block text-xs font-semibold text-slate-700">Mã lớp học phần *</label><input name="ma_lhp" value="{{ old('ma_lhp') }}" required placeholder="VD: LHP_WEB01" class="w-full rounded-xl border-slate-200 text-sm"></div>
            <div><label class="mb-1 block text-xs font-semibold text-slate-700">Môn học *</label><select name="mon_hoc_id" required class="w-full rounded-xl border-slate-200 text-sm"><option value="">-- Chọn môn học --</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}" @selected(old('mon_hoc_id') == $subject->id)>{{ $subject->ma_mon }} - {{ $subject->ten_mon }}</option>@endforeach</select></div>
            <div><label class="mb-1 block text-xs font-semibold text-slate-700">Học kỳ *</label><input name="hoc_ky" value="{{ old('hoc_ky', 'HK1') }}" required placeholder="HK1" class="w-full rounded-xl border-slate-200 text-sm"></div>
            <div><label class="mb-1 block text-xs font-semibold text-slate-700">Năm học *</label><input name="nam_hoc" value="{{ old('nam_hoc', '2026-2027') }}" required class="w-full rounded-xl border-slate-200 text-sm"></div>
            <div><label class="mb-1 block text-xs font-semibold text-slate-700">Phòng học *</label><input name="phong_hoc" value="{{ old('phong_hoc') }}" required placeholder="VD: A1.01" class="w-full rounded-xl border-slate-200 text-sm"></div>
            <div><label class="mb-1 block text-xs font-semibold text-slate-700">Giảng viên phụ trách *</label><select name="giang_vien_id" required class="w-full rounded-xl border-slate-200 text-sm"><option value="">-- Chọn giảng viên --</option>@foreach($lecturers as $lecturer)<option value="{{ $lecturer->id }}" @selected(old('giang_vien_id') == $lecturer->id)>{{ $lecturer->ho_ten }} - {{ $lecturer->email }}</option>@endforeach</select></div>
        </div>
        <div class="border-t border-slate-100 pt-5"><h2 class="text-sm font-bold text-slate-900">Lịch học tạo tự động</h2><p class="mt-1 text-xs text-slate-500">Hệ thống tạo 15 buổi, cách nhau 1 tuần, vào đúng thứ và tiết đã chọn.</p><div class="mt-4 grid gap-4 md:grid-cols-3"><div><label class="mb-1 block text-xs font-semibold text-slate-700">Ngày bắt đầu *</label><input type="date" name="ngay_bat_dau" value="{{ old('ngay_bat_dau') }}" required class="w-full rounded-xl border-slate-200 text-sm"></div><div><label class="mb-1 block text-xs font-semibold text-slate-700">Thứ *</label><select name="thu" required class="w-full rounded-xl border-slate-200 text-sm">@foreach([1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5', 5 => 'Thứ 6', 6 => 'Thứ 7', 7 => 'Chủ nhật'] as $day => $label)<option value="{{ $day }}" @selected(old('thu') == $day)>{{ $label }}</option>@endforeach</select></div><div><label class="mb-1 block text-xs font-semibold text-slate-700">Tiết bắt đầu *</label><select name="tiet_bat_dau" required class="w-full rounded-xl border-slate-200 text-sm">@for($period = 1; $period <= 10; $period++)<option value="{{ $period }}" @selected(old('tiet_bat_dau') == $period)>Tiết {{ $period }}</option>@endfor</select></div></div></div>
        <div class="flex justify-end gap-2 border-t border-slate-100 pt-5"><a href="{{ route('admin.class-sections.index') }}" class="rounded-xl bg-slate-100 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-200">Hủy</a><button class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Tạo lớp và lịch học</button></div>
    </form>
</div>
@endsection

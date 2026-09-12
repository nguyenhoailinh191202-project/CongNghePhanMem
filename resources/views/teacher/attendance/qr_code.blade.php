@extends('layouts.app')

@section('title', 'Điểm danh QR Code')
@section('page-title', 'Điểm danh bằng QR Code')
@section('page-subtitle', ($buoiHoc->lopHocPhan->monHoc->ten_mon ?? 'Môn học') . ' · ' . $buoiHoc->ngay_hoc->format('d/m/Y H:i'))

@section('page-actions')
    <a href="{{ route('teacher.attendance.sheet', $buoiHoc) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        Bảng điểm danh
    </a>
@endsection

@section('content')
<div id="attendance-qr" class="mx-auto max-w-xl rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm sm:p-8"
     data-token-url="{{ route('teacher.attendance.token', $buoiHoc) }}"
     data-qr-size="280">
    <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-700">
        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5h4.5V9h-4.5V4.5zm12 0h4.5V9h-4.5V4.5zM3.75 15h4.5v4.5h-4.5V15zm9-1.5h1.5v1.5h-1.5v-1.5zm3 3h1.5V18h-1.5v-1.5zm-3 0h1.5V18h-1.5v-1.5zm3-3h1.5V15h-1.5v-1.5z" /></svg>
    </div>
    <h2 class="text-lg font-bold text-slate-900">Mã QR điểm danh</h2>
    <p class="mt-1 text-sm text-slate-500">Sinh viên quét mã bằng điện thoại. Mã tự đổi sau mỗi 30 giây.</p>

    <div class="mx-auto mt-6 flex min-h-[300px] items-center justify-center rounded-xl bg-slate-50 p-3">
        <canvas id="attendance-qr-canvas" class="max-w-full rounded-lg bg-white p-2"></canvas>
        <p id="attendance-qr-status" class="hidden text-sm text-rose-600">Không thể tạo mã QR. Vui lòng thử lại.</p>
    </div>

    <div class="mt-5 flex items-center justify-center gap-2 text-sm text-slate-500">
        <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>
        Mã mới trong <strong id="attendance-qr-countdown" class="text-slate-800">30</strong> giây
    </div>
    <p id="attendance-qr-token" class="mt-3 break-all font-mono text-[11px] text-slate-400"></p>
</div>
@endsection

@push('scripts')
@vite('resources/js/attendance_qr.js')
@endpush

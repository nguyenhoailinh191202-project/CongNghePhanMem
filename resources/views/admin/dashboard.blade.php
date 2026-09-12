@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Tiêu đề trang -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Thống kê tổng quan</h1>
            <p class="text-sm text-slate-500 mt-1">Báo cáo hiệu suất chuyên cần và các chỉ số vận hành toàn hệ thống.</p>
        </div>
        <a href="{{ route('admin.warnings.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-50 text-rose-700 hover:bg-rose-100 font-semibold text-sm rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            Xem danh sách cảnh báo
        </a>
    </div>

    <!-- 4 Thẻ KPI Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tổng sinh viên</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalStudents) }}</h3>
            </div>
            <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tổng giảng viên</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalLecturers) }}</h3>
            </div>
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Lớp học phần</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($activeClassesCount) }}</h3>
            </div>
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-6v6m4-5v5m6-5v6c0 1.1-.9 2-2 2H4a2 2 0 01-2-2V7c0-1.1.9-2 2-2h4l2-2h4l2 2h4a2 2 0 012 2v11z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tỷ lệ vắng trung bình</p>
                <h3 class="text-2xl font-bold text-rose-600 mt-1">{{ $avgAbsenceRate }}%</h3>
            </div>
            <div class="p-3 bg-rose-50 text-rose-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
            </div>
        </div>
    </div>

    <!-- Biểu đồ Chart.js -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Biểu đồ Tỷ lệ Chuyên cần theo Môn học</h2>
                <p class="text-xs text-slate-500 mt-0.5">So sánh tỷ lệ đi học và vắng mặt giữa các môn</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg">Đơn vị: %</span>
        </div>
        <div class="relative w-full h-80">
            <canvas id="attendanceChart" data-chart="{{ json_encode($chartData) }}"></canvas>
        </div>
    </div>
</div>

<!-- Nạp Thư viện Chart.js & File JS khởi tạo -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@vite(['resources/js/dashboard_charts.js'])
@endsection
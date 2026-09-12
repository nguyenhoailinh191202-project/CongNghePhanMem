@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Cảnh báo sinh viên vắng học (&ge; 20%)</h1>
            <p class="text-sm text-slate-500 mt-1">Danh sách sinh viên vi phạm quy định số buổi vắng nguy cơ cấm thi.</p>
        </div>
        <div class="px-3.5 py-1.5 bg-rose-100 text-rose-800 text-xs font-bold rounded-full border border-rose-200">
            Cảnh báo: {{ count($warningList) }} sinh viên
        </div>
    </div>

    <!-- Bộ lọc đa năng -->
    <form method="GET" action="{{ route('admin.warnings.index') }}" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Môn học</label>
            <select name="mon_hoc_id" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">-- Tất cả môn học --</option>
                @foreach($subjects as $mon)
                    <option value="{{ $mon->id }}" {{ request('mon_hoc_id') == $mon->id ? 'selected' : '' }}>
                        {{ $mon->ten_mon ?? $mon->ten_mon_hoc }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Lớp học phần</label>
            <select name="lop_hoc_phan_id" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">-- Tất cả lớp học phần --</option>
                @foreach($classes as $lhp)
                    <option value="{{ $lhp->id }}" {{ request('lop_hoc_phan_id') == $lhp->id ? 'selected' : '' }}>
                        {{ $lhp->ma_lhp }} - {{ $lhp->ten_lop_hoc_phan ?? $lhp->monHoc->ten_mon ?? '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Học kỳ</label>
            <select name="hoc_ky" class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">-- Tất cả học kỳ --</option>
                @foreach($semesters as $hk)
                    <option value="{{ $hk }}" {{ request('hoc_ky') == $hk ? 'selected' : '' }}>{{ $hk }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition-colors shadow-sm">
                Lọc dữ liệu
            </button>
            <a href="{{ route('admin.warnings.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm rounded-xl transition-colors">
                Xóa
            </a>
        </div>
    </form>

    <!-- Bảng thông tin chi tiết -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold text-xs uppercase">
                        <th class="py-3.5 px-4">MSSV</th>
                        <th class="py-3.5 px-4">Họ và tên</th>
                        <th class="py-3.5 px-4">Môn học / Lớp HP</th>
                        <th class="py-3.5 px-4 text-center">Buổi vắng / Tổng buổi</th>
                        <th class="py-3.5 px-4 text-center">Tỷ lệ vắng</th>
                        <th class="py-3.5 px-4">Giảng viên phụ trách</th>
                        <th class="py-3.5 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($warningList as $row)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-semibold text-slate-900">{{ $row->mssv }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $row->student_name }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-slate-900 block">{{ $row->ten_mon }}</span>
                                <span class="text-xs text-slate-400 font-mono">{{ $row->ma_lhp }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-medium">
                                <span class="text-rose-600 font-bold">{{ $row->absent_sessions }}</span> / {{ $row->total_sessions }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                                    {{ $row->absence_rate }}%
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-medium">{{ $row->lecturer_name }}</td>
                            <td class="py-3.5 px-4 text-right">
                                @if(Route::has('admin.attendance.show'))
                                    <a href="{{ route('admin.attendance.show', $row->lop_hoc_phan_id) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                        Chi tiết điểm danh &rarr;
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                Không có sinh viên nào vi phạm ngưỡng cảnh báo vắng (&ge; 20%).
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
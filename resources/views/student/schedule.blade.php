@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- TIÊU ĐỀ & BỘ LỌC --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Thời khoá biểu</h1>
            <p class="text-sm text-slate-500 mt-1">Theo dõi lịch học chi tiết theo từng học kỳ và tuần học.</p>
        </div>
        <form method="GET" action="{{ route('student.schedule.index') }}" class="flex items-center gap-3">
            <select name="semester_id" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach($semesters ?? [] as $sem)
                    <option value="{{ $sem['id'] }}" {{ ($selectedSemester ?? 1) == $sem['id'] ? 'selected' : '' }}>
                        {{ $sem['name'] }}
                    </option>
                @endforeach
            </select>
            <select name="week" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach($weeks ?? [] as $w)
                    <option value="{{ $w }}" {{ ($selectedWeek ?? 10) == $w ? 'selected' : '' }}>
                        Tuần {{ $w }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- BẢNG THỜI KHÓA BIỂU --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-slate-800 text-base">Lịch học trong tuần</h2>
            <span class="text-xs font-medium text-slate-500">Cập nhật từ nhà trường</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                        <th class="py-3.5 px-5">Thứ / Ngày</th>
                        <th class="py-3.5 px-5">Môn học & Mã HP</th>
                        <th class="py-3.5 px-5">Thời gian / Tiết</th>
                        <th class="py-3.5 px-5">Phòng học</th>
                        <th class="py-3.5 px-5">Giảng viên</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse($schedules ?? [] as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-5">
                                <span class="font-semibold text-slate-800 block">{{ $item['day_of_week'] }}</span>
                                <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</span>
                            </td>
                            <td class="py-4 px-5">
                                <span class="font-semibold text-slate-800 block">{{ $item['course_name'] }}</span>
                                <span class="text-xs text-slate-400">Mã LHP: {{ $item['course_code'] }}</span>
                            </td>
                            <td class="py-4 px-5 font-medium text-slate-600">{{ $item['period'] }}</td>
                            <td class="py-4 px-5 font-semibold text-blue-600">{{ $item['room'] }}</td>
                            <td class="py-4 px-5 text-slate-600 font-medium">{{ $item['lecturer'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-sm">
                                Không có lịch học trong tuần đã chọn.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
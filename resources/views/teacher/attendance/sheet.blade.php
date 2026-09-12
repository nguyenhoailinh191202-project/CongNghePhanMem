@extends('layouts.app')

@section('title', 'Điểm danh lớp')
@section('page-title', 'Điểm danh lớp')
@section('page-subtitle', ($buoiHoc->lopHocPhan->monHoc->ten_mon ?? 'Môn học') . ' · ' . $buoiHoc->ngay_hoc->format('d/m/Y H:i'))

@section('page-actions')
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('teacher.export-attendance.excel', $buoiHoc->lop_hoc_phan_id) }}"
           class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100">
            Excel
        </a>
        <a href="{{ route('teacher.export-attendance.pdf', $buoiHoc->lop_hoc_phan_id) }}"
           class="inline-flex items-center gap-2 rounded-lg border border-rose-200 bg-rose-50 px-3.5 py-2.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-100">
            PDF
        </a>
        <a href="{{ route('teacher.attendance.qr', $buoiHoc) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5h4.5V9h-4.5V4.5zm12 0h4.5V9h-4.5V4.5zM3.75 15h4.5v4.5h-4.5V15zm9-1.5h1.5v1.5h-1.5v-1.5zm3 3h1.5V18h-1.5v-1.5zm-3 0h1.5V18h-1.5v-1.5zm3-3h1.5V15h-1.5v-1.5z" />
        </svg>
        Bật QR Code
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-5">
    <form method="POST" action="{{ route('teacher.attendance.save', $buoiHoc) }}" id="attendance-form">
        @csrf
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <p class="text-sm font-semibold text-slate-800">{{ $buoiHoc->lopHocPhan->ma_lhp }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">{{ $students->count() }} sinh viên trong lớp</p>
                </div>
                <button type="button" id="mark-all-present"
                        class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12.75l4.5 4.5L19 7.75" /></svg>
                    Đánh dấu tất cả có mặt
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">#</th>
                            <th class="px-5 py-3 font-semibold">Họ tên</th>
                            <th class="px-5 py-3 font-semibold">MSSV</th>
                            <th class="px-5 py-3 text-center font-semibold">Có mặt</th>
                            <th class="px-5 py-3 text-center font-semibold">Vắng không phép</th>
                            <th class="px-5 py-3 text-center font-semibold">Vắng có phép</th>
                            <th class="px-5 py-3 text-center font-semibold">Đi trễ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($students as $index => $student)
                            @php($status = $attendance[$student->id] ?? 'co_mat')
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-5 py-3.5 text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $student->ho_ten }}</td>
                                <td class="px-5 py-3.5 font-mono text-xs text-slate-500">{{ $student->ma_so }}</td>
                                @foreach(['co_mat' => 'emerald', 'vang_mat' => 'rose', 'vang_co_phep' => 'amber', 'di_tre' => 'blue'] as $value => $color)
                                    <td class="px-5 py-3.5 text-center">
                                        <input type="radio" name="attendance[{{ $student->id }}]" value="{{ $value }}"
                                               @checked($status === $value)
                                               class="attendance-radio h-4 w-4 border-slate-300 text-{{ $color }}-600 focus:ring-{{ $color }}-500">
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end border-t border-slate-100 bg-slate-50/50 px-5 py-4">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12.75l4.5 4.5L19 7.75" /></svg>
                    Lưu bảng điểm danh
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('mark-all-present')?.addEventListener('click', function () {
        document.querySelectorAll('input[value="co_mat"]').forEach((radio) => radio.checked = true);
    });
</script>
@endpush

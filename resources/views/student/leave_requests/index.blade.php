@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- TIÊU ĐỀ & NÚT TẠO ĐƠN --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Lịch sử đơn xin nghỉ</h1>
            <p class="text-sm text-slate-500 mt-1">Quản lý và theo dõi trạng thái các đơn xin nghỉ học đã gửi.</p>
        </div>
        <div>
            <a href="{{ route('student.leave-requests.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-md shadow-blue-500/20 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                 Nộp đơn xin nghỉ
            </a>
        </div>
    </div>

    {{-- BẢNG DANH SÁCH ĐƠN --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                        <th class="py-3.5 px-5">STT</th>
                        <th class="py-3.5 px-5">Buổi học & Môn học</th>
                        <th class="py-3.5 px-5">Ngày xin nghỉ</th>
                        <th class="py-3.5 px-5">Lý do</th>
                        <th class="py-3.5 px-5">Minh chứng</th>
                        <th class="py-3.5 px-5">Trạng thái</th>
                        <th class="py-3.5 px-5">Ngày gửi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse($leaveRequests as $index => $req)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-5 font-medium text-slate-400">{{ $leaveRequests->firstItem() + $index }}</td>
                            <td class="py-4 px-5 font-semibold text-slate-800">
                                {{ $req->buoiHoc?->lopHocPhan?->monHoc?->ten_mon ?? 'N/A' }}
                            </td>
                            <td class="py-4 px-5 text-slate-600">
                                {{ $req->buoiHoc?->ngay_hoc?->format('d/m/Y') ?? 'N/A' }}
                            </td>
                            <td class="py-4 px-5 text-slate-600">{{ $req->ly_do ?? 'N/A' }}</td>
                            <td class="py-4 px-5">
                                @if(!empty($req->hinh_anh_minh_chung))
                                    <a href="{{ asset('storage/' . $req->hinh_anh_minh_chung) }}" target="_blank" class="text-blue-600 hover:underline">Xem file</a>
                                @else
                                    <span class="text-slate-400">Không có</span>
                                @endif
                            </td>
                            <td class="py-4 px-5">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600 border border-amber-200">
                                    {{ $req->trang_thai ?? 'Đang chờ duyệt' }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-slate-500">{{ $req->created_at ? $req->created_at->format('d/m/Y') : '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                                Bạn chưa nộp đơn xin nghỉ học nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
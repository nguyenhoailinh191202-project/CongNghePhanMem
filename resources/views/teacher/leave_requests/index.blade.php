@extends('layouts.app')

@section('title', 'Duyệt đơn nghỉ')
@section('page-title', 'Duyệt đơn xin nghỉ học')
@section('page-subtitle', 'Xem xét đơn của sinh viên thuộc các lớp bạn phụ trách.')

@section('content')
<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-left text-sm">
            <thead class="border-b border-slate-100 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 font-semibold">Sinh viên</th>
                    <th class="px-5 py-3 font-semibold">Lớp / Môn học</th>
                    <th class="px-5 py-3 font-semibold">Buổi học</th>
                    <th class="px-5 py-3 font-semibold">Lý do</th>
                    <th class="px-5 py-3 font-semibold">Minh chứng</th>
                    <th class="px-5 py-3 font-semibold">Trạng thái</th>
                    <th class="px-5 py-3 text-right font-semibold">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($leaveRequests as $request)
                    <tr class="align-top hover:bg-slate-50/70">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-800">{{ $request->sinhVien->ho_ten }}</p>
                            <p class="mt-1 font-mono text-xs text-slate-500">{{ $request->sinhVien->ma_so }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-800">{{ $request->buoiHoc->lopHocPhan->ma_lhp }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $request->buoiHoc->lopHocPhan->monHoc->ten_mon }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ $request->buoiHoc->ngay_hoc->format('d/m/Y H:i') }}</td>
                        <td class="max-w-xs px-5 py-4 text-slate-600">{{ $request->ly_do }}</td>
                        <td class="px-5 py-4">
                            @if($request->hinh_anh_minh_chung)
                                <button type="button" data-proof-url="{{ asset('storage/' . $request->hinh_anh_minh_chung) }}" data-proof-title="{{ $request->sinhVien->ho_ten }}" class="proof-button font-semibold text-brand-700 hover:underline">
                                    Xem file
                                </button>
                            @else
                                <span class="text-slate-400">Không có</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $statusLabels = ['cho_duyet' => 'Chờ duyệt', 'da_duyet' => 'Đã duyệt', 'tu_choi' => 'Từ chối'];
                                $statusClasses = ['cho_duyet' => 'bg-amber-50 text-amber-700 border-amber-200', 'da_duyet' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'tu_choi' => 'bg-rose-50 text-rose-700 border-rose-200'];
                            @endphp
                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClasses[$request->trang_thai] ?? 'bg-slate-50 text-slate-600 border-slate-200' }}">
                                {{ $statusLabels[$request->trang_thai] ?? $request->trang_thai }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if($request->trang_thai === 'cho_duyet')
                                <div class="flex justify-end gap-2">
                                    <form method="POST" action="{{ route('teacher.leave-requests.approve', $request) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Duyệt</button>
                                    </form>
                                    <form method="POST" action="{{ route('teacher.leave-requests.reject', $request) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700">Từ chối</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs text-slate-400">Đã xử lý</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">Chưa có đơn xin nghỉ nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($leaveRequests->hasPages())
        <div class="border-t border-slate-100 px-5 py-4">{{ $leaveRequests->links() }}</div>
    @endif
</div>

<div id="proof-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 p-4" role="dialog" aria-modal="true">
    <div class="relative max-h-[90vh] w-full max-w-3xl rounded-xl bg-white p-4 shadow-2xl">
        <button type="button" id="close-proof-modal" class="absolute right-3 top-3 rounded-lg bg-slate-900/70 px-3 py-1.5 text-sm font-semibold text-white">Đóng</button>
        <p id="proof-modal-title" class="mb-3 pr-16 text-sm font-semibold text-slate-800"></p>
        <img id="proof-modal-image" src="" alt="Minh chứng đơn xin nghỉ" class="mx-auto max-h-[78vh] rounded-lg object-contain">
    </div>
</div>
@endsection

@push('scripts')
<script>
    const proofModal = document.getElementById('proof-modal');
    const proofImage = document.getElementById('proof-modal-image');
    const proofTitle = document.getElementById('proof-modal-title');

    document.querySelectorAll('.proof-button').forEach((button) => {
        button.addEventListener('click', () => {
            proofImage.src = button.dataset.proofUrl;
            proofTitle.textContent = `Minh chứng: ${button.dataset.proofTitle}`;
            proofModal.classList.remove('hidden');
            proofModal.classList.add('flex');
        });
    });

    const closeProofModal = () => {
        proofModal.classList.add('hidden');
        proofModal.classList.remove('flex');
        proofImage.src = '';
    };

    document.getElementById('close-proof-modal').addEventListener('click', closeProofModal);
    proofModal.addEventListener('click', (event) => {
        if (event.target === proofModal) closeProofModal();
    });
</script>
@endpush

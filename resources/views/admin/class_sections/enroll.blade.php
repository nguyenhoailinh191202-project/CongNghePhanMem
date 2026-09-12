@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3"><div><h1 class="text-2xl font-bold text-slate-900">Ghi danh sinh viên</h1><p class="mt-1 text-sm text-slate-500">{{ $classSection->ma_lhp }} - {{ $classSection->monHoc->ten_mon }} · {{ $classSection->hoc_ky }} {{ $classSection->nam_hoc }}</p></div><a href="{{ route('admin.class-sections.index') }}" class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-200">Quay lại</a></div>
    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">{{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('admin.class-sections.enroll.save', $classSection) }}" class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        @csrf @method('PUT')
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-4"><div><h2 class="text-sm font-bold text-slate-900">Danh sách sinh viên</h2><p class="mt-1 text-xs text-slate-500">Đã chọn <span id="selected-count">0</span> sinh viên</p></div><label class="flex items-center gap-2 text-sm font-semibold text-slate-700"><input type="checkbox" id="select-all" class="rounded border-slate-300 text-blue-600"> Chọn tất cả</label></div>
        <div class="grid max-h-[28rem] grid-cols-1 divide-y divide-slate-100 overflow-y-auto md:grid-cols-2 md:divide-x md:divide-y-0">
            @forelse($students as $student)
                <label class="flex cursor-pointer items-center gap-3 px-5 py-3 hover:bg-slate-50"><input type="checkbox" name="student_ids[]" value="{{ $student->id }}" @checked(in_array($student->id, $enrolledIds)) class="student-checkbox rounded border-slate-300 text-blue-600"><span><span class="block text-sm font-semibold text-slate-800">{{ $student->ho_ten }}</span><span class="block text-xs text-slate-400">{{ $student->ma_so }} · {{ $student->email }}</span></span></label>
            @empty
                <p class="col-span-2 px-5 py-10 text-center text-sm text-slate-400">Chưa có sinh viên trong hệ thống.</p>
            @endforelse
        </div>
        <div class="flex justify-end border-t border-slate-200 bg-slate-50 px-5 py-4"><button class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Lưu danh sách ghi danh</button></div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const selectAll = document.getElementById('select-all');
    const checkboxes = [...document.querySelectorAll('.student-checkbox')];
    const count = document.getElementById('selected-count');
    const updateCount = () => { count.textContent = checkboxes.filter((checkbox) => checkbox.checked).length; };
    selectAll?.addEventListener('change', () => { checkboxes.forEach((checkbox) => { checkbox.checked = selectAll.checked; }); updateCount(); });
    checkboxes.forEach((checkbox) => checkbox.addEventListener('change', updateCount));
    updateCount();
</script>
@endpush

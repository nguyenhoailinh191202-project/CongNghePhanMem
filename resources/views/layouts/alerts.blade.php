@php
    $flashes = [
        'success' => ['bg-emerald-50 text-emerald-800 border-emerald-200', 'M4.5 12.75l6 6 9-13.5'],
        'warning' => ['bg-amber-50 text-amber-800 border-amber-200', 'M12 9v3.75m0 3.75h.008M10.34 3.94L1.82 18a1.5 1.5 0 001.3 2.25h17.76A1.5 1.5 0 0022.18 18L13.66 3.94a1.5 1.5 0 00-2.6 0z'],
        'error'   => ['bg-red-50 text-red-800 border-red-200', 'M6 18L18 6M6 6l12 12'],
    ];
@endphp

@foreach ($flashes as $key => [$classes, $icon])
    @if (session($key))
        <div class="flex items-start gap-2.5 rounded-lg border px-4 py-3 text-sm font-medium {{ $classes }}">
            <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
            </svg>
            <span>{{ session($key) }}</span>
        </div>
    @endif
@endforeach

{{-- Tổng hợp lỗi validation của toàn form --}}
@if ($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <p class="font-semibold">Dữ liệu chưa hợp lệ, vui lòng kiểm tra lại:</p>
        <ul class="mt-1.5 list-inside list-disc space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

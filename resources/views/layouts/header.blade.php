@php
    $user = auth()->user();

    $roleLabels = [
        'admin'      => 'Quản trị viên',
        'giang_vien' => 'Giảng viên',
        'sinh_vien'  => 'Sinh viên',
    ];

    $initials = collect(explode(' ', trim($user->ho_ten)))
        ->filter()
        ->take(-2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
@endphp

<header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-slate-200 bg-white px-4 sm:px-6">
    <button type="button"
            @click="sidebarOpen = true"
            class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden"
            aria-label="Mở menu">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
        </svg>
    </button>

    <div class="hidden text-sm text-slate-500 sm:block">
        {{ now()->translatedFormat('l, d/m/Y') }}
    </div>

    <div class="ml-auto" x-data="{ open: false }" @click.outside="open = false">
        <button type="button" @click="open = !open"
                class="flex items-center gap-2.5 rounded-lg py-1.5 pl-1.5 pr-2 transition hover:bg-slate-100">
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-600 text-sm font-semibold text-white">
                {{ $initials }}
            </span>
            <span class="hidden text-left leading-tight sm:block">
                <span class="block text-sm font-semibold text-slate-900">{{ $user->ho_ten }}</span>
                <span class="block text-xs text-slate-500">{{ $roleLabels[$user->vai_tro] ?? $user->vai_tro }} &middot; {{ $user->ma_so }}</span>
            </span>
            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
            </svg>
        </button>

        <div x-show="open" x-cloak x-transition.opacity
             class="absolute right-4 mt-2 w-56 overflow-hidden rounded-lg border border-slate-200 bg-white py-1 shadow-lg">
            <div class="border-b border-slate-100 px-4 py-2.5 sm:hidden">
                <p class="text-sm font-semibold text-slate-900">{{ $user->ho_ten }}</p>
                <p class="text-xs text-slate-500">{{ $user->ma_so }}</p>
            </div>

            <a href="{{ route('password.change') }}"
               class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                Đổi mật khẩu
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                    Đăng xuất
                </button>
            </form>
        </div>
    </div>
</header>

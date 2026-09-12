@php
    $menus = [
        'admin' => [
            ['route' => 'admin.dashboard',        'label' => 'Tổng quan',        'icon' => 'M3 13.5h6v7.5H3zM15 3h6v18h-6zM9 8.25h6V21H9z'],
            ['route' => 'admin.users.index',      'label' => 'Tài khoản',        'icon' => 'M15 19.1v-.9a6 6 0 00-12 0v.9M9 10.5a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5zm12 8.6v-.9a6 6 0 00-4.5-5.8'],
            ['route' => 'admin.subjects.index',   'label' => 'Môn học',          'icon' => 'M12 6.04A7.5 7.5 0 003 5.25v12a7.5 7.5 0 019 .79 7.5 7.5 0 019-.79v-12a7.5 7.5 0 00-9 .79zm0 0V21'],
            ['route' => 'admin.sections.index',   'label' => 'Lớp học phần',     'icon' => 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5'],
            ['route' => 'admin.rooms.index',      'label' => 'Phòng học & Wifi', 'icon' => 'M2.25 21h19.5M4.5 3v18m15-18v18M9 6.75h1.5M9 12h1.5m3-5.25H15m-1.5 5.25H15'],
        ],
        'giang_vien' => [
            ['route' => 'teacher.attendance.index', 'label' => 'Điểm danh',      'icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['route' => 'teacher.leave-requests.index', 'label' => 'Duyệt đơn nghỉ', 'icon' => 'M19.5 14.25v-2.6c0-1.1-.9-2-2-2h-11c-1.1 0-2 .9-2 2v2.6M16.5 3.75h-9v6h9z'],
        ],
        'sinh_vien' => [
            ['route' => 'student.schedule.index',       'label' => 'Thời khoá biểu',   'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0V11.25h18v7.5'],
            ['route' => 'student.attendance.index',     'label' => 'Chuyên cần',       'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21H4.125A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'],
            ['route' => 'student.leave-requests.index',  'label' => 'Lịch sử đơn nghỉ', 'icon' => 'M19.5 14.25v-2.6c0-1.1-.9-2-2-2h-11c-1.1 0-2 .9-2 2v2.6M16.5 3.75h-9v6h9z'],
        ],
    ];

    $role = auth()->user()->vai_tro;
    $items = $menus[$role] ?? [];
@endphp

<aside x-cloak
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-40 flex w-64 shrink-0 flex-col bg-brand-900 text-slate-200 transition-transform duration-200
              lg:static lg:translate-x-0">

    <div class="flex h-16 items-center gap-2.5 border-b border-white/10 px-5">
        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/15">
            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </span>
        <span class="text-base font-bold tracking-tight text-white">Hệ thống Điểm Danh Trực Tuyến</span>

        <button type="button" @click="sidebarOpen = false"
                class="ml-auto rounded-lg p-1.5 text-slate-300 hover:bg-white/10 lg:hidden" aria-label="Đóng menu">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto p-3">
        @foreach ($items as $item)
            {{-- Bỏ qua mục có route chưa khai báo --}}
            @continue (! Route::has($item['route']))

            @php $active = request()->routeIs($item['route']); @endphp

            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                      {{ $active ? 'bg-white text-brand-900 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                </svg>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    {{-- Phần Chân Trang Sidebar: Cá nhân & Đăng xuất --}}
    <div class="border-t border-white/10 p-3 space-y-1">
        {{-- Nút Đổi mật khẩu dành cho tất cả tài khoản --}}
        @if (Route::has('password.change'))
            @php $isPasswordActive = request()->routeIs('password.change'); @endphp
            <a href="{{ route('password.change') }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                      {{ $isPasswordActive ? 'bg-white text-brand-900 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0121 7.5z" />
                </svg>
                Đổi mật khẩu
            </a>
        @endif

        {{-- Nút Đăng xuất --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H21"/>
                </svg>
                Đăng xuất
            </button>
        </form>
    </div>
</aside>
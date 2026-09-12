<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Trang chủ') - Hệ thống Điểm Danh Trực Tuyến</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full bg-slate-100 font-sans text-slate-800 antialiased">

<div x-data="{ sidebarOpen: false }" class="min-h-full lg:flex">

    {{-- Lớp phủ khi mở sidebar trên mobile --}}
    <div x-show="sidebarOpen"
         x-cloak
         @click="sidebarOpen = false"
         class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden"></div>

    @include('layouts.sidebar')

    <div class="flex min-w-0 flex-1 flex-col">
        @include('layouts.header')

        <main class="flex-1 p-4 sm:p-6">
            <div class="mx-auto w-full max-w-7xl space-y-5">

                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 sm:text-2xl">@yield('page-title', View::getSection('title'))</h1>
                        @hasSection('page-subtitle')
                            <p class="mt-1 text-sm text-slate-500">@yield('page-subtitle')</p>
                        @endif
                    </div>
                    @yield('page-actions')
                </div>

                @include('layouts.alerts')

                @yield('content')
            </div>
        </main>

        <footer class="border-t border-slate-200 bg-white px-6 py-3 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Hệ thống Điểm Danh Trực Tuyến
        </footer>
    </div>
</div>

@stack('scripts')
</body>
</html>
<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập - Hệ thống Điểm Danh Trực Tuyến</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex h-full items-center justify-center bg-slate-100 p-4 font-sans antialiased">

<div class="w-full max-w-md">
    <div class="mb-6 text-center">
        <!-- Thay thế logo SVG ban đầu bằng logo hình ảnh của trường -->
    <img src="{{ asset('images/logo.png') }}" alt="Logo Trường Cao đẳng Kỹ thuật Cao Thắng" class="mx-auto mb-3 h-16 w-auto">
        <h1 class="text-xl font-bold text-slate-900">Hệ thống Điểm Danh Trực Tuyến</h1>
        <p class="mt-1 text-sm text-slate-500">Đăng nhập bằng tài khoản nhà trường cấp</p>
    </div>

    <div class="card">
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ show: false }">
            @csrf

            <div>
                <label for="tai_khoan" class="form-label">Mã số / Email</label>
                <input id="tai_khoan"
                       name="tai_khoan"
                       type="text"
                       value="{{ old('tai_khoan') }}"
                       required
                       autofocus
                       autocomplete="username"
                       placeholder=""
                       class="form-input @error('tai_khoan') form-input-error @enderror">

                @error('tai_khoan')
                    <p class="form-error">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="password" class="form-label">Mật khẩu</label>
                <div class="relative">
                    <input id="password"
                           name="password"
                           :type="show ? 'text' : 'password'"
                           type="password"
                           required
                           autocomplete="current-password"
                           class="form-input pr-10 @error('password') form-input-error @enderror">

                    <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-600"
                            aria-label="Hiện/ẩn mật khẩu">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                </div>

                @error('password')
                    <p class="form-error">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))
                       class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                Ghi nhớ đăng nhập
            </label>

            <button type="submit" class="btn-primary w-full">Đăng nhập</button>
        </form>
    </div>

    <p class="mt-5 text-center text-xs text-slate-400">
        Quên mật khẩu? Liên hệ phòng Đào tạo để được cấp lại.
    </p>
</div>

</body>
</html>

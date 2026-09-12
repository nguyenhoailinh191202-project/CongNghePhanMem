<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị hệ thống</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 p-6 font-sans">
    <main class="mx-auto max-w-3xl rounded-xl bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900">Trang quản trị</h1>
        <p class="mt-2 text-slate-600">Xin chào, {{ auth()->user()->ho_ten }}.</p>

        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button type="submit" class="btn-primary">Đăng xuất</button>
        </form>
    </main>
</body>
</html>
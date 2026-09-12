@extends('layouts.app')

@section('title', 'Đổi mật khẩu')
@section('page-subtitle', 'Mật khẩu tối thiểu 8 ký tự, gồm cả chữ và số.')

@section('content')
    <div class="card max-w-xl">
        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="mat_khau_hien_tai" class="form-label">Mật khẩu hiện tại</label>
                <input id="mat_khau_hien_tai"
                       name="mat_khau_hien_tai"
                       type="password"
                       required
                       autocomplete="current-password"
                       class="form-input @error('mat_khau_hien_tai') form-input-error @enderror">
                @error('mat_khau_hien_tai')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="mat_khau_moi" class="form-label">Mật khẩu mới</label>
                <input id="mat_khau_moi"
                       name="mat_khau_moi"
                       type="password"
                       required
                       autocomplete="new-password"
                       class="form-input @error('mat_khau_moi') form-input-error @enderror">
                @error('mat_khau_moi')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="mat_khau_moi_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                <input id="mat_khau_moi_confirmation"
                       name="mat_khau_moi_confirmation"
                       type="password"
                       required
                       autocomplete="new-password"
                       class="form-input">
            </div>

            <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="btn-primary">Cập nhật mật khẩu</button>
                <a href="{{ url()->previous() }}" class="btn-ghost">Huỷ</a>
            </div>
        </form>
    </div>
@endsection

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        // Nếu đã đăng nhập trước đó mà vào lại trang /login -> tự động đưa tới Thời khóa biểu
        if (Auth::check()) {
            return $this->redirectToHome();
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tai_khoan' => ['required', 'string', 'max:100'],
            'password'  => ['required', 'string'],
        ], [
            'tai_khoan.required' => 'Vui lòng nhập mã số hoặc email.',
            'password.required'  => 'Vui lòng nhập mật khẩu.',
        ]);

        $this->ensureIsNotRateLimited($request);

        $field = filter_var($data['tai_khoan'], FILTER_VALIDATE_EMAIL) ? 'email' : 'ma_so';

        $credentials = [
            $field     => $data['tai_khoan'],
            'password' => $data['password'],
        ];

        // Đăng nhập không ghi nhớ cookie dài hạn (khi tắt tab/trình duyệt sẽ tự out)
        if (! Auth::attempt($credentials, false)) {
            RateLimiter::hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                'tai_khoan' => 'Tài khoản hoặc mật khẩu không chính xác.',
            ]);
        }

        $user = Auth::user();

        if (! $user->trang_thai) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'tai_khoan' => 'Tài khoản đã bị khoá. Vui lòng liên hệ quản trị viên.',
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));
        $request->session()->regenerate();

        // 2. Đăng nhập thành công -> Chuyển hướng trực tiếp sang Thời khóa biểu
        return $this->redirectToHome()
            ->with('success', 'Xin chào ' . $user->ho_ten . '!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Bạn đã đăng xuất khỏi hệ thống.');
    }

    public function showChangePasswordForm(): View
    {
        return view('auth.change_password');
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'mat_khau_hien_tai' => ['required', 'string'],
            'mat_khau_moi'      => ['required', 'string', 'confirmed'],
        ], [
            'mat_khau_hien_tai.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'mat_khau_moi.required'      => 'Vui lòng nhập mật khẩu mới.',
            'mat_khau_moi.confirmed'     => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        /** @var User $user */
        $user = $request->user();

        if (! Hash::check($data['mat_khau_hien_tai'], $user->password)) {
            throw ValidationException::withMessages([
                'mat_khau_hien_tai' => 'Mật khẩu hiện tại không chính xác.',
            ]);
        }

        if (Hash::check($data['mat_khau_moi'], $user->password)) {
            throw ValidationException::withMessages([
                'mat_khau_moi' => 'Mật khẩu mới phải khác mật khẩu hiện tại.',
            ]);
        }

        $user->forceFill([
            'password'             => Hash::make($data['mat_khau_moi']),
            'doi_mat_khau_lan_dau' => false,
        ])->save();

        Auth::logoutOtherDevices($data['mat_khau_moi']);
        $request->session()->regenerate();

        return $this->redirectToHome()
            ->with('success', 'Đổi mật khẩu thành công.');
    }

    private function redirectToHome(): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return match ($user->vai_tro) {
            User::VAI_TRO_ADMIN => redirect()->route('admin.dashboard'),
            User::VAI_TRO_GIANG_VIEN => redirect()->route('teacher.attendance.index'),
            User::VAI_TRO_SINH_VIEN => redirect()->route('student.schedule.index'),
            default => abort(403, 'Vai trò tài khoản không hợp lệ.'),
        };
    }

    private function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'tai_khoan' => "Đăng nhập sai quá nhiều lần. Vui lòng thử lại sau {$seconds} giây.",
        ]);
    }

    private function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('tai_khoan')) . '|' . $request->ip());
    }
}
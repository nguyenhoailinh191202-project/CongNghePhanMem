<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\BuoiHoc;
use App\Models\ChiTietDiemDanh;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    private const QR_TTL_SECONDS = 30;

    public function index(): RedirectResponse
    {
        $session = BuoiHoc::whereHas('lopHocPhan', function ($query) {
            $query->where('giang_vien_id', auth()->id());
        })->latest('ngay_hoc')->firstOrFail();

        return redirect()->route('teacher.attendance.sheet', $session);
    }

    public function sheet(BuoiHoc $buoiHoc): View
    {
        $this->authorizeSession($buoiHoc);

        $buoiHoc->load('lopHocPhan.monHoc');
        $students = $buoiHoc->lopHocPhan->sinhViens()
            ->orderBy('ho_ten')
            ->get();
        $attendance = $buoiHoc->chiTietDiemDanhs()->pluck('trang_thai', 'sinh_vien_id');

        return view('teacher.attendance.sheet', compact('buoiHoc', 'students', 'attendance'));
    }

    public function save(Request $request, BuoiHoc $buoiHoc): RedirectResponse
    {
        $this->authorizeSession($buoiHoc);

        $data = $request->validate([
            'attendance' => ['required', 'array'],
            'attendance.*' => ['required', 'in:co_mat,vang_mat,vang_co_phep,di_tre'],
        ]);

        $studentIds = $buoiHoc->lopHocPhan->sinhViens()->pluck('users.id')->all();
        $submittedIds = array_map('intval', array_keys($data['attendance']));

        abort_if(array_diff($submittedIds, $studentIds), 422, 'Danh sách sinh viên không hợp lệ.');

        $now = now();
        $rows = collect($data['attendance'])->map(function (string $status, string $studentId) use ($buoiHoc, $now) {
            return [
                'buoi_hoc_id' => $buoiHoc->id,
                'sinh_vien_id' => (int) $studentId,
                'trang_thai' => $status,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->values()->all();

        DB::transaction(function () use ($rows) {
            ChiTietDiemDanh::upsert(
                $rows,
                ['buoi_hoc_id', 'sinh_vien_id'],
                ['trang_thai', 'updated_at']
            );
        });

        return back()->with('success', 'Đã lưu bảng điểm danh thành công.');
    }

    public function qrCode(BuoiHoc $buoiHoc): View
    {
        $this->authorizeSession($buoiHoc);
        $buoiHoc->load('lopHocPhan.monHoc');

        return view('teacher.attendance.qr_code', compact('buoiHoc'));
    }

    public function token(BuoiHoc $buoiHoc): JsonResponse
    {
        $this->authorizeSession($buoiHoc);
        $token = Str::random(64);

        Cache::put($this->qrCacheKey($token), [
            'buoi_hoc_id' => $buoiHoc->id,
            'expires_at' => now()->addSeconds(self::QR_TTL_SECONDS)->timestamp,
        ], self::QR_TTL_SECONDS);

        return response()->json([
            'token' => $token,
            'url' => route('student.attendance.qr.scan', $token),
            'expires_in' => self::QR_TTL_SECONDS,
        ]);
    }

    public function scanQr(string $token): RedirectResponse
    {
        $payload = Cache::get($this->qrCacheKey($token));

        if (! $payload || ($payload['expires_at'] ?? 0) < now()->timestamp) {
            return redirect()->route('student.attendance.index')
                ->with('error', 'Mã QR đã hết hạn hoặc đã được sử dụng.');
        }

        $buoiHoc = BuoiHoc::with('lopHocPhan')->findOrFail($payload['buoi_hoc_id']);
        abort_unless(
            $buoiHoc->lopHocPhan->sinhViens()->whereKey(auth()->id())->exists(),
            403,
            'Bạn không thuộc lớp học phần này.'
        );

        ChiTietDiemDanh::updateOrCreate(
            [
                'buoi_hoc_id' => $buoiHoc->id,
                'sinh_vien_id' => auth()->id(),
            ],
            ['trang_thai' => 'co_mat']
        );

        return redirect()->route('student.attendance.index')
            ->with('success', 'Điểm danh bằng QR Code thành công.');
    }

    private function authorizeSession(BuoiHoc $buoiHoc): void
    {
        abort_unless(
            $buoiHoc->lopHocPhan()->where('giang_vien_id', auth()->id())->exists(),
            403,
            'Bạn không có quyền điểm danh buổi học này.'
        );
    }

    private function qrCacheKey(string $token): string
    {
        return 'attendance_qr:' . $token;
    }
}

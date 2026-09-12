<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuoiHoc;
use App\Models\LopHocPhan;
use App\Models\MonHoc;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClassSectionController extends Controller
{
    private const SESSION_COUNT = 15;

    public function index(Request $request): View
    {
        $query = LopHocPhan::with(['monHoc', 'giangVien'])
            ->withCount(['sinhViens', 'buoiHocs']);

        if ($request->filled('hoc_ky')) {
            $query->where('hoc_ky', $request->string('hoc_ky')->trim());
        }

        $classSections = $query->latest('id')->paginate(15)->withQueryString();
        $semesters = LopHocPhan::query()
            ->whereNotNull('hoc_ky')
            ->distinct()
            ->orderBy('hoc_ky')
            ->pluck('hoc_ky');

        return view('admin.class_sections.index', compact('classSections', 'semesters'));
    }

    public function create(): View
    {
        $subjects = MonHoc::orderBy('ma_mon')->get();
        $lecturers = User::where('vai_tro', User::VAI_TRO_GIANG_VIEN)
            ->orderBy('ho_ten')
            ->get();

        return view('admin.class_sections.create', compact('subjects', 'lecturers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ma_lhp' => ['required', 'string', 'max:50', 'unique:lop_hoc_phan,ma_lhp'],
            'mon_hoc_id' => ['required', 'exists:mon_hoc,id'],
            'giang_vien_id' => [
                'required',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('vai_tro', User::VAI_TRO_GIANG_VIEN)),
            ],
            'hoc_ky' => ['required', 'string', 'max:20'],
            'nam_hoc' => ['required', 'string', 'max:20'],
            'phong_hoc' => ['required', 'string', 'max:50'],
            'ngay_bat_dau' => ['required', 'date'],
            'thu' => ['required', 'integer', 'between:1,7'],
            'tiet_bat_dau' => ['required', 'integer', 'between:1,10'],
        ]);

        DB::transaction(function () use ($validated): void {
            $classSection = LopHocPhan::create([
                'ma_lhp' => $validated['ma_lhp'],
                'mon_hoc_id' => $validated['mon_hoc_id'],
                'giang_vien_id' => $validated['giang_vien_id'],
                'hoc_ky' => $validated['hoc_ky'],
                'nam_hoc' => $validated['nam_hoc'],
                'phong_hoc' => $validated['phong_hoc'],
            ]);

            $firstDate = Carbon::parse($validated['ngay_bat_dau']);
            $targetDay = (int) $validated['thu'];
            $firstDate->addDays(($targetDay - $firstDate->dayOfWeekIso + 7) % 7);
            $startTime = $this->timeForPeriod((int) $validated['tiet_bat_dau']);

            for ($session = 0; $session < self::SESSION_COUNT; $session++) {
                $sessionDate = $firstDate->copy()->addWeeks($session)->setTimeFromTimeString($startTime);

                BuoiHoc::create([
                    'lop_hoc_phan_id' => $classSection->id,
                    'ngay_hoc' => $sessionDate,
                    'trang_thai' => 'chua_hoc',
                ]);
            }
        });

        return redirect()->route('admin.class-sections.index')->with('success', 'Đã tạo lớp học phần và 15 buổi học.');
    }

    public function enroll(LopHocPhan $classSection): View
    {
        $classSection->load(['monHoc', 'sinhViens']);
        $enrolledIds = $classSection->sinhViens->pluck('id')->all();
        $students = User::where('vai_tro', User::VAI_TRO_SINH_VIEN)
            ->orderBy('ho_ten')
            ->get();

        return view('admin.class_sections.enroll', compact('classSection', 'students', 'enrolledIds'));
    }

    public function saveEnrollment(Request $request, LopHocPhan $classSection): RedirectResponse
    {
        $validated = $request->validate([
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => [
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('vai_tro', User::VAI_TRO_SINH_VIEN)),
            ],
        ]);

        $classSection->sinhViens()->sync($validated['student_ids'] ?? []);

        return redirect()->route('admin.class-sections.enroll', $classSection)
            ->with('success', 'Đã cập nhật danh sách sinh viên ghi danh.');
    }

    public function destroy(LopHocPhan $classSection): RedirectResponse
    {
        $classSection->delete();

        return redirect()->route('admin.class-sections.index')->with('success', 'Đã xóa lớp học phần.');
    }

    private function timeForPeriod(int $period): string
    {
        return [
            1 => '07:00', 2 => '07:50', 3 => '08:40', 4 => '09:40', 5 => '10:30',
            6 => '13:00', 7 => '13:50', 8 => '14:40', 9 => '15:40', 10 => '16:30',
        ][$period];
    }
}

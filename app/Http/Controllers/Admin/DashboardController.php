<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MonHoc;
use App\Models\LopHocPhan;
use App\Models\BuoiHoc;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Trang Thống kê tổng quan Dashboard (/admin/dashboard)
     */
    public function index(): View
    {
        // 1. Phân quyền người dùng an toàn (Role / Vai trò)
        $roleCol = Schema::hasColumn('users', 'role') 
            ? 'role' 
            : (Schema::hasColumn('users', 'vai_tro') ? 'vai_tro' : null);

        if ($roleCol) {
            $totalStudents = User::whereIn($roleCol, ['sinh_vien', 'student'])->count();
            $totalLecturers = User::whereIn($roleCol, ['giang_vien', 'lecturer', 'teacher'])->count();
        } else {
            $totalStudents = User::count();
            $totalLecturers = 0;
        }

        // Số lớp học phần đang hoạt động
        $activeClassesCount = LopHocPhan::count();

        // 2. Tỷ lệ vắng trung bình toàn hệ thống
        $hasDiemDanhTable = Schema::hasTable('diem_danh');
        
        $totalAttendanceRecords = $hasDiemDanhTable ? DB::table('diem_danh')->count() : 0;
        $totalAbsentRecords = $hasDiemDanhTable 
            ? DB::table('diem_danh')->whereIn(DB::raw('LOWER(trang_thai)'), ['vang', 'vắng', 'absent', '0'])->count() 
            : 0;
        
        $avgAbsenceRate = $totalAttendanceRecords > 0 
            ? round(($totalAbsentRecords / $totalAttendanceRecords) * 100, 1) 
            : 0;

        // 3. Chuẩn bị dữ liệu Biểu đồ Chart.js (Theo từng Môn học)
        $subjects = MonHoc::all();
        $subjectStats = $subjects->map(function ($monHoc) use ($hasDiemDanhTable) {
            $tenMon = $monHoc->ten_mon ?? $monHoc->ten_mon_hoc ?? $monHoc->ma_mon ?? 'Môn học';

            if (!$hasDiemDanhTable) {
                return [
                    'ten_mon'       => $tenMon,
                    'presence_rate' => 100,
                    'absence_rate'  => 0,
                ];
            }

            $lhpIds = LopHocPhan::where('mon_hoc_id', $monHoc->id)->pluck('id');
            $buoiIds = BuoiHoc::whereIn('lop_hoc_phan_id', $lhpIds)->pluck('id');

            $totalPresent = DB::table('diem_danh')
                ->whereIn('buoi_hoc_id', $buoiIds)
                ->whereIn(DB::raw('LOWER(trang_thai)'), ['co_mat', 'có mặt', 'present', '1'])
                ->count();

            $totalAbsent = DB::table('diem_danh')
                ->whereIn('buoi_hoc_id', $buoiIds)
                ->whereIn(DB::raw('LOWER(trang_thai)'), ['vang', 'vắng', 'absent', '0'])
                ->count();

            $total = $totalPresent + $totalAbsent;
            $absenceRate = $total > 0 ? round(($totalAbsent / $total) * 100, 1) : 0;
            $presenceRate = $total > 0 ? round(($totalPresent / $total) * 100, 1) : 100;

            return [
                'ten_mon'       => $tenMon,
                'presence_rate' => $presenceRate,
                'absence_rate'  => $absenceRate,
            ];
        });

        $chartData = [
            'labels'   => $subjectStats->pluck('ten_mon'),
            'presence' => $subjectStats->pluck('presence_rate'),
            'absence'  => $subjectStats->pluck('absence_rate'),
        ];

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalLecturers',
            'activeClassesCount',
            'avgAbsenceRate',
            'chartData'
        ));
    }

    /**
     * Danh sách Cảnh báo sinh viên vắng học >= 20% (/admin/warnings)
     */
    public function warnings(Request $request): View
    {
        $subjects = MonHoc::all();
        $semesters = LopHocPhan::select('hoc_ky')->whereNotNull('hoc_ky')->distinct()->pluck('hoc_ky');

        // Danh sách Lớp học phần cho dropdown lọc
        $classesQuery = LopHocPhan::with('monHoc');
        if ($request->filled('mon_hoc_id')) {
            $classesQuery->where('mon_hoc_id', $request->mon_hoc_id);
        }
        $classes = $classesQuery->get();

        // Lấy danh sách LHP cần truy vấn
        $lopHocPhansQuery = LopHocPhan::with(['monHoc', 'giangVien', 'sinhViens']);
        
        if ($request->filled('mon_hoc_id')) {
            $lopHocPhansQuery->where('mon_hoc_id', $request->mon_hoc_id);
        }
        if ($request->filled('lop_hoc_phan_id')) {
            $lopHocPhansQuery->where('id', $request->lop_hoc_phan_id);
        }
        if ($request->filled('hoc_ky')) {
            $lopHocPhansQuery->where('hoc_ky', $request->hoc_ky);
        }

        $lopHocPhans = $lopHocPhansQuery->get();
        $warningList = collect();

        if (Schema::hasTable('diem_danh')) {
            foreach ($lopHocPhans as $lhp) {
                // Chỉ đếm các buổi học ĐÃ DIỄN RA tính đến hiện tại
                $buoiHocIds = BuoiHoc::where('lop_hoc_phan_id', $lhp->id)
                    ->whereDate('ngay_hoc', '<=', now())
                    ->pluck('id');

                $totalSessions = $buoiHocIds->count();
                if ($totalSessions === 0) continue;

                // Tổng hợp số buổi vắng từng sinh viên trong lớp
                $absentCounts = DB::table('diem_danh')
                    ->whereIn('buoi_hoc_id', $buoiHocIds)
                    ->whereIn(DB::raw('LOWER(trang_thai)'), ['vang', 'vắng', 'absent', '0'])
                    ->select('sinh_vien_id', DB::raw('COUNT(*) as total_absent'))
                    ->groupBy('sinh_vien_id')
                    ->pluck('total_absent', 'sinh_vien_id');

                foreach ($lhp->sinhViens as $student) {
                    $absentSessions = $absentCounts->get($student->id, 0);
                    $absenceRate = round(($absentSessions / $totalSessions) * 100, 1);

                    // Lọc chính xác điều kiện vắng >= 20%
                    if ($absenceRate >= 20.0) {
                        $mssv = $student->code ?? $student->mssv ?? $student->ma_sv ?? ('SV' . str_pad($student->id, 4, '0', STR_PAD_LEFT));

                        $warningList->push((object)[
                            'lop_hoc_phan_id' => $lhp->id,
                            'student_id'      => $student->id,
                            'mssv'            => $mssv,
                            'student_name'    => $student->name ?? $student->ho_ten ?? 'N/A',
                            'ten_mon'         => $lhp->monHoc->ten_mon ?? $lhp->monHoc->ten_mon_hoc ?? 'N/A',
                            'ma_lhp'          => $lhp->ma_lhp ?? ('LHP-' . $lhp->id),
                            'ten_lop_hoc_phan'=> $lhp->ten_lop_hoc_phan ?? 'Lớp HP',
                            'hoc_ky'          => $lhp->hoc_ky ?? 'HK1',
                            'lecturer_name'   => $lhp->giangVien->name ?? $lhp->giangVien->ho_ten ?? 'Chưa phân công',
                            'total_sessions'  => $totalSessions,
                            'absent_sessions' => $absentSessions,
                            'absence_rate'    => $absenceRate,
                        ]);
                    }
                }
            }
        }

        return view('admin.warnings.index', compact('warningList', 'subjects', 'classes', 'semesters'));
    }
}
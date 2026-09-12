<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * Trang 1: Thời khóa biểu
     */
    public function schedule(Request $request)
    {
        $selectedSemester = $request->input('semester_id', 1);
        $selectedWeek = $request->input('week', 10);

        $semesters = [
            ['id' => 1, 'name' => 'Học kỳ 1 - 2025-2026'],
            ['id' => 2, 'name' => 'Học kỳ 2 - 2025-2026'],
        ];

        $weeks = range(1, 20);

        $schedules = collect([
            [
                'day_of_week' => 'Thứ Hai',
                'date'        => '2026-03-09',
                'course_code' => 'INT1401',
                'course_name' => 'Lập trình Web nâng cao',
                'room'        => 'A2.301',
                'period'      => 'Tiết 1 - 3 (07:00 - 09:30)',
                'lecturer'    => 'TS. Nguyễn Văn A',
                'status'      => 'completed',
            ],
            [
                'day_of_week' => 'Thứ Ba',
                'date'        => '2026-03-10',
                'course_code' => 'INT1402',
                'course_name' => 'Cơ sở dữ liệu phân tán',
                'room'        => 'B1.102',
                'period'      => 'Tiết 4 - 6 (09:35 - 12:00)',
                'lecturer'    => 'ThS. Trần Thị B',
                'status'      => 'completed',
            ],
            [
                'day_of_week' => 'Thứ Năm',
                'date'        => '2026-03-12',
                'course_code' => 'INT1403',
                'course_name' => 'Phân tích & Thiết kế Hệ thống',
                'room'        => 'C3.205',
                'period'      => 'Tiết 7 - 9 (13:00 - 15:30)',
                'lecturer'    => 'PGS.TS. Lê Văn C',
                'status'      => 'upcoming',
            ],
        ]);

        return view('student.schedule', compact(
            'semesters',
            'weeks',
            'selectedSemester',
            'selectedWeek',
            'schedules'
        ));
    }

    /**
     * Trang 2: Báo cáo chuyên cần
     */
    public function attendanceReport(Request $request)
    {
        $studentId = Auth::id();

        $attendanceHistory = collect([
            [
                'date'        => '09/03/2026',
                'course_name' => 'Lập trình Web nâng cao',
                'lecturer'    => 'TS. Nguyễn Văn A',
                'status'      => 'present',
            ],
            [
                'date'        => '02/03/2026',
                'course_name' => 'Lập trình Web nâng cao',
                'lecturer'    => 'TS. Nguyễn Văn A',
                'status'      => 'late',
            ],
            [
                'date'        => '24/02/2026',
                'course_name' => 'Cơ sở dữ liệu phân tán',
                'lecturer'    => 'ThS. Trần Thị B',
                'status'      => 'absent',
            ],
            [
                'date'        => '17/02/2026',
                'course_name' => 'Phân tích & Thiết kế Hệ thống',
                'lecturer'    => 'PGS.TS. Lê Văn C',
                'status'      => 'excused',
            ],
            [
                'date'        => '10/02/2026',
                'course_name' => 'Cơ sở dữ liệu phân tán',
                'lecturer'    => 'ThS. Trần Thị B',
                'status'      => 'absent',
            ],
        ]);

        $totalSessions = $attendanceHistory->count();
        $presentCount  = $attendanceHistory->where('status', 'present')->count();
        $absentCount   = $attendanceHistory->where('status', 'absent')->count();
        $excusedCount  = $attendanceHistory->where('status', 'excused')->count();
        $lateCount     = $attendanceHistory->where('status', 'late')->count();

        $attendanceRate = $totalSessions > 0 
            ? round((($presentCount + $lateCount) / $totalSessions) * 100, 1) 
            : 100;

        $absenceRate = $totalSessions > 0 
            ? round((($absentCount + $excusedCount) / $totalSessions) * 100, 1) 
            : 0;

        return view('student.attendance_report', compact(
            'totalSessions',
            'presentCount',
            'absentCount',
            'excusedCount',
            'lateCount',
            'attendanceRate',
            'absenceRate',
            'attendanceHistory'
        ));
    }
}
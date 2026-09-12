<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeaveRequest;
use App\Models\BuoiHoc;
use App\Models\DonXinPhep; 
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    /**
     * Lịch sử danh sách đơn xin nghỉ học của sinh viên.
     */
    public function index(): View
    {
        $leaveRequests = DonXinPhep::with(['buoiHoc.lopHocPhan.monHoc'])
            ->where('sinh_vien_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('student.leave_requests.index', compact('leaveRequests'));
    }

    /**
     * Hiển thị form tạo đơn xin nghỉ học.
     */
    public function create(): View
    {
        $studentId = auth()->id();

        $upcomingSessions = BuoiHoc::with('lopHocPhan.monHoc')
            ->whereHas('lopHocPhan.sinhViens', function ($query) use ($studentId) {
                $query->where('users.id', $studentId);
            })
            ->where('ngay_hoc', '>=', now()->toDateString())
            ->orderBy('ngay_hoc', 'asc')
            ->get();

        return view('student.leave_requests.create', compact('upcomingSessions'));
    }

    /**
     * Lưu đơn xin nghỉ học vào CSDL.
     */
    public function store(StoreLeaveRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $filePath = null;

        if ($request->hasFile('file_minh_chung')) {
            $filePath = $request->file('file_minh_chung')->store('leave_proofs', 'public');
        }

        DonXinPhep::create([
            'sinh_vien_id'    => auth()->id(),
            'buoi_hoc_id'     => $data['buoi_hoc_id'],
            'ly_do'           => $data['ly_do'],
            'hinh_anh_minh_chung' => $filePath,
            'trang_thai'      => 'cho_duyet',
        ]);

        return redirect()
            ->route('student.leave-requests.index')
            ->with('success', 'Nộp đơn xin nghỉ học thành công. Vui lòng chờ duyệt!');
    }
}
<?php

namespace App\Http\Controllers\Teacher;

use App\Exports\AttendanceExport;
use App\Http\Controllers\Controller;
use App\Models\ChiTietDiemDanh;
use App\Models\DonXinPhep;
use App\Models\LopHocPhan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;

class LeaveApprovalController extends Controller
{
    public function index(): View
    {
        $leaveRequests = DonXinPhep::with([
            'sinhVien',
            'buoiHoc.lopHocPhan.monHoc',
        ])
            ->whereHas('buoiHoc.lopHocPhan', function ($query) {
                $query->where('giang_vien_id', auth()->id());
            })
            ->latest()
            ->paginate(15);

        return view('teacher.leave_requests.index', compact('leaveRequests'));
    }

    public function approve(DonXinPhep $donXinPhep): RedirectResponse
    {
        $this->authorizeRequest($donXinPhep);

        DB::transaction(function () use ($donXinPhep) {
            $donXinPhep->update(['trang_thai' => 'da_duyet']);

            ChiTietDiemDanh::updateOrCreate(
                [
                    'buoi_hoc_id' => $donXinPhep->buoi_hoc_id,
                    'sinh_vien_id' => $donXinPhep->sinh_vien_id,
                ],
                ['trang_thai' => 'vang_co_phep']
            );
        });

        return back()->with('success', 'Đã duyệt đơn và cập nhật trạng thái vắng có phép.');
    }

    public function reject(DonXinPhep $donXinPhep): RedirectResponse
    {
        $this->authorizeRequest($donXinPhep);
        $donXinPhep->update(['trang_thai' => 'tu_choi']);

        return back()->with('success', 'Đã từ chối đơn xin nghỉ.');
    }

    public function exportExcel(LopHocPhan $lopHocPhan): Response
    {
        $this->authorizeSection($lopHocPhan);

        return Excel::download(
            new AttendanceExport($lopHocPhan),
            'bao-cao-diem-danh-' . $lopHocPhan->ma_lhp . '.xlsx'
        );
    }

    public function exportPdf(LopHocPhan $lopHocPhan): Response
    {
        $this->authorizeSection($lopHocPhan);

        $export = new AttendanceExport($lopHocPhan);

        return Pdf::loadView('exports.attendance_pdf', [
            'lopHocPhan' => $lopHocPhan->load('monHoc'),
            'headings' => $export->headings(),
            'rows' => $export->collection()->map(fn ($student) => $export->map($student)),
        ])->setPaper('a4', 'landscape')->download(
            'bao-cao-diem-danh-' . $lopHocPhan->ma_lhp . '.pdf'
        );
    }

    private function authorizeRequest(DonXinPhep $donXinPhep): void
    {
        abort_unless(
            $donXinPhep->buoiHoc()
                ->whereHas('lopHocPhan', fn ($query) => $query->where('giang_vien_id', auth()->id()))
                ->exists(),
            403,
            'Bạn không có quyền xử lý đơn xin nghỉ này.'
        );
    }

    private function authorizeSection(LopHocPhan $lopHocPhan): void
    {
        abort_unless($lopHocPhan->giang_vien_id === auth()->id(), 403, 'Bạn không có quyền xuất lớp học phần này.');
    }
}

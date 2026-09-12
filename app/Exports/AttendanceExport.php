<?php

namespace App\Exports;

use App\Models\BuoiHoc;
use App\Models\LopHocPhan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    private Collection $students;

    private int $sessionCount;

    public function __construct(private readonly LopHocPhan $lopHocPhan)
    {
        $this->lopHocPhan->load(['monHoc', 'sinhViens']);

        $sessions = BuoiHoc::where('lop_hoc_phan_id', $this->lopHocPhan->id)
            ->with('chiTietDiemDanhs')
            ->get();

        $this->sessionCount = $sessions->count();
        $attendance = $sessions->flatMap->chiTietDiemDanhs;

        $this->students = $this->lopHocPhan->sinhViens->map(function ($student) use ($attendance) {
            $records = $attendance->where('sinh_vien_id', $student->id);

            return (object) [
                'ma_so' => $student->ma_so,
                'ho_ten' => $student->ho_ten,
                'co_mat' => $records->where('trang_thai', 'co_mat')->count(),
                'vang_mat' => $records->where('trang_thai', 'vang_mat')->count(),
                'vang_co_phep' => $records->where('trang_thai', 'vang_co_phep')->count(),
                'di_tre' => $records->where('trang_thai', 'di_tre')->count(),
            ];
        });
    }

    public function collection(): Collection
    {
        return $this->students;
    }

    public function headings(): array
    {
        return [
            'MSSV',
            'Họ tên',
            'Tổng số buổi',
            'Có mặt',
            'Vắng không phép',
            'Vắng có phép',
            'Đi trễ',
            'Tỷ lệ chuyên cần (%)',
        ];
    }

    public function map($student): array
    {
        $attended = $student->co_mat + $student->di_tre;
        $rate = $this->sessionCount > 0
            ? round(($attended / $this->sessionCount) * 100, 2)
            : 0;

        return [
            $student->ma_so,
            $student->ho_ten,
            $this->sessionCount,
            $student->co_mat,
            $student->vang_mat,
            $student->vang_co_phep,
            $student->di_tre,
            $rate,
        ];
    }
}

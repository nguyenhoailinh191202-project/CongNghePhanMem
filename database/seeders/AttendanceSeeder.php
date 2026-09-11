<?php

namespace Database\Seeders;

use App\Models\BuoiHoc;
use App\Models\ChiTietDiemDanh;
use App\Models\DanhSachLop;
use App\Models\LopHocPhan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $danhSachLhp = LopHocPhan::all();

        foreach ($danhSachLhp as $lhp) {
            // Lấy danh sách sinh viên thuộc lớp học phần
            $danhSachSv = DanhSachLop::where('lop_hoc_phan_id', $lhp->id)->pluck('sinh_vien_id');

            // Tạo 10 buổi học trong quá khứ (mỗi tuần 1 buổi)
            for ($b = 10; $b >= 1; $b--) {
                $buoiHoc = BuoiHoc::create([
                    'lop_hoc_phan_id' => $lhp->id,
                    'ngay_hoc'        => Carbon::now()->subWeeks($b)->setHour(8)->setMinute(0),
                    'trang_thai'      => 'da_hoc',
                ]);

                // Điểm danh cho từng sinh viên trong buổi
                foreach ($danhSachSv as $svId) {
                    $rand = rand(1, 100);
                    if ($rand <= 80) {
                        $trangThai = 'co_mat';
                    } elseif ($rand <= 90) {
                        $trangThai = 'vang_mat';
                    } else {
                        $trangThai = 'di_tre';
                    }

                    ChiTietDiemDanh::create([
                        'buoi_hoc_id'  => $buoiHoc->id,
                        'sinh_vien_id' => $svId,
                        'trang_thai'   => $trangThai,
                    ]);
                }
            }
        }
    }
}
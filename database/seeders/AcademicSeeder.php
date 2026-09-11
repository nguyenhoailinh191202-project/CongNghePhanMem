<?php

namespace Database\Seeders;

use App\Models\DanhSachLop;
use App\Models\LopHocPhan;
use App\Models\MonHoc;
use App\Models\User;
use Illuminate\Database\Seeder;

class AcademicSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo 3 môn học mẫu
        $monHoc1 = MonHoc::create(['ma_mon' => 'MH01', 'ten_mon' => 'Lập trình Web PHP (Laravel)', 'so_tin_chi' => 3]);
        $monHoc2 = MonHoc::create(['ma_mon' => 'MH02', 'ten_mon' => 'Lập trình Thiết bị Di động', 'so_tin_chi' => 3]);
        $monHoc3 = MonHoc::create(['ma_mon' => 'MH03', 'ten_mon' => 'Cơ sở Dữ liệu Nâng cao', 'so_tin_chi' => 3]);

        // Lấy danh sách giảng viên và sinh viên
        $giangViens = User::where('vai_tro', 'giang_vien')->pluck('id')->toArray();
        $sinhViens  = User::where('vai_tro', 'sinh_vien')->pluck('id')->toArray();

        // 2. Tạo 5 Lớp học phần
        $danhSachLHP = [
            ['ma_lhp' => 'LHP_WEB01', 'mon_hoc_id' => $monHoc1->id, 'giang_vien_id' => $giangViens[0], 'hoc_ky' => 'HK1', 'nam_hoc' => '2026-2027', 'phong_hoc' => 'A1.01'],
            ['ma_lhp' => 'LHP_WEB02', 'mon_hoc_id' => $monHoc1->id, 'giang_vien_id' => $giangViens[1], 'hoc_ky' => 'HK1', 'nam_hoc' => '2026-2027', 'phong_hoc' => 'A1.02'],
            ['ma_lhp' => 'LHP_MOB01', 'mon_hoc_id' => $monHoc2->id, 'giang_vien_id' => $giangViens[2], 'hoc_ky' => 'HK1', 'nam_hoc' => '2026-2027', 'phong_hoc' => 'B2.01'],
            ['ma_lhp' => 'LHP_MOB02', 'mon_hoc_id' => $monHoc2->id, 'giang_vien_id' => $giangViens[3], 'hoc_ky' => 'HK1', 'nam_hoc' => '2026-2027', 'phong_hoc' => 'B2.02'],
            ['ma_lhp' => 'LHP_DB01',  'mon_hoc_id' => $monHoc3->id, 'giang_vien_id' => $giangViens[4], 'hoc_ky' => 'HK1', 'nam_hoc' => '2026-2027', 'phong_hoc' => 'C3.01'],
        ];

        foreach ($danhSachLHP as $lhpData) {
            $lhp = LopHocPhan::create($lhpData);

            // Ghi danh (Enroll) khoảng 30 sinh viên ngẫu nhiên vào từng lớp
            $svRandom = collect($sinhViens)->random(30);
            foreach ($svRandom as $svId) {
                DanhSachLop::create([
                    'sinh_vien_id'   => $svId,
                    'lop_hoc_phan_id'=> $lhp->id,
                ]);
            }
        }
    }
}
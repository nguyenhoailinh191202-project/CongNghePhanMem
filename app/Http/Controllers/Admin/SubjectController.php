<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonHoc;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = MonHoc::orderBy('ma_mon')->paginate(15);

        return view('admin.subjects.index', compact('subjects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ma_mon' => ['required', 'string', 'max:20', 'unique:mon_hoc,ma_mon'],
            'ten_mon' => ['required', 'string', 'max:200'],
            'so_tin_chi' => ['required', 'integer', 'min:0', 'max:20'],
            'so_tiet_ly_thuyet' => ['required', 'integer', 'min:0', 'max:999'],
            'so_tiet_thuc_hanh' => ['required', 'integer', 'min:0', 'max:999'],
        ]);

        MonHoc::create($validated);

        return redirect()->route('admin.subjects.index')->with('success', 'Đã thêm môn học.');
    }

    public function update(Request $request, MonHoc $subject): RedirectResponse
    {
        $validated = $request->validate([
            'ma_mon' => ['required', 'string', 'max:20', 'unique:mon_hoc,ma_mon,' . $subject->id],
            'ten_mon' => ['required', 'string', 'max:200'],
            'so_tin_chi' => ['required', 'integer', 'min:0', 'max:20'],
            'so_tiet_ly_thuyet' => ['required', 'integer', 'min:0', 'max:999'],
            'so_tiet_thuc_hanh' => ['required', 'integer', 'min:0', 'max:999'],
        ]);

        $subject->update($validated);

        return redirect()->route('admin.subjects.index')->with('success', 'Đã cập nhật môn học.');
    }

    public function destroy(MonHoc $subject): RedirectResponse
    {
        if ($subject->lopHocPhans()->exists()) {
            return redirect()->back()->with('error', 'Không thể xóa môn học đang được lớp học phần tham chiếu.');
        }

        $subject->delete();

        return redirect()->route('admin.subjects.index')->with('success', 'Đã xóa môn học.');
    }
}

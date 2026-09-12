<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Danh sách tài khoản phân trang, tìm kiếm & lọc vai trò (/admin/users)
     */
    public function index(Request $request): View
    {
        // 1. Tự động phát hiện cột vai trò trong bảng users
        $roleCol = null;
        foreach (['role', 'vai_tro', 'chuc_vu', 'type', 'role_id'] as $col) {
            if (Schema::hasColumn('users', $col)) {
                $roleCol = $col;
                break;
            }
        }

        $query = User::query();

        // 2. Tìm kiếm đa năng (Mã số, Họ tên, Email)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('email', 'LIKE', "%{$search}%");

                foreach (['ho_ten', 'name', 'full_name'] as $col) {
                    if (Schema::hasColumn('users', $col)) {
                        $q->orWhere($col, 'LIKE', "%{$search}%");
                    }
                }

                foreach (['ma_so', 'code', 'mssv', 'ma_sv', 'username', 'mgv'] as $col) {
                    if (Schema::hasColumn('users', $col)) {
                        $q->orWhere($col, 'LIKE', "%{$search}%");
                    }
                }
            });
        }

        // 3. Lọc theo Vai trò (Bao phủ cả Tiếng Việt có dấu, không dấu, Tiếng Anh & ID)
        if ($request->filled('role')) {
            $selectedRole = strtolower(trim($request->role));

            // Bản đồ ánh xạ toàn bộ từ khóa có thể có trong CSDL
            $roleMap = [
                'sinh_vien'  => ['sinh_vien', 'sinh vien', 'sinh viên', 'student', 'sv', '3'],
                'giang_vien' => ['giang_vien', 'giang vien', 'giảng viên', 'lecturer', 'teacher', 'gv', '2'],
                'admin'      => ['admin', 'quan_tri', 'quan tri', 'quản trị', 'quản trị viên', 'administrator', '1'],
            ];

            $possibleValues = $roleMap[$selectedRole] ?? [$selectedRole];

            if ($roleCol) {
                // Nếu cột vai trò nằm trực tiếp trong bảng users
                $query->where(function ($q) use ($roleCol, $possibleValues) {
                    foreach ($possibleValues as $val) {
                        $q->orWhere($roleCol, 'LIKE', "%{$val}%");
                    }
                });
            } elseif (method_exists(User::class, 'roles')) {
                // Nếu sử dụng Spatie Permission hoặc Bảng quan hệ roles
                $query->whereHas('roles', function ($q) use ($possibleValues) {
                    $q->where(function ($subQ) use ($possibleValues) {
                        foreach ($possibleValues as $val) {
                            $subQ->orWhere('name', 'LIKE', "%{$val}%");
                        }
                    });
                });
            }
        }

        // 4. Số lượng hiển thị (per_page: 10, 25, 50, 100 hoặc xem tất cả 'all')
        $perPage = $request->get('per_page', 10);

        if ($perPage === 'all') {
            $total = (clone $query)->count();
            $users = $query->orderBy('id', 'desc')->paginate($total > 0 ? $total : 1)->withQueryString();
        } else {
            $users = $query->orderBy('id', 'desc')->paginate((int) $perPage)->withQueryString();
        }

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $roleCol = Schema::hasColumn('users', 'role') ? 'role' : 'vai_tro';
        $codeCol = Schema::hasColumn('users', 'ma_so') ? 'ma_so' : (Schema::hasColumn('users', 'code') ? 'code' : 'mssv');
        $nameCol = Schema::hasColumn('users', 'ho_ten') ? 'ho_ten' : 'name';

        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'code'     => 'nullable|string|unique:users,' . $codeCol,
            'role'     => 'required|in:sinh_vien,giang_vien,admin',
            'password' => 'nullable|string|min:6',
        ];

        $validated = $request->validate($rules);

        $userData = [
            $nameCol  => $validated['name'],
            'email'   => $validated['email'],
            $roleCol  => $validated['role'],
            'password'=> Hash::make($validated['password'] ?? '12345678'),
        ];

        if (Schema::hasColumn('users', $codeCol) && !empty($validated['code'])) {
            $userData[$codeCol] = $validated['code'];
        }

        if (Schema::hasColumn('users', 'is_active')) {
            $userData['is_active'] = true;
        } elseif (Schema::hasColumn('users', 'trang_thai')) {
            $userData['trang_thai'] = 'hoat_dong';
        }

        User::create($userData);

        return redirect()->route('admin.users.index')->with('success', 'Tạo tài khoản thành công! Mật khẩu mặc định: 12345678');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $roleCol = Schema::hasColumn('users', 'role') ? 'role' : 'vai_tro';
        $codeCol = Schema::hasColumn('users', 'ma_so') ? 'ma_so' : (Schema::hasColumn('users', 'code') ? 'code' : 'mssv');
        $nameCol = Schema::hasColumn('users', 'ho_ten') ? 'ho_ten' : 'name';

        $rules = [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'code'  => 'nullable|string|unique:users,' . $codeCol . ',' . $user->id,
            'role'  => 'required|in:sinh_vien,giang_vien,admin',
        ];

        $validated = $request->validate($rules);

        $userData = [
            $nameCol => $validated['name'],
            'email'  => $validated['email'],
            $roleCol => $validated['role'],
        ];

        if (Schema::hasColumn('users', $codeCol) && isset($validated['code'])) {
            $userData[$codeCol] = $validated['code'];
        }

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật tài khoản thành công!');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $user->update([
            'password' => Hash::make('12345678')
        ]);

        return redirect()->back()->with('success', "Đã reset mật khẩu tài khoản {$user->email} về mặc định (12345678)!");
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if (Schema::hasColumn('users', 'is_active')) {
            $user->is_active = !$user->is_active;
            $user->save();
            $statusText = $user->is_active ? 'Mở khóa' : 'Khóa';
        } elseif (Schema::hasColumn('users', 'trang_thai')) {
            $user->trang_thai = ($user->trang_thai === 'hoat_dong') ? 'khoa' : 'hoat_dong';
            $user->save();
            $statusText = ($user->trang_thai === 'hoat_dong') ? 'Mở khóa' : 'Khóa';
        } else {
            return redirect()->back()->with('error', 'Cơ sở dữ liệu chưa có cột trạng thái tài khoản!');
        }

        return redirect()->back()->with('success', "Đã {$statusText} tài khoản thành công!");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Bạn không thể tự xóa tài khoản của chính mình!');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Đã xóa tài khoản thành công!');
    }

    public function showImportForm(): View
    {
        return view('admin.users.import');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'Vui lòng chọn file Excel để upload.',
            'file.mimes'    => 'File phải thuộc định dạng .xlsx, .xls hoặc .csv',
            'file.max'      => 'Kích thước file không được vượt quá 5MB.',
        ]);

        try {
            Excel::import(new UsersImport, $request->file('file'));
            return redirect()->route('admin.users.index')->with('success', 'Đã nhập danh sách người dùng từ file Excel thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi nhập dữ liệu: ' . $e->getMessage());
        }
    }

    public function downloadSample()
    {
        $path = storage_path('app/mau_import_sinh_vien.xlsx');
        if (!file_exists($path)) {
            $path = public_path('mau_import_sinh_vien.xlsx');
        }

        if (file_exists($path)) {
            return response()->download($path, 'mau_import_sinh_vien.xlsx');
        }

        return redirect()->back()->with('error', 'Chưa tìm thấy file mẫu trên hệ thống.');
    }
}
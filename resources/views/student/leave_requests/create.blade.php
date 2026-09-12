<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nộp đơn xin nghỉ học</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50/40 to-indigo-50/30 min-h-screen py-10 px-4 sm:px-6">

    <div class="max-w-3xl mx-auto">
        {{-- Nút quay lại trang trước --}}
        <div class="mb-6 flex items-center justify-between">
            <button type="button" onclick="history.back()" 
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-medium text-slate-600 bg-white border border-slate-200/80 shadow-sm hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200 group">
                <svg class="w-4 h-4 text-slate-500 group-hover:text-blue-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Quay lại
            </button>

            <span class="text-xs font-semibold uppercase tracking-wider text-blue-600 bg-blue-100/70 px-3 py-1 rounded-full border border-blue-200/60">
                Sinh viên
            </span>
        </div>

        {{-- Card Form --}}
        <div class="bg-white rounded-2xl shadow-xl shadow-blue-900/5 border border-blue-100 overflow-hidden">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 p-6 sm:p-8 text-white relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                
                <div class="flex items-center gap-4 relative z-10">
                    <div class="p-3 bg-white/15 backdrop-blur-md rounded-xl border border-white/20 shadow-inner">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight">Nộp Đơn Xin Nghỉ Học</h1>
                        <p class="text-blue-100 text-xs sm:text-sm mt-1">Vui lòng chọn buổi học và nhập lý do xin nghỉ để gửi tới giảng viên.</p>
                    </div>
                </div>
            </div>

            {{-- Form Body --}}
            <form action="{{ route('student.leave-requests.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
                @csrf

                {{-- Thông báo lỗi tổng quát --}}
                @if ($errors->any())
                    <div class="p-4 rounded-xl bg-red-50 border border-red-200/80 text-red-700 text-sm flex gap-3">
                        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        <div>
                            <span class="font-semibold block mb-1">Có lỗi xảy ra:</span>
                            <ul class="list-disc list-inside space-y-0.5 text-xs sm:text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- Select Buổi học --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-slate-700">
                        Buổi học xin nghỉ <span class="text-red-500">*</span>
                    </label>

                    @if(isset($upcomingSessions) && $upcomingSessions->count() > 0)
                        <div class="relative">
                            <select name="buoi_hoc_id" required 
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-3 text-sm text-slate-800 transition duration-200 focus:bg-white focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-500/10 appearance-none cursor-pointer">
                                <option value="" disabled selected>-- Chọn buổi học từ danh sách --</option>
                                @foreach($upcomingSessions as $session)
                                    <option value="{{ $session->id }}" {{ old('buoi_hoc_id') == $session->id ? 'selected' : '' }}>
                                        [{{ $session->lopHocPhan->ma_lhp ?? 'LHP' }}] {{ $session->lopHocPhan->monHoc->ten_mon_hoc ?? 'Môn học' }} — Ngày: {{ \Carbon\Carbon::parse($session->ngay_hoc)->format('d/m/Y') }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200/60 text-blue-800 text-sm flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12v-.008z" />
                            </svg>
                            <span>Hiện tại chưa có buổi học sắp tới nào dành cho bạn.</span>
                        </div>
                    @endif

                    @error('buoi_hoc_id')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lý do nghỉ --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-slate-700">
                        Lý do xin nghỉ <span class="text-red-500">*</span>
                    </label>
                    <textarea name="ly_do" rows="4" required 
                              placeholder="Trình bày chi tiết lý do xin nghỉ học..." 
                              class="w-full rounded-xl border border-slate-200 bg-slate-50/60 p-3.5 text-sm text-slate-800 placeholder-slate-400 transition duration-200 focus:bg-white focus:border-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-500/10">{{ old('ly_do') }}</textarea>
                    
                    @error('ly_do')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tệp minh chứng --}}
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-slate-700">
                        Tệp minh chứng <span class="text-xs font-normal text-slate-500">(Tùy chọn)</span>
                    </label>
                    
                    <div class="relative border-2 border-dashed border-blue-200 hover:border-blue-400 bg-blue-50/30 rounded-2xl p-4 text-center transition-colors cursor-pointer group">
                        <input type="file" name="file_minh_chung" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               onchange="document.getElementById('file-name').innerText = this.files[0] ? this.files[0].name : 'Chưa chọn tệp nào'">
                        
                        <div class="flex flex-col items-center justify-center pointer-events-none">
                            <div class="p-2.5 bg-blue-100 text-blue-600 rounded-full mb-2 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                </svg>
                            </div>
                            <p class="text-xs sm:text-sm font-medium text-slate-700">
                                <span class="text-blue-600 font-semibold">Tải tệp lên</span> hoặc kéo thả vào đây
                            </p>
                            <p class="text-[11px] text-slate-400 mt-0.5">PNG, JPG, PDF, DOCX (Tối đa 5MB)</p>
                            <p id="file-name" class="text-xs font-semibold text-blue-700 mt-2 bg-blue-100/80 px-2.5 py-1 rounded-md max-w-full truncate hidden group-has-[:valid]:inline-block">Chưa chọn tệp nào</p>
                        </div>
                    </div>

                    @error('file_minh_chung')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nút thao tác --}}
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="history.back()" 
                            class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium bg-white hover:bg-slate-50 transition-all duration-200">
                        Hủy bỏ
                    </button>
                    
                    <button type="submit" 
                            @if(!isset($upcomingSessions) || $upcomingSessions->count() == 0) disabled @endif
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-lg shadow-blue-600/25 hover:shadow-blue-600/35 active:scale-[0.98] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                        Gửi đơn xin nghỉ
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
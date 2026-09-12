<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreLeaveRequest extends FormRequest
{
    /**
     * Xác định xem người dùng có quyền gửi request này hay không.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Quy tắc validation cho đơn xin nghỉ học.
     */
    public function rules(): array
    {
        return [
            'buoi_hoc_id'     => ['required', 'exists:buoi_hoc,id'],
            'ly_do'           => ['required', 'string', 'min:10', 'max:1000'],
            'file_minh_chung' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    /**
     * Thông báo lỗi validation tiếng Việt.
     */
    public function messages(): array
    {
        return [
            'buoi_hoc_id.required'     => 'Vui lòng chọn buổi học muốn xin nghỉ.',
            'buoi_hoc_id.exists'       => 'Buổi học được chọn không hợp lệ.',
            'ly_do.required'           => 'Vui lòng nhập lý do xin nghỉ học.',
            'ly_do.min'                => 'Lý do xin nghỉ học phải có ít nhất 10 ký tự.',
            'ly_do.max'                => 'Lý do xin nghỉ học không vượt quá 1000 ký tự.',
            'file_minh_chung.file'     => 'Tệp đính kèm không hợp lệ.',
            'file_minh_chung.mimes'    => 'Tệp minh chứng chỉ hỗ trợ định dạng: JPG, JPEG, PNG, PDF.',
            'file_minh_chung.max'      => 'Dung lượng tệp minh chứng không được vượt quá 2MB (2048 KB).',
        ];
    }
}
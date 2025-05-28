<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; // ถ้าต้องการ
use Illuminate\Validation\Rule; // Import Rule class

class UpdateSubjectGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // return Auth::check() && Auth::user()->is_admin;
        return true; // หรือตามการ authorize ของคุณ
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // ดึง SubjectGroup instance ที่กำลังจะถูก update จาก route parameter
        // ชื่อ parameter ใน route ต้องตรงกับชื่อ variable ใน method ของ controller (เช่น 'subject_group')
        $subjectGroupId = $this->route('subject_group')->id ?? null;
        // หรือถ้าชื่อ parameter ใน route คือ 'subject_group' ที่เป็น ID โดยตรง
        // $subjectGroupId = $this->route('subject_group');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subject_groups')->ignore($subjectGroupId), // Ignore ID ปัจจุบัน
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('subject_groups')->ignore($subjectGroupId), // Ignore ID ปัจจุบัน
            ],
        ];
    }

    /**
     * Get the custom validation messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'กรุณากรอกชื่อกลุ่มวิชาเอก',
            'name.string' => 'ชื่อกลุ่มวิชาเอกต้องเป็นข้อความ',
            'name.max' => 'ชื่อกลุ่มวิชาเอกต้องมีความยาวไม่เกิน 255 ตัวอักษร',
            'name.unique' => 'ชื่อกลุ่มวิชาเอกนี้มีในระบบแล้ว กรุณาใช้ชื่ออื่น',

            'code.string' => 'รหัสกลุ่มวิชาต้องเป็นข้อความ',
            'code.max' => 'รหัสกลุ่มวิชาต้องมีความยาวไม่เกิน 50 ตัวอักษร',
            'code.unique' => 'รหัสกลุ่มวิชานี้มีในระบบแล้ว กรุณาใช้รหัสอื่น',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'ชื่อกลุ่มวิชาเอก',
            'code' => 'รหัสกลุ่มวิชา',
        ];
    }
}

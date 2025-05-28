<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; // ถ้าต้องการตรวจสอบสิทธิ์เฉพาะ admin

class StoreSubjectGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // กำหนดสิทธิ์ว่าใครสามารถสร้าง Subject Group ได้
        // ในที่นี้ สมมติว่าเฉพาะ admin ที่ login แล้วเท่านั้น
        // return Auth::check() && Auth::user()->is_admin;
        return true; // หรือ return true; ถ้าไม่ต้องการการ authorize ในระดับ Form Request นี้ (อาจจะจัดการใน middleware แล้ว)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:subject_groups,name',
            'code' => 'nullable|string|max:50|unique:subject_groups,code',
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

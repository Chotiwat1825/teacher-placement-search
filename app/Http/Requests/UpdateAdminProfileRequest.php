<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateAdminProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // อนุญาตให้เฉพาะผู้ที่ login แล้ว และเป็น admin เท่านั้น (หรือแค่ login แล้วก็พอถ้าหน้านี้ถูกป้องกันด้วย middleware 'admin' แล้ว)
        return Auth::check(); // && Auth::user()->is_admin; (ถ้า middleware 'admin' ยังไม่ได้ครอบคลุม route นี้)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $userId = Auth::id(); // ID ของ user ที่กำลัง login

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId), // ตรวจสอบ email ไม่ซ้ำ ยกเว้น email ของตัวเอง
            ],
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // ไฟล์รูปภาพไม่เกิน 2MB
            'remove_profile_image' => 'nullable|boolean', // สำหรับ checkbox ลบรูป
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'กรุณากรอกชื่อ-นามสกุล',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้วโดยผู้ใช้อื่น',
            'profile_image.image' => 'ไฟล์ที่อัปโหลดต้องเป็นรูปภาพเท่านั้น',
            'profile_image.mimes' => 'รองรับเฉพาะไฟล์รูปภาพประเภท: jpeg, png, jpg, gif, svg',
            'profile_image.max' => 'ขนาดรูปโปรไฟล์ต้องไม่เกิน 2MB',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'ชื่อ-นามสกุล',
            'email' => 'อีเมล',
            'profile_image' => 'รูปโปรไฟล์',
        ];
    }
}

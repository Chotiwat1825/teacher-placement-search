<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // สำหรับตรวจสอบรหัสผ่านปัจจุบัน
use Illuminate\Validation\Rules\Password; // สำหรับ Rule การตั้งรหัสผ่านใหม่

class UpdateAdminPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check(); // ผู้ใช้ที่ login แล้วสามารถเปลี่ยนรหัสผ่านตัวเองได้
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
                // Custom rule เพื่อตรวจสอบว่ารหัสผ่านปัจจุบันถูกต้องหรือไม่
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, Auth::user()->password)) {
                        $fail('รหัสผ่านปัจจุบันที่ป้อนไม่ถูกต้อง');
                    }
                },
            ],
            'new_password' => [
                'required',
                'string',
                Password::min(8) // กำหนดความยาวขั้นต่ำ 8 ตัวอักษร
                    ->letters() // ต้องมีตัวอักษรอย่างน้อย 1 ตัว
                    ->mixedCase() // ต้องมีทั้งตัวพิมพ์เล็กและตัวพิมพ์ใหญ่อย่างน้อย 1 ตัว
                    ->numbers() // ต้องมีตัวเลขอย่างน้อย 1 ตัว
                    ->symbols() // ต้องมีสัญลักษณ์พิเศษอย่างน้อย 1 ตัว (@, #, $, % etc.)
                    ->uncompromised(), // (Optional) ตรวจสอบกับฐานข้อมูลรหัสผ่านที่เคยรั่วไหล (ต้องต่อเน็ต)
                'confirmed', // ต้องมี field 'new_password_confirmation' ที่ตรงกัน
                'different:current_password', // รหัสผ่านใหม่ต้องแตกต่างจากรหัสผ่านปัจจุบัน
            ],
            // 'new_password_confirmation' => 'required', // ไม่จำเป็นต้องใส่ rule นี้ เพราะ 'confirmed' จัดการให้แล้ว
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'กรุณากรอกรหัสผ่านปัจจุบัน',
            'new_password.required' => 'กรุณากรอกรหัสผ่านใหม่',
            'new_password.min' => 'รหัสผ่านใหม่ต้องมีอย่างน้อย :min ตัวอักษร',
            'new_password.confirmed' => 'การยืนยันรหัสผ่านใหม่ไม่ตรงกัน',
            'new_password.different' => 'รหัสผ่านใหม่ต้องแตกต่างจากรหัสผ่านปัจจุบัน',

            // Laravel password rules (ควรใช้ key ตามนี้)
            'new_password.mixed' => 'รหัสผ่านใหม่ต้องมีตัวอักษรพิมพ์เล็กและพิมพ์ใหญ่อย่างน้อยหนึ่งตัว',
            'new_password.letters' => 'รหัสผ่านใหม่ต้องมีตัวอักษรอย่างน้อยหนึ่งตัว',
            'new_password.symbols' => 'รหัสผ่านใหม่ต้องมีสัญลักษณ์พิเศษอย่างน้อยหนึ่งตัว',
            'new_password.numbers' => 'รหัสผ่านใหม่ต้องมีตัวเลขอย่างน้อยหนึ่งตัว',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'current_password' => 'รหัสผ่านปัจจุบัน',
            'new_password' => 'รหัสผ่านใหม่',
            'new_password_confirmation' => 'ยืนยันรหัสผ่านใหม่',
        ];
    }
}

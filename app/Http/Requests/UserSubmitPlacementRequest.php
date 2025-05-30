<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserSubmitPlacementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // อนุญาตเฉพาะผู้ใช้ที่ login แล้ว และไม่ใช่ Admin (ถ้าต้องการป้องกัน Admin ใช้ฟอร์มนี้)
        return Auth::check() && !Auth::user()->is_admin;
        // หรือ return Auth::check(); ถ้า Admin ก็สามารถส่งข้อมูลผ่านหน้านี้ได้ (แต่ปกติ Admin จะมีฟอร์มของตัวเอง)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Rules จะคล้ายกับ StorePlacementRecordRequest ของ Admin
        // แต่อาจจะมีการปรับเปลี่ยนบางอย่างตามความเหมาะสมสำหรับ User
        return [
            'academic_year' => 'required|integer|min:' . (now()->year + 543 - 5) . '|max:' . (now()->year + 543 + 1), // จำกัดช่วงปีที่ user ส่งได้
            'announcement_date' => 'required|date_format:Y-m-d|before_or_equal:today', // วันที่ประกาศต้องไม่เป็นอนาคต
            'educational_area_id' => 'required|exists:educational_areas,id',
            'round_number' => 'required|integer|min:1',
            'subject_groups' => 'required|array|min:1',
            'subject_groups.*' => 'required|exists:subject_groups,id',
            'placement_type_id' => 'nullable|exists:placement_types,id',
            'source_link' => 'required|url|max:2048', // User ควรจะต้องใส่ link ที่มา
            'notes' => 'nullable|string|max:2000',
            'attachments' => 'nullable|array',
            // จำกัดขนาดไฟล์และประเภทไฟล์สำหรับ User ให้เข้มงวดขึ้น (ถ้าต้องการ)
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ];
    }

    public function messages(): array
    {
        // Custom messages (คล้ายกับของ Admin แต่ปรับให้เหมาะสม)
        return [
            'academic_year.required' => 'กรุณากรอกปีการบรรจุ',
            'academic_year.min' => 'ปีการบรรจุไม่ถูกต้อง',
            'academic_year.max' => 'ปีการบรรจุไม่ถูกต้อง',
            'announcement_date.required' => 'กรุณาเลือกวันที่ประกาศ',
            'announcement_date.before_or_equal' => 'วันที่ประกาศต้องไม่เป็นวันในอนาคต',
            'educational_area_id.required' => 'กรุณาเลือกเขตพื้นที่การศึกษา',
            'round_number.required' => 'กรุณากรอกรอบการเรียกบรรจุ',
            'subject_groups.required' => 'กรุณาเลือกกลุ่มวิชาเอกอย่างน้อย 1 รายการ',
            'source_link.required' => 'กรุณาระบุ Link ที่มาของข้อมูล',
            'source_link.url' => 'รูปแบบ Link ที่มาไม่ถูกต้อง',
            'notes.max' => 'หมายเหตุต้องมีความยาวไม่เกิน 2000 ตัวอักษร',
            'attachments.*.mimes' => 'รองรับไฟล์: PDF, JPG, PNG เท่านั้น',
            'attachments.*.max' => 'ขนาดไฟล์แนบต้องไม่เกิน 5MB',
        ];
    }

    public function attributes(): array
    {
        return [
            'academic_year' => 'ปีการบรรจุ',
            'announcement_date' => 'วันที่ประกาศ',
            'educational_area_id' => 'เขตพื้นที่การศึกษา',
            'round_number' => 'รอบการเรียกบรรจุ',
            'subject_groups' => 'กลุ่มวิชาเอก',
            'placement_type_id' => 'ประเภทการบรรจุ',
            'source_link' => 'Link ที่มา',
            'notes' => 'หมายเหตุ',
            'attachments' => 'ไฟล์แนบ',
        ];
    }
}

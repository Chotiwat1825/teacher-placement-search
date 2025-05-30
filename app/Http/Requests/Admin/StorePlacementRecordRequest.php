<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\PlacementRecord; // สำหรับ Status constants

class StorePlacementRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->is_admin; // หรือ logic การอนุญาตของคุณ
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'academic_year' => 'required|integer|min:2500|max:2700',
            'announcement_date' => 'required|date_format:Y-m-d',
            'educational_area_id' => 'required|exists:educational_areas,id',
            'round_number' => 'required|integer|min:1',
            'subject_groups' => 'required|array|min:1',
            'subject_groups.*' => 'required|exists:subject_groups,id',
            'placement_type_id' => 'nullable|exists:placement_types,id', // << เพิ่ม
            'source_link' => 'nullable|url|max:2048',
            'notes' => 'nullable|string|max:5000', // << เพิ่ม
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,ppt,pptx|max:10240', // 10MB
            // 'status' => Rule::in([...]) // ถ้าจะ set status ตอนสร้างเลย (ปกติ Admin สร้าง = approved)
        ];
    }

    public function messages(): array
    {
        return [
            'academic_year.required' => 'กรุณากรอกปีการบรรจุ',
            'announcement_date.required' => 'กรุณาเลือกวันที่ประกาศ',
            'educational_area_id.required' => 'กรุณาเลือกเขตพื้นที่การศึกษา',
            'round_number.required' => 'กรุณากรอกรอบการเรียกบรรจุ',
            'subject_groups.required' => 'กรุณาเลือกกลุ่มวิชาเอกอย่างน้อย 1 รายการ',
            'subject_groups.*.exists' => 'กลุ่มวิชาเอกที่เลือกไม่ถูกต้อง',
            'placement_type_id.exists' => 'ประเภทการบรรจุที่เลือกไม่ถูกต้อง',
            'notes.max' => 'หมายเหตุต้องมีความยาวไม่เกิน 5000 ตัวอักษร',
            'source_link.url' => 'รูปแบบ Link ที่มาไม่ถูกต้อง',
            'attachments.*.mimes' => 'ประเภทไฟล์แนบไม่ถูกต้อง (อนุญาต: PDF, JPG, PNG, DOC, XLS, PPT)',
            'attachments.*.max' => 'ขนาดไฟล์แนบต้องไม่เกิน 10MB',
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

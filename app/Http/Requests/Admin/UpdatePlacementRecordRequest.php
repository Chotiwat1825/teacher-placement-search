<?php

namespace App\Http\Requests\Admin; // << ตรวจสอบ Namespace

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\PlacementRecord;

class UpdatePlacementRecordRequest extends FormRequest // << ตรวจสอบชื่อ Class
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public function rules(): array
    {
        $placementRecordId = $this->route('placement_record')->id; // ถ้าใช้ Route Model Binding และ parameter ชื่อ placement_record

        return [
            'academic_year' => 'required|integer|min:2500|max:2700',
            'announcement_date' => 'required|date_format:Y-m-d',
            'educational_area_id' => 'required|exists:educational_areas,id',
            'round_number' => 'required|integer|min:1',
            'subject_groups' => 'nullable|array',
            'subject_groups.*' => 'sometimes|exists:subject_groups,id',
            'placement_type_id' => 'nullable|exists:placement_types,id',
            'source_link' => 'nullable|url|max:2048',
            'notes' => 'nullable|string|max:5000',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'delete_attachments' => 'nullable|array',
            'delete_attachments.*' => 'integer|exists:placement_attachments,id',
            'status' => ['nullable', Rule::in([PlacementRecord::STATUS_PENDING, PlacementRecord::STATUS_APPROVED, PlacementRecord::STATUS_REJECTED])],
            'rejection_reason' => 'nullable|string|max:1000|required_if:status,' . PlacementRecord::STATUS_REJECTED,
        ];
    }

    public function messages(): array
    {
        // ... (custom messages ของคุณ) ...
        return [
            'academic_year.required' => 'กรุณากรอกปีการบรรจุ',
            // ...
            'status.in' => 'สถานะที่เลือกไม่ถูกต้อง',
            'rejection_reason.required_if' => 'กรุณากรอกเหตุผลในการปฏิเสธ',
        ];
    }

    public function attributes(): array
    {
        // ... (custom attributes ของคุณ) ...
        return [
            'academic_year' => 'ปีการบรรจุ',
            // ...
            'status' => 'สถานะการอนุมัติ',
            'rejection_reason' => 'เหตุผลในการปฏิเสธ',
        ];
    }
}

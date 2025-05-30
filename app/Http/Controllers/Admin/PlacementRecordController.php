<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlacementRecord;
use App\Models\EducationalArea;
use App\Models\SubjectGroup;
use App\Models\PlacementType; // << Import PlacementType Model
use App\Models\PlacementAttachment;
use Illuminate\Http\Request; // จะเปลี่ยนเป็น FormRequest
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // สำหรับ Logging
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

// แนะนำให้สร้าง Form Requests:
// php artisan make:request Admin/StorePlacementRecordRequest
// php artisan make:request Admin/UpdatePlacementRecordRequest

class PlacementRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PlacementRecord::query()
            // Eager load relationships ที่ต้องการแสดงในตาราง index
            ->with(['educationalArea', 'subjectGroups', 'user', 'placementType']) // << ตรวจสอบ 'placementType'
            ->orderBy('announcement_date', 'desc')
            ->orderBy('academic_year', 'desc')
            ->orderBy('round_number', 'asc');

        // ... (ส่วน Search และ Filter เหมือนเดิม) ...
        // ในส่วน filter อาจจะเพิ่ม filter ตาม placement_type_id ด้วย
        if ($request->filled('filter_placement_type_id')) {
            $query->where('placement_type_id', $request->filter_placement_type_id);
        }
        // ...

        $placementRecords = $query->paginate(15)->withQueryString();

        // Data for filter dropdowns
        $educationalAreas = EducationalArea::orderBy('name')->get();
        $subjectGroups = SubjectGroup::orderBy('name')->get();
        $placementTypes = PlacementType::where('is_active', true)->orderBy('name')->get(); // << สำหรับ filter
        $academicYears = PlacementRecord::select('academic_year')->distinct()->orderBy('academic_year', 'desc')->pluck('academic_year');
        $statuses = [
            // << สำหรับ filter status (ถ้ามี)
            PlacementRecord::STATUS_PENDING => 'รออนุมัติ',
            PlacementRecord::STATUS_APPROVED => 'อนุมัติแล้ว',
            PlacementRecord::STATUS_REJECTED => 'ถูกปฏิเสธ',
        ];

        return view(
            'admin.placement_records.index',
            compact(
                'placementRecords',
                'educationalAreas',
                'subjectGroups',
                'academicYears',
                'placementTypes', // << ส่งไป view สำหรับ filter
                'statuses', // << ส่งไป view สำหรับ filter
            ),
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $educationalAreas = EducationalArea::orderBy('name')->get();
        $subjectGroups = SubjectGroup::orderBy('name')->get();
        $placementTypes = PlacementType::where('is_active', true)->orderBy('name')->get();
        $currentThaiYear = now()->year + 543;
        $academicYears = range($currentThaiYear + 3, $currentThaiYear - 5);
        $LastYear = PlacementRecord::max('academic_year') ?? $currentThaiYear;

        return view('admin.placement_records.create', compact('educationalAreas', 'subjectGroups', 'academicYears', 'placementTypes', 'LastYear'));
    }

    /**
     * Store a newly created resource in storage.
     * (ควรใช้ StorePlacementRecordRequest)
     */
    public function store(StorePlacementRecordRequest $request)
    {
        // << ใช้ Form Request
        $validatedData = $request->validated(); // ดึงข้อมูลที่ผ่านการ validate แล้ว

        try {
            $placementData = [
                'academic_year' => $validatedData['academic_year'],
                'announcement_date' => $validatedData['announcement_date'],
                'educational_area_id' => $validatedData['educational_area_id'],
                'round_number' => $validatedData['round_number'],
                'placement_type_id' => $validatedData['placement_type_id'] ?? null, // << เพิ่ม
                'source_link' => $validatedData['source_link'] ?? null,
                'notes' => $validatedData['notes'] ?? null, // << เพิ่ม
                'user_id' => Auth::id(),
                // Admin สร้างข้อมูล, ตั้งสถานะเป็น Approved โดยอัตโนมัติ
                // ถ้าเป็นระบบ User Submission, status ควรเป็น PENDING
                'status' => PlacementRecord::STATUS_APPROVED,
                'processed_by_user_id' => Auth::id(), // Admin ที่สร้างก็คือคนที่ process
                'processed_at' => now(),
            ];

            $placementRecord = PlacementRecord::create($placementData);

            if (!empty($validatedData['subject_groups'])) {
                $placementRecord->subjectGroups()->attach($validatedData['subject_groups']);
            }

            if ($request->hasFile('attachments')) {
                $diskName = config('filesystems.default_private_disk', 'private');
                $attachmentFolder = 'placement_attachments/' . $placementRecord->id;
                if (!Storage::disk($diskName)->exists($attachmentFolder)) {
                    Storage::disk($diskName)->makeDirectory($attachmentFolder);
                }
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        $originalFilename = $file->getClientOriginalName();
                        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs($attachmentFolder, $filename, $diskName);
                        $placementRecord->attachments()->create([
                            'file_path' => $path,
                            'original_filename' => $originalFilename,
                            'mime_type' => $file->getMimeType(),
                            'type' => Str::startsWith($file->getMimeType(), 'image/') ? 'image' : 'file',
                        ]);
                    }
                }
            }
            return redirect()
                ->route('admin.placement-records.index')
                ->with('success', 'สร้างข้อมูลการบรรจุ "' . $placementRecord->academic_year . ' รอบ ' . $placementRecord->round_number . '" สำเร็จ');
        } catch (\Exception $e) {
            Log::error('Error storing placement record: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()
                ->back()
                ->with('error', 'เกิดข้อผิดพลาดในการสร้างข้อมูลการบรรจุ: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
     public function show(PlacementRecord $placementRecord)
    {
        // Eager load relationships ทั้งหมดที่ต้องการแสดงในหน้ารายละเอียด
        $placementRecord->load(['educationalArea', 'subjectGroups', 'attachments', 'user', 'placementType']); // << ตรวจสอบ 'placementType'
        return view('admin.placement_records.show', compact('placementRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlacementRecord $placementRecord)
    {
        $placementRecord->load(['subjectGroups', 'attachments', 'placementType']); // << เพิ่ม 'placementType'
        $educationalAreas = EducationalArea::orderBy('name')->get();
        $subjectGroups = SubjectGroup::orderBy('name')->get();
        $placementTypes = PlacementType::where('is_active', true)->orderBy('name')->get();
        $currentThaiYear = now()->year + 543;
        $academicYears = range($currentThaiYear + 3, $currentThaiYear - 10);
        $selectedSubjectGroupIds = $placementRecord->subjectGroups->pluck('id')->toArray();
        $LastYear = PlacementRecord::max('academic_year') ?? $currentThaiYear;

        return view('admin.placement_records.edit', compact('placementRecord', 'educationalAreas', 'subjectGroups', 'academicYears', 'selectedSubjectGroupIds', 'placementTypes', 'LastYear'));
    }

    /**
     * Update the specified resource in storage.
     * (ควรใช้ UpdatePlacementRecordRequest)
     */
    public function update(UpdatePlacementRecordRequest $request, PlacementRecord $placementRecord)
    {
        // << ใช้ Form Request
        $validatedData = $request->validated();

        try {
            $updateData = [
                'academic_year' => $validatedData['academic_year'],
                'announcement_date' => $validatedData['announcement_date'],
                'educational_area_id' => $validatedData['educational_area_id'],
                'round_number' => $validatedData['round_number'],
                'placement_type_id' => $validatedData['placement_type_id'] ?? null, // << เพิ่ม
                'source_link' => $validatedData['source_link'] ?? null,
                'notes' => $validatedData['notes'] ?? null, // << เพิ่ม
            ];

            // (Optional) ถ้า Admin สามารถเปลี่ยน Status และเหตุผลจากหน้านี้ได้
            if (isset($validatedData['status'])) {
                $updateData['status'] = $validatedData['status'];
                if ($validatedData['status'] === PlacementRecord::STATUS_REJECTED && isset($validatedData['rejection_reason'])) {
                    $updateData['rejection_reason'] = $validatedData['rejection_reason'];
                } else {
                    $updateData['rejection_reason'] = null; // Clear reason if not rejected or no reason provided
                }
                // อัปเดตผู้ดำเนินการและเวลา ถ้า status มีการเปลี่ยนแปลงที่มีนัยสำคัญ
                if ($placementRecord->status !== $validatedData['status'] && in_array($validatedData['status'], [PlacementRecord::STATUS_APPROVED, PlacementRecord::STATUS_REJECTED])) {
                    $updateData['processed_by_user_id'] = Auth::id();
                    $updateData['processed_at'] = now();
                }
            }

            $placementRecord->update($updateData);

            // Sync subject groups
            if ($request->filled('subject_groups')) {
                // ใช้ filled() เพื่อเช็คว่ามี key นี้ส่งมา (แม้จะเป็น array ว่าง)
                $placementRecord->subjectGroups()->sync($validatedData['subject_groups'] ?? []); // ใช้ ?? [] ถ้า subject_groups อาจจะไม่มีใน validatedData
            } else {
                // ถ้า 'subject_groups' ไม่ได้ถูกส่งมาเลย (เช่น checkbox ถูก uncheck ทั้งหมด และ field ไม่ได้ถูกส่ง)
                // หรือคุณต้องการให้การไม่ส่ง array 'subject_groups' หมายถึงการลบทั้งหมด
                $placementRecord->subjectGroups()->detach();
            }

            // Handle deleting existing attachments
            if ($request->filled('delete_attachments')) {
                $diskName = config('filesystems.default_private_disk', 'private');
                $attachmentsToDelete = PlacementAttachment::whereIn('id', $validatedData['delete_attachments'])->where('placement_record_id', $placementRecord->id)->get();
                foreach ($attachmentsToDelete as $attachment) {
                    if (Storage::disk($diskName)->exists($attachment->file_path)) {
                        Storage::disk($diskName)->delete($attachment->file_path);
                    }
                    $attachment->delete();
                }
            }

            // Handle new file attachments
            if ($request->hasFile('attachments')) {
                $diskName = config('filesystems.default_private_disk', 'private');
                $attachmentFolder = 'placement_attachments/' . $placementRecord->id;
                if (!Storage::disk($diskName)->exists($attachmentFolder)) {
                    Storage::disk($diskName)->makeDirectory($attachmentFolder);
                }
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        $originalFilename = $file->getClientOriginalName();
                        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs($attachmentFolder, $filename, $diskName);
                        $placementRecord->attachments()->create([
                            'file_path' => $path,
                            'original_filename' => $originalFilename,
                            'mime_type' => $file->getMimeType(),
                            'type' => Str::startsWith($file->getMimeType(), 'image/') ? 'image' : 'file',
                        ]);
                    }
                }
            }

            return redirect()->route('admin.placement-records.index')->with('success', 'แก้ไขข้อมูลการบรรจุสำเร็จ');
        } catch (\Exception $e) {
            Log::error("Error updating placement record ID {$placementRecord->id}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()
                ->back()
                ->with('error', 'เกิดข้อผิดพลาดในการแก้ไขข้อมูลการบรรจุ: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlacementRecord $placementRecord)
    {
        // ... (โค้ด destroy เหมือนเดิม) ...
        try {
            $recordIdentifier = "ปี {$placementRecord->academic_year} - เขตฯ " . ($placementRecord->educationalArea->name ?? 'N/A') . " - รอบ {$placementRecord->round_number}";
            $diskName = config('filesystems.default_private_disk', 'private');
            foreach ($placementRecord->attachments as $attachment) {
                if (Storage::disk($diskName)->exists($attachment->file_path)) {
                    Storage::disk($diskName)->delete($attachment->file_path);
                }
            }
            $placementRecord->subjectGroups()->detach();
            $placementRecord->delete();
            return redirect()
                ->route('admin.placement-records.index')
                ->with('success', 'ลบข้อมูลการบรรจุ "' . $recordIdentifier . '" สำเร็จ');
        } catch (\Exception $e) {
            Log::error("Error deleting placement record ID {$placementRecord->id}: " . $e->getMessage());
            return redirect()->route('admin.placement-records.index')->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล');
        }
    }
}

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
            ->with(['educationalArea', 'subjectGroups', 'creator', 'placementType', 'processor']) // << เพิ่ม 'processor' ถ้ามี
            ->orderBy('status', 'asc') // << ให้รายการ pending ขึ้นก่อน (optional)
            ->orderBy('created_at', 'desc'); // หรือ announcement_date

        // ... (Search term filter) ...
        if ($request->filled('search_term')) {
            /* ... */
        }

        // ... (Filter by Educational Area, Subject Group, Academic Year, Placement Type) ...
        if ($request->filled('filter_educational_area_id')) {
            /* ... */
        }
        if ($request->filled('filter_subject_group_id')) {
            /* ... */
        }
        if ($request->filled('filter_academic_year')) {
            /* ... */
        }
        if ($request->filled('filter_placement_type_id')) {
            /* ... */
        }

        // Filter by Status
        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        $placementRecords = $query->paginate(15)->withQueryString();

        $educationalAreas = EducationalArea::orderBy('name')->get();
        $subjectGroups = SubjectGroup::orderBy('name')->get();
        $placementTypes = PlacementType::where('is_active', true)->orderBy('name')->get();
        $academicYears = PlacementRecord::select('academic_year')->distinct()->orderBy('academic_year', 'desc')->pluck('academic_year');
        $statuses = [
            PlacementRecord::STATUS_PENDING => 'รอการอนุมัติ',
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
                'placementTypes',
                'statuses', // << ส่ง statuses ไปให้ view
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
                'creator_id' => Auth::id(),
                // Admin สร้างข้อมูล, ตั้งสถานะเป็น Approved โดยอัตโนมัติ
                // ถ้าเป็นระบบ creator Submission, status ควรเป็น PENDING
                'status' => PlacementRecord::STATUS_APPROVED,
                'processed_by_creator_id' => Auth::id(), // Admin ที่สร้างก็คือคนที่ process
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
        $placementRecord->load(['educationalArea', 'subjectGroups', 'attachments', 'creator', 'placementType']); // << ตรวจสอบ 'placementType'
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
                    $updateData['processed_by_creator_id'] = Auth::id();
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
    public function processAction(Request $request, PlacementRecord $placementRecord)
    {
        $request->validate([
            'admin_action_status' => ['required', Rule::in([PlacementRecord::STATUS_APPROVED, PlacementRecord::STATUS_REJECTED])],
            'rejection_reason' => 'nullable|string|max:1000|required_if:admin_action_status,' . PlacementRecord::STATUS_REJECTED,
            // อาจจะ validate fields อื่นๆ ที่ Admin อาจจะแก้ไขก่อน approve/reject ด้วย (ถ้า form edit ส่งมาทั้งหมด)
            // หรือจะให้ form นี้มีแค่ status กับ reason ก็ได้
        ]);

        // ตรวจสอบว่า record ยังเป็น pending อยู่หรือไม่ (ป้องกันการ process ซ้ำ)
        if ($placementRecord->status !== PlacementRecord::STATUS_PENDING) {
            return redirect()->route('admin.placement-records.index')->with('warning', 'รายการนี้ได้รับการดำเนินการไปแล้ว');
        }

        $newStatus = $request->input('admin_action_status');
        $message = '';

        try {
            $updateData = [
                'status' => $newStatus,
                'processed_by_creator_id' => Auth::id(),
                'processed_at' => now(),
                'rejection_reason' => $newStatus === PlacementRecord::STATUS_REJECTED ? $request->input('rejection_reason') : null,
            ];

            // (Optional) ถ้า Admin แก้ไขข้อมูลอื่นๆ ในฟอร์ม edit ก่อนกด Approve/Reject
            // คุณอาจจะต้องดึงค่าเหล่านั้นจาก $request มา update ด้วย
            // $updateData['academic_year'] = $request->input('academic_year', $placementRecord->academic_year);
            // ... etc ...
            // แต่ถ้า form นี้มีแค่ status กับ reason ก็ไม่ต้อง

            $placementRecord->update($updateData);

            if ($newStatus === PlacementRecord::STATUS_APPROVED) {
                $message = 'อนุมัติข้อมูลการบรรจุสำเร็จ';
                // (Optional) Send notification to creator
            } elseif ($newStatus === PlacementRecord::STATUS_REJECTED) {
                $message = 'ปฏิเสธข้อมูลการบรรจุสำเร็จ';
                // (Optional) Send notification to creator with rejection_reason
            }

            return redirect()->route('admin.placement-records.index')->with('success', $message);
        } catch (\Exception $e) {
            Log::error("Error processing placement record ID {$placementRecord->id}: " . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'เกิดข้อผิดพลาดในการดำเนินการ: ' . $e->getMessage())
                ->withInput();
        }
    }
}

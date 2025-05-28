<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlacementRecord;
use App\Models\EducationalArea;
use App\Models\SubjectGroup;
use App\Models\PlacementAttachment; // เพิ่ม
use Illuminate\Http\Request; // ควรเปลี่ยนเป็น FormRequest (เช่น UpdatePlacementRecordRequest)
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule; // ถ้าต้องการ validation ที่ซับซ้อน

class PlacementRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = PlacementRecord::query()
            ->with(['educationalArea', 'subjectGroups', 'user']) // Eager load relationships
            ->orderBy('announcement_date', 'desc') // เรียงตามวันที่ประกาศล่าสุด
            ->orderBy('academic_year', 'desc')
            ->orderBy('round_number', 'asc');

        // Search functionality
        if ($request->filled('search_term')) {
            $searchTerm = $request->search_term;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('academic_year', 'like', "%{$searchTerm}%")
                    ->orWhere('round_number', 'like', "%{$searchTerm}%")
                    ->orWhereHas('educationalArea', function ($sq) use ($searchTerm) {
                        $sq->where('name', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('subjectGroups', function ($sq) use ($searchTerm) {
                        $sq->where('name', 'like', "%{$searchTerm}%");
                    });
                // สามารถเพิ่มการค้นหาจาก source_link หรือ user->name ได้ถ้าต้องการ
            });
        }

        // Filter by Educational Area
        if ($request->filled('filter_educational_area_id')) {
            $query->where('educational_area_id', $request->filter_educational_area_id);
        }

        // Filter by Subject Group
        if ($request->filled('filter_subject_group_id')) {
            $query->whereHas('subjectGroups', function ($q) use ($request) {
                $q->where('subject_groups.id', $request->filter_subject_group_id);
            });
        }

        // Filter by Academic Year
        if ($request->filled('filter_academic_year')) {
            $query->where('academic_year', $request->filter_academic_year);
        }

        $placementRecords = $query->paginate(15)->withQueryString();

        // Data for filter dropdowns
        $educationalAreas = EducationalArea::orderBy('name')->get();
        $subjectGroups = SubjectGroup::orderBy('name')->get();
        $academicYears = PlacementRecord::select('academic_year')->distinct()->orderBy('academic_year', 'desc')->pluck('academic_year'); // ดึงปีที่มีข้อมูลจริง

        return view('admin.placement_records.index', compact('placementRecords', 'educationalAreas', 'subjectGroups', 'academicYears'));
    }

    // ... (create, store, show, edit, update, destroy methods ตามที่เคยให้ไป หรือจะสร้างใหม่) ...
    // นี่คือตัวอย่างเพื่อให้ view index ทำงานได้ครบถ้วน
    public function create()
    {
        $educationalAreas = EducationalArea::orderBy('name')->get();
        $subjectGroups = SubjectGroup::orderBy('name')->get();

        // สร้างช่วงปีการศึกษา (พ.ศ.) สำหรับ dropdown
        // คุณอาจจะต้องการ logic ที่ยืดหยุ่นกว่านี้ในการสร้างปี เช่น ดึงจาก config หรือ helper
        $currentThaiYear = now()->year + 543;
        $academicYears = range($currentThaiYear + 3, $currentThaiYear - 5); // สร้างช่วงปี เช่น 2570 ถึง 2562

        return view('admin.placement_records.create', compact(
            'educationalAreas',
            'subjectGroups',
            'academicYears'
        ));
    }

    // (ควรใช้ Form Request สำหรับ store, update)
    public function store(Request $request)
    {
        // หรือ StorePlacementRecordRequest $request
        // === Validation Rules (ควรย้ายไป StorePlacementRecordRequest) ===
        $validatedData = $request->validate(
            [
                'academic_year' => 'required|integer|min:2500|max:2700', // ปรับ min/max ตามความเหมาะสม
                'announcement_date' => 'required|date_format:Y-m-d', // Laravel จะพยายาม parse วันที่ตาม format นี้
                'educational_area_id' => 'required|exists:educational_areas,id',
                'round_number' => 'required|integer|min:1',
                'subject_groups' => 'required|array|min:1', // ต้องมีอย่างน้อย 1 กลุ่มวิชาเอก
                'subject_groups.*' => 'required|exists:subject_groups,id', // ตรวจสอบว่า ID ของกลุ่มวิชาเอกมีอยู่จริง
                'source_link' => 'nullable|url|max:2048',
                'attachments' => 'nullable|array', // attachments สามารถเป็น array ของไฟล์
                'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,ppt,pptx|max:10240', // 10MB max per file, เพิ่ม ppt
            ],
            [
                'academic_year.required' => 'กรุณากรอกปีการบรรจุ',
                'announcement_date.required' => 'กรุณาเลือกวันที่ประกาศ',
                'announcement_date.date_format' => 'รูปแบบวันที่ประกาศไม่ถูกต้อง (ต้องการ YYYY-MM-DD)',
                'educational_area_id.required' => 'กรุณาเลือกเขตพื้นที่การศึกษา',
                'round_number.required' => 'กรุณากรอกรอบการเรียกบรรจุ',
                'subject_groups.required' => 'กรุณาเลือกกลุ่มวิชาเอกอย่างน้อย 1 รายการ',
                'subject_groups.*.exists' => 'กลุ่มวิชาเอกที่เลือกไม่ถูกต้อง',
                'source_link.url' => 'รูปแบบ Link ที่มาไม่ถูกต้อง',
                'attachments.*.file' => 'ไฟล์แนบต้องเป็นไฟล์',
                'attachments.*.mimes' => 'ประเภทไฟล์แนบไม่ถูกต้อง (อนุญาต: PDF, JPG, PNG, DOC, XLS, PPT)',
                'attachments.*.max' => 'ขนาดไฟล์แนบต้องไม่เกิน 10MB',
            ],
        );
        // === End Validation Rules ===

        try {
            // 1. เตรียมข้อมูลสำหรับ PlacementRecord หลัก
            $placementData = [
                'academic_year' => $validatedData['academic_year'],
                'announcement_date' => $validatedData['announcement_date'], // format Y-m-d อยู่แล้ว
                'educational_area_id' => $validatedData['educational_area_id'],
                'round_number' => $validatedData['round_number'],
                'source_link' => $validatedData['source_link'] ?? null,
                'user_id' => Auth::id(), // ID ของ admin ที่ login อยู่
            ];

            // 2. สร้าง PlacementRecord
            $placementRecord = PlacementRecord::create($placementData);

            // 3. Attach Subject Groups (Many-to-Many relationship)
            if (!empty($validatedData['subject_groups'])) {
                $placementRecord->subjectGroups()->attach($validatedData['subject_groups']);
            }

            // 4. Handle File Attachments (ถ้ามี)
            if ($request->hasFile('attachments')) {
                $diskName = config('filesystems.default_private_disk', 'private');
                // สร้าง folder เฉพาะสำหรับ record นี้เพื่อจัดระเบียบไฟล์
                $attachmentFolder = 'placement_attachments/' . $placementRecord->id;

                // สร้าง folder ถ้ายังไม่มี
                if (!Storage::disk($diskName)->exists($attachmentFolder)) {
                    Storage::disk($diskName)->makeDirectory($attachmentFolder);
                }

                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        // ตรวจสอบว่าไฟล์อัปโหลดมาสมบูรณ์
                        $originalFilename = $file->getClientOriginalName();
                        // สร้างชื่อไฟล์ใหม่ที่ไม่ซ้ำกันเพื่อป้องกันการเขียนทับและปัญหา encoding
                        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs($attachmentFolder, $filename, $diskName);

                        // สร้าง record ในตาราง placement_attachments
                        $placementRecord->attachments()->create([
                            'file_path' => $path,
                            'original_filename' => $originalFilename,
                            'mime_type' => $file->getMimeType(),
                            'type' => Str::startsWith($file->getMimeType(), 'image/') ? 'image' : 'file',
                        ]);
                    }
                }
            }

            return redirect()->route('admin.placement-records.index')->with('success', 'สร้างข้อมูลการบรรจุใหม่สำเร็จเรียบร้อย');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors จะถูก handle โดย Laravel และ redirect กลับพร้อม errors โดยอัตโนมัติ
            // แต่ถ้าต้องการดักจับเพื่อทำอย่างอื่น ก็สามารถทำได้
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // จัดการ Error อื่นๆ ที่อาจเกิดขึ้น
            Log::error('Error storing placement record: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()
                ->back()
                ->with('error', 'เกิดข้อผิดพลาดในการสร้างข้อมูลการบรรจุ: ' . $e->getMessage())
                ->withInput(); // ส่งข้อมูลที่กรอกกลับไปที่ฟอร์ม
        }
    }

    public function show(PlacementRecord $placementRecord)
    {
        // Eager load all necessary related data for the show page
        $placementRecord->load(['educationalArea', 'subjectGroups', 'attachments', 'user']);

        return view('admin.placement_records.show', compact('placementRecord'));
    }
    public function edit(PlacementRecord $placementRecord)
    {
        // Eager load current subject groups and attachments for the form
        $placementRecord->load(['subjectGroups', 'attachments']);

        $educationalAreas = EducationalArea::orderBy('name')->get();
        $subjectGroups = SubjectGroup::orderBy('name')->get(); // All subject groups for selection

        // Generate academic years (you might want a more dynamic way or a helper)
        $currentThaiYear = now()->year + 543;
        $academicYears = range($currentThaiYear + 3, $currentThaiYear - 10); // ช่วงปีที่กว้างขึ้น

        $selectedSubjectGroupIds = $placementRecord->subjectGroups->pluck('id')->toArray();

        return view('admin.placement_records.edit', compact('placementRecord', 'educationalAreas', 'subjectGroups', 'academicYears', 'selectedSubjectGroupIds'));
    }
    public function update(Request $request, PlacementRecord $placementRecord)
    {
        // ควรเป็น UpdatePlacementRecordRequest
        $validated = $request->validate([
            'academic_year' => 'required|integer|min:2500|max:2700',
            'announcement_date' => 'required|date_format:Y-m-d', // หรือ 'd/m/Y' แล้วแปลง
            'educational_area_id' => 'required|exists:educational_areas,id',
            'round_number' => 'required|integer|min:1',
            'subject_groups' => 'nullable|array', // เปลี่ยนเป็น nullable ถ้าอนุญาตให้ไม่มีวิชาเอกเลย (แต่ปกติควรมีอย่างน้อย 1)
            'subject_groups.*' => 'sometimes|exists:subject_groups,id', // 'sometimes' ถ้า array อาจจะว่าง
            'source_link' => 'nullable|url|max:2048',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240', // 10MB max
            'delete_attachments' => 'nullable|array',
            'delete_attachments.*' => 'integer|exists:placement_attachments,id',
        ]);

        $placementData = $request->only(['academic_year', 'announcement_date', 'educational_area_id', 'round_number', 'source_link']);
        // user_id ไม่ควรเปลี่ยนตอน update เว้นแต่มี logic เฉพาะ
        // $placementData['user_id'] = auth()->id();

        $placementRecord->update($placementData);

        // Sync subject groups
        if ($request->has('subject_groups')) {
            $placementRecord->subjectGroups()->sync($request->input('subject_groups'));
        } else {
            // ถ้าไม่มี subject_groups ส่งมาเลย และคุณต้องการลบทั้งหมดที่เคยมี
            $placementRecord->subjectGroups()->detach();
        }

        // Handle deleting existing attachments
        if ($request->filled('delete_attachments')) {
            $diskName = config('filesystems.default_private_disk', 'private');
            $attachmentsToDelete = PlacementAttachment::whereIn('id', $request->input('delete_attachments'))
                ->where('placement_record_id', $placementRecord->id) // Ensure they belong to this record
                ->get();
            foreach ($attachmentsToDelete as $attachment) {
                Storage::disk($diskName)->delete($attachment->file_path);
                $attachment->delete();
            }
        }

        // Handle new file attachments
        if ($request->hasFile('attachments')) {
            $diskName = config('filesystems.default_private_disk', 'private');
            $attachmentFolder = 'placement_attachments/' . $placementRecord->id; // โฟลเดอร์เฉพาะสำหรับ record นี้

            // สร้าง folder ถ้ายังไม่มี
            if (!Storage::disk($diskName)->exists($attachmentFolder)) {
                Storage::disk($diskName)->makeDirectory($attachmentFolder);
            }

            foreach ($request->file('attachments') as $file) {
                $originalFilename = $file->getClientOriginalName();
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension(); // สร้างชื่อไฟล์ที่ไม่ซ้ำ
                $path = $file->storeAs($attachmentFolder, $filename, $diskName);

                $placementRecord->attachments()->create([
                    'file_path' => $path,
                    'original_filename' => $originalFilename,
                    'mime_type' => $file->getMimeType(),
                    'type' => Str::startsWith($file->getMimeType(), 'image/') ? 'image' : 'file',
                ]);
            }
        }

        return redirect()->route('admin.placement-records.index')->with('success', 'แก้ไขข้อมูลการบรรจุสำเร็จ');
    }
    public function destroy(PlacementRecord $placementRecord)
    {
        try {
            // 1. ดึงชื่อหรือข้อมูลบางอย่างของ record ไว้สำหรับแสดงใน success message ก่อนที่จะลบ
            $recordIdentifier = "ปี {$placementRecord->academic_year} - เขตฯ " . ($placementRecord->educationalArea->name ?? 'N/A') . " - รอบ {$placementRecord->round_number}";

            // 2. ลบไฟล์แนบทั้งหมดที่เกี่ยวข้องกับ PlacementRecord นี้ออกจาก Storage
            // และลบ records ในตาราง placement_attachments
            // (ถ้า Foreign Key Constraint ในตาราง placement_attachments มี onDelete('cascade')
            // การลบ record ในตาราง placement_attachments จะเกิดขึ้นอัตโนมัติเมื่อ placementRecord ถูกลบ
            // แต่การลบไฟล์ออกจาก storage ยังคงต้องทำเอง)

            $diskName = config('filesystems.default_private_disk', 'private');
            foreach ($placementRecord->attachments as $attachment) {
                if (Storage::disk($diskName)->exists($attachment->file_path)) {
                    Storage::disk($diskName)->delete($attachment->file_path);
                }
                // ไม่จำเป็นต้อง $attachment->delete() ที่นี่ถ้า DB constraint มี onDelete('cascade')
                // แต่ถ้าไม่มี หรือต้องการความแน่นอน ก็สามารถทำได้
                // $attachment->delete();
            }

            // (Optional) ถ้าโฟลเดอร์ที่เก็บไฟล์แนบเป็นแบบเฉพาะ record (เช่น placement_attachments/{record_id})
            // คุณสามารถลบทั้งโฟลเดอร์ได้หลังจากลบไฟล์ทั้งหมดแล้ว
            // $attachmentFolder = 'placement_attachments/' . $placementRecord->id;
            // if (Storage::disk($diskName)->exists($attachmentFolder) && count(Storage::disk($diskName)->allFiles($attachmentFolder)) === 0) {
            //     Storage::disk($diskName)->deleteDirectory($attachmentFolder);
            // }

            // 3. Detach ความสัมพันธ์ Many-to-Many (Subject Groups)
            // ถ้า Foreign Key Constraint ในตาราง pivot (placement_record_subject_group)
            // มี onDelete('cascade') การ detach นี้จะไม่จำเป็น เพราะ record ใน pivot table จะถูกลบอัตโนมัติ
            // แต่การทำ detach เองก็ไม่เสียหายและเป็นการจัดการที่ชัดเจน
            $placementRecord->subjectGroups()->detach();

            // 4. ลบ PlacementRecord หลัก
            $placementRecord->delete();

            return redirect()
                ->route('admin.placement-records.index')
                ->with('success', 'ลบข้อมูลการบรรจุ "' . $recordIdentifier . '" สำเร็จเรียบร้อย');
        } catch (\Illuminate\Database\QueryException $e) {
            // จัดการ Database errors อื่นๆ (ที่ไม่ใช่ Foreign Key ที่ถูก handle โดย onDelete('cascade'))
            Log::error("Error deleting placement record ID {$placementRecord->id}: " . $e->getMessage());
            return redirect()->route('admin.placement-records.index')->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูลการบรรจุ กรุณาลองใหม่อีกครั้ง');
        } catch (\Exception $e) {
            // จัดการ Errors อื่นๆ ที่อาจเกิดขึ้น (เช่น ปัญหาการลบไฟล์)
            Log::error("General error deleting placement record ID {$placementRecord->id}: " . $e->getMessage());
            return redirect()
                ->route('admin.placement-records.index')
                ->with('error', 'เกิดข้อผิดพลาดไม่คาดคิดในการลบข้อมูล: ' . $e->getMessage());
        }
    }
}

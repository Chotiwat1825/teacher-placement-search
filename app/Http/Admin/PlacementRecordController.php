<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlacementRecord;
use App\Models\EducationalArea;
use App\Models\SubjectGroup;
use App\Models\PlacementAttachment; // เพิ่ม
use Illuminate\Http\Request; // ควรใช้ FormRequest
use Illuminate\Support\Facades\Storage; // เพิ่ม
use Illuminate\Support\Str; // เพิ่ม

class PlacementRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = PlacementRecord::with(['educationalArea', 'subjectGroups'])->latest('announcement_date');

        // Add search/filter logic if needed
        // if ($request->filled('search_term')) {
        //     $query->where(...);
        // }

        $placementRecords = $query->paginate(15);
        return view('admin.placement_records.index', compact('placementRecords'));
    }

    public function create()
    {
        $educationalAreas = EducationalArea::orderBy('name')->get();
        $subjectGroups = SubjectGroup::orderBy('name')->get();
        $currentThaiYear = now()->year + 543;
        $academicYears = range($currentThaiYear + 2, $currentThaiYear - 5);

        return view('admin.placement_records.create', compact('educationalAreas', 'subjectGroups', 'academicYears'));
    }

    public function store(Request $request)
    {
        // ควรเป็น StorePlacementRecordRequest
        $validated = $request->validate([
            'academic_year' => 'required|integer|min:2500|max:2700',
            'announcement_date' => 'required|date_format:Y-m-d', // หรือ d/m/Y แล้วแปลง
            'educational_area_id' => 'required|exists:educational_areas,id',
            'round_number' => 'required|integer|min:1',
            'subject_groups' => 'required|array|min:1',
            'subject_groups.*' => 'exists:subject_groups,id',
            'source_link' => 'nullable|url|max:2048',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max per file
        ]);

        $placementData = $request->only(['academic_year', 'announcement_date', 'educational_area_id', 'round_number', 'source_link']);
        $placementData['user_id'] = auth()->id(); // หรือ Auth::id()

        $placementRecord = PlacementRecord::create($placementData);

        // Attach subject groups
        if ($request->has('subject_groups')) {
            $placementRecord->subjectGroups()->sync($request->input('subject_groups'));
        }

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            $diskName = config('filesystems.default_private_disk', 'private');
            foreach ($request->file('attachments') as $file) {
                $originalFilename = $file->getClientOriginalName();
                // สร้างชื่อไฟล์ใหม่เพื่อป้องกันการชนกันและปัญหา encoding
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('placement_attachments/' . $placementRecord->id, $filename, $diskName);

                $placementRecord->attachments()->create([
                    'file_path' => $path,
                    'original_filename' => $originalFilename,
                    'mime_type' => $file->getMimeType(),
                    'type' => Str::startsWith($file->getMimeType(), 'image/') ? 'image' : 'file',
                ]);
            }
        }

        return redirect()->route('admin.placement-records.index')->with('success', 'สร้างข้อมูลการบรรจุสำเร็จ');
    }

    public function show(PlacementRecord $placementRecord)
    {
        $placementRecord->load(['educationalArea', 'subjectGroups', 'attachments', 'user']);
        return view('admin.placement_records.show', compact('placementRecord'));
    }

    public function edit(PlacementRecord $placementRecord)
    {
        $placementRecord->load('subjectGroups'); // Eager load current subject groups
        $educationalAreas = EducationalArea::orderBy('name')->get();
        $subjectGroups = SubjectGroup::orderBy('name')->get();
        $currentThaiYear = now()->year + 543;
        $academicYears = range($currentThaiYear + 2, $currentThaiYear - 5);

        return view('admin.placement_records.edit', compact('placementRecord', 'educationalAreas', 'subjectGroups', 'academicYears'));
    }

    public function update(Request $request, PlacementRecord $placementRecord)
    {
        // ควรเป็น UpdatePlacementRecordRequest
        $validated = $request->validate([
            'academic_year' => 'required|integer|min:2500|max:2700',
            'announcement_date' => 'required|date_format:Y-m-d',
            'educational_area_id' => 'required|exists:educational_areas,id',
            'round_number' => 'required|integer|min:1',
            'subject_groups' => 'sometimes|array', // 'sometimes' means only validate if present
            'subject_groups.*' => 'exists:subject_groups,id',
            'source_link' => 'nullable|url|max:2048',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'delete_attachments' => 'nullable|array', // For deleting existing attachments
            'delete_attachments.*' => 'integer|exists:placement_attachments,id',
        ]);

        $placementData = $request->only(['academic_year', 'announcement_date', 'educational_area_id', 'round_number', 'source_link']);
        $placementRecord->update($placementData);

        // Sync subject groups
        if ($request->has('subject_groups')) {
            $placementRecord->subjectGroups()->sync($request->input('subject_groups'));
        } else {
            // If no subject_groups are submitted, and you want to remove all existing ones
            $placementRecord->subjectGroups()->detach();
        }

        // Handle deleting existing attachments
        if ($request->filled('delete_attachments')) {
            $diskName = config('filesystems.default_private_disk', 'private');
            foreach ($request->input('delete_attachments') as $attachmentId) {
                $attachment = PlacementAttachment::find($attachmentId);
                if ($attachment && $attachment->placement_record_id == $placementRecord->id) {
                    // Ensure it belongs to this record
                    Storage::disk($diskName)->delete($attachment->file_path);
                    $attachment->delete();
                }
            }
        }

        // Handle new file attachments
        if ($request->hasFile('attachments')) {
            $diskName = config('filesystems.default_private_disk', 'private');
            foreach ($request->file('attachments') as $file) {
                $originalFilename = $file->getClientOriginalName();
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('placement_attachments/' . $placementRecord->id, $filename, $diskName);

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
        $diskName = config('filesystems.default_private_disk', 'private');
        // Delete associated attachments from storage and database
        foreach ($placementRecord->attachments as $attachment) {
            Storage::disk($diskName)->delete($attachment->file_path);
            $attachment->delete(); // This will also be handled by onDelete('cascade') if set on DB
        }
        // Detach subject groups (pivot table records will be deleted by onDelete('cascade'))
        // $placementRecord->subjectGroups()->detach(); // Not strictly necessary if DB cascade is set

        $placementRecord->delete();

        return redirect()->route('admin.placement-records.index')->with('success', 'ลบข้อมูลการบรรจุสำเร็จ');
    }
}

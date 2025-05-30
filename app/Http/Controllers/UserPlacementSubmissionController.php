<?php

namespace App\Http\Controllers; // << Namespace อาจจะเป็น App\Http\Controllers เฉยๆ

use App\Models\PlacementRecord;
use App\Models\EducationalArea;
use App\Models\SubjectGroup;
use App\Models\PlacementType;
use Illuminate\Http\Request; // จะเปลี่ยนเป็น FormRequest
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // ถ้า User อัปโหลดไฟล์
use Illuminate\Support\Str; // ถ้า User อัปโหลดไฟล์
use App\Http\Requests\UserSubmitPlacementRequest; // << สร้าง Form Request นี้

class UserPlacementSubmissionController extends Controller
{
    /**
     * Display a listing of the submissions for the authenticated user.
     * (จะทำในขั้นตอนถัดไป)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = PlacementRecord::where('user_id', $user->id) // <<<< ดึงเฉพาะข้อมูลของ user ที่ login
            ->with(['educationalArea', 'subjectGroups', 'placementType']) // Eager load
            ->orderBy('created_at', 'desc'); // เรียงตามวันที่สร้างล่าสุด

        // (Optional) Filter by status
        if ($request->filled('status_filter') && in_array($request->status_filter, [PlacementRecord::STATUS_PENDING, PlacementRecord::STATUS_APPROVED, PlacementRecord::STATUS_REJECTED])) {
            $query->where('status', $request->status_filter);
        }

        // (Optional) Search term (ถ้า User ต้องการค้นหาข้อมูลของตัวเอง)
        if ($request->filled('search_term_user')) {
            $searchTerm = $request->search_term_user;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('academic_year', 'like', "%{$searchTerm}%")
                    ->orWhere('round_number', 'like', "%{$searchTerm}%")
                    ->orWhereHas('educationalArea', fn($sq) => $sq->where('name', 'like', "%{$searchTerm}%"))
                    ->orWhereHas('subjectGroups', fn($sq) => $sq->where('name', 'like', "%{$searchTerm}%"))
                    ->orWhereHas('placementType', fn($sq) => $sq->where('name', 'like', "%{$searchTerm}%"))
                    ->orWhere('notes', 'like', "%{$searchTerm}%");
            });
        }

        $submissions = $query->paginate(10)->withQueryString();

        $statusOptions = [
            PlacementRecord::STATUS_PENDING => 'รอการอนุมัติ',
            PlacementRecord::STATUS_APPROVED => 'อนุมัติแล้ว',
            PlacementRecord::STATUS_REJECTED => 'ถูกปฏิเสธ',
        ];

        return view('user.submissions.index', compact('submissions', 'statusOptions'));
    }

    /**
     * Show the form for creating a new placement submission by a user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // ป้องกัน Admin เข้าหน้านี้ (ถ้าต้องการ)
        // if (Auth::user()->is_admin) {
        //     return redirect()->route('admin.dashboard')->with('info', 'ผู้ดูแลระบบไม่จำเป็นต้องใช้หน้านี้');
        // }

        $educationalAreas = EducationalArea::orderBy('name')->get();
        $subjectGroups = SubjectGroup::orderBy('name')->get();
        $placementTypes = PlacementType::where('is_active', true)->orderBy('name')->get();
        $currentThaiYear = now()->year + 543;
        // User อาจจะส่งข้อมูลย้อนหลังได้ไม่มากเท่า Admin
        $academicYears = range($currentThaiYear + 1, $currentThaiYear - 3);
        $LastYear = PlacementRecord::max('academic_year') ?? $currentThaiYear;

        return view(
            'user.placements.create',
            compact(
                // << Path ไปยัง View ของ User
                'educationalAreas',
                'subjectGroups',
                'academicYears',
                'placementTypes',
                'LastYear',
            ),
        );
    }

    /**
     * Store a newly created placement submission in storage.
     *
     * @param  \App\Http\Requests\UserSubmitPlacementRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserSubmitPlacementRequest $request)
    {
        // << ใช้ FormRequest
        $validatedData = $request->validated();

        try {
            $placementData = [
                'academic_year' => $validatedData['academic_year'],
                'announcement_date' => $validatedData['announcement_date'],
                'educational_area_id' => $validatedData['educational_area_id'],
                'round_number' => $validatedData['round_number'],
                'placement_type_id' => $validatedData['placement_type_id'] ?? null,
                'source_link' => $validatedData['source_link'] ?? null,
                'notes' => $validatedData['notes'] ?? null,
                'user_id' => Auth::id(), // ID ของ User ที่ login และส่งข้อมูล
                'status' => PlacementRecord::STATUS_PENDING, // << สถานะเริ่มต้นคือ "รออนุมัติ"
                // processed_by_user_id และ processed_at จะเป็น null จนกว่า Admin จะดำเนินการ
            ];

            $placementRecord = PlacementRecord::create($placementData);

            if (!empty($validatedData['subject_groups'])) {
                $placementRecord->subjectGroups()->attach($validatedData['subject_groups']);
            }

            // Handle file attachments (ถ้า User สามารถอัปโหลดได้)
            if ($request->hasFile('attachments')) {
                $diskName = config('filesystems.default_private_disk', 'private');
                $attachmentFolder = 'placement_attachments/' . $placementRecord->id; // ใช้ ID ของ record ที่เพิ่งสร้าง
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
                ->route('user.submissions.index') // หรือ route('home') หรือหน้าที่เหมาะสม
                ->with('success', 'ส่งข้อมูลการบรรจุสำเร็จ! ข้อมูลของคุณจะถูกตรวจสอบโดยผู้ดูแลระบบ');
        } catch (\Exception $e) {
            \Log::error('User submission error for user ' . Auth::id() . ': ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()
                ->back()
                ->with('error', 'เกิดข้อผิดพลาดในการส่งข้อมูล: ' . $e->getMessage())
                ->withInput();
        }
    }
}

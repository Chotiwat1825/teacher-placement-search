<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EducationalArea;
use App\Models\SubjectGroup;
use App\Models\PlacementType;
use App\Models\PlacementRecord;
use Carbon\Carbon;


class PublicSearchController extends Controller
{
    /**
     * Display the search page and search results.
     */
    public function index(Request $request)
    {
        $educationalAreas = EducationalArea::orderBy('name')->get();
        $subjectGroupsForFilter = SubjectGroup::orderBy('name')->get();
        $placementTypesForFilter = PlacementType::where('is_active', true)->orderBy('name')->get(); // << เพิ่ม ถ้าจะให้ filter ตามประเภท
        $currentThaiYear = Carbon::now()->year + 543;
        $academicYears = [];
        for ($i = $currentThaiYear + 1; $i >= $currentThaiYear - 5; $i--) {
            // ปรับช่วงปีตามความเหมาะสม
            $academicYears[] = $i;
        }

        // Query placement records
        $query = PlacementRecord::query()
            ->where('status', PlacementRecord::STATUS_APPROVED) // <<<< เงื่อนไขสำคัญ: แสดงเฉพาะที่อนุมัติแล้ว
            ->with(['educationalArea', 'subjectGroups', 'placementType']); // Eager load

        // Apply filters from request
        if ($request->filled('educational_area_id')) {
            $query->where('educational_area_id', $request->educational_area_id);
        }
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        if ($request->filled('subject_group_id')) {
            $query->whereHas('subjectGroups', function ($q) use ($request) {
                $q->where('subject_groups.id', $request->subject_group_id);
            });
        }
        if ($request->filled('placement_type_id')) {
            // << เพิ่ม filter ประเภทการบรรจุ
            $query->where('placement_type_id', $request->placement_type_id);
        }
        // (Optional) Search term
        if ($request->filled('q')) {
            // สมมติใช้ q สำหรับ search term
            $searchTerm = $request->q;
            $query->where(function ($subQuery) use ($searchTerm) {
                $subQuery
                    ->where('notes', 'like', "%{$searchTerm}%") // ค้นหาในหมายเหตุ
                    ->orWhereHas('educationalArea', fn($sq) => $sq->where('name', 'like', "%{$searchTerm}%"))
                    ->orWhereHas('subjectGroups', fn($sq) => $sq->where('name', 'like', "%{$searchTerm}%"))
                    ->orWhereHas('placementType', fn($sq) => $sq->where('name', 'like', "%{$searchTerm}%"));
            });
        }

        $placements = $query->orderBy('announcement_date', 'desc')->orderBy('academic_year', 'desc')->orderBy('round_number', 'asc')->paginate(15)->withQueryString();

        return view(
            'frontend.search',
            compact(
                'educationalAreas',
                'subjectGroupsForFilter',
                'placementTypesForFilter', // << ส่งไป view
                'academicYears',
                'placements',
                'request',
            ),
        );
    }

    /**
     * Display the details of a specific placement record.
     * Uses Route Model Binding.
     */
    public function showDetails(PlacementRecord $placementRecord)
    {
        // Route Model Binding
        // ตรวจสอบสถานะ ถ้าไม่ approved อาจจะ redirect หรือ abort
        if ($placementRecord->status !== PlacementRecord::STATUS_APPROVED) {
            // อาจจะ redirect ไปหน้า search พร้อม message หรือแสดงหน้า 404
            // return redirect()->route('search.index')->with('warning', 'ไม่พบข้อมูลการบรรจุตามที่ระบุ');
            abort(404, 'Placement record not found or not approved.');
        }

        $placementRecord->load(['educationalArea', 'subjectGroups', 'attachments', 'placementType', 'creator' /* หรือ user() */]);

        // Query ข้อมูลรอบอื่นๆ ที่เกี่ยวข้อง (และต้อง approved ด้วย)
        $relatedRoundsQuery = PlacementRecord::where('status', PlacementRecord::STATUS_APPROVED) // <<<< เพิ่มเงื่อนไข status
            ->where('educational_area_id', $placementRecord->educational_area_id)
            ->where('academic_year', $placementRecord->academic_year)
            ->where('id', '!=', $placementRecord->id);

        if ($placementRecord->subjectGroups->isNotEmpty()) {
            $subjectGroupIds = $placementRecord->subjectGroups->pluck('id')->toArray();
            $relatedRoundsQuery->whereHas('subjectGroups', function ($q) use ($subjectGroupIds) {
                $q->whereIn('subject_groups.id', $subjectGroupIds);
            });
        }
        // (Optional) ถ้ามี placement_type_id ก็อาจจะ filter รอบอื่นที่มี type เดียวกัน
        // if ($placementRecord->placement_type_id) {
        //     $relatedRoundsQuery->where('placement_type_id', $placementRecord->placement_type_id);
        // }

        $relatedRounds = $relatedRoundsQuery->orderBy('round_number', 'asc')->get();

        return view('frontend.details', compact('placementRecord', 'relatedRounds'));
    }
}

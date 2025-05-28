<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PlacementRecord;
use App\Models\EducationalArea;
use App\Models\SubjectGroup;
use App\Models\User;
use Carbon\Carbon; // สำหรับการทำงานกับวันที่
use Illuminate\Support\Facades\DB; // สำหรับ Raw DB Queries (ถ้าจำเป็น)

class DashboardController extends Controller
{
    public function index()
    {
        // --- ข้อมูลสำหรับ Info Boxes ---
        $totalPlacements = PlacementRecord::count();
        $totalEducationalAreas = EducationalArea::count();
        $totalSubjectGroups = SubjectGroup::count();

        // จำนวนประกาศในเดือนปัจจุบัน
        $placementsThisMonth = PlacementRecord::whereMonth('announcement_date', Carbon::now()->month)
            ->whereYear('announcement_date', Carbon::now()->year)
            ->count();

        // --- ข้อมูลสำหรับตาราง "การบรรจุล่าสุด" ---
        $latestPlacements = PlacementRecord::with(['educationalArea', 'subjectGroups'])
            ->orderBy('announcement_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(7) // แสดง 7 รายการล่าสุด
            ->get();

        // --- ข้อมูลสำหรับ Bar Chart: จำนวนการบรรจุต่อปี ---
        $placementCountsByYear = PlacementRecord::selectRaw('academic_year, count(*) as count')->groupBy('academic_year')->orderBy('academic_year', 'asc')->pluck('count', 'academic_year');

        // --- (Optional) ข้อมูลสำหรับ Pie Chart: สัดส่วนการบรรจุตามประเภทเขตพื้นที่ฯ ---
        $placementsByAreaType = PlacementRecord::join('educational_areas', 'placement_records.educational_area_id', '=', 'educational_areas.id')->selectRaw('educational_areas.type, count(placement_records.id) as count')->groupBy('educational_areas.type')->pluck('count', 'type');

        // --- (Optional) ข้อมูลสำหรับ Pie Chart: 5 กลุ่มวิชาเอกที่มีการประกาศบรรจุมากที่สุด ---
        $topSubjectGroups = DB::table('placement_record_subject_group')->join('subject_groups', 'placement_record_subject_group.subject_group_id', '=', 'subject_groups.id')->selectRaw('subject_groups.name, count(placement_record_subject_group.placement_record_id) as count')->groupBy('subject_groups.name')->orderBy('count', 'desc')->take(5)->pluck('count', 'name');

        return view(
            'admin.dashboard',
            compact(
                'totalPlacements',
                'totalEducationalAreas',
                'totalSubjectGroups',
                'placementsThisMonth',
                'latestPlacements',
                'placementCountsByYear',
                'placementsByAreaType', // ส่งไป view
                'topSubjectGroups', // ส่งไป view
            ),
        );
    }
}

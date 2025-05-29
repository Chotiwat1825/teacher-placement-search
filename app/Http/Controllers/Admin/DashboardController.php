<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PlacementRecord;
use App\Models\EducationalArea;
use App\Models\SubjectGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // --- ข้อมูลสำหรับ Info Boxes ---
        $totalPlacements = PlacementRecord::count();
        $totalEducationalAreas = EducationalArea::count();
        $totalSubjectGroups = SubjectGroup::count();
        $placementsThisMonth = PlacementRecord::whereMonth('announcement_date', Carbon::now()->month)
            ->whereYear('announcement_date', Carbon::now()->year)
            ->count();
        $activeUsers = User::whereNotNull('last_seen_at') // สมมติว่ามี field 'last_seen_at'
            ->where('last_seen_at', '>', Carbon::now()->subMinutes(30)) // active ใน 30 นาทีล่าสุด
            ->count();
        // $pendingApprovals = YourModelForApproval::where('status', 'pending')->count(); // ถ้ามีระบบอนุมัติ

        // --- ข้อมูลสำหรับตาราง "การบรรจุล่าสุด" ---
        $latestPlacements = PlacementRecord::with(['educationalArea', 'subjectGroups'])
            ->orderBy('announcement_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(7)
            ->get();

        // --- ข้อมูลสำหรับ Bar Chart: จำนวนการบรรจุต่อปี ---
        $placementCountsByYear = PlacementRecord::selectRaw('academic_year, count(*) as count')
            ->groupBy('academic_year')
            ->orderBy('academic_year', 'desc') // เรียงจากปีล่าสุดไปเก่า
            ->take(5) // แสดง 5 ปีล่าสุด
            ->get()
            ->pluck('count', 'academic_year')
            ->reverse(); // กลับด้านให้ปีเก่าไปใหม่สำหรับ chart

        // --- ข้อมูลสำหรับ Pie Chart: สัดส่วนการบรรจุตามประเภทเขตพื้นที่ฯ ---
        $placementsByAreaType = PlacementRecord::join('educational_areas', 'placement_records.educational_area_id', '=', 'educational_areas.id')->selectRaw('educational_areas.type, count(placement_records.id) as count')->groupBy('educational_areas.type')->pluck('count', 'type');

        // --- ข้อมูลสำหรับ Bar Chart: Top 5 เขตพื้นที่ฯ ที่มีการประกาศบรรจุมากที่สุด ---
        $topEducationalAreas = PlacementRecord::join('educational_areas', 'placement_records.educational_area_id', '=', 'educational_areas.id')
            ->selectRaw('educational_areas.name as area_name, count(placement_records.id) as count')
            ->groupBy('educational_areas.id', 'educational_areas.name') // Group by ID and Name
            ->orderBy('count', 'desc')
            ->take(5)
            ->pluck('count', 'area_name');

        // --- ข้อมูลสำหรับ Line Chart: แนวโน้มการประกาศบรรจุรายเดือน (6 เดือนล่าสุด) ---
        $monthlyPlacements = PlacementRecord::select(
            DB::raw("DATE_FORMAT(announcement_date, '%Y-%m') as month_year"), // MySQL/MariaDB
            // DB::raw("strftime('%Y-%m', announcement_date) as month_year"), // SQLite
            // DB::raw("TO_CHAR(announcement_date, 'YYYY-MM') as month_year"), // PostgreSQL
            DB::raw('count(*) as count'),
        )
            ->where('announcement_date', '>=', Carbon::now()->subMonths(5)->startOfMonth()) // 6 เดือนรวมเดือนปัจจุบัน
            ->groupBy('month_year')
            ->orderBy('month_year', 'asc')
            ->pluck('count', 'month_year');
        $topSubjectGroups = DB::table('placement_record_subject_group')
            ->join('subject_groups', 'placement_record_subject_group.subject_group_id', '=', 'subject_groups.id')
            ->selectRaw('subject_groups.name, count(placement_record_subject_group.placement_record_id) as count')
            ->groupBy('subject_groups.name') // หรือ subject_groups.id, subject_groups.name
            ->orderBy('count', 'desc')
            ->take(5)
            ->pluck('count', 'name');

        return view('admin.dashboard', compact(
        'totalPlacements',
        'totalEducationalAreas',
        'totalSubjectGroups',
        'placementsThisMonth',
        'activeUsers',
        'latestPlacements',
        'placementCountsByYear',
        'placementsByAreaType',
        'topEducationalAreas',
        'monthlyPlacements',
        'topSubjectGroups',
            ),
        );
    }
}

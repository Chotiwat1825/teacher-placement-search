<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PlacementRecord;
use App\Models\EducationalArea;
use App\Models\SubjectGroup;
use App\Models\User; // ถ้าต้องการแสดงจำนวนผู้ใช้

class DashboardController extends Controller
{
    public function index()
    {
        // ดึงข้อมูลสรุป
        $totalPlacements = PlacementRecord::count();
        $totalEducationalAreas = EducationalArea::count();
        $totalSubjectGroups = SubjectGroup::count();
        $totalUsers = User::count(); // จำนวนผู้ใช้ทั้งหมด (รวม admin และ user ทั่วไป)
        $totalAdminUsers = User::where('is_admin', true)->count(); // จำนวน admin

        // ดึงข้อมูลการบรรจุล่าสุด 5 รายการ (ตัวอย่าง)
        $latestPlacements = PlacementRecord::with(['educationalArea', 'subjectGroups'])
            ->orderBy('announcement_date', 'desc') // เรียงจากวันที่ประกาศล่าสุด
            ->orderBy('created_at', 'desc') // หรือวันที่สร้างล่าสุด
            ->take(5) // เอามา 5 รายการ
            ->get();

        // ข้อมูลสำหรับ Chart (ตัวอย่างง่ายๆ: จำนวนการบรรจุต่อปี)
        // ในการใช้งานจริง อาจจะต้อง query ที่ซับซ้อนกว่านี้ หรือใช้ library ช่วยสร้าง chart data
        $placementCountsByYear = PlacementRecord::selectRaw('academic_year, count(*) as count')->groupBy('academic_year')->orderBy('academic_year', 'asc')->pluck('count', 'academic_year'); // ให้ผลลัพธ์เป็น ['2566' => 10, '2567' => 15]

        return view('admin.dashboard', compact('totalPlacements', 'totalEducationalAreas', 'totalSubjectGroups', 'totalUsers', 'totalAdminUsers', 'latestPlacements', 'placementCountsByYear'));
    }
}

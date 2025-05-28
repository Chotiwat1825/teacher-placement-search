<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EducationalArea;
use Illuminate\Http\Request; // ควรใช้ FormRequest ในภายหลัง

class EducationalAreaController extends Controller
{
    public function index()
    {
        $educationalAreas = EducationalArea::orderBy('name')->paginate(15);
        return view('admin.educational_areas.index', compact('educationalAreas'));
    }

    public function create()
    {
        return view('admin.educational_areas.create');
    }

    public function store(Request $request)
    {
        // เปลี่ยนเป็น FormRequest เช่น StoreEducationalAreaRequest
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:educational_areas,name',
            'code' => 'nullable|string|max:50|unique:educational_areas,code',
            'type' => 'required|in:primary,secondary',
        ]);

        EducationalArea::create($validated);

        return redirect()->route('admin.educational-areas.index')->with('success', 'เพิ่มเขตพื้นที่การศึกษาสำเร็จ');
    }

    public function show(EducationalArea $educationalArea)
    {
        // ปกติ resource controller จะมี show แต่ถ้าไม่ใช้ ก็ redirect หรือแสดง view
        return view('admin.educational_areas.show', compact('educationalArea'));
    }

    public function edit(EducationalArea $educationalArea)
    {
        return view('admin.educational_areas.edit', compact('educationalArea'));
    }

    public function update(Request $request, EducationalArea $educationalArea)
    {
        // เปลี่ยนเป็น FormRequest เช่น UpdateEducationalAreaRequest
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:educational_areas,name,' . $educationalArea->id,
            'code' => 'nullable|string|max:50|unique:educational_areas,code,' . $educationalArea->id,
            'type' => 'required|in:primary,secondary',
        ]);

        $educationalArea->update($validated);

        return redirect()->route('admin.educational-areas.index')->with('success', 'แก้ไขเขตพื้นที่การศึกษาสำเร็จ');
    }

    public function destroy(EducationalArea $educationalArea)
    {
        try {
            // ตรวจสอบ dependencies ก่อนลบ ถ้าจำเป็น (เช่น ถ้ามี placement records อ้างอิงอยู่ และ onDelete ไม่ใช่ cascade)
            // if ($educationalArea->placementRecords()->exists()) {
            //     return redirect()->route('admin.educational-areas.index')->with('error', 'ไม่สามารถลบเขตพื้นที่ฯ นี้ได้ เนื่องจากมีการใช้งานอยู่');
            // }
            $educationalArea->delete();
            return redirect()->route('admin.educational-areas.index')->with('success', 'ลบเขตพื้นที่การศึกษาสำเร็จ');
        } catch (\Illuminate\Database\QueryException $e) {
            // จัดการ Foreign key constraint violation error (ถ้า onDelete = 'restrict')
            if ($e->errorInfo[1] == 1451) {
                // MySQL error code for foreign key constraint
                return redirect()->route('admin.educational-areas.index')->with('error', 'ไม่สามารถลบเขตพื้นที่ฯ นี้ได้ เนื่องจากมีการอ้างอิงข้อมูลอื่นอยู่');
            }
            return redirect()
                ->route('admin.educational-areas.index')
                ->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage());
        }
    }
}

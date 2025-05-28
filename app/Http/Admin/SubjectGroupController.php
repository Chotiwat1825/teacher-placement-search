<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubjectGroup;
use Illuminate\Http\Request; // จะเปลี่ยนเป็น FormRequest ในภายหลัง
use Illuminate\Validation\Rule; // สำหรับ Unique rule ตอน Update

class SubjectGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SubjectGroup::query()->orderBy('name', 'asc');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")->orWhere('code', 'like', "%{$searchTerm}%");
            });
        }

        $subjectGroups = $query->paginate(15)->withQueryString(); // ให้ pagination จำค่า search ด้วย
        return view('admin.subject_groups.index', compact('subjectGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.subject_groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ควรเป็น: StoreSubjectGroupRequest $request
        $validatedData = $request->validate(
            [
                'name' => 'required|string|max:255|unique:subject_groups,name',
                'code' => 'nullable|string|max:50|unique:subject_groups,code',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อกลุ่มวิชาเอก',
                'name.unique' => 'ชื่อกลุ่มวิชาเอกนี้มีในระบบแล้ว',
                'code.unique' => 'รหัสกลุ่มวิชานี้มีในระบบแล้ว',
            ],
        );

        SubjectGroup::create($validatedData);

        return redirect()->route('admin.subject-groups.index')->with('success', 'เพิ่มกลุ่มวิชาเอกสำเร็จเรียบร้อย');
    }

    /**
     * Display the specified resource.
     * (อาจจะไม่ค่อยได้ใช้ใน CRUD ที่แสดงผลใน index และ edit/create)
     */
    public function show(SubjectGroup $subjectGroup)
    {
        // ถ้าต้องการแสดงรายละเอียดของกลุ่มวิชาเอกในหน้าแยกต่างหาก
        return view('admin.subject_groups.show', compact('subjectGroup'));
        // หรือถ้าไม่ใช้หน้านี้ ก็ redirect ไปหน้า edit หรือ index
        // return redirect()->route('admin.subject-groups.edit', $subjectGroup);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubjectGroup $subjectGroup)
    {
        return view('admin.subject_groups.edit', compact('subjectGroup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubjectGroup $subjectGroup)
    {
        // ควรเป็น: UpdateSubjectGroupRequest $request
        $validatedData = $request->validate(
            [
                'name' => ['required', 'string', 'max:255', Rule::unique('subject_groups')->ignore($subjectGroup->id)],
                'code' => ['nullable', 'string', 'max:50', Rule::unique('subject_groups')->ignore($subjectGroup->id)],
            ],
            [
                'name.required' => 'กรุณากรอกชื่อกลุ่มวิชาเอก',
                'name.unique' => 'ชื่อกลุ่มวิชาเอกนี้มีในระบบแล้ว',
                'code.unique' => 'รหัสกลุ่มวิชานี้มีในระบบแล้ว',
            ],
        );

        $subjectGroup->update($validatedData);

        return redirect()->route('admin.subject-groups.index')->with('success', 'แก้ไขกลุ่มวิชาเอกสำเร็จเรียบร้อย');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubjectGroup $subjectGroup)
    {
        try {
            // ตรวจสอบว่ามี PlacementRecord อ้างอิงถึง SubjectGroup นี้หรือไม่
            // ถ้า Foreign Key Constraint ในตาราง placement_record_subject_group
            // ถูกตั้งค่าเป็น onDelete('restrict') การลบจะล้มเหลวถ้ามีการใช้งานอยู่
            if ($subjectGroup->placementRecords()->exists()) {
                return redirect()
                    ->route('admin.subject-groups.index')
                    ->with('error', 'ไม่สามารถลบกลุ่มวิชาเอก "' . $subjectGroup->name . '" ได้ เนื่องจากมีการใช้งานในข้อมูลการบรรจุ');
            }

            $subjectGroup->delete();

            return redirect()
                ->route('admin.subject-groups.index')
                ->with('success', 'ลบกลุ่มวิชาเอก "' . $subjectGroup->name . '" สำเร็จเรียบร้อย');
        } catch (\Illuminate\Database\QueryException $e) {
            // จัดการ Foreign key constraint violation error (ถ้า onDelete = 'restrict' ในตารางอื่นที่อาจอ้างอิง)
            // MySQL error code for foreign key constraint violation is 1451
            if ($e->errorInfo[1] == 1451) {
                return redirect()
                    ->route('admin.subject-groups.index')
                    ->with('error', 'ไม่สามารถลบกลุ่มวิชาเอก "' . $subjectGroup->name . '" ได้ เนื่องจากมีการอ้างอิงข้อมูลอื่นอยู่');
            }
            // สำหรับ error อื่นๆ
            return redirect()
                ->route('admin.subject-groups.index')
                ->with('error', 'เกิดข้อผิดพลาดในการลบกลุ่มวิชาเอก: ' . $e->getMessage());
        }
    }
}

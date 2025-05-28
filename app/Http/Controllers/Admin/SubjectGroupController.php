<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubjectGroup;
use App\Http\Requests\StoreSubjectGroupRequest; // Import Store Request
use App\Http\Requests\UpdateSubjectGroupRequest; // Import Update Request
use Illuminate\Http\Request; // ใช้สำหรับ index method ที่มีการ search

class SubjectGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
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

        // เพิ่มการนับจำนวน placement_records ที่เกี่ยวข้อง (ถ้าต้องการแสดงในตาราง index)
        // การใช้ withCount จะมีประสิทธิภาพดีกว่าการ loop แล้ว count ใน view
        $subjectGroups = $query->withCount('placementRecords')->paginate(15)->withQueryString();

        return view('admin.subject_groups.index', compact('subjectGroups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.subject_groups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSubjectGroupRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreSubjectGroupRequest $request)
    {
        // Validation is handled by StoreSubjectGroupRequest
        // If validation passes, $request->validated() contains the validated data
        $validatedData = $request->validated();

        SubjectGroup::create($validatedData);

        return redirect()
            ->route('admin.subject-groups.index')
            ->with('success', 'เพิ่มกลุ่มวิชาเอก "' . $validatedData['name'] . '" สำเร็จเรียบร้อย');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SubjectGroup  $subjectGroup
     * @return \Illuminate\View\View
     */
    public function show(SubjectGroup $subjectGroup)
    {
        // Eager load related placement records for the show page if needed
        // $subjectGroup->load(['placementRecords' => function ($query) {
        //     $query->with('educationalArea')->latest('announcement_date')->take(10);
        // }]);
        return view('admin.subject_groups.show', compact('subjectGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SubjectGroup  $subjectGroup
     * @return \Illuminate\View\View
     */
    public function edit(SubjectGroup $subjectGroup)
    {
        return view('admin.subject_groups.edit', compact('subjectGroup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSubjectGroupRequest  $request
     * @param  \App\Models\SubjectGroup  $subjectGroup
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateSubjectGroupRequest $request, SubjectGroup $subjectGroup)
    {
        // Validation is handled by UpdateSubjectGroupRequest
        $validatedData = $request->validated();

        $subjectGroup->update($validatedData);

        return redirect()
            ->route('admin.subject-groups.index')
            ->with('success', 'แก้ไขกลุ่มวิชาเอก "' . $subjectGroup->name . '" สำเร็จเรียบร้อย');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SubjectGroup  $subjectGroup
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(SubjectGroup $subjectGroup)
    {
        try {
            // Check if the subject group is being used by any placement records
            // This check is more explicit than relying solely on DB foreign key constraints
            // if onDelete for placement_record_subject_group is 'restrict' or not set to 'cascade'
            if ($subjectGroup->placementRecords()->exists()) {
                return redirect()
                    ->route('admin.subject-groups.index')
                    ->with('error', 'ไม่สามารถลบกลุ่มวิชาเอก "' . $subjectGroup->name . '" ได้ เนื่องจากมีการใช้งานในข้อมูลการบรรจุอยู่');
            }

            $groupName = $subjectGroup->name; // Store name before deleting
            $subjectGroup->delete();

            return redirect()
                ->route('admin.subject-groups.index')
                ->with('success', 'ลบกลุ่มวิชาเอก "' . $groupName . '" สำเร็จเรียบร้อย');
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle potential foreign key constraint violation if not checked above or if other tables reference it
            // MySQL error code for foreign key constraint violation is 1451
            if ($e->errorInfo[1] == 1451) {
                return redirect()
                    ->route('admin.subject-groups.index')
                    ->with('error', 'ไม่สามารถลบกลุ่มวิชาเอก "' . $subjectGroup->name . '" ได้ เนื่องจากมีการอ้างอิงข้อมูลอื่นอยู่');
            }
            // For other database errors
            \Log::error('Error deleting subject group: ' . $e->getMessage()); // Log the error
            return redirect()->route('admin.subject-groups.index')->with('error', 'เกิดข้อผิดพลาดในการลบกลุ่มวิชาเอก กรุณาลองใหม่อีกครั้ง');
        }
    }
}

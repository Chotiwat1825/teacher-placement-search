<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EducationalArea;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // สำหรับ unique rule ตอน update

class EducationalAreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = EducationalArea::query()->orderBy('name', 'asc');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('code', 'like', "%{$searchTerm}%")
                    ->orWhere('type', 'like', "%{$searchTerm}%"); // ค้นหาตามประเภทด้วย
            });
        }

        // Filter by type (primary or secondary)
        if ($request->filled('type_filter') && in_array($request->type_filter, ['primary', 'secondary'])) {
            $query->where('type', $request->type_filter);
        }

        // Eager load count of related placement records for efficiency
        $educationalAreas = $query->withCount('placementRecords')->paginate(20)->withQueryString();
        // เพิ่มจำนวนรายการต่อหน้าเป็น 20 เนื่องจากมีเขตพื้นที่เยอะ

        return view('admin.educational_areas.index', compact('educationalAreas'));
    }

    // ... (ส่วน create, store, show, edit, update, destroy methods จะตามมาทีหลัง) ...

    public function create()
    {
        return view('admin.educational_areas.create');
    }

    // Store method (ตัวอย่าง - ควรใช้ FormRequest)
    public function store(Request $request)
    {
        // ควรเปลี่ยนเป็น StoreEducationalAreaRequest
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255|unique:educational_areas,name',
                'code' => 'nullable|string|max:50|unique:educational_areas,code',
                'type' => 'required|in:primary,secondary',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อเขตพื้นที่การศึกษา',
                'name.unique' => 'ชื่อเขตพื้นที่การศึกษานี้มีในระบบแล้ว',
                'code.unique' => 'รหัสเขตพื้นที่ฯ นี้มีในระบบแล้ว',
                'type.required' => 'กรุณาเลือกประเภทเขตพื้นที่การศึกษา',
            ],
        );

        EducationalArea::create($validated);

        return redirect()
            ->route('admin.educational-areas.index')
            ->with('success', 'เพิ่มเขตพื้นที่การศึกษา "' . $validated['name'] . '" สำเร็จเรียบร้อย');
    }

    public function show(EducationalArea $educationalArea)
    {
        // Eager load placement records associated with this educational area
        // and also the subject groups for each of those placement records.
        // Paginate the placement records for better display if there are many.
        $educationalArea->load([
            'placementRecords' => function ($query) {
                $query
                    ->with('subjectGroups') // Eager load subject groups for each placement
                    ->orderBy('announcement_date', 'desc')
                    ->orderBy('academic_year', 'desc')
                    ->orderBy('round_number', 'asc');
            },
        ]);

        // If you want to paginate the placement records directly on the educational area
        // $placementRecords = $educationalArea->placementRecords()
        //                                     ->with('subjectGroups')
        //                                     ->orderBy('announcement_date', 'desc')
        //                                     ->paginate(10); // Example: 10 records per page

        return view('admin.educational_areas.show', compact('educationalArea'));
        // ถ้าคุณ paginate placementRecordsแยกต่างหาก ก็ส่งตัวแปรนั้นไปด้วย
        // return view('admin.educational_areas.show', compact('educationalArea', 'placementRecords'));
    }

    public function edit(EducationalArea $educationalArea)
    {
        // ส่ง instance ของ EducationalArea ที่ถูก resolve โดย Route Model Binding ไปยัง view
        return view('admin.educational_areas.edit', compact('educationalArea'));
    }

    // Update method (ตัวอย่าง - ควรใช้ FormRequest)
    public function update(Request $request, EducationalArea $educationalArea)
    {
        // ควรเปลี่ยนเป็น UpdateEducationalAreaRequest
        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255', Rule::unique('educational_areas')->ignore($educationalArea->id)],
                'code' => ['nullable', 'string', 'max:50', Rule::unique('educational_areas')->ignore($educationalArea->id)],
                'type' => 'required|in:primary,secondary',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อเขตพื้นที่การศึกษา',
                'name.unique' => 'ชื่อเขตพื้นที่การศึกษานี้มีในระบบแล้ว',
                'code.unique' => 'รหัสเขตพื้นที่ฯ นี้มีในระบบแล้ว',
                'type.required' => 'กรุณาเลือกประเภทเขตพื้นที่การศึกษา',
            ],
        );

        $educationalArea->update($validated);

        return redirect()
            ->route('admin.educational-areas.index')
            ->with('success', 'แก้ไขเขตพื้นที่การศึกษา "' . $educationalArea->name . '" สำเร็จเรียบร้อย');
    }

    // Destroy method (ตัวอย่าง)
    public function destroy(EducationalArea $educationalArea)
    {
        if ($educationalArea->placementRecords()->exists()) {
            return redirect()
                ->route('admin.educational-areas.index')
                ->with('error', 'ไม่สามารถลบเขตพื้นที่ฯ "' . $educationalArea->name . '" ได้ เนื่องจากมีการใช้งานในข้อมูลการบรรจุ');
        }
        $educationalArea->delete();
        return redirect()->route('admin.educational-areas.index')->with('success', 'ลบเขตพื้นที่ฯ สำเร็จ');
    }
}

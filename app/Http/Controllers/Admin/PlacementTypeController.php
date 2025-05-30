<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlacementType;
use Illuminate\Http\Request; // จะเปลี่ยนเป็น FormRequest
use Illuminate\Validation\Rule;

// (แนะนำ) สร้าง Form Requests:
// php artisan make:request StorePlacementTypeRequest
// php artisan make:request UpdatePlacementTypeRequest

class PlacementTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PlacementType::query()->orderBy('name', 'asc');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'like', "%{$searchTerm}%")->orWhere('description', 'like', "%{$searchTerm}%");
        }

        $placementTypes = $query->withCount('placementRecords')->paginate(15)->withQueryString();
        return view('admin.placement_types.index', compact('placementTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.placement_types.create');
    }

    /**
     * Store a newly created resource in storage.
     * (ควรใช้ StorePlacementTypeRequest)
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate(
            [
                'name' => 'required|string|max:255|unique:placement_types,name',
                'description' => 'nullable|string|max:1000',
                'is_active' => 'nullable|boolean', // รับค่าจาก checkbox
            ],
            [
                'name.required' => 'กรุณากรอกชื่อประเภทการบรรจุ',
                'name.unique' => 'ชื่อประเภทการบรรจุนี้มีในระบบแล้ว',
            ],
        );

        // แปลงค่า is_active จาก checkbox
        $validatedData['is_active'] = $request->has('is_active'); // ถ้า check มาคือ true, ไม่ check คือ false

        PlacementType::create($validatedData);

        return redirect()
            ->route('admin.placement-types.index')
            ->with('success', 'เพิ่มประเภทการบรรจุ "' . $validatedData['name'] . '" สำเร็จ');
    }

    /**
     * Display the specified resource.
     */
    public function show(PlacementType $placementType)
    {
        // $placementType->loadCount('placementRecords'); // โหลด count ถ้าต้องการ
        return view('admin.placement_types.show', compact('placementType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlacementType $placementType)
    {
        return view('admin.placement_types.edit', compact('placementType'));
    }

    /**
     * Update the specified resource in storage.
     * (ควรใช้ UpdatePlacementTypeRequest)
     */
    public function update(Request $request, PlacementType $placementType)
    {
        $validatedData = $request->validate(
            [
                'name' => ['required', 'string', 'max:255', Rule::unique('placement_types')->ignore($placementType->id)],
                'description' => 'nullable|string|max:1000',
                'is_active' => 'nullable|boolean',
            ],
            [
                'name.required' => 'กรุณากรอกชื่อประเภทการบรรจุ',
                'name.unique' => 'ชื่อประเภทการบรรจุนี้มีในระบบแล้ว',
            ],
        );

        $validatedData['is_active'] = $request->has('is_active');

        $placementType->update($validatedData);

        return redirect()
            ->route('admin.placement-types.index')
            ->with('success', 'แก้ไขประเภทการบรรจุ "' . $placementType->name . '" สำเร็จ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlacementType $placementType)
    {
        try {
            // ตรวจสอบว่ามี PlacementRecord อ้างอิงหรือไม่ (ถ้า onDelete ของ FK placement_type_id เป็น 'restrict')
            if ($placementType->placementRecords()->exists()) {
                return redirect()
                    ->route('admin.placement-types.index')
                    ->with('error', 'ไม่สามารถลบประเภทการบรรจุ "' . $placementType->name . '" ได้ เนื่องจากมีการใช้งานอยู่');
            }

            $typeName = $placementType->name;
            $placementType->delete();

            return redirect()
                ->route('admin.placement-types.index')
                ->with('success', 'ลบประเภทการบรรจุ "' . $typeName . '" สำเร็จ');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1451) {
                // MySQL foreign key constraint error
                return redirect()
                    ->route('admin.placement-types.index')
                    ->with('error', 'ไม่สามารถลบประเภทการบรรจุ "' . $placementType->name . '" ได้ เนื่องจากมีการอ้างอิงข้อมูลอื่นอยู่');
            }
            \Log::error("Error deleting placement type: {$e->getMessage()}");
            return redirect()->route('admin.placement-types.index')->with('error', 'เกิดข้อผิดพลาดในการลบประเภทการบรรจุ');
        }
    }

    /** (Optional) Toggle active status
    public function toggleActiveStatus(PlacementType $placementType)
    {
        $placementType->update(['is_active' => !$placementType->is_active]);
        $status = $placementType->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
        return redirect()->route('admin.placement-types.index')
                         ->with('success', "เปลี่ยนสถานะประเภทการบรรจุ \"{$placementType->name}\" เป็น {$status} สำเร็จ");
    }
    */
}

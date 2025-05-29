<?php

namespace App\Http\Controllers\Admin; // << ตรวจสอบ Namespace

use App\Http\Controllers\Controller; // << ตรวจสอบการ extends
use Illuminate\Http\Request; // หรือ FormRequest ถ้าใช้
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Http\Requests\UpdateAdminProfileRequest; // << Import FormRequest ที่สร้างไว้

class ProfileController extends Controller // << ตรวจสอบชื่อ Class
{
    /**
     * Show the form for editing the currently authenticated admin's profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // <<<< ตรวจสอบว่ามี method นี้ และชื่อถูกต้อง (edit)
        $user = Auth::user(); // ดึงข้อมูล User ที่กำลัง Login อยู่
        if (!$user) {
            // กรณีที่ไม่ควรเกิดขึ้นถ้ามี middleware 'auth' ป้องกันอยู่
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบ');
        }
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Update the currently authenticated admin's profile.
     *
     * @param  \App\Http\Requests\UpdateAdminProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateAdminProfileRequest $request)
    {
        $user = Auth::user();
        $validatedData = $request->validated();

        $updateData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
        ];

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $imageName = time() . '_' . $request->file('profile_image')->getClientOriginalName();
            $path = $request->file('profile_image')->storeAs('profile_images/admins', $imageName, 'public');
            $updateData['profile_image'] = $path;
        } elseif ($request->input('remove_profile_image') == '1') {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $updateData['profile_image'] = null;
        }

        $user->update($updateData);

        return redirect()->route('admin.profile.edit')->with('success', 'อัปเดตข้อมูลโปรไฟล์สำเร็จเรียบร้อย');
    }
}

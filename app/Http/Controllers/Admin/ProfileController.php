<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminProfileRequest; // << ใช้ Form Request
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // สำหรับ Logging

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบ');
        }
        return view('admin.profile.edit', compact('user'));
    }

    public function update(UpdateAdminProfileRequest $request)
    {
        $user = Auth::user();
        $validatedData = $request->validated(); // ดึงข้อมูลที่ผ่านการ validate จาก FormRequest

        // ข้อมูลที่จะอัปเดต, เริ่มจาก name และ email
        $updateData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
        ];

        // จัดการการอัปโหลดรูปโปรไฟล์ใหม่
        if ($request->hasFile('profile_image')) {
            // 1. ลบรูปโปรไฟล์เก่า (ถ้ามี)
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                try {
                    Storage::disk('public')->delete($user->profile_image);
                } catch (\Exception $e) {
                    Log::error("Could not delete old profile image for user {$user->id}: {$user->profile_image}. Error: {$e->getMessage()}");
                    // อาจจะแจ้งเตือนผู้ใช้หรือดำเนินการต่อโดยไม่หยุด
                }
            }

            // 2. บันทึกรูปใหม่และเก็บ path
            try {
                $imageName = time() . '_' . uniqid() . '.' . $request->file('profile_image')->getClientOriginalExtension();
                // ใช้ storeAs('folder', 'filename', 'disk')
                $path = $request->file('profile_image')->storeAs('profile_images/admins', $imageName, 'public');
                $updateData['profile_image'] = $path;
            } catch (\Exception $e) {
                Log::error("Could not upload new profile image for user {$user->id}. Error: {$e->getMessage()}");
                return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการอัปโหลดรูปภาพโปรไฟล์')->withInput();
            }
        } elseif ($request->input('remove_profile_image') == '1') {
            // ถ้ามีการ check ให้ลบรูปโปรไฟล์ปัจจุบัน
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                try {
                    Storage::disk('public')->delete($user->profile_image);
                } catch (\Exception $e) {
                    Log::error("Could not delete profile image (on remove request) for user {$user->id}: {$user->profile_image}. Error: {$e->getMessage()}");
                }
            }
            $updateData['profile_image'] = null; // ตั้งค่า path เป็น null
        }
        // ถ้าไม่มีการอัปโหลดรูปใหม่ และไม่มีการสั่งลบ, $updateData จะไม่มี key 'profile_image'
        // ซึ่งหมายความว่าค่า profile_image เดิมในฐานข้อมูลจะไม่ถูกเปลี่ยนแปลง (พฤติกรรมของ Eloquent update)

        try {
            $user->update($updateData);
        } catch (\Exception $e) {
            Log::error("Error updating user profile for user {$user->id}. Error: {$e->getMessage()}");
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูลโปรไฟล์')->withInput();
        }

        return redirect()->route('admin.profile.edit')->with('success', 'อัปเดตข้อมูลโปรไฟล์สำเร็จเรียบร้อย');
    }
}

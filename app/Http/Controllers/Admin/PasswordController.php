<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminPasswordRequest; // Import Form Request
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Http\Request; // ไม่ได้ใช้แล้ว

class PasswordController extends Controller
{
    /**
     * Show the form for editing the current user's password.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('admin.password.edit');
    }

    /**
     * Update the current user's password.
     *
     * @param  \App\Http\Requests\UpdateAdminPasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateAdminPasswordRequest $request)
    {
        // Validation is handled by UpdateAdminPasswordRequest
        // The current_password is also validated there.

        $user = Auth::user();

        // อัปเดตรหัสผ่านของผู้ใช้ด้วยรหัสผ่านใหม่ที่ผ่านการ validate แล้ว
        $user->update([
            'password' => Hash::make($request->input('new_password')),
        ]);

        // (Optional) Logout other browser sessions
        // Auth::logoutOtherDevices($request->input('current_password')); // ต้องระวังถ้า current_password ไม่ได้ถูกส่งมาใน form หรือใช้ชื่ออื่น

        // (Optional) บังคับให้ Login ใหม่หลังเปลี่ยนรหัสผ่าน
        // Auth::logout();
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();
        // return redirect()->route('login')->with('status', 'เปลี่ยนรหัสผ่านสำเร็จ กรุณาเข้าสู่ระบบใหม่อีกครั้ง');

        return redirect()->route('admin.password.edit')->with('success', 'เปลี่ยนรหัสผ่านสำเร็จเรียบร้อย');
    }
}

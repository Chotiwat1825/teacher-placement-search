<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // ควรใช้ FormRequest
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password; // เพิ่ม

class PasswordController extends Controller
{
    public function edit()
    {
        return view('admin.password.edit');
    }

    public function update(Request $request)
    {
        // ควรเป็น UpdatePasswordRequest
        $user = Auth::user();

        $request->validate([
            'current_password' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('รหัสผ่านปัจจุบันไม่ถูกต้อง');
                    }
                },
            ],
            'new_password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'],
            // 'new_password_confirmation' => 'required', // handled by 'confirmed' rule
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Optional: Logout other devices
        // Auth::logoutOtherDevices($request->current_password);

        return redirect()->route('admin.password.edit')->with('success', 'เปลี่ยนรหัสผ่านสำเร็จ');
    }
}

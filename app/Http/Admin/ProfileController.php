<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // ควรใช้ FormRequest
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // เพิ่ม
use Illuminate\Support\Str; // เพิ่ม
use Illuminate\Validation\Rule; // เพิ่ม

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        // ควรเป็น UpdateProfileRequest
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // 2MB max
        ]);

        $userData = $request->only(['name', 'email']);

        if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image); // สมมติเก็บรูปโปรไฟล์ใน public disk
            }
            // Store new profile image
            $path = $request->file('profile_image')->store('profile-images', 'public'); // เก็บใน storage/app/public/profile-images
            $userData['profile_image'] = $path;
        }

        $user->update($userData);

        return redirect()->route('admin.profile.edit')->with('success', 'แก้ไขข้อมูลโปรไฟล์สำเร็จ');
    }
}

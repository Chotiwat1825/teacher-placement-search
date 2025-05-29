<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
// เพิ่ม Form Requests ในภายหลัง:
// use App\Http\Requests\Admin\StoreUserRequest;
// use App\Http\Requests\Admin\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = User::query()->orderBy('name', 'asc');

        // Search functionality
        if ($request->filled('search_term')) {
            $searchTerm = $request->search_term;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by role (is_admin)
        if ($request->filled('filter_role')) {
            if ($request->filter_role === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->filter_role === 'user') {
                $query->where('is_admin', false);
            }
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // ส่งข้อมูลที่จำเป็นสำหรับฟอร์ม create (ถ้ามี)
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     * (ควรใช้ StoreUserRequest)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'],
            'is_admin' => 'nullable|boolean', // is_admin สามารถเป็น 0 หรือ 1 จาก checkbox/select
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => $request->boolean('is_admin'), // แปลงเป็น boolean
            'email_verified_at' => now(), // หรือตาม logic การ verify email ของคุณ
        ];

        if ($request->hasFile('profile_image')) {
            $imageName = time() . '_' . uniqid() . '.' . $request->file('profile_image')->getClientOriginalExtension();
            $path = $request->file('profile_image')->storeAs('profile_images/users', $imageName, 'public');
            $userData['profile_image'] = $path;
        }

        $user = User::create($userData);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'เพิ่มผู้ใช้ "' . $user->name . '" สำเร็จ');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     * (ควรใช้ UpdateUserRequest)
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'], // Nullable ถ้าไม่ต้องการเปลี่ยน
            'is_admin' => 'nullable|boolean',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'remove_profile_image' => 'nullable|boolean',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_admin' => $request->boolean('is_admin'),
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $imageName = time() . '_' . uniqid() . '.' . $request->file('profile_image')->getClientOriginalExtension();
            $path = $request->file('profile_image')->storeAs('profile_images/users', $imageName, 'public');
            $updateData['profile_image'] = $path;
        } elseif ($request->input('remove_profile_image') == '1') {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $updateData['profile_image'] = null;
        }

        $user->update($updateData);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'แก้ไขข้อมูลผู้ใช้ "' . $user->name . '" สำเร็จ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // ป้องกันการลบ admin คนปัจจุบัน (หรือ admin คนสุดท้าย)
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'ไม่สามารถลบโปรไฟล์ของตัวเองได้');
        }
        // (Optional) อาจจะมีการตรวจสอบเพิ่มเติม เช่น ไม่ให้ลบ Super Admin คนสุดท้าย

        // ลบรูปโปรไฟล์ (ถ้ามี)
        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $userName = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'ลบผู้ใช้ "' . $userName . '" สำเร็จ');
    }
}

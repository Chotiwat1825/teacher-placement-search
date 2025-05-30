<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;

// Frontend Controllers
use App\Http\Controllers\PublicSearchController; // <<<< ตรวจสอบบรรทัดนี้ให้แน่ใจว่ามีและถูกต้อง
use App\Http\Controllers\AttachmentController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EducationalAreaController as AdminEducationalAreaController;
use App\Http\Controllers\Admin\SubjectGroupController as AdminSubjectGroupController;
//SubjectGroupController
use App\Http\Controllers\Admin\PlacementRecordController as AdminPlacementRecordController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\PasswordController as AdminPasswordController;

use App\Http\Controllers\Admin\UserController as AdminUserController; // สำหรับจัดการ User ของ Admin

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Frontend Routes
Route::get('/', [PublicSearchController::class, 'index'])->name('search.index');
// Route::get('/search', [PublicSearchController::class, 'index'])->name('search.show'); // อาจไม่จำเป็นถ้าใช้ /
Route::get('/placement/{placementRecord}', [PublicSearchController::class, 'showDetails'])->name('placement.details');
Route::get('/attachments/{attachment}/view', [AttachmentController::class, 'viewOrDownload'])->name('attachments.view');

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// Route สำหรับ Dashboard ของผู้ใช้ทั่วไป (ถ้ามี) หรือ redirect
Route::get('/dashboard', function () {
    if (auth()->check() && auth()->user()->is_admin) {
        return redirect()->route('admin.dashboard');
    }
    // ถ้า User ทั่วไปไม่มีหน้า dashboard ของตัวเอง อาจจะ redirect ไปหน้าอื่น
    // return view('dashboard'); // ถ้ามี view 'dashboard.blade.php' สำหรับ User
    return redirect()->route('search.index'); // หรือ redirect ไปหน้าค้นหา
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
// =====================================================================

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__ . '/auth.php';

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Admin Dashboard
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        }); // หน้า Dashboard ของ Admin
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        // CRUD สำหรับ เขตพื้นที่การศึกษา
        Route::resource('educational-areas', AdminEducationalAreaController::class);

        // =========================================================================
        // CRUD สำหรับ กลุ่มวิชาเอก (Subject Groups)
        // =========================================================================
        Route::resource('subject-groups', AdminSubjectGroupController::class);

        // =========================================================================
        // CRUD สำหรับ จัดการผู้ใช้ (Users)
        // =========================================================================
        Route::resource('users', AdminUserController::class);

        // CRUD สำหรับ ข้อมูลการบรรจุ
        Route::resource('placement-records', AdminPlacementRecordController::class);

        // โปรไฟล์ Admin
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [AdminProfileController::class, 'update'])->name('profile.update');

        // เปลี่ยนรหัสผ่าน Admin
        Route::get('/password/change', [AdminPasswordController::class, 'edit'])->name('password.edit');
        Route::put('/password/change', [AdminPasswordController::class, 'update'])->name('password.update');
    });

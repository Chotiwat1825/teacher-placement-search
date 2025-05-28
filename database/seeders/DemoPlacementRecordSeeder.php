<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlacementRecord;
use App\Models\EducationalArea;
use App\Models\SubjectGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage; // สำหรับสร้างไฟล์จำลอง
use Illuminate\Support\Str;

class DemoPlacementRecordSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@example.com')->first();
        if (!$adminUser) {
            $this->command->error('Admin user not found. Please run AdminUserSeeder first.');
            return;
        }

        // ดึง ID ของเขตและวิชาเอก (ควรตรวจสอบว่ามีข้อมูลจริง)
        $eaBkk = EducationalArea::where('name', 'สพป. กรุงเทพมหานคร')->first();
        $eaKorat = EducationalArea::where('name', 'สพม. นครราชสีมา')->first();
        $eaChiangmai = EducationalArea::where('name', 'สพป. เชียงใหม่ เขต 1')->first();

        $sgMath = SubjectGroup::where('name', 'คณิตศาสตร์')->first();
        $sgSci = SubjectGroup::where('name', 'วิทยาศาสตร์ทั่วไป')->first();
        $sgEng = SubjectGroup::where('name', 'ภาษาอังกฤษ')->first();
        $sgComp = SubjectGroup::where('name', 'คอมพิวเตอร์ศึกษา')->first();
        $sgEarly = SubjectGroup::where('name', 'ปฐมวัยศึกษา')->first();

        if (!$eaBkk || !$eaKorat || !$eaChiangmai || !$sgMath || !$sgSci || !$sgEng || !$sgComp || !$sgEarly) {
             $this->command->error('Required educational areas or subject groups not found. Please run their seeders.');
             return;
        }

        // สร้าง directory สำหรับเก็บไฟล์แนบจำลอง
        $diskName = config('filesystems.default_private_disk', 'private');
        Storage::disk($diskName)->makeDirectory('placement_attachments/demo');


        // --- Record 1 ---
        $record1 = PlacementRecord::create([
            'academic_year' => 2567,
            'announcement_date' => Carbon::createFromFormat('Y-m-d', '2024-01-15')->toDateString(),
            'educational_area_id' => $eaBkk->id,
            'round_number' => 1,
            'source_link' => 'https://example.com/ประกาศผล-กทม-รอบ1-2567',
            'user_id' => $adminUser->id,
        ]);
        $record1->subjectGroups()->sync([$sgMath->id, $sgComp->id]); // คณิตศาสตร์, คอมพิวเตอร์

        // ไฟล์แนบจำลองสำหรับ Record 1
        $filePath1_1 = 'placement_attachments/demo/record1_ประกาศ.pdf';
        Storage::disk($diskName)->put($filePath1_1, 'This is a dummy PDF content for record 1.');
        $record1->attachments()->create([
            'file_path' => $filePath1_1,
            'original_filename' => 'ประกาศผล_รอบ1_กทม.pdf',
            'mime_type' => 'application/pdf',
            'type' => 'file',
        ]);
        $filePath1_2 = 'placement_attachments/demo/record1_รูปภาพ.jpg';
        Storage::disk($diskName)->put($filePath1_2, 'This is a dummy JPG content for record 1.'); // สร้างไฟล์เปล่า หรือใส่ binary data ถ้าต้องการ
        $record1->attachments()->create([
            'file_path' => $filePath1_2,
            'original_filename' => 'บรรยากาศการสอบ.jpg',
            'mime_type' => 'image/jpeg',
            'type' => 'image',
        ]);


        // --- Record 2 ---
        $record2 = PlacementRecord::create([
            'academic_year' => 2567,
            'announcement_date' => Carbon::createFromFormat('Y-m-d', '2024-02-01')->toDateString(),
            'educational_area_id' => $eaKorat->id,
            'round_number' => 1,
            'source_link' => null,
            'user_id' => $adminUser->id,
        ]);
        $record2->subjectGroups()->sync([$sgSci->id]); // วิทยาศาสตร์ทั่วไป

        // ไฟล์แนบจำลองสำหรับ Record 2
        $filePath2_1 = 'placement_attachments/demo/record2_เอกสารเพิ่มเติม.pdf';
        Storage::disk($diskName)->put($filePath2_1, 'Dummy PDF content for record 2 additional document.');
        $record2->attachments()->create([
            'file_path' => $filePath2_1,
            'original_filename' => 'เอกสารเพิ่มเติม_โคราช.pdf',
            'mime_type' => 'application/pdf',
            'type' => 'file',
        ]);


        // --- Record 3 ---
        $record3 = PlacementRecord::create([
            'academic_year' => 2566,
            'announcement_date' => Carbon::createFromFormat('Y-m-d', '2023-11-20')->toDateString(),
            'educational_area_id' => $eaChiangmai->id,
            'round_number' => 2,
            'source_link' => 'https://example.com/ประกาศ-เชียงใหม่-รอบ2-2566',
            'user_id' => $adminUser->id,
        ]);
        $record3->subjectGroups()->sync([$sgEng->id, $sgEarly->id]); // ภาษาอังกฤษ, ปฐมวัย

        // --- Record 4 (รอบที่เกี่ยวข้องกับ Record 1) ---
        $record4 = PlacementRecord::create([
            'academic_year' => 2567, // ปีเดียวกับ record 1
            'announcement_date' => Carbon::createFromFormat('Y-m-d', '2024-01-25')->toDateString(),
            'educational_area_id' => $eaBkk->id, // เขตเดียวกับ record 1
            'round_number' => 2, // รอบถัดไป
            'source_link' => 'https://example.com/ประกาศผล-กทม-รอบ2-2567',
            'user_id' => $adminUser->id,
        ]);
        // วิชาเอกเหมือน record 1 เพื่อให้เชื่อมโยงกันได้
        $record4->subjectGroups()->sync([$sgMath->id, $sgComp->id]);


        $this->command->info('Demo placement records with attachments seeded successfully.');
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SubjectGroup; // Import model

class SubjectGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('subject_groups')->delete(); // ล้างข้อมูลเก่า (ถ้าต้องการ)

        $subjectGroups = [
            ['name' => 'คณิตศาสตร์', 'code' => 'MATH'],
            ['name' => 'วิทยาศาสตร์ทั่วไป', 'code' => 'SCI'],
            ['name' => 'ฟิสิกส์', 'code' => 'PHY'],
            ['name' => 'เคมี', 'code' => 'CHEM'],
            ['name' => 'ชีววิทยา', 'code' => 'BIO'],
            ['name' => 'ภาษาไทย', 'code' => 'THAI'],
            ['name' => 'ภาษาอังกฤษ', 'code' => 'ENG'],
            ['name' => 'สังคมศึกษา', 'code' => 'SOC'],
            ['name' => 'ประวัติศาสตร์', 'code' => 'HIST'],
            ['name' => 'ภูมิศาสตร์', 'code' => 'GEO'],
            ['name' => 'พระพุทธศาสนา', 'code' => 'BUDD'],
            ['name' => 'สุขศึกษาและพลศึกษา', 'code' => 'HPE'],
            ['name' => 'ศิลปะ', 'code' => 'ART'],
            ['name' => 'ดนตรีศึกษา', 'code' => 'MUSIC'],
            ['name' => 'นาฏศิลป์', 'code' => 'DANCE'],
            ['name' => 'คอมพิวเตอร์', 'code' => 'COMP'],
            ['name' => 'เทคโนโลยีสารสนเทศ', 'code' => 'IT'],
            ['name' => 'เกษตรกรรม', 'code' => 'AGRI'],
            ['name' => 'คหกรรมศาสตร์', 'code' => 'HOMEEC'],
            ['name' => 'อุตสาหกรรมศิลป์', 'code' => 'INDART'],
            ['name' => 'ธุรกิจศึกษา', 'code' => 'BUSINESS'],
            ['name' => 'การงานอาชีพ', 'code' => 'CAREER'],
            ['name' => 'ปฐมวัย', 'code' => 'EARLYCHILD'],
            ['name' => 'ประถมศึกษา', 'code' => 'PRIMARYED'],
            ['name' => 'การศึกษาพิเศษ', 'code' => 'SPECIALED'],
            ['name' => 'จิตวิทยาและการแนะแนว', 'code' => 'PSYCHGUID'],
            ['name' => 'บรรณารักษ์', 'code' => 'LIBR'],
            ['name' => 'วัดผลและประเมินผลการศึกษา', 'code' => 'EVAL'],
            ['name' => 'เทคโนโลยีการศึกษา', 'code' => 'EDTECH'],
            ['name' => 'ภาษาจีน', 'code' => 'CHINESE'],
            ['name' => 'ภาษาญี่ปุ่น', 'code' => 'JAPANESE'],
            ['name' => 'ภาษาฝรั่งเศส', 'code' => 'FRENCH'],
            ['name' => 'ภาษาเยอรมัน', 'code' => 'GERMAN'],
            ['name' => 'ภาษาเกาหลี', 'code' => 'KOREAN'],
            ['name' => 'ดุริยางคศิลป์', 'code' => 'ORCHESTRA'],
            // เพิ่มเติมตามความต้องการ
        ];

        foreach ($subjectGroups as $group) {
            SubjectGroup::firstOrCreate(
                ['name' => $group['name']], // Key to check
                ['code' => $group['code'] ?? null] // Values
            );
        }
    }
}
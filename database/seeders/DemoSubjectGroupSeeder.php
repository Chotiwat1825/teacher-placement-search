<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubjectGroup;

class DemoSubjectGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['name' => 'คณิตศาสตร์', 'code' => 'MATH01'],
            ['name' => 'วิทยาศาสตร์ทั่วไป', 'code' => 'SCI01'],
            ['name' => 'ภาษาอังกฤษ', 'code' => 'ENG01'],
            ['name' => 'คอมพิวเตอร์ศึกษา', 'code' => 'COMP01'],
            ['name' => 'ปฐมวัยศึกษา', 'code' => 'EARLY01'],
        ];

        foreach ($groups as $group) {
            SubjectGroup::updateOrCreate(
                ['name' => $group['name']], // Key to check
                $group // Values
            );
        }
        $this->command->info('Demo subject groups seeded successfully.');
    }
}
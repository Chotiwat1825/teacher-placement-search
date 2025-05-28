<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class, // << ต้องมาก่อน PlacementRecordSeeder
            EducationalAreaSeeder::class,
            SubjectGroupSeeder::class,
            //DemoEducationalAreaSeeder::class, // << ต้องอยู่ก่อน
            //DemoSubjectGroupSeeder::class, // << ต้องอยู่ก่อน
            DemoPlacementRecordSeeder::class,
        ]);
    }
}

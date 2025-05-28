<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EducationalArea;

class DemoEducationalAreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            ['name' => 'สพป. กรุงเทพมหานคร', 'code' => 'กทม.01', 'type' => 'primary'],
            ['name' => 'สพม. นครราชสีมา', 'code' => 'นม.01', 'type' => 'secondary'],
            ['name' => 'สพป. เชียงใหม่ เขต 1', 'code' => 'ชม.01', 'type' => 'primary'],
            ['name' => 'สพม. สงขลา', 'code' => 'สข.01', 'type' => 'secondary'],
            ['name' => 'สพป. ขอนแก่น เขต 5', 'code' => 'ขก.05', 'type' => 'primary'],
        ];

        foreach ($areas as $area) {
            EducationalArea::updateOrCreate(
                ['name' => $area['name']], // Key to check
                $area // Values
            );
        }
        $this->command->info('Demo educational areas seeded successfully.');
    }
}
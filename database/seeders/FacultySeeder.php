<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;

class FacultySeeder extends Seeder
{
    public function run()
    {
        $faculties = [
            [
                'name' => 'Faculty of Business Administration & Business Studies',
                'code' => 'FBAMS',
            ],
            [
                'name' => 'Faculty of Education',
                'code' => 'FEDUC',
            ],
            [
                'name' => 'Faculty of Science and Technology',
                'code' => 'FST',
            ],
            [
                'name' => 'Faculty of Arts and Social Sciences',
                'code' => 'FASS',
            ],
            
        ];

        foreach ($faculties as $faculty) {
            Faculty::create($faculty);
        }

        $this->command->info('Faculties seeded successfully!');
    }
}
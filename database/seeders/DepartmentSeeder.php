<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $businessDepartments = [
            [
                'name' => 'Department of Business Administration',
                'code' => 'DptBA',
            ],
            [
                'name' => 'Department of Management Studies and Economics',
                'code' => 'DptMSE',
            ],
        ];

        $business = Faculty::where('code','FBAMS')->first();

        if ($business) {
            $business->departments()->createMany($businessDepartments);
        }

        $educationDepartments = [
            [
                'name' => 'Department of Foundation of Education',
                'code' => 'DptFE',
            ],
            [
                'name' => 'Department of InService',
                'code' => 'DptIS',
            ],
        ];

        $education = Faculty::where('code','FEDUC')->first();

        if ($education) {
            $education->departments()->createMany($educationDepartments);
        }


        $scienceDepartments = [
            [
                'name' => 'Department of Informatics and Engineering',
                'code' => 'DptIE',
            ],
            [
                'name' => 'Department of Industrial Art and Design',
                'code' => 'DptIAD',
            ],
            [
                'name' => 'Department of Life Science and Natural Resources',
                'code' => 'DptLSNR',
            ],
        ];

        $science = Faculty::where('code','FST')->first();

        if ($science) {
            $science->departments()->createMany($scienceDepartments);
        }


        $artsDepartments = [
            [
                'name' => 'Department of Political Science and Development Studies',
                'code' => 'DptPSDS',
            ],
            [
                'name' => 'Department of Mass Communication, Social Works and Social Administration',
                'code' => 'DptMCSWSA',
            ],
        ];

        $arts = Faculty::where('code','FASS')->first();

        if ($arts) {
            $arts->departments()->createMany($artsDepartments);
        }

        $this->command->info('Departments seeded successfully!');
    }
}
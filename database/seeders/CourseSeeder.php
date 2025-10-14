<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Department;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $departments = Department::all();

        $businessCourses = [
            // Faculty of Business Administration (FBAMS) and Mgt Studies
            // Department of Business Administration (DeptBA)
            [
                'name' => 'Masters of Business Administration and Management',
                'code' => 'MBA',
                'department_id' => $departments->where('code', 'DptBA')->first()->id,
                'duration_years' => 2,
                'description' => 'A comprehensive program covering computer programming, algorithms, data structures, and software engineering.',
            ],
            [
                'name' => 'Bachelor of Procurement and Supply Chain Management',
                'code' => 'BPSM',
                'department_id' => $departments->where('code', 'DptBA')->first()->id,
                'duration_years' => 3,
                'description' => 'Focuses on software development methodologies, project management, and quality assurance.',
            ],
            [
                'name' => 'Bachelor of Business Administration',
                'code' => 'BABA',
                'department_id' => $departments->where('code', 'DptBA')->first()->id,
                'duration_years' => 3,
                'description' => 'Covers business management, marketing, finance, and organizational behavior.',
            ],
            [
                'name' => 'Diploma In Business Administration',
                'code' => 'DBA',
                'department_id' => $departments->where('code', 'DptBA')->first()->id,
                'duration_years' => 2,
                'description' => 'Focuses on accounting, finance, economics, and business law.',
            ],
            [
                'name' => 'National Certificate In Business Administration',
                'code' => 'NCBA',
                'department_id' => $departments->where('code', 'DptBA')->first()->id,
                'duration_years' => 2,
                'description' => 'Focuses on accounting, finance, economics, and business law.',
            ],

            // Department of Management Studies and Economics (DptMSE)
            [
                'name' => 'Bachelor of Human Resource Management',
                'code' => 'BHRM',
                'department_id' => $departments->where('code', 'DptMSE')->first()->id,
                'duration_years' => 3,
                'description' => 'A comprehensive program covering computer programming, algorithms, data structures, and software engineering.',
            ],
            [
                'name' => 'Bachelor of Tourism and Hospitality Management',
                'code' => 'BTHM',
                'department_id' => $departments->where('code', 'DptMSE')->first()->id,
                'duration_years' => 3,
                'description' => 'Focuses on software development methodologies, project management, and quality assurance.',
            ],
            [
                'name' => 'Bachelor of Economics',
                'code' => 'BAEC',
                'department_id' => $departments->where('code', 'DptMSE')->first()->id,
                'duration_years' => 3,
                'description' => 'Covers business management, marketing, finance, and organizational behavior.',
            ],
            [
                'name' => 'Bachelor of Statistics',
                'code' => 'BAST',
                'department_id' => $departments->where('code', 'DptMSE')->first()->id,
                'duration_years' => 3,
                'description' => 'Focuses on accounting, finance, economics, and business law.',
            ],
            [
                'name' => 'Diploma in Tourism and Hospitality Management',
                'code' => 'DTHM',
                'department_id' => $departments->where('code', 'DptMSE')->first()->id,
                'duration_years' => 2,
                'description' => 'Focuses on accounting, finance, economics, and business law.',
            ],            
        ];

        foreach ($businessCourses as $course) {
            Course::create($course);
        }


        $educationCourses = [
            // Faculty of Education (FEDUC) 
            // Department of Foundation of Education (DptFE)
            [
                'name' => 'Masters of Education Administration and Management',
                'code' => 'MAEM',
                'department_id' => $departments->where('code', 'DptFE')->first()->id,
                'duration_years' => 2,
                'description' => 'A comprehensive program covering computer programming, algorithms, data structures, and software engineering.',
            ],
            [
                'name' => 'Postgraduate Diploma in Education',
                'code' => 'PGDE',
                'department_id' => $departments->where('code', 'DptFE')->first()->id,
                'duration_years' => 1,
                'description' => 'Focuses on software development methodologies, project management, and quality assurance.',
            ],
            [
                'name' => 'Bachelor of Arts with Education',
                'code' => 'BAED',
                'department_id' => $departments->where('code', 'DptFE')->first()->id,
                'duration_years' => 3,
                'description' => 'Covers business management, marketing, finance, and organizational behavior.',
            ],
            [
                'name' => 'Higher Education Certificate',
                'code' => 'HEC',
                'department_id' => $departments->where('code', 'DptFE')->first()->id,
                'duration_years' => 1,
                'description' => 'Focuses on accounting, finance, economics, and business law.',
            ],
            
            // Department of InService (DptIS)
            [
                'name' => 'Bachelor of Education (Primary) Inservice',
                'code' => 'BEDIp',
                'department_id' => $departments->where('code', 'DptIS')->first()->id,
                'duration_years' => 2,
                'description' => 'A comprehensive program covering computer programming, algorithms, data structures, and software engineering.',
            ],
            [
                'name' => 'Bachelor of Education (Secondary) Inservice',
                'code' => 'BEDIs',
                'department_id' => $departments->where('code', 'DptIS')->first()->id,
                'duration_years' => 2,
                'description' => 'Focuses on software development methodologies, project management, and quality assurance.',
            ],
            [
                'name' => 'Diploma in Primary Education Inservice',
                'code' => 'DPE',
                'department_id' => $departments->where('code', 'DptIS')->first()->id,
                'duration_years' => 2,
                'description' => 'Covers business management, marketing, finance, and organizational behavior.',
            ],
        ];

        foreach ($educationCourses as $course) {
            Course::create($course);
        }


        $scienceCourses = [
            // Faculty of Science and Technology (FST) 
            // Department of Informatics and Engineering (DptIE)
            [
                'name' => 'Bachelor of Information Technology',
                'code' => 'BAIT',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'duration_years' => 3,
                'description' => 'A comprehensive program covering computer programming, algorithms, data structures, and software engineering.',
            ],
            [
                'name' => 'Bachelor of Records and Archives Management',
                'code' => 'BRAM',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'duration_years' => 3,
                'description' => 'Focuses on software development methodologies, project management, and quality assurance.',
            ],
            [
                'name' => 'Bachelor of Science in Telecommunication Engineering',
                'code' => 'BSTE',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'duration_years' => 4,
                'description' => 'Covers business management, marketing, finance, and organizational behavior.',
            ],
            [
                'name' => 'Bachelor of Science in Computer Engineering',
                'code' => 'BSc.CE',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'duration_years' => 3,
                'description' => 'Focuses on accounting, finance, economics, and business law.',
            ],
            [
                'name' => 'Bachelor of Computer Science',
                'code' => 'BACS',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'duration_years' => 3,
                'description' => 'A comprehensive program covering computer programming, algorithms, data structures, and software engineering.',
            ],
            [
                'name' => 'Diploma in Information Tehnology',
                'code' => 'DIT',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'duration_years' => 2,
                'description' => 'Focuses on software development methodologies, project management, and quality assurance.',
            ],
            [
                'name' => 'Diploma in Computer Science',
                'code' => 'DCS',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'duration_years' => 2,
                'description' => 'Covers business management, marketing, finance, and organizational behavior.',
            ],
            [
                'name' => 'Diploma in Computer Engineering',
                'code' => 'DCE',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'duration_years' => 2,
                'description' => 'Focuses on accounting, finance, economics, and business law.',
            ],
            [
                'name' => 'National Certificate in Communication and Information Technology',
                'code' => 'NCICT',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'duration_years' => 2,
                'description' => 'Focuses on accounting, finance, economics, and business law.',
            ],

            // Department of Industrial Art and Design (DptIAD)
            [
                'name' => 'Bachelor of Industrial Art and Design',
                'code' => 'BIAD',
                'department_id' => $departments->where('code', 'DptIAD')->first()->id,
                'duration_years' => 3,
                'description' => 'A comprehensive program covering computer programming, algorithms, data structures, and software engineering.',
            ],
            [
                'name' => 'National Diploma in Interior and Landscape Design',
                'code' => 'NDILD',
                'department_id' => $departments->where('code', 'DptIAD')->first()->id,
                'duration_years' => 2,
                'description' => 'Focuses on software development methodologies, project management, and quality assurance.',
            ],
            [
                'name' => 'National Diploma in Fashion and Design',
                'code' => 'NDFD',
                'department_id' => $departments->where('code', 'DptIAD')->first()->id,
                'duration_years' => 2,
                'description' => 'Covers business management, marketing, finance, and organizational behavior.',
            ],
            [
                'name' => 'National Certificate in Fashion and Design',
                'code' => 'NCID',
                'department_id' => $departments->where('code', 'DptIAD')->first()->id,
                'duration_years' => 2,
                'description' => 'Covers business management, marketing, finance, and organizational behavior.',
            ],

            // Department of Life Science and Natural Resources (DptLSNR)
            [
                'name' => 'Bachelor of Science in Public Health',
                'code' => 'BSPH',
                'department_id' => $departments->where('code', 'DptLSNR')->first()->id,
                'duration_years' => 3,
                'description' => 'A comprehensive program covering computer programming, algorithms, data structures, and software engineering.',
            ],
            [
                'name' => 'Bachelor of Medical Records and Management',
                'code' => 'BMRM',
                'department_id' => $departments->where('code', 'DptLSNR')->first()->id,
                'duration_years' => 3,
                'description' => 'Focuses on software development methodologies, project management, and quality assurance.',
            ],
            [
                'name' => 'Diploma in Medical Records and Management',
                'code' => 'DMRM',
                'department_id' => $departments->where('code', 'DptLSNR')->first()->id,
                'duration_years' => 2,
                'description' => 'Covers business management, marketing, finance, and organizational behavior.',
            ],
            [
                'name' => 'Diploma in Public Health',
                'code' => 'DPH',
                'department_id' => $departments->where('code', 'DptLSNR')->first()->id,
                'duration_years' => 2,
                'description' => 'Covers business management, marketing, finance, and organizational behavior.',
            ],
        ];

        foreach ($scienceCourses as $course) {
            Course::create($course);
        }


        $artsCourses = [
            // Faculty of Arts and Social Sciences (FASS) 
            // Department of Political Science and Development Studies (DptPSDS)
            [
                'name' => 'Bachelor of Public Administration and Management',
                'code' => 'BAPA',
                'department_id' => $departments->where('code', 'DptPSDS')->first()->id,
                'duration_years' => 3,
                'description' => 'A comprehensive program covering computer programming, algorithms, data structures, and software engineering.',
            ],
            [
                'name' => 'Bachelor of Diplomacy and International Relations',
                'code' => 'DADI',
                'department_id' => $departments->where('code', 'DptPSDS')->first()->id,
                'duration_years' => 3,
                'description' => 'Focuses on software development methodologies, project management, and quality assurance.',
            ],
            [
                'name' => 'Bachelor of Development Studies',
                'code' => 'BADS',
                'department_id' => $departments->where('code', 'DptPSDS')->first()->id,
                'duration_years' => 3,
                'description' => 'Covers business management, marketing, finance, and organizational behavior.',
            ],
            [
                'name' => 'Diploma in Public Administration and Management',
                'code' => 'DPA',
                'department_id' => $departments->where('code', 'DptPSDS')->first()->id,
                'duration_years' => 2,
                'description' => 'Focuses on accounting, finance, economics, and business law.',
            ],


            // Department of Mass Communication, Social Works and Social Administration (DptMCSWSA)
            [
                'name' => 'Bachelor of Mass Communication and Journalism',
                'code' => 'BAMA',
                'department_id' => $departments->where('code', 'DptMCSWSA')->first()->id,
                'duration_years' => 3,
                'description' => 'A comprehensive program covering computer programming, algorithms, data structures, and software engineering.',
            ],
            [
                'name' => 'Bachelor of Social Work and Social Administration',
                'code' => 'BASA',
                'department_id' => $departments->where('code', 'DptMCSWSA')->first()->id,
                'duration_years' => 3,
                'description' => 'Focuses on software development methodologies, project management, and quality assurance.',
            ],
            [
                'name' => 'Diploma in Mass Communication and Journalism',
                'code' => 'DMC',
                'department_id' => $departments->where('code', 'DptMCSWSA')->first()->id,
                'duration_years' => 2,
                'description' => 'Covers business management, marketing, finance, and organizational behavior.',
            ],
            [
                'name' => 'Diploma in Social Work and Social Administration',
                'code' => 'DSA',
                'department_id' => $departments->where('code', 'DptMCSWSA')->first()->id,
                'duration_years' => 2,
                'description' => 'Covers business management, marketing, finance, and organizational behavior.',
            ],
        ];

        foreach ($artsCourses as $course) {
            Course::create($course);
        }


        $this->command->info('Courses seeded successfully!');
    }
}
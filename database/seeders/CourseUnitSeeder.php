<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\Department;
use Illuminate\Database\Seeder;

class CourseUnitSeeder extends Seeder
{
    public function run()
    {
        $departments = Department::all();
        $courses = Course::all();

        $courseUnits = [
            // =============================================
            // BACHELOR OF INFORMATION TECHNOLOGY (BAIT)
            // =============================================
            
            // YEAR 1 - SEMESTER 1
            [
                'code' => 'BAIT1101',
                'name' => 'Introduction to Information Technology',
                'description' => 'Fundamental concepts of IT, computer hardware, software, and basic computer operations.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT1102',
                'name' => 'Computer Programming Fundamentals',
                'description' => 'Introduction to programming concepts using Python, algorithms, and problem-solving techniques.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 4,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT1103',
                'name' => 'Mathematics for Computing',
                'description' => 'Discrete mathematics, logic, sets, relations, and basic statistical methods for computing.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT1104',
                'name' => 'Communication Skills',
                'description' => 'Effective communication, technical writing, and presentation skills for IT professionals.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 2,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT1105',
                'name' => 'Computer Applications',
                'description' => 'Practical skills in office productivity software and basic digital literacy.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 2,
                'semester' => 1,
                'academic_year' => 2024,
            ],

            // YEAR 1 - SEMESTER 2
            [
                'code' => 'BAIT1201',
                'name' => 'Object-Oriented Programming',
                'description' => 'Principles of OOP using Java, classes, objects, inheritance, and polymorphism.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 4,
                'semester' => 2,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT1202',
                'name' => 'Web Development Fundamentals',
                'description' => 'HTML, CSS, JavaScript and basic front-end web development techniques.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 2,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT1203',
                'name' => 'Database Management Systems',
                'description' => 'Introduction to database concepts, SQL, and relational database design.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 2,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT1204',
                'name' => 'Computer Networks',
                'description' => 'Network fundamentals, protocols, TCP/IP, and basic network administration.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 2,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT1205',
                'name' => 'Entrepreneurship Skills',
                'description' => 'Business fundamentals, innovation, and entrepreneurship in IT sector.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 2,
                'semester' => 2,
                'academic_year' => 2024,
            ],

            // YEAR 2 - SEMESTER 1
            [
                'code' => 'BAIT2101',
                'name' => 'Data Structures and Algorithms',
                'description' => 'Advanced data structures, algorithm analysis, and complexity theory.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 4,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT2102',
                'name' => 'Systems Analysis and Design',
                'description' => 'Software development lifecycle, requirements analysis, and system design methodologies.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT2103',
                'name' => 'Advanced Database Systems',
                'description' => 'Advanced SQL, database administration, and NoSQL databases.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT2104',
                'name' => 'Operating Systems',
                'description' => 'OS concepts, process management, memory management, and file systems.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT2105',
                'name' => 'Research Methodology',
                'description' => 'Research techniques, academic writing, and IT project proposal development.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 2,
                'semester' => 1,
                'academic_year' => 2024,
            ],

            // YEAR 2 - SEMESTER 2
            [
                'code' => 'BAIT2201',
                'name' => 'Web Application Development',
                'description' => 'Server-side programming, web frameworks, and full-stack web development.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 4,
                'semester' => 2,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT2202',
                'name' => 'Mobile Application Development',
                'description' => 'Cross-platform mobile app development using modern frameworks.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 2,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT2203',
                'name' => 'Network Security',
                'description' => 'Cybersecurity fundamentals, encryption, and network protection techniques.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 2,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT2204',
                'name' => 'Software Engineering',
                'description' => 'Software development methodologies, project management, and quality assurance.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 2,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT2205',
                'name' => 'Professional Practice',
                'description' => 'IT ethics, professional standards, and workplace readiness skills.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 2,
                'semester' => 2,
                'academic_year' => 2024,
            ],

            // YEAR 3 - SEMESTER 1
            [
                'code' => 'BAIT3101',
                'name' => 'Enterprise Systems',
                'description' => 'ERP systems, business process integration, and enterprise architecture.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT3102',
                'name' => 'Cloud Computing',
                'description' => 'Cloud services, virtualization, and cloud deployment models.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT3103',
                'name' => 'Artificial Intelligence',
                'description' => 'AI fundamentals, machine learning, and intelligent systems.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT3104',
                'name' => 'Project Management',
                'description' => 'IT project planning, execution, and management methodologies.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT3105',
                'name' => 'Elective I: Data Science',
                'description' => 'Data analysis, visualization, and statistical computing.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],

            // YEAR 3 - SEMESTER 2
            [
                'code' => 'BAIT3201',
                'name' => 'Final Year Project',
                'description' => 'Capstone project demonstrating comprehensive IT skills and knowledge.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 6,
                'semester' => 2,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT3202',
                'name' => 'Industrial Training',
                'description' => 'Practical work experience in IT industry or organization.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 4,
                'semester' => 2,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT3203',
                'name' => 'Emerging Technologies',
                'description' => 'IoT, blockchain, and other cutting-edge technologies.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 2,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BAIT3204',
                'name' => 'Elective II: Cybersecurity',
                'description' => 'Advanced security concepts, penetration testing, and digital forensics.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 2,
                'academic_year' => 2024,
            ],

            // =============================================
            // DIPLOMA IN INFORMATION TECHNOLOGY (DIT)
            // =============================================
            
            // YEAR 1 - SEMESTER 1
            [
                'code' => 'DIT1101',
                'name' => 'Introduction to Computing',
                'description' => 'Basic computer concepts, hardware, software, and applications.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'DIT1102',
                'name' => 'Programming Basics',
                'description' => 'Fundamental programming concepts and problem-solving techniques.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'DIT1103',
                'name' => 'Computer Applications',
                'description' => 'Office productivity software and practical computer skills.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 2,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'DIT1104',
                'name' => 'Mathematics for IT',
                'description' => 'Basic mathematics relevant to information technology.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 2,
                'semester' => 1,
                'academic_year' => 2024,
            ],

            // YEAR 1 - SEMESTER 2
            [
                'code' => 'DIT1201',
                'name' => 'Web Design and Development',
                'description' => 'HTML, CSS, and basic web development techniques.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 2,
                'academic_year' => 2024,
            ],
            [
                'code' => 'DIT1202',
                'name' => 'Database Fundamentals',
                'description' => 'Introduction to databases and basic SQL queries.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 2,
                'academic_year' => 2024,
            ],
            [
                'code' => 'DIT1203',
                'name' => 'Networking Essentials',
                'description' => 'Basic network concepts and configuration.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 2,
                'academic_year' => 2024,
            ],
            [
                'code' => 'DIT1204',
                'name' => 'Communication Skills',
                'description' => 'Effective communication for IT professionals.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 2,
                'semester' => 2,
                'academic_year' => 2024,
            ],

            // YEAR 2 - SEMESTER 1
            [
                'code' => 'DIT2101',
                'name' => 'Object-Oriented Programming',
                'description' => 'OOP concepts using Java or C++.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'DIT2102',
                'name' => 'System Administration',
                'description' => 'Operating system installation and administration.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'DIT2103',
                'name' => 'Web Programming',
                'description' => 'Server-side web development with PHP or similar technologies.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'DIT2104',
                'name' => 'IT Project',
                'description' => 'Small-scale IT project development.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                
                'credits' => 4,
                'semester' => 1,
                'academic_year' => 2024,
            ],

            // =============================================
            // DIPLOMA IN COMPUTER SCIENCE (DCS)
            // =============================================
            
            // YEAR 1 - SEMESTER 1
            [
                'code' => 'DCS1101',
                'name' => 'Computer Fundamentals',
                'description' => 'Introduction to computer systems and architecture.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'DCS1102',
                'name' => 'Programming Principles',
                'description' => 'Structured programming and algorithm development.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'DCS1103',
                'name' => 'Discrete Mathematics',
                'description' => 'Mathematical foundations for computer science.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],

            // YEAR 1 - SEMESTER 2
            [
                'code' => 'DCS1201',
                'name' => 'Data Structures',
                'description' => 'Arrays, linked lists, stacks, queues, and basic algorithms.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'credits' => 3,
                'semester' => 2,
                'academic_year' => 2024,
            ],
            [
                'code' => 'DCS1202',
                'name' => 'Database Systems',
                'description' => 'Relational databases and SQL programming.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'credits' => 3,
                'semester' => 2,
                'academic_year' => 2024,
            ],
            [
                'code' => 'DCS1203',
                'name' => 'Web Technologies',
                'description' => 'Client-side and server-side web development.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'credits' => 3,
                'semester' => 2,
                'academic_year' => 2024,
            ],

            // =============================================
            // BACHELOR OF COMPUTER SCIENCE (BACS)
            // =============================================
            
            // Core Computer Science Units
            [
                'code' => 'BACS3101',
                'name' => 'Advanced Algorithms',
                'description' => 'Complex algorithm design and analysis techniques.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'credits' => 4,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BACS3102',
                'name' => 'Computer Architecture',
                'description' => 'Processor design, memory systems, and computer organization.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'credits' => 3,
                'semester' => 1,
                'academic_year' => 2024,
            ],
            [
                'code' => 'BACS3103',
                'name' => 'Software Development',
                'description' => 'Advanced software engineering principles and practices.',
                'department_id' => $departments->where('code', 'DptIE')->first()->id,
                'credits' => 4,
                'semester' => 1,
                'academic_year' => 2024,
            ],
        ];

        // Create course units
        foreach ($courseUnits as $courseUnitData) {
            $courseUnit = CourseUnit::create($courseUnitData);
            
            // Link course units to appropriate courses based on course codes
            $this->linkCourseUnitToCourses($courseUnit);
        }
    }

    private function linkCourseUnitToCourses(CourseUnit $courseUnit)
    {
        $courseMappings = [
            // BAIT Course Units
            'BAIT1101' => ['BAIT'],
            'BAIT1102' => ['BAIT'],
            'BAIT1103' => ['BAIT'],
            'BAIT1104' => ['BAIT', 'DIT', 'DCS'],
            'BAIT1105' => ['BAIT', 'DIT'],
            'BAIT1201' => ['BAIT'],
            'BAIT1202' => ['BAIT', 'DIT'],
            'BAIT1203' => ['BAIT', 'DIT', 'DCS'],
            'BAIT1204' => ['BAIT'],
            'BAIT1205' => ['BAIT'],
            'BAIT2101' => ['BAIT', 'BACS'],
            'BAIT2102' => ['BAIT'],
            'BAIT2103' => ['BAIT'],
            'BAIT2104' => ['BAIT', 'BACS'],
            'BAIT2105' => ['BAIT'],
            'BAIT2201' => ['BAIT'],
            'BAIT2202' => ['BAIT'],
            'BAIT2203' => ['BAIT'],
            'BAIT2204' => ['BAIT', 'BACS'],
            'BAIT2205' => ['BAIT'],
            'BAIT3101' => ['BAIT'],
            'BAIT3102' => ['BAIT'],
            'BAIT3103' => ['BAIT', 'BACS'],
            'BAIT3104' => ['BAIT'],
            'BAIT3105' => ['BAIT'],
            'BAIT3201' => ['BAIT'],
            'BAIT3202' => ['BAIT'],
            'BAIT3203' => ['BAIT'],
            'BAIT3204' => ['BAIT'],

            // DIT Course Units
            'DIT1101' => ['DIT'],
            'DIT1102' => ['DIT'],
            'DIT1103' => ['DIT'],
            'DIT1104' => ['DIT'],
            'DIT1201' => ['DIT'],
            'DIT1202' => ['DIT'],
            'DIT1203' => ['DIT'],
            'DIT1204' => ['DIT'],
            'DIT2101' => ['DIT'],
            'DIT2102' => ['DIT'],
            'DIT2103' => ['DIT'],
            'DIT2104' => ['DIT'],

            // DCS Course Units
            'DCS1101' => ['DCS'],
            'DCS1102' => ['DCS'],
            'DCS1103' => ['DCS'],
            'DCS1201' => ['DCS'],
            'DCS1202' => ['DCS'],
            'DCS1203' => ['DCS'],

            // BACS Course Units
            'BACS3101' => ['BACS'],
            'BACS3102' => ['BACS'],
            'BACS3103' => ['BACS'],
        ];

        $courseUnitCode = $courseUnit->code;
        if (isset($courseMappings[$courseUnitCode])) {
            foreach ($courseMappings[$courseUnitCode] as $courseCode) {
                $course = Course::where('code', $courseCode)->first();
                if ($course) {
                    // Determine default year and semester based on course unit code
                    $defaultYear = $this->getDefaultYearFromCode($courseUnitCode);
                    $defaultSemester = $this->getDefaultSemesterFromCode($courseUnitCode);
                    
                    $courseUnit->courses()->attach($course->id, [
                        'default_year' => $defaultYear,
                        'default_semester' => $defaultSemester,
                        'is_core' => true,
                    ]);
                }
            }
        }
    }

    private function getDefaultYearFromCode(string $courseUnitCode): int
    {
        // Extract year from course unit code (e.g., BAIT1101 -> Year 1, BAIT2101 -> Year 2)
        // The 4th character is the year digit
        $yearDigit = substr($courseUnitCode, 4, 1);
        return (int) $yearDigit;
    }

    private function getDefaultSemesterFromCode(string $courseUnitCode): int
    {
        // Extract semester from course unit code 
        // (e.g., BAIT1101 -> Semester 1, BAIT1201 -> Semester 2)
        // The 5th character is the semester digit
        $semesterDigit = substr($courseUnitCode, 5, 1);
        return (int) $semesterDigit;
    }

}
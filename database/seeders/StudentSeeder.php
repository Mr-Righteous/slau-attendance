<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use App\Models\Department;
use Carbon\Carbon;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $users = User::role('student')->get();
        $courses = Course::all();
        $departments = Department::all();

        $students = [];
        $studentCounters = []; // To track sequential numbers per course/intake

        foreach ($users as $index => $user) {
            $course = $courses->where('department_id', $user->department_id)->first();
            
            if (!$course) {
                continue;
            }

            // Get course code (first 3-4 letters of course name in uppercase)
            $courseCode = $this->getCourseCode($course->name);
            
            // Year of entry (last 2 digits of current year or previous years)
            $entryYear = $this->getRandomEntryYear();
            
            // Country of origin (random from predefined list)
            $countryCode = $this->getRandomCountryCode();
            
            // Intake (F for February, A for August, S for September)
            $intakeCode = $this->getRandomIntakeCode();
            
            // Generate registration number
            $regNumber = $this->generateRegistrationNumber(
                $courseCode, 
                $entryYear, 
                $countryCode, 
                $intakeCode, 
                $studentCounters
            );

            $students[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'gender' => $this->getRandomGender(),
                'course_id' => $course->id,
                'dob' => $this->getRandomDateOfBirth($entryYear),
                'registration_number' => $regNumber,
                'department_id' => $user->department_id,
                'current_year' => $this->calculateCurrentYear($entryYear),
                'academic_year' => '2024',
                'current_semester' => rand(1, 2),
                'email' => $user->email,
                'phone' => $this->generateRandomPhone(),
                'address' => $this->getRandomAddress(),
                'city' => $this->getRandomCity(),
                'state' => $this->getRandomState(),
                'zip' => $this->generateRandomZip(),
                'country' => $this->getCountryName($countryCode),
                'photo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert all students
        Student::insert($students);

        $this->command->info('Students seeded successfully with new registration number format!');
    }

    private function getCourseCode($courseName)
    {
        $courseCodes = [
            'Bachelor of Science in Computer Science' => 'BSCS',
            'Bachelor of Science in Software Engineering' => 'BSSE',
            'Bachelor of Business Administration' => 'BBA',
            'Bachelor of Commerce' => 'BCOM',
            'Bachelor of Engineering in Civil Engineering' => 'BECE',
            'Bachelor of Engineering in Electrical Engineering' => 'BEEE',
            'Bachelor of Arts in English Literature' => 'BAEL',
            'Bachelor of Arts in History' => 'BAH',
        ];

        return $courseCodes[$courseName] ?? strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $courseName), 0, 4));
    }

    private function getRandomEntryYear()
    {
        // Students could have entered between 2020 and 2024
        $years = ['20', '21', '22', '23', '24'];
        return $years[array_rand($years)];
    }

    private function getRandomCountryCode()
    {
        $countries = [
            'U' => 'Uganda',
            'K' => 'Kenya',
            'T' => 'Tanzania',
            'R' => 'Rwanda',
            'B' => 'Burundi',
            'S' => 'Sudan',
            'E' => 'Ethiopia',
            'N' => 'Nigeria',
            'G' => 'Ghana',
            'Z' => 'South Africa',
        ];
        
        return array_rand($countries);
    }

    private function getCountryName($countryCode)
    {
        $countries = [
            'U' => 'Uganda',
            'K' => 'Kenya',
            'T' => 'Tanzania',
            'R' => 'Rwanda',
            'B' => 'Burundi',
            'S' => 'Sudan',
            'E' => 'Ethiopia',
            'N' => 'Nigeria',
            'G' => 'Ghana',
            'Z' => 'South Africa',
        ];
        
        return $countries[$countryCode] ?? 'Uganda';
    }

    private function getRandomIntakeCode()
    {
        $intakes = [
            'F' => 'February',
            'A' => 'August', 
            'S' => 'September',
        ];
        
        return array_rand($intakes);
    }

    private function generateRegistrationNumber($courseCode, $entryYear, $countryCode, $intakeCode, &$counters)
    {
        // Create a unique key for this combination
        $key = $courseCode . $entryYear . $countryCode . $intakeCode;
        
        // Initialize or increment counter for this combination
        if (!isset($counters[$key])) {
            $counters[$key] = 1;
        } else {
            $counters[$key]++;
        }
        
        // Format the sequential number with leading zeros
        $sequentialNumber = str_pad($counters[$key], 4, '0', STR_PAD_LEFT);
        
        return "{$courseCode}/{$entryYear}{$countryCode}/{$intakeCode}{$sequentialNumber}";
    }

    private function getRandomGender()
    {
        $genders = ['male', 'female'];
        return $genders[array_rand($genders)];
    }

    private function getRandomDateOfBirth($entryYear)
    {
        // Calculate approximate age (18-25 years old at time of entry)
        $entryFullYear = 2000 + intval($entryYear);
        $maxBirthYear = $entryFullYear - 18;
        $minBirthYear = $entryFullYear - 25;
        
        $birthYear = rand($minBirthYear, $maxBirthYear);
        $birthMonth = rand(1, 12);
        $birthDay = rand(1, 28); // Safe day for all months
        
        return Carbon::create($birthYear, $birthMonth, $birthDay);
    }

    private function calculateCurrentYear($entryYear)
    {
        $entryFullYear = 2000 + intval($entryYear);
        $currentYear = 2024; // Assuming current year is 2024
        
        $yearsSinceEntry = $currentYear - $entryFullYear;
        
        // Students can be in year 1, 2, 3, or 4
        return min(max($yearsSinceEntry + 1, 1), 4);
    }

    private function generateRandomPhone()
    {
        $prefixes = ['+256', '+254', '+255', '+250', '+257', '+249', '+251', '+234', '+233', '+27'];
        return $prefixes[array_rand($prefixes)] . rand(700000000, 799999999);
    }

    private function getRandomAddress()
    {
        $addresses = [
            '123 Main Street',
            '456 Oak Avenue',
            '789 Pine Road',
            '321 Elm Street',
            '654 Maple Drive',
            '987 Cedar Lane',
            '246 Birch Boulevard',
            '135 Walnut Street',
            '753 University Road',
            '864 Campus View',
        ];
        return $addresses[array_rand($addresses)];
    }

    private function getRandomCity()
    {
        $cities = [
            'Kampala', 'Entebbe', 'Jinja', 'Mbale', 'Gulu', 'Lira', 'Mbarara', 'Fort Portal',
            'Nairobi', 'Mombasa', 'Kisumu', 'Nakuru',
            'Dar es Salaam', 'Arusha', 'Mwanza', 'Dodoma',
            'Kigali', 'Butare', 'Gisenyi',
            'Bujumbura', 'Gitega',
            'Khartoum', 'Omdurman',
            'Addis Ababa', 'Dire Dawa',
            'Lagos', 'Abuja', 'Port Harcourt',
            'Accra', 'Kumasi',
            'Johannesburg', 'Cape Town', 'Durban'
        ];
        return $cities[array_rand($cities)];
    }

    private function getRandomState()
    {
        $states = [
            'Central', 'Eastern', 'Northern', 'Western', 'Southern',
            'Nairobi County', 'Coast Province', 'Rift Valley',
            'Dar es Salaam Region', 'Arusha Region',
            'Kigali City', 'Southern Province',
            'Bujumbura Mairie', 'Bujumbura Rural',
            'Khartoum State', 'Kassala State',
            'Addis Ababa', 'Oromia',
            'Lagos State', 'Federal Capital Territory',
            'Greater Accra', 'Ashanti',
            'Gauteng', 'Western Cape'
        ];
        return $states[array_rand($states)];
    }

    private function generateRandomZip()
    {
        return rand(10000, 99999);
    }
}
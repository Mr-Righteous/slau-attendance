# Attendance Evaluation System

A Laravel-based attendance management system for universities with role-based access control.

## Tech Stack

- Laravel 12
- Livewire 3
- MySQL
- Tailwind CSS 4
- Filament Components
- Spatie Laravel Permission

## Requirements

- PHP 8.1+
- Composer
- MySQL 8.0+
- Node.js & NPM

## Installation

### 1. Clone & Install Dependencies

```bash
git clone <repository-url>
cd attendance-system
composer install
npm install
```

### 2. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=attendance_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Database Setup

```bash
# Create database
mysql -u root -p
CREATE DATABASE attendance_db;
exit;

# Run migrations
php artisan migrate

# Publish Spatie permissions
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### 4. Create Admin User

Run this in Tinker or create a seeder:

```bash
php artisan tinker
```

```php
$admin = \App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@university.edu',
    'password' => bcrypt('password123'),
    'registration_number' => 'ADMIN001',
    'password_changed' => false,
]);

// Create roles
$adminRole = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
$lecturerRole = \Spatie\Permission\Models\Role::create(['name' => 'lecturer']);
$studentRole = \Spatie\Permission\Models\Role::create(['name' => 'student']);

// Assign admin role
$admin->assignRole($adminRole);
```

### 5. Create Sample Departments (Optional)

```bash
php artisan tinker
```

```php
\App\Models\Department::create(['name' => 'Computer Science', 'code' => 'CS']);
\App\Models\Department::create(['name' => 'Information Technology', 'code' => 'IT']);
\App\Models\Department::create(['name' => 'Business Administration', 'code' => 'BA']);
\App\Models\Department::create(['name' => 'Engineering', 'code' => 'ENG']);
```

### 6. Compile Assets

```bash
npm run dev
```

For production:
```bash
npm run build
```

### 7. Run the Application

```bash
php artisan serve
```

Visit: `http://localhost:8000`

**Admin Login:**
- Email: `admin@university.edu`
- Password: `password123`

## Features

### Admin Features
- Import students, lecturers, courses, and enrollments via CSV
- Mark attendance for class sessions
- Create class sessions
- View all attendance records
- Manage courses and enrollments

### Lecturer Features
- View assigned courses
- View student attendance statistics
- View attendance per session
- Flag low attendance sessions

### Student Features
- View enrolled courses
- View attendance per course
- View attendance percentage
- Overall attendance status

## Routes Setup

Add these routes to `routes/web.php`:

```php
use App\Livewire\Admin\ImportUsers;
use App\Livewire\Admin\MarkAttendance;
// Add other Livewire components as you create them

Route::middleware(['auth'])->group(function () {
    
    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/import', ImportUsers::class)->name('admin.import');
        Route::get('/attendance', MarkAttendance::class)->name('admin.attendance');
    });
    
    // Lecturer routes
    Route::middleware(['role:lecturer'])->prefix('lecturer')->group(function () {
        // Add lecturer routes here
    });
    
    // Student routes
    Route::middleware(['role:student'])->prefix('student')->group(function () {
        // Add student routes here
    });
});
```

## CSV Import Format

### Students Import
**File:** `students.csv`
```csv
registration_number,name,email,department_code
S2024001,John Doe,john@example.com,CS
S2024002,Jane Smith,jane@example.com,IT
```

### Lecturers Import
**File:** `lecturers.csv`
```csv
staff_number,name,email,department_code
L001,Dr. Smith,smith@example.com,CS
L002,Prof. Jones,jones@example.com,IT
```

### Courses Import
**File:** `courses.csv`
```csv
course_code,course_name,lecturer_staff_number,department_code,semester,academic_year,credits
CS101,Intro to Computer Science,L001,CS,1,2024/2025,3
IT201,Database Systems,L002,IT,2,2024/2025,4
```

### Enrollments Import
**File:** `enrollments.csv`
```csv
registration_number,course_code
S2024001,CS101
S2024001,IT201
S2024002,CS101
```

## Import Order

Follow this order when importing data:

1. **Departments** (Create manually or via Tinker)
2. **Students** (via CSV import)
3. **Lecturers** (via CSV import)
4. **Courses** (via CSV import - requires lecturers to exist)
5. **Enrollments** (via CSV import - requires students & courses to exist)

## Default Passwords

- **Students:** Registration number (e.g., `S2024001`)
- **Lecturers:** Staff number (e.g., `L001`)
- **Admin:** Set manually during creation

Users must change password on first login (tracked by `password_changed` field).

## Middleware Setup

Create role middleware or use Spatie's built-in middleware:

In `app/Http/Kernel.php`:
```php
protected $middlewareAliases = [
    // ... other middleware
    'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
    'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
];
```

## Database Structure

### Main Tables
- `users` - All users (students, lecturers, admins)
- `departments` - University departments
- `courses` - Course catalog
- `enrollments` - Student course enrollments
- `class_sessions` - Individual class meetings
- `attendance_records` - Attendance tracking
- `roles` & `permissions` - Spatie permission tables

## Development Workflow

### Branch Strategy
- `main` - Production-ready code
- `develop` - Integration branch
- `feature/*` - Feature branches

### Daily Workflow
```bash
# Pull latest changes
git pull origin develop

# Create feature branch
git checkout -b feature/your-feature-name

# Make changes, commit, and push
git add .
git commit -m "Description of changes"
git push origin feature/your-feature-name

# Merge to develop
git checkout develop
git merge feature/your-feature-name
```

## Common Issues & Solutions

### Issue: Spatie permissions not working
**Solution:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan optimize:clear
```

### Issue: Livewire components not loading
**Solution:**
```bash
composer dump-autoload
php artisan livewire:discover
```

### Issue: CSV import fails
**Solution:**
- Check CSV encoding (should be UTF-8)
- Ensure no extra spaces in headers
- Verify column names match exactly
- Check for proper line endings (LF, not CRLF)

### Issue: File upload not working
**Solution:**
```bash
# Ensure storage is linked
php artisan storage:link

# Check permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## Testing

### Create Test Data
```bash
php artisan tinker
```

```php
// Create test student
$student = \App\Models\User::create([
    'name' => 'Test Student',
    'email' => 'student@test.com',
    'password' => bcrypt('password'),
    'registration_number' => 'TEST001',
]);
$student->assignRole('student');

// Create test course
$course = \App\Models\Course::create([
    'code' => 'TEST101',
    'name' => 'Test Course',
    'department_id' => 1,
    'semester' => '1',
    'academic_year' => '2024/2025',
]);

// Enroll student
\App\Models\Enrollment::create([
    'student_id' => $student->id,
    'course_id' => $course->id,
]);
```

## MVP Checklist (Week 1)

- [x] Database migrations
- [x] User models & relationships
- [x] CSV import functionality
- [x] Mark attendance component
- [ ] Student dashboard (view attendance)
- [ ] Lecturer dashboard (view statistics)
- [ ] Admin dashboard (overview)
- [ ] Authentication & role middleware
- [ ] Basic styling with Tailwind

## Post-MVP Features (Week 2+)

- [ ] QR code attendance
- [ ] Email notifications
- [ ] PDF/Excel report export
- [ ] Attendance trends & analytics
- [ ] Lecturer evaluation reports
- [ ] Mobile responsive optimization
- [ ] Attendance appeal system

## Team Assignments

**Developer 1:** Authentication, layouts, import system
**Developer 2:** Admin features, attendance marking, courses
**Developer 3:** Student & lecturer dashboards, statistics

## Support

For issues or questions, contact the development team or create an issue in the repository.

## License

[Add your license here]
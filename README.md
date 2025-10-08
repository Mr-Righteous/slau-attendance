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
git clone https://github.com/Mr-Righteous/slau-attendance
cd slau-attendance
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
php artisan migrate --seed

# Publish Spatie permissions
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### 4. Create Sample data files 

```bash
cp scripts/generate_all.py ~/Desktop
cd ~/Desktop
python3 generate_all.py

```

### 5. Compile Assets

```bash
npm run dev
```

For production:
```bash
npm run build
```

### 6. Run the Application

```bash
php artisan serve 
or
composer run dev
```

Visit: `http://localhost:8000` OR `http://127.0.0.1:8000`

**Admin Login:**
- Email: `admin@example.com`
- Password: `password`

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

## CSV Import Format

### Students Import
**File:** `students.csv`


### Lecturers Import
**File:** `lecturers.csv`


### Courses Import
**File:** `courses.csv`


## Import Order

Follow this order when importing data:

1. **Departments** (via CSV import)
2. **Courses** (via CSV import - requires lecturers to exist)
3. **Lecturers** (via CSV import)
4. **CourseUnits** (via CSV import - requires lecturers to exist)
5. **Students** (via CSV import)

## Default Passwords

- **Students:** Registration number (e.g., `S2024001`)
- **Lecturers:** Staff number (e.g., `L001`)
- **Admin:** Set manually during creation

## Database Structure

### Main Tables
- `users` - All users (students, lecturers, admins)
- `departments` - University departments
- `courses` - Course catalog
- `class_sessions` - Individual class meetings
- `attendance_records` - Attendance tracking
- `roles` & `permissions` - Spatie permission tables

## Development Workflow

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

## Support

For issues or questions, contact the development team or create an issue in the repository.

## License

[none]
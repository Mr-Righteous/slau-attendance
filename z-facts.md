# Attendance Evaluation System - MVP Plan (1 Week)

## Tech Stack
- Laravel 10/11 + Livewire 3
- MySQL
- Filament Components (Tables, Forms only)
- Tailwind CSS

## Database Schema

```sql
-- Users table (extend Laravel default)
users: id, name, email, password, role(enum: admin,lecturer,student), registration_number, department_id

departments: id, name, code

courses: id, code, name, lecturer_id, department_id, semester, academic_year, credits

enrollments: id, course_id, student_id, enrolled_at

class_sessions: id, course_id, date, start_time, end_time, topic, venue

attendance_records: id, class_session_id, student_id, status(enum: present,absent,late,excused), marked_by, marked_at
```

## Data Import Strategy (Existing University Data)

### Phase 1: Bulk Import (Day 1)
1. **Excel/CSV Import Command**
   - `php artisan import:students {file}` - imports students
   - `php artisan import:courses {file}` - imports courses
   - `php artisan import:enrollments {file}` - imports enrollments
   - `php artisan import:lecturers {file}` - imports lecturers

2. **Required CSV Formats**
   ```
   students.csv: registration_number, name, email, department_code
   lecturers.csv: staff_number, name, email, department_code
   courses.csv: course_code, course_name, lecturer_staff_number, semester, year
   enrollments.csv: registration_number, course_code
   ```

3. **Default Passwords**: Use registration/staff number, force change on first login

### Phase 2: Admin Interface (Post-MVP)
- Manual add/edit students/courses via Livewire forms

## Team Task Distribution

### Developer 1: Foundation (Days 1-2)
- [ ] Laravel setup with Livewire & Filament
- [ ] Database migrations
- [ ] Auth with roles (Breeze + role enum)
- [ ] Seeder for admin user
- [ ] **Import commands for bulk data**
- [ ] Base layouts (admin, lecturer, student)
- [ ] Middleware for role-based access

**Deliverables**: Working auth, database ready, import commands functional

### Developer 2: Admin Module (Days 2-5)
- [ ] Admin dashboard (stats cards)
- [ ] Course management (list, create, edit)
- [ ] Student enrollment management
- [ ] **Class session creation form**
- [ ] **Attendance marking interface** (main feature)
  - Select course → Select/create session → Mark all students
  - Bulk checkbox interface
  - Save all attendance records at once
- [ ] View attendance by course/session

**Deliverables**: Admin can mark attendance for any class

### Developer 3: Lecturer & Student Views (Days 2-5)
- [ ] Student Dashboard
  - List enrolled courses
  - Attendance per course (table)
  - Attendance percentage per course
  - Overall attendance percentage
- [ ] Lecturer Dashboard
  - List assigned courses
  - View students in each course
  - Attendance statistics per course
  - Attendance rate per session
  - Flag low attendance sessions
- [ ] Shared components (stats cards, attendance tables)

**Deliverables**: Students see their attendance, lecturers see class stats

### All Devs: Integration & Testing (Days 6-7)
- [ ] Cross-testing all features
- [ ] UI polish with Tailwind
- [ ] Data validation
- [ ] Import sample data
- [ ] Bug fixes
- [ ] Prepare demo

## Key Livewire Components

```
app/Livewire/
├── Admin/
│   ├── Dashboard.php
│   ├── ManageCourses.php
│   ├── CreateSession.php
│   └── MarkAttendance.php (CRITICAL)
├── Lecturer/
│   ├── Dashboard.php
│   ├── ViewCourses.php
│   └── AttendanceStatistics.php
├── Student/
│   ├── Dashboard.php
│   └── MyAttendance.php
└── Shared/
    ├── AttendanceTable.php
    └── StatsCard.php
```

## Attendance Marking Flow (Core Feature)

1. Admin navigates to "Mark Attendance"
2. Selects course from dropdown
3. Either:
   - Creates new session (date, time, topic)
   - OR selects existing session
4. System displays enrolled students (Filament Table)
5. Admin checks Present/Absent/Late/Excused for each student
6. Clicks "Save Attendance" (bulk insert)
7. Success notification

## Evaluation Metrics

### Students
- Attendance % = (Present + Late) / Total Sessions × 100
- Status: Good (≥75%), Warning (50-74%), Critical (<50%)

### Lecturers (for admin evaluation)
- Average class attendance across all courses
- Number of sessions with <50% attendance
- Trend over time

## MVP Features Checklist

**Week 1 Must-Haves:**
- ✅ Role-based authentication
- ✅ Bulk data import via CSV
- ✅ Course & enrollment management
- ✅ Class session creation
- ✅ Attendance marking (admin only)
- ✅ Student attendance view
- ✅ Lecturer statistics view
- ✅ Basic dashboards for all roles

**Post-MVP (Week 2+):**
- QR code attendance
- Email notifications
- Advanced reports (PDF/Excel export)
- Attendance trends & analytics
- Lecturer evaluation reports
- Mobile responsive optimization

## Critical Dependencies

**Day 1:** Database + Import commands ready
**Day 2:** Auth + Enrollments working
**Day 4:** Attendance marking functional
**Day 5:** All views integrated
**Day 7:** Demo ready

## File Structure

```
app/
├── Console/Commands/
│   ├── ImportStudents.php
│   ├── ImportLecturers.php
│   ├── ImportCourses.php
│   └── ImportEnrollments.php
├── Livewire/ (components above)
├── Models/
│   ├── User.php
│   ├── Course.php
│   ├── ClassSession.php
│   ├── Enrollment.php
│   └── AttendanceRecord.php
└── Services/
    ├── AttendanceService.php
    └── ImportService.php

database/migrations/
├── 2024_xx_add_role_to_users_table.php
├── 2024_xx_create_departments_table.php
├── 2024_xx_create_courses_table.php
├── 2024_xx_create_enrollments_table.php
├── 2024_xx_create_class_sessions_table.php
└── 2024_xx_create_attendance_records_table.php
```

## Testing Data

Create seeders for:
- 1 Admin user
- 5 Lecturers
- 50 Students
- 10 Courses
- Sample enrollments
- 20 Class sessions
- Sample attendance records

## Git Workflow

- `main` - production
- `develop` - integration branch
- `feature/auth` (Dev 1)
- `feature/admin` (Dev 2)
- `feature/views` (Dev 3)

Daily standup: Morning sync, merge to develop EOD

## Notes

- Start with import commands on Day 1 - critical for working with existing data
- Focus on attendance marking interface - it's the core feature
- Keep UI simple, functionality first
- Use Filament components for tables/forms to save time
- Validate imported data (check for duplicates, invalid refs)
- Default password = registration number (force change)

1. AI-Powered Competency Gap Analyzer & Personalized Learning Pathways
System: Analyzes students' performance across different competencies (not just grades), identifies specific skill gaps, and generates personalized learning resources. Uses NLP to parse curriculum documents and match them with industry requirements from job postings in Uganda.

Could integrate with existing LMS platforms
Tracks competency development over time with visual dashboards
Suggests internships/projects that fill specific gaps

2. Intelligent Curriculum-Industry Alignment Platform
System: Uses AI to continuously analyze Uganda's job market trends, industry requirements, and emerging skills. Automatically suggests curriculum updates to deans/HODs to keep programs relevant.

Web scraping + NLP for job posting analysis
Competency mapping between what's taught vs what's needed
Generates reports on curriculum relevance scores
Could partner with platforms like BrighterMonday Uganda, Fuzu, etc.

3. AI Assessment Generator for Competency-Based Evaluation
System: Helps lecturers create assessments that truly test competencies rather than memorization. Uses AI to generate scenarios, case studies, and practical problems aligned with specific learning outcomes.

Natural language processing to understand competency descriptions
Generates multiple assessment types (projects, scenarios, rubrics)
Anti-plagiarism checking with local context awareness
Adaptive difficulty based on student performance

4. Virtual Competency Coach & Career Advisor
System: An AI chatbot that guides students through their competency development journey, suggests courses, projects, and extracurriculars. Provides career guidance based on their competency profile and Ugandan job market.

24/7 availability (crucial for large student populations)
Multi-language support (English, Luganda, etc.)
Integration with student records
Tracks soft skills development

5. AI-Driven Collaborative Learning Matcher
System: Matches students for group projects/study groups based on complementary competencies, learning styles, and goals. Uses machine learning to optimize team formations for better learning outcomes.

Personality + competency profiling
Success prediction for team compositions
Facilitates peer-to-peer learning
Could work across different universities

6. Intelligent Research Paper & Project Evaluator
System: Assists supervisors in evaluating student research/projects against competency frameworks. Provides detailed feedback on methodology, literature review quality, innovation, and practical application.

NLP for document analysis
Plagiarism detection with local thesis database
Competency scoring automation
Reduces supervisor workload significantly

7. AI-Powered Practical Skills Verification System
System: Uses computer vision and AI to verify and assess practical competencies (e.g., lab work, presentations, technical demonstrations). Creates digital portfolios of verified skills.

Video analysis of practical demonstrations
Automated skill verification certificates
Blockchain integration for credential verification
Useful for employers to verify actual competencies

8. Predictive Student Success & Intervention System
System: Uses machine learning to predict which students are at risk of not developing key competencies. Triggers early interventions (tutoring, counseling, resources) before they fall too far behind.

Multiple data sources (attendance, assessments, engagement)
Privacy-conscious design
Intervention recommendation engine
Dashboard for academic advisors
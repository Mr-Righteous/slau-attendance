<?php

use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\ImportUsers;
use App\Livewire\Admin\ManageCourses;
use App\Livewire\Admin\MarkClassAttendance;
use App\Livewire\Admin\ViewAttendance;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {

    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/import', ImportUsers::class)->name('admin.import');
        Route::get('/courses', ManageCourses::class)->name('admin.courses');
        Route::get('/course-units', ManageCourses::class)->name('admin.course-units');
        // Route::get('/courses', ManageCourses::class)->name('admin.courses');
        Route::get('/manage-departments', \App\Livewire\Admin\ManageDepartments::class)->name('admin.manage-departments');
        Route::get('/manage-course-units', \App\Livewire\Admin\ManageCourseUnits::class)->name('admin.manage-course-units');
        Route::get('/manage-students', \App\Livewire\Admin\ManageStudents::class)->name('admin.manage-students');
        Route::get('/view-attendance', ViewAttendance::class)->name('admin.view-attendance');
        Route::get('/reports', \App\Livewire\Admin\AdvancedReports::class)->name('admin.reports');
        Route::get('/dashboard', AdminDashboard::class)->name('admin.dashboard');
        Route::get('/attendance', MarkClassAttendance::class)->name('admin.attendance');
    });

    // Lecturer routes
    Route::middleware(['role:lecturer'])->prefix('lecturer')->name('lecturer.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Lecturer\LecturerDashboard::class)->name('dashboard');
        Route::get('/courses', \App\Livewire\Lecturer\MyCourses::class)->name('courses');
        Route::get('/attendance', \App\Livewire\Lecturer\ViewCourseAttendance::class)->name('attendance');
    });

    // Student routes
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Student\StudentDashboard::class)->name('dashboard');
        Route::get('/courses', \App\Livewire\Student\StudentCourses::class)->name('courses');
        Route::get('/attendance', \App\Livewire\Student\MyAttendance::class)->name('attendance');
    });


    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

require __DIR__.'/auth.php';

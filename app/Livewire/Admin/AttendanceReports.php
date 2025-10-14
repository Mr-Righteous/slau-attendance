<?php

namespace App\Livewire\Admin;

use App\Exports\AttendanceExport;
use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;

use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;





class AttendanceReports extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public string $reportType = 'students'; // students, sessions, lecturers, courses
    public array $stats = [];

    public function mount()
    {
        $this->calculateStats();
    }

    public function updatedReportType()
    {
        $this->calculateStats();
        $this->resetTable();
    }

    public function calculateStats()
    {
        $user = Auth::user();
        $query = AttendanceRecord::query();

        // Apply role-based filtering
        if ($user->hasRole('dpt-hod') && $user->department) {
            $query->whereHas('student', function ($q) use ($user) {
                $q->where('department_id', $user->department->id);
            });
        }

        $total = $query->count();
        $present = (clone $query)->where('status', 'present')->count();
        $late = (clone $query)->where('status', 'late')->count();
        $absent = (clone $query)->where('status', 'absent')->count();
        $excused = (clone $query)->where('status', 'excused')->count();

        $this->stats = [
            'total_records' => $total,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'excused' => $excused,
            'attendance_rate' => $total > 0 ? round((($present + $late) / $total) * 100, 2) : 0,
        ];
    }

    public function table(Table $table): Table
    {
        return match($this->reportType) {
            'students' => $this->studentsTable($table),
            'sessions' => $this->sessionsTable($table),
            'lecturers' => $this->lecturersTable($table),
            'courses' => $this->coursesTable($table),
            default => $this->studentsTable($table),
        };
    }

    // ==========================================
    // STUDENTS REPORT
    // ==========================================
    protected function studentsTable(Table $table): Table
    {
        return $table
            ->query(
                Student::query()
                    ->with(['department', 'course'])
                    ->withCount([
                        'attendanceRecords as total_sessions',
                        'attendanceRecords as present_count' => fn($q) => $q->where('status', 'present'),
                        'attendanceRecords as late_count' => fn($q) => $q->where('status', 'late'),
                        'attendanceRecords as absent_count' => fn($q) => $q->where('status', 'absent'),
                    ])
                    ->when(Auth::user()->hasRole('dpt-hod') && Auth::user()->department, function ($q) {
                        $q->where('department_id', Auth::user()->department->id);
                    })
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('registration_number')
                    ->label('Reg. Number')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('course.name')
                    ->label('Course')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('current_year')
                    ->label('Year')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                
                TextColumn::make('current_semester')
                    ->label('Sem')
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                
                TextColumn::make('total_sessions')
                    ->label('Total Sessions')
                    ->sortable()
                    ->alignCenter(),
                
                TextColumn::make('present_count')
                    ->label('Present')
                    ->sortable()
                    ->alignCenter()
                    ->color('success'),
                
                TextColumn::make('late_count')
                    ->label('Late')
                    ->sortable()
                    ->alignCenter()
                    ->color('warning'),
                
                TextColumn::make('absent_count')
                    ->label('Absent')
                    ->sortable()
                    ->alignCenter()
                    ->color('danger'),
                
                TextColumn::make('attendance_rate')
                    ->label('Attendance %')
                    ->state(function (Student $record): string {
                        $total = $record->total_sessions;
                        $attended = $record->present_count + $record->late_count;
                        return $total > 0 ? round(($attended / $total) * 100, 2) . '%' : '0%';
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw(
                            "((present_count + late_count) / NULLIF(total_sessions, 0) * 100) {$direction}"
                        );
                    })
                    ->badge()
                    ->color(fn (Student $record): string => match (true) {
                        $record->total_sessions == 0 => 'gray',
                        (($record->present_count + $record->late_count) / $record->total_sessions * 100) >= 75 => 'success',
                        (($record->present_count + $record->late_count) / $record->total_sessions * 100) >= 50 => 'warning',
                        default => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('course_id')
                    ->label('Course')
                    ->options(Course::forUserRole(Auth::user())->pluck('name', 'id'))
                    ->searchable(),
                
                SelectFilter::make('department_id')
                    ->label('Department')
                    ->options(Department::pluck('name', 'id'))
                    ->searchable()
                    ->visible(fn() => Auth::user()->hasAnyRole(['super-admin', 'big-admin', 'faculty-dean'])),
                
                SelectFilter::make('current_year')
                    ->label('Year of Study')
                    ->options([
                        1 => 'Year 1',
                        2 => 'Year 2',
                        3 => 'Year 3',
                        4 => 'Year 4',
                    ]),
                
                SelectFilter::make('current_semester')
                    ->label('Semester')
                    ->options([
                        1 => 'Semester 1',
                        2 => 'Semester 2',
                    ]),
                
                Filter::make('low_attendance')
                    ->label('Low Attendance (< 75%)')
                    ->query(fn (Builder $query): Builder => 
                        $query->having('total_sessions', '>', 0)
                              ->havingRaw('((present_count + late_count) / total_sessions * 100) < 75')
                    )
                    ->toggle(),
                
                Filter::make('critical_attendance')
                    ->label('Critical Attendance (< 50%)')
                    ->query(fn (Builder $query): Builder => 
                        $query->having('total_sessions', '>', 0)
                              ->havingRaw('((present_count + late_count) / total_sessions * 100) < 50')
                    )
                    ->toggle(),
            ])
            ->recordActions([
                Action::make('view_details')
                    ->label('Details')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Student $record): string => route('admin.dashboard', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolBarActions([
                Action::make('export_selected')
                    ->label('Export Selected')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        return $this->exportStudentsData($records);
                    }),
            ])
            ->defaultSort('name');
    }

    // ==========================================
    // SESSIONS REPORT
    // ==========================================
    protected function sessionsTable(Table $table): Table
    {
        return $table
            ->query(
                ClassSession::query()
                    ->with(['courseUnit.courses', 'lecturer'])
                    ->withCount([
                        'attendanceRecords as total_marked',
                        'attendanceRecords as present_count' => fn($q) => $q->where('status', 'present'),
                        'attendanceRecords as late_count' => fn($q) => $q->where('status', 'late'),
                        'attendanceRecords as absent_count' => fn($q) => $q->where('status', 'absent'),
                    ])
                    ->when(Auth::user()->hasRole('dpt-hod') && Auth::user()->department, function ($q) {
                        $q->whereHas('courseUnit', function ($query) {
                            $query->where('department_id', Auth::user()->department->id);
                        });
                    })
            )
            ->columns([
                TextColumn::make('date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('courseUnit.code')
                    ->label('Course Unit')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('courseUnit.name')
                    ->label('Unit Name')
                    ->searchable()
                    ->toggleable()
                    ->wrap(),
                
                TextColumn::make('lecturer.name')
                    ->label('Lecturer')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('week')
                    ->label('Week')
                    ->sortable()
                    ->alignCenter()
                    ->badge(),
                
                TextColumn::make('start_time')
                    ->label('Start')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('end_time')
                    ->label('End')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('duration')
                    ->label('Duration')
                    ->state(function (ClassSession $record): string {
                        if (!$record->start_time || !$record->end_time) return 'N/A';
                        $start = \Carbon\Carbon::parse($record->start_time);
                        $end = \Carbon\Carbon::parse($record->end_time);
                        $diff = $start->diffInMinutes($end);
                        $hours = floor($diff / 60);
                        $mins = $diff % 60;
                        return $hours > 0 ? "{$hours}h {$mins}m" : "{$mins}m";
                    })
                    ->toggleable(),
                
                TextColumn::make('total_marked')
                    ->label('Total')
                    ->sortable()
                    ->alignCenter(),
                
                TextColumn::make('present_count')
                    ->label('Present')
                    ->sortable()
                    ->alignCenter()
                    ->color('success'),
                
                TextColumn::make('absent_count')
                    ->label('Absent')
                    ->sortable()
                    ->alignCenter()
                    ->color('danger'),
                
                TextColumn::make('attendance_rate')
                    ->label('Rate')
                    ->state(function (ClassSession $record): string {
                        $total = $record->total_marked;
                        $attended = $record->present_count + $record->late_count;
                        return $total > 0 ? round(($attended / $total) * 100, 1) . '%' : '0%';
                    })
                    ->badge()
                    ->color(fn (ClassSession $record): string => match (true) {
                        $record->total_marked == 0 => 'gray',
                        (($record->present_count + $record->late_count) / $record->total_marked * 100) >= 75 => 'success',
                        (($record->present_count + $record->late_count) / $record->total_marked * 100) >= 50 => 'warning',
                        default => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('course_unit_id')
                    ->label('Course Unit')
                    ->relationship('courseUnit', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('lecturer_id')
                    ->label('Lecturer')
                    ->relationship('lecturer', 'name')
                    ->searchable()
                    ->preload(),
                
                Filter::make('date_range')
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        \Filament\Forms\Components\DatePicker::make('to')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('date', '>=', $date))
                            ->when($data['to'], fn($q, $date) => $q->whereDate('date', '<=', $date));
                    }),
                
                Filter::make('this_month')
                    ->label('This Month')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereMonth('date', now()->month)
                              ->whereYear('date', now()->year)
                    )
                    ->toggle(),
                
                Filter::make('low_attendance_session')
                    ->label('Low Attendance Sessions (< 75%)')
                    ->query(fn (Builder $query): Builder => 
                        $query->having('total_marked', '>', 0)
                              ->havingRaw('((present_count + late_count) / total_marked * 100) < 75')
                    )
                    ->toggle(),
            ])
            ->defaultSort('date', 'desc');
    }

    // ==========================================
    // LECTURERS REPORT
    // ==========================================
    protected function lecturersTable(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->whereHas('roles', fn($q) => $q->where('name', 'lecturer'))
                    ->with('department')
                    ->withCount([
                        'taughtSessions as total_sessions',
                        'taughtSessions as total_hours' => function ($q) {
                            $q->select(DB::raw('SUM(TIMESTAMPDIFF(MINUTE, start_time, end_time))'));
                        },
                    ])
                    ->when(Auth::user()->hasRole('dpt-hod') && Auth::user()->department, function ($q) {
                        $q->where('department_id', Auth::user()->department->id);
                    })
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Lecturer Name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('total_sessions')
                    ->label('Sessions')
                    ->sortable()
                    ->alignCenter(),
                
                TextColumn::make('total_hours')
                    ->label('Total Hours')
                    ->state(function (User $record): string {
                        $minutes = $record->total_hours ?? 0;
                        $hours = floor($minutes / 60);
                        $mins = $minutes % 60;
                        return $hours > 0 ? "{$hours}h {$mins}m" : "{$mins}m";
                    })
                    ->sortable()
                    ->alignCenter(),
                
                TextColumn::make('avg_session_length')
                    ->label('Avg Length')
                    ->state(function (User $record): string {
                        if ($record->total_sessions == 0) return 'N/A';
                        $avgMinutes = round(($record->total_hours ?? 0) / $record->total_sessions);
                        $hours = floor($avgMinutes / 60);
                        $mins = $avgMinutes % 60;
                        return $hours > 0 ? "{$hours}h {$mins}m" : "{$mins}m";
                    })
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('department_id')
                    ->label('Department')
                    ->options(Department::pluck('name', 'id'))
                    ->searchable()
                    ->visible(fn() => Auth::user()->hasAnyRole(['super-admin', 'big-admin', 'faculty-dean'])),
                
                Filter::make('active_this_month')
                    ->label('Active This Month')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereHas('taughtSessions', function ($q) {
                            $q->whereMonth('date', now()->month)
                              ->whereYear('date', now()->year);
                        })
                    )
                    ->toggle(),
            ])
            ->defaultSort('name');
    }

    // ==========================================
    // COURSES REPORT
    // ==========================================
    protected function coursesTable(Table $table): Table
    {
        return $table
            ->query(
                CourseUnit::query()
                    ->with(['department', 'lecturer'])
                    ->withCount([
                        'classSessions as total_sessions',
                    ])
                    ->when(Auth::user()->hasRole('dpt-hod') && Auth::user()->department, function ($q) {
                        $q->where('department_id', Auth::user()->department->id);
                    })
            )
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('name')
                    ->label('Course Unit')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                
                TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable(),
                
                TextColumn::make('lecturer.name')
                    ->label('Lecturer')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('credit_units')
                    ->label('CUs')
                    ->sortable()
                    ->alignCenter(),
                
                TextColumn::make('total_sessions')
                    ->label('Sessions')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('department_id')
                    ->label('Department')
                    ->options(Department::pluck('name', 'id'))
                    ->searchable()
                    ->visible(fn() => Auth::user()->hasAnyRole(['super-admin', 'big-admin', 'faculty-dean'])),
                
                SelectFilter::make('lecturer_id')
                    ->label('Lecturer')
                    ->relationship('lecturer', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('code');
    }

    // ==========================================
    // EXPORT METHODS
    // ==========================================
    // protected function exportStudentsData(Collection $records)
    // {
    //     // Implementation for export
    //     // You would use maatwebsite/excel here
    //     return response()->streamDownload(function () use ($records) {
    //         echo "Student Name,Reg Number,Course,Year,Semester,Total Sessions,Present,Late,Absent,Attendance Rate\n";
    //         foreach ($records as $student) {
    //             $total = $student->total_sessions;
    //             $attended = $student->present_count + $student->late_count;
    //             $rate = $total > 0 ? round(($attended / $total) * 100, 2) : 0;
                
    //             echo implode(',', [
    //                 $student->name,
    //                 $student->registration_number,
    //                 $student->course->name ?? 'N/A',
    //                 $student->current_year,
    //                 $student->current_semester,
    //                 $total,
    //                 $student->present_count,
    //                 $student->late_count,
    //                 $student->absent_count,
    //                 $rate . '%'
    //             ]) . "\n";
    //         }
    //     }, 'students-attendance-report-' . now()->format('Y-m-d') . '.csv');
    // }

    protected function exportStudentsData(Collection $records)
    {
        return Excel::download(
            new AttendanceExport($records, 'students'),
            'students-attendance-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.admin.attendance-reports');
    }
}
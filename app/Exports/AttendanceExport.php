<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $records;
    protected $type;

    public function __construct($records, $type = 'students')
    {
        $this->records = $records;
        $this->type = $type;
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        if ($this->type === 'students') {
            return [
                'Student Name',
                'Registration Number',
                'Course',
                'Department',
                'Year',
                'Semester',
                'Total Sessions',
                'Present',
                'Late',
                'Absent',
                'Excused',
                'Attendance Rate (%)',
            ];
        }

        if ($this->type === 'sessions') {
            return [
                'Date',
                'Course Unit Code',
                'Course Unit Name',
                'Lecturer',
                'Week',
                'Start Time',
                'End Time',
                'Duration (mins)',
                'Total Students',
                'Present',
                'Late',
                'Absent',
                'Attendance Rate (%)',
            ];
        }

        return [];
    }

    public function map($record): array
    {
        if ($this->type === 'students') {
            $total = $record->total_sessions;
            $attended = $record->present_count + $record->late_count;
            $rate = $total > 0 ? round(($attended / $total) * 100, 2) : 0;

            return [
                $record->name,
                $record->registration_number,
                $record->course->name ?? 'N/A',
                $record->department->name ?? 'N/A',
                $record->current_year,
                $record->current_semester,
                $total,
                $record->present_count,
                $record->late_count,
                $record->absent_count,
                $record->excused_count ?? 0,
                $rate,
            ];
        }

        if ($this->type === 'sessions') {
            $total = $record->total_marked;
            $attended = $record->present_count + $record->late_count;
            $rate = $total > 0 ? round(($attended / $total) * 100, 2) : 0;

            $duration = null;
            if ($record->start_time && $record->end_time) {
                $start = \Carbon\Carbon::parse($record->start_time);
                $end = \Carbon\Carbon::parse($record->end_time);
                $duration = $start->diffInMinutes($end);
            }

            return [
                $record->date->format('Y-m-d'),
                $record->courseUnit->code ?? 'N/A',
                $record->courseUnit->name ?? 'N/A',
                $record->lecturer->name ?? 'N/A',
                $record->week,
                $record->start_time ? \Carbon\Carbon::parse($record->start_time)->format('H:i') : 'N/A',
                $record->end_time ? \Carbon\Carbon::parse($record->end_time)->format('H:i') : 'N/A',
                $duration ?? 'N/A',
                $total,
                $record->present_count,
                $record->late_count,
                $record->absent_count,
                $rate,
            ];
        }

        return [];
    }
}
<?php

namespace App\Traits;

use App\Models\Faculty;

trait HasFacultyScope
{
    //

    public function scopeInFaculty($query, $facultyId)
    {
        $id = $facultyId instanceof Faculty ? $facultyId->id : $facultyId;

        return $query->whereHas('department', 
        function ($q) use ($id) {
            $q->where('faculty_id', $id);
        });
    }
}

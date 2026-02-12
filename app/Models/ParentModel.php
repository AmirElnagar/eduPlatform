<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParentModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'address',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id')
            ->withTimestamps();
    }

    public function pendingRequests()
    {
        return $this->hasMany(ParentStudentRequest::class, 'parent_id')
            ->where('status', 'pending');
    }

    public function requests()
    {
        return $this->hasMany(ParentStudentRequest::class, 'parent_id');
    }

    // Helper Methods
    public function hasChild(Student $student): bool
    {
        return $this->students()->where('students.id', $student->id)->exists();
    }

    public function hasPendingRequestFor(Student $student): bool
    {
        return $this->pendingRequests()
            ->where('student_id', $student->id)
            ->exists();
    }
}
<?php

namespace App\Models;

use App\Enums\ParentRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentStudentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'student_id',
        'status',
        'message',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ParentRequestStatus::class,
            'responded_at' => 'datetime',
        ];
    }

    // Relationships
    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Helper Methods
    public function accept()
    {
        $this->update([
            'status' => ParentRequestStatus::ACCEPTED,
            'responded_at' => now(),
        ]);

        // ربط ولي الأمر بالطالب
        $this->parent->students()->attach($this->student_id);
    }

    public function reject()
    {
        $this->update([
            'status' => ParentRequestStatus::REJECTED,
            'responded_at' => now(),
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === ParentRequestStatus::PENDING;
    }
}
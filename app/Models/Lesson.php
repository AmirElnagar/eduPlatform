<?php

namespace App\Models;

use App\Enums\LessonType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id',
        'title',
        'description',
        'type',
        'video_path',
        'duration_minutes',
        'file_size',
        'meeting_url',
        'meeting_id',
        'meeting_password',
        'scheduled_at',
        'started_at',
        'ended_at',
        'is_published',
        'is_free',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'type' => LessonType::class,
            'duration_minutes' => 'integer',
            'file_size' => 'integer',
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'is_published' => 'boolean',
            'is_free' => 'boolean',
            'order' => 'integer',
        ];
    }

    // Relationships
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function attendance()
    {
        return $this->hasMany(LessonAttendance::class);
    }

    public function views()
    {
        return $this->hasMany(LessonView::class);
    }

    // Helper Methods
    public function isOffline(): bool
    {
        return $this->type === LessonType::OFFLINE;
    }

    public function isRecorded(): bool
    {
        return $this->type === LessonType::RECORDED;
    }

    public function isLive(): bool
    {
        return $this->type === LessonType::LIVE;
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->video_path ? asset('storage/' . $this->video_path) : null;
    }

    public function getFileSizeInMbAttribute(): ?float
    {
        return $this->file_size ? round($this->file_size / 1048576, 2) : null;
    }

    public function hasStudentWatched(Student $student): bool
    {
        return $this->views()->where('student_id', $student->id)->exists();
    }
}
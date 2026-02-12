<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonView extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'student_id',
        'watch_duration',
        'last_position',
        'completed',
        'last_watched_at',
    ];

    protected function casts(): array
    {
        return [
            'watch_duration' => 'integer',
            'last_position' => 'integer',
            'completed' => 'boolean',
            'last_watched_at' => 'datetime',
        ];
    }

    // Relationships
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Helper Methods
    public function getProgressPercentageAttribute(): float
    {
        if (!$this->lesson->duration_minutes) {
            return 0;
        }

        $totalSeconds = $this->lesson->duration_minutes * 60;
        return min(100, round(($this->watch_duration / $totalSeconds) * 100, 2));
    }

    public function updateProgress(int $position, int $duration)
    {
        $this->update([
            'last_position' => $position,
            'watch_duration' => $duration,
            'last_watched_at' => now(),
            'completed' => $this->isFullyWatched($position, $duration),
        ]);
    }

    private function isFullyWatched(int $position, int $duration): bool
    {
        if (!$this->lesson->duration_minutes) {
            return false;
        }

        $totalSeconds = $this->lesson->duration_minutes * 60;
        return $duration >= ($totalSeconds * 0.95); // 95% watched = completed
    }
}
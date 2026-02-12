<?php

namespace App\Models;

use App\Enums\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'subject',
        'bio',
        'years_of_experience',
        'hourly_rate',
        'is_subscribed',
        'subscription_start',
        'subscription_end',
    ];

    protected function casts(): array
    {
        return [
            'subject' => Subject::class,
            'years_of_experience' => 'integer',
            'hourly_rate' => 'decimal:2',
            'is_subscribed' => 'boolean',
            'subscription_start' => 'datetime',
            'subscription_end' => 'datetime',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Group::class);
    }

    public function exams()
    {
        return $this->hasManyThrough(Exam::class, Group::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Helper Methods
    public function isSubscriptionActive(): bool
    {
        if (!$this->is_subscribed) {
            return true; // إذا الاشتراكات معطلة، كل المدرسين active
        }

        return $this->subscription_end && $this->subscription_end->isFuture();
    }

    public function totalStudents(): int
    {
        return $this->groups()->withCount('students')->get()->sum('students_count');
    }

    public function totalEarnings(): float
    {
        return $this->payments()
            ->where('status', 'completed')
            ->sum('amount');
    }
}
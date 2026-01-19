<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'student_id',
        'rating',
        'comment',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Approve review
    public function approve()
    {
        $this->update(['is_approved' => true]);

        // Update teacher rating
        $this->teacher->updateRating();
    }

    // Reject review
    public function reject()
    {
        $this->update(['is_approved' => false]);
    }

    // Scope approved reviews
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    // Scope by rating
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    // Scope minimum rating
    public function scopeMinRating($query, $minRating)
    {
        return $query->where('rating', '>=', $minRating);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'specialization',
        'experience_years',
        'profile_image',
        'cover_image',
        'accept_online_payment',
        'is_verified',
        'rating',
        'total_students',
    ];

    protected $casts = [
        'accept_online_payment' => 'boolean',
        'is_verified' => 'boolean',
        'rating' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grades()
    {
        return $this->belongsToMany(Grade::class, 'teacher_grades');
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function activeGroups()
    {
        return $this->groups()->where('is_active', true);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->reviews()->where('is_approved', true);
    }

    // Calculate average rating
    public function updateRating()
    {
        $avgRating = $this->approvedReviews()->avg('rating');
        $this->update(['rating' => round($avgRating, 2)]);
    }

    // Get total enrolled students across all groups
    public function updateTotalStudents()
    {
        $total = $this->groups()->sum('current_students');
        $this->update(['total_students' => $total]);
    }

    // Scope for verified teachers
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    // Scope for teachers by specialization
    public function scopeBySpecialization($query, $specialization)
    {
        return $query->where('specialization', 'like', "%{$specialization}%");
    }

    // Get profile completion percentage
    public function getProfileCompletionAttribute()
    {
        $fields = ['bio', 'specialization', 'experience_years', 'profile_image'];
        $filled = 0;

        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $filled++;
            }
        }

        return ($filled / count($fields)) * 100;
    }
}

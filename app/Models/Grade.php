<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// ===================================
// Grade Model
// ===================================
class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'level',
        'order_index',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_grades');
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // Scope active grades
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope by level
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }
}

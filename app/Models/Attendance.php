<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'student_id',
        'session_date',
        'status',
        'notes',
        'marked_at',
    ];

    protected $casts = [
        'session_date' => 'date',
        'marked_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Mark as present
    public function markPresent()
    {
        $this->update([
            'status' => 'present',
            'marked_at' => now(),
        ]);
    }

    // Mark as absent
    public function markAbsent()
    {
        $this->update([
            'status' => 'absent',
            'marked_at' => now(),
        ]);
    }

    // Scope by date
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('session_date', $date);
    }

    // Scope by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope by date range
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('session_date', [$startDate, $endDate]);
    }
}

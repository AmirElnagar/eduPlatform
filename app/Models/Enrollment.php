<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// ===================================
// Enrollment Model
// ===================================
class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'group_id',
        'status',
        'payment_method',
        'payment_status',
        'amount_paid',
        'enrolled_at',
        'expires_at',
        'cancelled_at',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'enrolled_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Check if enrollment is active
    public function isActive()
    {
        return $this->status === 'active' &&
            (!$this->expires_at || $this->expires_at->isFuture());
    }

    // Check if enrollment has expired
    public function hasExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    // Activate enrollment
    public function activate()
    {
        $this->update([
            'status' => 'active',
            'enrolled_at' => now(),
            'expires_at' => now()->addDays($this->group->subscription_duration_days),
        ]);
    }

    // Cancel enrollment
    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $this->group->decrementStudents();
    }

    // Scope for active enrollments
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    // Scope for expired enrollments
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
            ->where('status', '!=', 'cancelled');
    }
}

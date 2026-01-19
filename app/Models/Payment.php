<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'amount',
        'payment_method',
        'payment_gateway',
        'transaction_id',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    // Mark as completed
    public function markCompleted($transactionId = null)
    {
        $this->update([
            'status' => 'completed',
            'transaction_id' => $transactionId,
            'paid_at' => now(),
        ]);

        // Update enrollment payment status
        $this->enrollment->update([
            'payment_status' => 'paid',
            'amount_paid' => $this->amount,
        ]);

        // Activate enrollment if it was pending
        if ($this->enrollment->status === 'pending') {
            $this->enrollment->activate();
            $this->enrollment->group->incrementStudents();
        }
    }

    // Mark as failed
    public function markFailed()
    {
        $this->update(['status' => 'failed']);
    }

    // Mark as refunded
    public function markRefunded()
    {
        $this->update(['status' => 'refunded']);

        $this->enrollment->update([
            'payment_status' => 'refunded',
        ]);
    }

    // Scope by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope completed payments
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Scope pending payments
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}

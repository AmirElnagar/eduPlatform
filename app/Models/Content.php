<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'title',
        'description',
        'type',
        'file_path',
        'file_size',
        'duration_minutes',
        'thumbnail',
        'order_index',
        'is_free',
        'is_published',
        'views_count',
        'published_at',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function accessControl()
    {
        return $this->hasOne(ContentAccess::class);
    }

    // Check if student can access this content
    public function canBeAccessedBy($studentId)
    {
        // Free content is accessible by everyone
        if ($this->is_free) return true;

        // Check if student is enrolled in the group
        $enrollment = Enrollment::where('student_id', $studentId)
            ->where('group_id', $this->group_id)
            ->active()
            ->first();

        if (!$enrollment) return false;

        // Check access control rules
        if ($this->accessControl) {
            return $this->accessControl->checkAccess($studentId);
        }

        return true;
    }

    // Increment views
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    // Publish content
    public function publish()
    {
        $this->update([
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    // Unpublish content
    public function unpublish()
    {
        $this->update(['is_published' => false]);
    }

    // Scope for published content
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Scope by type
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Get file size in MB
    public function getFileSizeMbAttribute()
    {
        return $this->file_size ? round($this->file_size / 1024 / 1024, 2) : 0;
    }
}

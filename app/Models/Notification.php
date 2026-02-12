<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasUuids;

    protected $table = 'notifications';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'user_id',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data'    => 'array',
        'read_at'=> 'datetime',
    ];

    /**
     * Polymorphic relation (notifiable)
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * المستخدم المستهدف بالإشعار
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace Aura\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemUpdateRead extends Model
{
    protected $table = 'aura_system_update_reads';

    protected $fillable = [
        'system_update_id',
        'user_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Get the system update that was read.
     */
    public function systemUpdate(): BelongsTo
    {
        return $this->belongsTo(SystemUpdate::class);
    }

    /**
     * Get the user who read the update.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('aura.resources.user', 'App\\Models\\User'));
    }
}

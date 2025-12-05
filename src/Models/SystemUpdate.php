<?php

namespace Aura\Notifications\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemUpdate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'aura_system_updates';

    protected $fillable = [
        'team_id',
        'user_id',
        'version',
        'title',
        'slug',
        'body',
        'category',
        'tags',
        'published_at',
        'created_by',
        'is_pinned',
        'is_published',
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
        'is_pinned' => 'boolean',
        'is_published' => 'boolean',
    ];

    /**
     * Scope to get only published updates.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at');
    }

    /**
     * Scope to get pinned updates.
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get all reads for this update.
     */
    public function reads(): HasMany
    {
        return $this->hasMany(SystemUpdateRead::class);
    }

    /**
     * Check if a specific user has read this update.
     */
    public function isReadBy(int $userId): bool
    {
        return $this->reads()
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Get the user who created this update.
     */
    public function creator()
    {
        return $this->belongsTo(config('aura.resources.user', 'App\\Models\\User'), 'created_by');
    }

    /**
     * Get the team that owns this update.
     */
    public function team()
    {
        return $this->belongsTo(config('aura.resources.team', 'Aura\\Base\\Resources\\Team'), 'team_id');
    }

    /**
     * Get the user who owns this update.
     */
    public function user()
    {
        return $this->belongsTo(config('aura.resources.user', 'App\\Models\\User'), 'user_id');
    }

    /**
     * Scope to filter by team.
     */
    public function scopeForTeam($query, ?int $teamId = null)
    {
        if ($teamId) {
            return $query->where(function ($q) use ($teamId) {
                $q->where('team_id', $teamId)
                    ->orWhereNull('team_id'); // Global updates (no team)
            });
        }

        return $query->whereNull('team_id'); // Only global updates
    }

    /**
     * Scope to get global updates (no team restriction).
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('team_id');
    }
}

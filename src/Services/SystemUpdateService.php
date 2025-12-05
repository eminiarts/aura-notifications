<?php

namespace Aura\Notifications\Services;

use Aura\Notifications\Models\SystemUpdate;
use Aura\Notifications\Models\SystemUpdateRead;
use Illuminate\Support\Collection;

class SystemUpdateService
{
    /**
     * Create a new system update.
     */
    public function createUpdate(array $data): SystemUpdate
    {
        return SystemUpdate::create($data);
    }

    /**
     * Publish an update (makes it visible to all users).
     */
    public function publish(SystemUpdate $update): void
    {
        $update->update([
            'is_published' => true,
            'published_at' => $update->published_at ?? now(),
        ]);
    }

    /**
     * Unpublish an update.
     */
    public function unpublish(SystemUpdate $update): void
    {
        $update->update([
            'is_published' => false,
        ]);
    }

    /**
     * Mark update as read for a user.
     */
    public function markAsRead(SystemUpdate $update, int $userId): void
    {
        $update->reads()->firstOrCreate(
            ['user_id' => $userId],
            ['read_at' => now()]
        );
    }

    /**
     * Mark update as unread for a user.
     */
    public function markAsUnread(SystemUpdate $update, int $userId): void
    {
        $update->reads()
            ->where('user_id', $userId)
            ->delete();
    }

    /**
     * Get unread updates for a user.
     */
    public function getUnreadUpdates(int $userId, ?int $teamId = null): Collection
    {
        return SystemUpdate::published()
            ->forTeam($teamId)
            ->whereDoesntHave('reads', fn ($q) => $q->where('user_id', $userId))
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->get();
    }

    /**
     * Get all published updates with read status for a user.
     */
    public function getAllUpdates(int $userId, ?int $teamId = null): Collection
    {
        return SystemUpdate::published()
            ->forTeam($teamId)
            ->with(['reads' => fn ($q) => $q->where('user_id', $userId)])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->get()
            ->map(function ($update) use ($userId) {
                $update->is_read = $update->reads->where('user_id', $userId)->isNotEmpty();

                return $update;
            });
    }

    /**
     * Get pinned updates.
     */
    public function getPinnedUpdates(?int $teamId = null): Collection
    {
        return SystemUpdate::published()
            ->forTeam($teamId)
            ->pinned()
            ->orderBy('published_at', 'desc')
            ->get();
    }

    /**
     * Get updates by category.
     */
    public function getUpdatesByCategory(string $category, ?int $userId = null, ?int $teamId = null): Collection
    {
        $query = SystemUpdate::published()
            ->forTeam($teamId)
            ->category($category)
            ->orderBy('published_at', 'desc');

        if ($userId) {
            return $query->with(['reads' => fn ($q) => $q->where('user_id', $userId)])
                ->get()
                ->map(function ($update) use ($userId) {
                    $update->is_read = $update->reads->where('user_id', $userId)->isNotEmpty();

                    return $update;
                });
        }

        return $query->get();
    }

    /**
     * Get unread count for a user.
     */
    public function getUnreadCount(int $userId, ?int $teamId = null): int
    {
        return SystemUpdate::published()
            ->forTeam($teamId)
            ->whereDoesntHave('reads', fn ($q) => $q->where('user_id', $userId))
            ->count();
    }
}

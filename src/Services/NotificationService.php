<?php

namespace Aura\Notifications\Services;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Send notification to a notifiable entity.
     */
    public function notify($notifiable, $notification): void
    {
        $notifiable->notify($notification);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(DatabaseNotification $notification): void
    {
        $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead($user): void
    {
        $user->unreadNotifications->markAsRead();
    }

    /**
     * Archive (soft delete) a notification.
     */
    public function archive(DatabaseNotification $notification): void
    {
        $notification->delete();
    }

    /**
     * Get unread notification count.
     */
    public function getUnreadCount($user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Get user notifications with optional filters.
     */
    public function getUserNotifications($user, string $filter = 'unread'): Collection
    {
        return match ($filter) {
            'unread' => $user->unreadNotifications,
            'read' => $user->readNotifications,
            'archived' => $user->notifications()->onlyTrashed()->get(),
            default => $user->notifications,
        };
    }

    /**
     * Filter notifications by category from data JSON.
     */
    public function filterByCategory(Collection $notifications, string $category): Collection
    {
        return $notifications->filter(function ($notification) use ($category) {
            return isset($notification->data['category']) && $notification->data['category'] === $category;
        });
    }

    /**
     * Filter notifications by level from data JSON.
     */
    public function filterByLevel(Collection $notifications, string $level): Collection
    {
        return $notifications->filter(function ($notification) use ($level) {
            return isset($notification->data['level']) && $notification->data['level'] === $level;
        });
    }

    /**
     * Get notifications paginated.
     */
    public function getPaginated($user, int $perPage = 50, string $filter = 'all'): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = match ($filter) {
            'unread' => $user->unreadNotifications(),
            'read' => $user->readNotifications(),
            default => $user->notifications(),
        };

        return $query->latest()->paginate($perPage);
    }

    /**
     * Group notifications by date.
     */
    public function groupByDate(Collection $notifications): Collection
    {
        return $notifications->groupBy(function ($notification) {
            return $notification->created_at->format('Y-m-d');
        });
    }

    /**
     * Get notification statistics for a user.
     */
    public function getStatistics($user): array
    {
        $all = $user->notifications;
        $unread = $user->unreadNotifications;

        return [
            'total' => $all->count(),
            'unread' => $unread->count(),
            'read' => $all->count() - $unread->count(),
            'by_category' => $all->groupBy(fn ($n) => $n->data['category'] ?? 'uncategorized')
                ->map->count()
                ->toArray(),
            'by_level' => $all->groupBy(fn ($n) => $n->data['level'] ?? 'info')
                ->map->count()
                ->toArray(),
        ];
    }
}

<?php

namespace Aura\Notifications\Livewire;

use Aura\Notifications\Services\NotificationService;
use Aura\Notifications\Services\SystemUpdateService;
use Livewire\Component;

class NotificationsBell extends Component
{
    protected $listeners = [
        'notificationReceived' => '$refresh',
    ];

    public function getUnreadCountProperty(): int
    {
        if (! auth()->check()) {
            return 0;
        }

        $notificationCount = app(NotificationService::class)->getUnreadCount(auth()->user());
        $updateCount = app(SystemUpdateService::class)->getUnreadCount(auth()->id());

        return $notificationCount + $updateCount;
    }

    public function getMaxBadgeProperty(): int
    {
        return config('aura-notifications.display.max_unread_badge', 99);
    }

    public function getBadgeTextProperty(): string
    {
        $count = $this->unreadCount;
        $max = $this->maxBadge;

        if ($count === 0) {
            return '';
        }

        return $count > $max ? "{$max}+" : (string) $count;
    }

    public function openPanel(): void
    {
        $this->dispatch('openSlideOver', 'notifications');
    }

    public function togglePanel(): void
    {
        $this->dispatch('toggleSlideOver', 'notifications');
    }

    public function render()
    {
        return view('aura-notifications::livewire.notifications-bell');
    }
}

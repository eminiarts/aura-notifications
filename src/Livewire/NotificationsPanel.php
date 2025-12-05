<?php

namespace Aura\Notifications\Livewire;

use Aura\Notifications\Services\NotificationService;
use Aura\Notifications\Services\SystemUpdateService;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationsPanel extends Component
{
    use WithPagination;

    public bool $open = false;

    public string $activeTab = 'notifications'; // 'notifications' or 'updates'

    public string $filter = 'unread'; // 'unread', 'read', 'all'

    protected $listeners = [
        'openSlideOver' => 'handleSlideOver',
        'notificationReceived' => 'refresh',
    ];

    public function handleSlideOver($key)
    {
        if ($key === 'notifications') {
            $this->open = true;
        }
    }

    public function activate($params = [])
    {
        $this->open = true;
    }

    public function close()
    {
        $this->open = false;
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->filter = 'unread';
        $this->resetPage();
    }

    public function switchFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function getNotificationsProperty()
    {
        if (! auth()->check()) {
            return collect();
        }

        $service = app(NotificationService::class);

        return $service->getUserNotifications(auth()->user(), $this->filter);
    }

    public function getUnreadCountProperty(): int
    {
        if (! auth()->check()) {
            return 0;
        }

        return app(NotificationService::class)->getUnreadCount(auth()->user());
    }

    public function getSystemUpdatesProperty()
    {
        if (! auth()->check()) {
            return collect();
        }

        $service = app(SystemUpdateService::class);

        if ($this->filter === 'unread') {
            return $service->getUnreadUpdates(auth()->id());
        }

        return $service->getAllUpdates(auth()->id());
    }

    public function getUnreadUpdatesCountProperty(): int
    {
        if (! auth()->check()) {
            return 0;
        }

        return app(SystemUpdateService::class)->getUnreadCount(auth()->id());
    }

    public function markNotificationAsRead(string $notificationId): void
    {
        if (! auth()->check()) {
            return;
        }

        $notification = auth()->user()->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllNotificationsAsRead(): void
    {
        if (! auth()->check()) {
            return;
        }

        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
    }

    public function markSystemUpdateAsRead(int $updateId): void
    {
        if (! auth()->check()) {
            return;
        }

        $service = app(SystemUpdateService::class);
        $update = \Aura\Notifications\Models\SystemUpdate::find($updateId);

        if ($update) {
            $service->markAsRead($update, auth()->id());
        }
    }

    public function refresh(): void
    {
        // This will trigger a re-render
    }

    public function render()
    {
        return view('aura-notifications::livewire.notifications-panel');
    }
}

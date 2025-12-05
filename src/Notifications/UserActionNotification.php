<?php

namespace Aura\Notifications\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UserActionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $title,
        public ?string $body = null,
        public string $level = 'info', // info|success|warning|error
        public ?string $url = null,
        public ?string $actionText = null,
        public string $category = 'user', // user|system|maintenance|release
        public string $icon = 'bell',
        public array $meta = [],
        public array $tags = [],
    ) {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Can add 'broadcast' for real-time
    }

    /**
     * Get the array representation of the notification (stored in database).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'level' => $this->level,
            'url' => $this->url,
            'action_text' => $this->actionText,
            'category' => $this->category,
            'icon' => $this->icon,
            'tags' => $this->tags,
            'meta' => $this->meta,
            'created_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'level' => $this->level,
            'url' => $this->url,
        ];
    }
}

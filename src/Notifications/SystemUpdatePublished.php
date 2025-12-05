<?php

namespace Aura\Notifications\Notifications;

use Aura\Notifications\Models\SystemUpdate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SystemUpdatePublished extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public SystemUpdate $update
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
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->update->title,
            'body' => str($this->update->body)->limit(140)->toString(),
            'level' => 'info',
            'url' => route('aura.updates.show', $this->update->slug),
            'action_text' => 'View Update',
            'category' => 'system',
            'icon' => 'sparkles',
            'tags' => $this->update->tags ?? [],
            'meta' => [
                'version' => $this->update->version,
                'update_id' => $this->update->id,
                'update_category' => $this->update->category,
            ],
            'created_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): array
    {
        return [
            'title' => $this->update->title,
            'body' => str($this->update->body)->limit(100)->toString(),
            'url' => route('aura.updates.show', $this->update->slug),
        ];
    }
}

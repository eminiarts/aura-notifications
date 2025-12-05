<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification System Enabled
    |--------------------------------------------------------------------------
    |
    | Enable or disable the notification system globally.
    |
    */
    'enabled' => env('AURA_NOTIFICATIONS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Real-time Method
    |--------------------------------------------------------------------------
    |
    | Choose between 'polling' or 'broadcasting' for real-time notifications.
    | Polling is simpler and doesn't require WebSockets.
    |
    */
    'realtime_method' => env('AURA_NOTIFICATIONS_REALTIME', 'polling'), // 'polling' or 'broadcasting'

    /*
    |--------------------------------------------------------------------------
    | Polling Interval
    |--------------------------------------------------------------------------
    |
    | Interval in milliseconds for polling new notifications (default: 30 seconds).
    |
    */
    'polling_interval' => env('AURA_NOTIFICATIONS_POLLING_INTERVAL', 30000),

    /*
    |--------------------------------------------------------------------------
    | Display Settings
    |--------------------------------------------------------------------------
    */
    'display' => [
        'max_unread_badge' => 99, // Show "99+" for counts above this
        'notifications_per_page' => 50,
        'show_avatars' => true,
        'show_timestamps' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-archive Settings
    |--------------------------------------------------------------------------
    |
    | Automatically archive old read notifications after specified days.
    |
    */
    'auto_archive' => [
        'enabled' => env('AURA_NOTIFICATIONS_AUTO_ARCHIVE', true),
        'after_days' => env('AURA_NOTIFICATIONS_AUTO_ARCHIVE_DAYS', 90),
    ],

    /*
    |--------------------------------------------------------------------------
    | System Updates
    |--------------------------------------------------------------------------
    */
    'system_updates' => [
        'enabled' => true,
        'auto_show_new' => true, // Auto-open panel for new updates
        'highlight_breaking_changes' => true,
        'categories' => [
            'release' => 'Release',
            'maintenance' => 'Maintenance',
            'announcement' => 'Announcement',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Categories
    |--------------------------------------------------------------------------
    |
    | Define available notification categories and their display settings.
    |
    */
    'categories' => [
        'user' => [
            'label' => 'User Actions',
            'icon' => 'user',
            'color' => 'blue',
        ],
        'system' => [
            'label' => 'System',
            'icon' => 'cog',
            'color' => 'gray',
        ],
        'maintenance' => [
            'label' => 'Maintenance',
            'icon' => 'wrench',
            'color' => 'yellow',
        ],
        'release' => [
            'label' => 'Releases',
            'icon' => 'sparkles',
            'color' => 'purple',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Levels
    |--------------------------------------------------------------------------
    |
    | Define notification levels with their display properties.
    |
    */
    'levels' => [
        'info' => [
            'label' => 'Info',
            'color' => 'blue',
            'icon' => 'information-circle',
        ],
        'success' => [
            'label' => 'Success',
            'color' => 'green',
            'icon' => 'check-circle',
        ],
        'warning' => [
            'label' => 'Warning',
            'color' => 'yellow',
            'icon' => 'exclamation-triangle',
        ],
        'error' => [
            'label' => 'Error',
            'color' => 'red',
            'icon' => 'x-circle',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Types (Extendable)
    |--------------------------------------------------------------------------
    |
    | Register custom notification classes here.
    |
    */
    'notification_types' => [
        'user_action' => \Aura\Notifications\Notifications\UserActionNotification::class,
        'system_update' => \Aura\Notifications\Notifications\SystemUpdatePublished::class,
    ],
];

# Aura CMS Notifications Plugin

A comprehensive notification system for Aura CMS that provides user notifications and system updates with a beautiful slide-over panel interface.

## Features

- **User Notifications** - Laravel notification system integration with database channel
- **System Updates** - Announce new features, security patches, and maintenance schedules
- **Slide-over Panel** - Beautiful right-side panel with blurred backdrop
- **Bell Icon** - Sidebar notification bell with unread count badge (toggle open/close)
- **Tabs** - Switch between "All" notifications and "Updates"
- **Mark as Read** - Mark individual or all notifications as read
- **Team Support** - Filter updates by team or show global updates
- **Dark Mode** - Full dark mode support
- **Responsive** - Works on desktop and mobile devices

## Requirements

- PHP 8.1+
- Laravel 10+
- Aura CMS
- Livewire 3.x

## Installation

### 1. Add the plugin to your project

Copy the plugin folder to your project's `plugins/aura/notifications` directory.

### 2. Register the namespace in composer.json

Add the following to your `composer.json` autoload section:

```json
{
    "autoload": {
        "psr-4": {
            "Aura\\Notifications\\": "plugins/aura/notifications/src"
        }
    }
}
```

Then run:

```bash
composer dump-autoload
```

### 3. Register the Service Provider

Add the service provider to your `config/app.php` providers array:

```php
'providers' => [
    // ...
    Aura\Notifications\NotificationsServiceProvider::class,
],
```

Or if using Laravel's auto-discovery, add to your `composer.json`:

```json
{
    "extra": {
        "laravel": {
            "providers": [
                "Aura\\Notifications\\NotificationsServiceProvider"
            ]
        }
    }
}
```

### 4. Run migrations

```bash
php artisan migrate
```

This creates the following tables:
- `aura_system_updates` - Stores system update announcements
- `aura_system_update_reads` - Tracks which users have read which updates

### 5. Publish configuration (optional)

```bash
php artisan vendor:publish --tag=aura-notifications-config
```

### 6. Add the Bell Icon to Your Sidebar

Publish Aura's navigation view if you haven't already:

```bash
php artisan vendor:publish --tag=aura-views
```

Then add the bell component to `resources/views/vendor/aura/livewire/navigation.blade.php` in the sidebar footer section, next to the user profile:

```blade
{{-- Notifications Bell --}}
<div class="ml-2 hide-collapsed">
    <livewire:aura-notifications::bell />
</div>
```

### 7. Enable notifications in Aura config

Make sure notifications are enabled in your `config/aura.php`:

```php
'features' => [
    'notifications' => true,
    // ...
],
```

## Configuration

The configuration file `config/aura-notifications.php` allows you to customize:

```php
return [
    // Enable/disable notification features
    'enabled' => true,

    // System updates settings
    'system_updates' => [
        'enabled' => true,
        'categories' => [
            'feature' => 'New Feature',
            'improvement' => 'Improvement',
            'security' => 'Security Update',
            'maintenance' => 'Maintenance',
            'announcement' => 'Announcement',
        ],
    ],

    // Display settings
    'display' => [
        'max_unread_badge' => 99,
        'per_page' => 10,
    ],

    // Notification types with icons and colors
    'types' => [
        'success' => [
            'icon' => 'check-circle',
            'color' => 'green',
        ],
        'info' => [
            'icon' => 'information-circle',
            'color' => 'blue',
        ],
        'warning' => [
            'icon' => 'exclamation-triangle',
            'color' => 'yellow',
        ],
        'error' => [
            'icon' => 'x-circle',
            'color' => 'red',
        ],
    ],
];
```

## Usage

### Sending User Notifications

Use Laravel's built-in notification system. Create a notification class:

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    public function __construct(
        public string $title,
        public string $message,
        public ?string $actionUrl = null,
        public ?string $actionText = null,
        public string $level = 'info'
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
            'action_text' => $this->actionText,
            'level' => $this->level, // success, info, warning, error
        ];
    }
}
```

Send the notification:

```php
use App\Notifications\NewCommentNotification;

$user->notify(new NewCommentNotification(
    title: 'New comment on your post',
    message: 'John left a comment: "Great article!"',
    actionUrl: route('posts.show', $post),
    actionText: 'View Comment',
    level: 'info'
));
```

### Using the Built-in UserActionNotification

The plugin includes a ready-to-use notification class:

```php
use Aura\Notifications\Notifications\UserActionNotification;

// Send to a single user
$user->notify(new UserActionNotification(
    title: 'Export Complete',
    body: 'Your data export has finished processing',
    level: 'success',
    url: route('exports.show', $export),
    actionText: 'View Export',
    category: 'user'
));

// Send to multiple users
use Illuminate\Support\Facades\Notification;

Notification::send($users, new UserActionNotification(
    title: 'System Maintenance',
    body: 'Scheduled maintenance on Sunday at 2 AM',
    level: 'warning'
));
```

### Creating System Updates

Use the `SystemUpdateService` to create announcements:

```php
use Aura\Notifications\Services\SystemUpdateService;

$service = app(SystemUpdateService::class);

$update = $service->createUpdate([
    'title' => 'New Dark Mode Feature',
    'slug' => 'dark-mode-v2',
    'content' => 'We have released an improved dark mode with better contrast and more color options.',
    'excerpt' => 'Dark mode is now available!',
    'category' => 'feature',
    'is_published' => true,
    'published_at' => now(),
    'is_pinned' => false,
    'team_id' => null, // null for global, or specific team ID
]);
```

### Using the SystemUpdate Model Directly

```php
use Aura\Notifications\Models\SystemUpdate;

// Create an update
$update = SystemUpdate::create([
    'title' => 'Security Patch Released',
    'slug' => 'security-patch-2024-01',
    'content' => 'Important security fixes have been applied.',
    'excerpt' => 'Critical security update',
    'category' => 'security',
    'is_published' => true,
    'published_at' => now(),
]);

// Get all published updates
$updates = SystemUpdate::published()->latest()->get();

// Get updates by category
$features = SystemUpdate::published()->byCategory('feature')->get();

// Check if user has read an update
$hasRead = $update->isReadBy($userId);

// Get unread updates for a user
$unread = SystemUpdate::published()
    ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $userId))
    ->get();
```

### Notification Service Methods

```php
use Aura\Notifications\Services\NotificationService;

$service = app(NotificationService::class);

// Get user's notifications
$notifications = $service->getUserNotifications($user, 'unread'); // 'unread', 'read', 'all'

// Get unread count
$count = $service->getUnreadCount($user);

// Mark notification as read
$service->markAsRead($notification);

// Mark all as read
$service->markAllAsRead($user);
```

### System Update Service Methods

```php
use Aura\Notifications\Services\SystemUpdateService;

$service = app(SystemUpdateService::class);

// Get all updates for a user
$updates = $service->getAllUpdates($userId);

// Get unread updates
$unread = $service->getUnreadUpdates($userId);

// Get unread count
$count = $service->getUnreadCount($userId);

// Mark update as read
$service->markAsRead($update, $userId);

// Create a new update
$update = $service->createUpdate([...]);

// Publish an update
$service->publishUpdate($update);
```

## Routes

The plugin registers the following routes:

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/admin/notifications` | `aura.notifications.index` | Notifications list page |
| POST | `/admin/notifications/{id}/read` | `aura.notifications.read` | Mark notification as read |
| POST | `/admin/notifications/mark-all-read` | `aura.notifications.mark-all-read` | Mark all as read |
| GET | `/admin/updates` | `aura.updates.index` | System updates list page |
| GET | `/admin/updates/{slug}` | `aura.updates.show` | Single update page |

## Livewire Components

### Bell Component

```blade
<livewire:aura-notifications::bell />
```

Displays the notification bell icon with unread count badge. Clicking toggles the notification panel open/closed.

**Features:**
- White icon on dark sidebar, gray on hover
- Red badge with unread count
- Click to toggle panel (open → close → open)

### Panel Component

```blade
<livewire:aura::notifications />
```

The slide-over panel component. This is automatically included by Aura CMS when `config('aura.features.notifications')` is enabled.

**Features:**
- Slides in from the right side
- Blurred backdrop (click to close)
- ~30% screen width on desktop
- Tabs for "All" and "Updates"
- Mark all as read functionality
- Close with X button or Escape key

## Customizing Views

Publish the views to customize them:

```bash
php artisan vendor:publish --tag=aura-notifications-views
```

Views will be published to `resources/views/vendor/aura-notifications/`.

### Key View Files

- `livewire/notifications-bell.blade.php` - Bell icon component
- `livewire/notifications-panel.blade.php` - Slide-over panel
- `livewire/partials/compact-notifications-list.blade.php` - Notification items
- `livewire/partials/compact-updates-list.blade.php` - System update items
- `notifications/index.blade.php` - Full notifications page
- `updates/index.blade.php` - Full updates page
- `updates/show.blade.php` - Single update page

## Seeding Test Data

### System Updates Seeder

```php
<?php

namespace Database\Seeders;

use Aura\Notifications\Models\SystemUpdate;
use Illuminate\Database\Seeder;

class SystemUpdateSeeder extends Seeder
{
    public function run(): void
    {
        $updates = [
            [
                'title' => 'Welcome to Aura CMS',
                'slug' => 'welcome-to-aura',
                'content' => 'Thank you for choosing Aura CMS...',
                'excerpt' => 'Get started with Aura CMS',
                'category' => 'announcement',
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Security Update v1.0.1',
                'slug' => 'security-update-v101',
                'content' => 'Important security patches...',
                'excerpt' => 'Critical security fixes',
                'category' => 'security',
                'is_published' => true,
                'published_at' => now()->subDays(2),
            ],
        ];

        foreach ($updates as $update) {
            SystemUpdate::updateOrCreate(
                ['slug' => $update['slug']],
                $update
            );
        }
    }
}
```

### Notifications Seeder

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Aura\Notifications\Notifications\UserActionNotification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $notifications = [
            ['title' => 'Welcome!', 'body' => 'Welcome to the platform.', 'level' => 'success'],
            ['title' => 'New feature', 'body' => 'Check out dark mode.', 'level' => 'info'],
            ['title' => 'Password expiring', 'body' => 'Update your password.', 'level' => 'warning'],
        ];

        foreach ($notifications as $data) {
            $user->notify(new UserActionNotification(
                title: $data['title'],
                body: $data['body'],
                level: $data['level']
            ));
        }
    }
}
```

## Events

The plugin uses the following Alpine.js events:

| Event | Description |
|-------|-------------|
| `open-notifications-panel` | Opens the notification panel |
| `toggle-notifications-panel` | Toggles the notification panel open/closed |

And Livewire events:

| Event | Description |
|-------|-------------|
| `notificationReceived` | Refreshes the bell component |
| `openSlideOver` | Opens the slide-over panel |
| `toggleSlideOver` | Toggles the slide-over panel |

## Testing

Run the plugin tests:

```bash
php artisan test --filter=Notification
```

For browser testing with Playwright:

```bash
npx playwright test tests/playwright/notifications.spec.ts
```

## Permissions

The plugin registers the following Gates:

| Gate | Description |
|------|-------------|
| `view-notifications` | Can view notifications (all authenticated users) |
| `view-system-updates` | Can view system updates (all authenticated users) |
| `manage-system-updates` | Can create/edit/delete system updates (super admins) |

## Admin Panel

System updates can be managed through the Aura CMS admin panel:

- Navigate to **System > System-Updates** in the sidebar
- Create, edit, publish, and delete system updates
- Pin important updates to show them first
- Filter by category

## Troubleshooting

### Panel not opening

1. Make sure `config('aura.features.notifications')` is set to `true`
2. Clear view cache: `php artisan view:clear`
3. Check browser console for JavaScript errors

### Two panels appearing

Ensure the panel component is only included once. Remove any duplicate `<livewire:aura-notifications::panel />` from your views. The default Aura layout includes it automatically.

### Bell icon not visible

The bell icon is white by default for dark sidebars. If your sidebar is light-colored, publish and customize the bell component view.

### Migrations not running

If migrations don't run automatically:

```bash
php artisan vendor:publish --tag=aura-notifications-migrations
php artisan migrate
```

### Notifications not showing

1. Check that notifications exist in the `notifications` table
2. Verify the user ID matches the `notifiable_id` in the database
3. Ensure notifications use the `database` channel

## Changelog

### v1.0.0
- Initial release
- User notifications with Laravel's database channel
- System updates with read tracking
- Slide-over panel with blurred backdrop
- Bell icon with toggle functionality
- Tabs for All/Updates
- Mark all as read
- Team support
- Dark mode support

## Credits

- [Bajram Emini](https://github.com/eminiarts)
- Built for [Aura CMS](https://github.com/eminiarts/aura-cms)
- Uses [Laravel Notifications](https://laravel.com/docs/notifications)
- UI powered by [Tailwind CSS](https://tailwindcss.com) and [Alpine.js](https://alpinejs.dev)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

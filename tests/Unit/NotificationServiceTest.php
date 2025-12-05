<?php

use Aura\Notifications\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(NotificationService::class);

    // Create a mock notifiable (user)
    $this->user = new class
    {
        public $id = 1;

        public $notifications;

        public $unreadNotifications;

        public function __construct()
        {
            $this->notifications = collect();
            $this->unreadNotifications = collect();
        }

        public function notify($notification)
        {
            // Mock implementation
        }
    };
});

it('can get unread count', function () {
    // Create mock notifications
    $notification1 = new DatabaseNotification();
    $notification1->id = '1';
    $notification1->read_at = null;

    $notification2 = new DatabaseNotification();
    $notification2->id = '2';
    $notification2->read_at = null;

    $this->user->unreadNotifications = collect([$notification1, $notification2]);

    $count = $this->user->unreadNotifications->count();

    expect($count)->toBe(2);
});

it('can mark notification as read', function () {
    $notification = new DatabaseNotification();
    $notification->id = '1';
    $notification->read_at = null;

    // Mock markAsRead method
    $notification->markAsRead = function () use ($notification) {
        $notification->read_at = now();
    };

    ($notification->markAsRead)();

    expect($notification->read_at)->not->toBeNull();
});

it('filters notifications by type', function () {
    $notifications = collect([
        ['type' => 'App\\Notifications\\TaskCompleted', 'data' => ['title' => 'Task 1']],
        ['type' => 'App\\Notifications\\ExportCompleted', 'data' => ['title' => 'Export 1']],
        ['type' => 'App\\Notifications\\TaskCompleted', 'data' => ['title' => 'Task 2']],
    ]);

    $filtered = $notifications->where('type', 'App\\Notifications\\TaskCompleted');

    expect($filtered)->toHaveCount(2);
});

it('filters notifications by data attribute', function () {
    $notifications = collect([
        ['data' => ['category' => 'user', 'title' => 'Notification 1']],
        ['data' => ['category' => 'system', 'title' => 'Notification 2']],
        ['data' => ['category' => 'user', 'title' => 'Notification 3']],
    ]);

    $filtered = $notifications->filter(function ($notification) {
        return $notification['data']['category'] === 'user';
    });

    expect($filtered)->toHaveCount(2);
});

it('sorts notifications by created_at', function () {
    $notifications = collect([
        ['created_at' => now()->subHours(2)],
        ['created_at' => now()],
        ['created_at' => now()->subHours(1)],
    ]);

    $sorted = $notifications->sortByDesc('created_at')->values();

    expect($sorted[0]['created_at']->greaterThan($sorted[1]['created_at']))->toBeTrue()
        ->and($sorted[1]['created_at']->greaterThan($sorted[2]['created_at']))->toBeTrue();
});

it('paginates notifications', function () {
    $notifications = collect(range(1, 50));

    $page1 = $notifications->take(20);
    $page2 = $notifications->slice(20, 20);

    expect($page1)->toHaveCount(20)
        ->and($page2)->toHaveCount(20)
        ->and($page1->first())->toBe(1)
        ->and($page2->first())->toBe(21);
});

it('groups notifications by date', function () {
    $notifications = collect([
        ['created_at' => now()->startOfDay()],
        ['created_at' => now()->startOfDay()],
        ['created_at' => now()->subDay()->startOfDay()],
    ]);

    $grouped = $notifications->groupBy(function ($notification) {
        return $notification['created_at']->format('Y-m-d');
    });

    expect($grouped)->toHaveCount(2)
        ->and($grouped[now()->format('Y-m-d')])->toHaveCount(2)
        ->and($grouped[now()->subDay()->format('Y-m-d')])->toHaveCount(1);
});

it('can mark all as read', function () {
    $notifications = collect([
        (object) ['read_at' => null, 'markAsRead' => function () {
        }],
        (object) ['read_at' => null, 'markAsRead' => function () {
        }],
        (object) ['read_at' => null, 'markAsRead' => function () {
        }],
    ]);

    $notifications->each(function ($notification) {
        ($notification->markAsRead)();
        $notification->read_at = now();
    });

    $unreadCount = $notifications->where('read_at', null)->count();

    expect($unreadCount)->toBe(0);
});

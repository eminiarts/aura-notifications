<?php

use Aura\Notifications\Models\SystemUpdate;
use Aura\Notifications\Models\SystemUpdateRead;
use Aura\Notifications\Services\SystemUpdateService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(SystemUpdateService::class);
});

it('can create a system update', function () {
    $data = [
        'version' => '1.0.0',
        'title' => 'New Feature Release',
        'slug' => 'new-feature-release',
        'body' => '# What\'s New\n\nExciting new features!',
        'category' => 'release',
        'tags' => ['feature', 'update'],
    ];

    $update = $this->service->createUpdate($data);

    expect($update)->toBeInstanceOf(SystemUpdate::class)
        ->and($update->version)->toBe('1.0.0')
        ->and($update->title)->toBe('New Feature Release')
        ->and($update->is_published)->toBeFalse();
});

it('can publish an update', function () {
    $update = SystemUpdate::create([
        'title' => 'Test Update',
        'slug' => 'test',
        'body' => 'Body',
        'category' => 'release',
        'is_published' => false,
    ]);

    $this->service->publish($update);

    expect($update->fresh()->is_published)->toBeTrue()
        ->and($update->fresh()->published_at)->not->toBeNull();
});

it('can unpublish an update', function () {
    $update = SystemUpdate::create([
        'title' => 'Test Update',
        'slug' => 'test',
        'body' => 'Body',
        'category' => 'release',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->service->unpublish($update);

    expect($update->fresh()->is_published)->toBeFalse();
});

it('can mark update as read for user', function () {
    $update = SystemUpdate::create([
        'title' => 'Test',
        'slug' => 'test',
        'body' => 'Body',
        'category' => 'release',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $userId = 1;

    $this->service->markAsRead($update, $userId);

    expect($update->isReadBy($userId))->toBeTrue();
});

it('does not create duplicate read records', function () {
    $update = SystemUpdate::create([
        'title' => 'Test',
        'slug' => 'test',
        'body' => 'Body',
        'category' => 'release',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $userId = 1;

    $this->service->markAsRead($update, $userId);
    $this->service->markAsRead($update, $userId);

    $readCount = SystemUpdateRead::where('system_update_id', $update->id)
        ->where('user_id', $userId)
        ->count();

    expect($readCount)->toBe(1);
});

it('can get unread updates for user', function () {
    $read = SystemUpdate::create([
        'title' => 'Read Update',
        'slug' => 'read',
        'body' => 'Body',
        'category' => 'release',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $unread = SystemUpdate::create([
        'title' => 'Unread Update',
        'slug' => 'unread',
        'body' => 'Body',
        'category' => 'release',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $userId = 1;

    SystemUpdateRead::create([
        'system_update_id' => $read->id,
        'user_id' => $userId,
        'read_at' => now(),
    ]);

    $unreadUpdates = $this->service->getUnreadUpdates($userId);

    expect($unreadUpdates)->toHaveCount(1)
        ->and($unreadUpdates->first()->id)->toBe($unread->id);
});

it('can get all updates with read status', function () {
    $update1 = SystemUpdate::create([
        'title' => 'Update 1',
        'slug' => 'update-1',
        'body' => 'Body',
        'category' => 'release',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $update2 = SystemUpdate::create([
        'title' => 'Update 2',
        'slug' => 'update-2',
        'body' => 'Body',
        'category' => 'release',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $userId = 1;

    SystemUpdateRead::create([
        'system_update_id' => $update1->id,
        'user_id' => $userId,
        'read_at' => now(),
    ]);

    $updates = $this->service->getAllUpdates($userId);

    expect($updates)->toHaveCount(2)
        ->and($updates[0]->is_read)->toBeTrue()
        ->and($updates[1]->is_read)->toBeFalse();
});

it('only returns published updates', function () {
    SystemUpdate::create([
        'title' => 'Published',
        'slug' => 'published',
        'body' => 'Body',
        'category' => 'release',
        'is_published' => true,
        'published_at' => now(),
    ]);

    SystemUpdate::create([
        'title' => 'Draft',
        'slug' => 'draft',
        'body' => 'Body',
        'category' => 'release',
        'is_published' => false,
    ]);

    $updates = $this->service->getUnreadUpdates(1);

    expect($updates)->toHaveCount(1)
        ->and($updates->first()->title)->toBe('Published');
});

it('orders updates by published date descending', function () {
    $older = SystemUpdate::create([
        'title' => 'Older',
        'slug' => 'older',
        'body' => 'Body',
        'category' => 'release',
        'is_published' => true,
        'published_at' => now()->subDays(2),
    ]);

    $newer = SystemUpdate::create([
        'title' => 'Newer',
        'slug' => 'newer',
        'body' => 'Body',
        'category' => 'release',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $updates = $this->service->getAllUpdates(1);

    expect($updates->first()->id)->toBe($newer->id)
        ->and($updates->last()->id)->toBe($older->id);
});

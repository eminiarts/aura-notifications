<?php

use Aura\Notifications\Models\SystemUpdate;
use Aura\Notifications\Models\SystemUpdateRead;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a system update', function () {
    $update = SystemUpdate::create([
        'version' => '1.0.0',
        'title' => 'Initial Release',
        'slug' => 'initial-release',
        'body' => '# What\'s New\n\nInitial release of the system.',
        'category' => 'release',
        'published_at' => now(),
    ]);

    expect($update)->toBeInstanceOf(SystemUpdate::class)
        ->and($update->version)->toBe('1.0.0')
        ->and($update->title)->toBe('Initial Release')
        ->and($update->slug)->toBe('initial-release')
        ->and($update->category)->toBe('release');
});

it('can filter published updates', function () {
    SystemUpdate::create([
        'title' => 'Published Update',
        'slug' => 'published',
        'body' => 'Body',
        'category' => 'release',
        'is_published' => true,
        'published_at' => now(),
    ]);

    SystemUpdate::create([
        'title' => 'Draft Update',
        'slug' => 'draft',
        'body' => 'Body',
        'category' => 'release',
        'is_published' => false,
    ]);

    $published = SystemUpdate::published()->get();

    expect($published)->toHaveCount(1)
        ->and($published->first()->title)->toBe('Published Update');
});

it('can track read status for users', function () {
    $update = SystemUpdate::create([
        'title' => 'Test Update',
        'slug' => 'test-update',
        'body' => 'Body',
        'category' => 'release',
        'is_published' => true,
        'published_at' => now(),
    ]);

    // Mock user ID (we'll use integer since we're testing in isolation)
    $userId = 1;

    $read = SystemUpdateRead::create([
        'system_update_id' => $update->id,
        'user_id' => $userId,
        'read_at' => now(),
    ]);

    expect($read)->toBeInstanceOf(SystemUpdateRead::class)
        ->and($read->system_update_id)->toBe($update->id)
        ->and($read->user_id)->toBe($userId);
});

it('has relationship with reads', function () {
    $update = SystemUpdate::create([
        'title' => 'Test Update',
        'slug' => 'test',
        'body' => 'Body',
        'category' => 'release',
        'is_published' => true,
        'published_at' => now(),
    ]);

    SystemUpdateRead::create([
        'system_update_id' => $update->id,
        'user_id' => 1,
        'read_at' => now(),
    ]);

    SystemUpdateRead::create([
        'system_update_id' => $update->id,
        'user_id' => 2,
        'read_at' => now(),
    ]);

    expect($update->reads)->toHaveCount(2);
});

it('can be filtered by category', function () {
    SystemUpdate::create([
        'title' => 'Release',
        'slug' => 'release',
        'body' => 'Body',
        'category' => 'release',
        'is_published' => true,
        'published_at' => now(),
    ]);

    SystemUpdate::create([
        'title' => 'Maintenance',
        'slug' => 'maintenance',
        'body' => 'Body',
        'category' => 'maintenance',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $releases = SystemUpdate::where('category', 'release')->get();

    expect($releases)->toHaveCount(1)
        ->and($releases->first()->category)->toBe('release');
});

it('casts dates properly', function () {
    $update = SystemUpdate::create([
        'title' => 'Test',
        'slug' => 'test',
        'body' => 'Body',
        'category' => 'release',
        'published_at' => '2025-01-01 12:00:00',
    ]);

    expect($update->published_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

it('casts tags as array', function () {
    $update = SystemUpdate::create([
        'title' => 'Test',
        'slug' => 'test',
        'body' => 'Body',
        'category' => 'release',
        'tags' => ['feature', 'bug-fix'],
        'published_at' => now(),
    ]);

    $update->refresh();

    expect($update->tags)->toBeArray()
        ->and($update->tags)->toContain('feature', 'bug-fix');
});

it('can determine if pinned', function () {
    $pinned = SystemUpdate::create([
        'title' => 'Pinned',
        'slug' => 'pinned',
        'body' => 'Body',
        'category' => 'announcement',
        'is_pinned' => true,
        'is_published' => true,
        'published_at' => now(),
    ]);

    $normal = SystemUpdate::create([
        'title' => 'Normal',
        'slug' => 'normal',
        'body' => 'Body',
        'category' => 'release',
        'is_pinned' => false,
        'is_published' => true,
        'published_at' => now(),
    ]);

    expect($pinned->is_pinned)->toBeTrue()
        ->and($normal->is_pinned)->toBeFalse();
});

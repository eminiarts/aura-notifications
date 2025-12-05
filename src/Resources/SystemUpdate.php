<?php

namespace Aura\Notifications\Resources;

use Aura\Base\Resource;

class SystemUpdate extends Resource
{
    public static $customTable = true;

    public static string $type = 'SystemUpdate';

    public static ?string $slug = 'system-update';

    protected static ?string $group = 'System';

    public static ?int $sort = 100;

    public static bool $globalSearch = true;

    protected $table = 'aura_system_updates';

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
        'is_pinned' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function getIcon()
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
</svg>';
    }

    public function title()
    {
        return $this->title ?? 'Untitled Update';
    }

    public static function getFields()
    {
        return [
            [
                'name' => 'Main Content',
                'type' => 'Aura\\Base\\Fields\\Panel',
                'slug' => 'main-panel',
                'style' => [
                    'width' => '75',
                ],
            ],
            [
                'name' => 'Title',
                'type' => 'Aura\\Base\\Fields\\Text',
                'validation' => 'required|max:255',
                'on_index' => true,
                'on_view' => true,
                'on_forms' => true,
                'slug' => 'title',
                'searchable' => true,
                'style' => [
                    'width' => '100',
                ],
            ],
            [
                'name' => 'Slug',
                'type' => 'Aura\\Base\\Fields\\Text',
                'validation' => 'required|max:255|unique:aura_system_updates,slug',
                'on_index' => false,
                'on_view' => true,
                'on_forms' => true,
                'slug' => 'slug',
                'style' => [
                    'width' => '100',
                ],
                'instructions' => 'URL-friendly identifier for this update',
            ],
            [
                'name' => 'Body',
                'type' => 'Aura\\Base\\Fields\\Textarea',
                'validation' => 'required',
                'on_index' => false,
                'on_view' => true,
                'on_forms' => true,
                'slug' => 'body',
                'style' => [
                    'width' => '100',
                ],
                'rows' => 10,
                'instructions' => 'Markdown supported',
            ],
            [
                'name' => 'Sidebar',
                'type' => 'Aura\\Base\\Fields\\Panel',
                'slug' => 'sidebar-panel',
                'style' => [
                    'width' => '25',
                ],
            ],
            [
                'name' => 'Version',
                'type' => 'Aura\\Base\\Fields\\Text',
                'validation' => 'nullable|max:255',
                'on_index' => true,
                'on_view' => true,
                'on_forms' => true,
                'slug' => 'version',
                'style' => [
                    'width' => '100',
                ],
                'instructions' => 'e.g., 1.0.0',
            ],
            [
                'name' => 'Category',
                'type' => 'Aura\\Base\\Fields\\Select',
                'validation' => 'required',
                'on_index' => true,
                'on_view' => true,
                'on_forms' => true,
                'slug' => 'category',
                'style' => [
                    'width' => '100',
                ],
                'options' => [
                    ['key' => 'release', 'value' => 'Release'],
                    ['key' => 'maintenance', 'value' => 'Maintenance'],
                    ['key' => 'announcement', 'value' => 'Announcement'],
                ],
            ],
            [
                'name' => 'Tags',
                'type' => 'Aura\\Base\\Fields\\Tags',
                'validation' => 'nullable',
                'on_index' => false,
                'on_view' => true,
                'on_forms' => true,
                'slug' => 'tags',
                'style' => [
                    'width' => '100',
                ],
            ],
            [
                'name' => 'Published',
                'type' => 'Aura\\Base\\Fields\\Boolean',
                'validation' => 'nullable',
                'on_index' => true,
                'on_view' => true,
                'on_forms' => true,
                'slug' => 'is_published',
                'style' => [
                    'width' => '100',
                ],
            ],
            [
                'name' => 'Pinned',
                'type' => 'Aura\\Base\\Fields\\Boolean',
                'validation' => 'nullable',
                'on_index' => false,
                'on_view' => true,
                'on_forms' => true,
                'slug' => 'is_pinned',
                'style' => [
                    'width' => '100',
                ],
                'instructions' => 'Pin this update to the top',
            ],
            [
                'name' => 'Published At',
                'type' => 'Aura\\Base\\Fields\\DateTime',
                'validation' => 'nullable',
                'on_index' => true,
                'on_view' => true,
                'on_forms' => true,
                'slug' => 'published_at',
                'style' => [
                    'width' => '100',
                ],
            ],
        ];
    }

    public static function getWidgets(): array
    {
        return [
            [
                'name' => 'Total Updates',
                'slug' => 'total_updates',
                'type' => 'Aura\\Base\\Widgets\\ValueWidget',
                'method' => 'count',
                'cache' => 300,
                'style' => [
                    'width' => '33.33',
                ],
            ],
            [
                'name' => 'Published Updates',
                'slug' => 'published_updates',
                'type' => 'Aura\\Base\\Widgets\\ValueWidget',
                'method' => 'count',
                'filter' => ['is_published' => true],
                'cache' => 300,
                'style' => [
                    'width' => '33.33',
                ],
            ],
            [
                'name' => 'Pinned Updates',
                'slug' => 'pinned_updates',
                'type' => 'Aura\\Base\\Widgets\\ValueWidget',
                'method' => 'count',
                'filter' => ['is_pinned' => true],
                'cache' => 300,
                'style' => [
                    'width' => '33.33',
                ],
            ],
        ];
    }
}

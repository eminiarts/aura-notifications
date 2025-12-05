<div class="space-y-4">
    @forelse($this->systemUpdates as $update)
        @php
            $isRead = $update->is_read ?? false;
            $categoryColors = [
                'release' => 'purple',
                'maintenance' => 'yellow',
                'announcement' => 'blue',
            ];
            $color = $categoryColors[$update->category] ?? 'gray';
        @endphp
        <div
            wire:key="update-{{ $update->id }}"
            class="relative p-4 rounded-lg border transition-all {{ !$isRead ? 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-800' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700' }}"
        >
            {{-- Unread indicator --}}
            @if(!$isRead)
                <div class="absolute top-4 right-4 w-2 h-2 bg-purple-500 rounded-full"></div>
            @endif

            {{-- Header --}}
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-2">
                    @if($update->version)
                        <span class="px-2 py-0.5 text-xs font-semibold rounded bg-{{ $color }}-100 dark:bg-{{ $color }}-900 text-{{ $color }}-700 dark:text-{{ $color }}-300">
                            v{{ $update->version }}
                        </span>
                    @endif
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                        {{ ucfirst($update->category) }}
                    </span>
                    @if($update->is_pinned)
                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    @endif
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $update->published_at?->diffForHumans() ?? $update->created_at->diffForHumans() }}
                </span>
            </div>

            {{-- Title --}}
            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                {{ $update->title }}
            </h3>

            {{-- Body preview --}}
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
                {{ Str::limit(strip_tags($update->body), 200) }}
            </p>

            {{-- Tags --}}
            @if(!empty($update->tags) && is_array($update->tags))
                <div class="flex flex-wrap gap-1 mt-3">
                    @foreach($update->tags as $tag)
                        <span class="px-2 py-0.5 text-xs rounded bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex items-center mt-4 space-x-3">
                <a
                    href="{{ route('aura.updates.show', $update->slug) }}"
                    wire:click="markSystemUpdateAsRead({{ $update->id }})"
                    class="text-sm font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300"
                >
                    {{ __('Read more') }}
                </a>

                @if(!$isRead)
                    <button
                        wire:click="markSystemUpdateAsRead({{ $update->id }})"
                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                        {{ __('Mark as read') }}
                    </button>
                @endif
            </div>
        </div>
    @empty
        <div class="flex flex-col justify-center items-center py-12 text-center">
            <svg class="w-12 h-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            <p class="mt-4 text-sm font-medium text-gray-900 dark:text-white">
                {{ __('No updates') }}
            </p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if($filter === 'unread')
                    {{ __("You've read all the latest updates!") }}
                @else
                    {{ __('No system updates to display.') }}
                @endif
            </p>
        </div>
    @endforelse
</div>

<x-aura::layout.app>
    @section('title', __('System Updates'))

    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('System Updates') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Stay up to date with the latest changes and improvements.') }}
                </p>
            </div>
            @if($unreadCount > 0)
                <span class="px-3 py-1 text-sm font-medium text-white bg-primary-500 rounded-full">
                    {{ $unreadCount }} {{ __('unread') }}
                </span>
            @endif
        </div>

        {{-- Updates Timeline --}}
        <div class="space-y-6">
            @forelse($updates as $update)
                @php
                    $isRead = $update->is_read ?? false;
                    $categoryColors = [
                        'release' => 'purple',
                        'maintenance' => 'yellow',
                        'announcement' => 'blue',
                    ];
                    $color = $categoryColors[$update->category] ?? 'gray';
                @endphp

                <div class="relative pl-8 pb-6 {{ !$loop->last ? 'border-l-2 border-gray-200 dark:border-gray-700' : '' }}">
                    {{-- Timeline dot --}}
                    <div class="absolute left-0 top-0 w-4 h-4 -translate-x-1/2 rounded-full {{ !$isRead ? 'bg-primary-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg border {{ !$isRead ? 'border-primary-200 dark:border-primary-800' : 'border-gray-200 dark:border-gray-700' }} p-6 shadow-sm">
                        {{-- Header --}}
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-2">
                                @if($update->version)
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-{{ $color }}-100 dark:bg-{{ $color }}-900 text-{{ $color }}-700 dark:text-{{ $color }}-300">
                                        v{{ $update->version }}
                                    </span>
                                @endif
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                    {{ ucfirst($update->category) }}
                                </span>
                                @if($update->is_pinned)
                                    <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endif
                                @if(!$isRead)
                                    <span class="px-2 py-0.5 text-xs font-medium text-white bg-primary-500 rounded-full">
                                        {{ __('New') }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $update->published_at?->format('M d, Y') ?? $update->created_at->format('M d, Y') }}
                            </span>
                        </div>

                        {{-- Title --}}
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            <a href="{{ route('aura.updates.show', $update->slug) }}" class="hover:text-primary-600 dark:hover:text-primary-400">
                                {{ $update->title }}
                            </a>
                        </h2>

                        {{-- Preview --}}
                        <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">
                            {{ Str::limit(strip_tags($update->body), 250) }}
                        </p>

                        {{-- Tags --}}
                        @if(!empty($update->tags) && is_array($update->tags))
                            <div class="flex flex-wrap gap-1 mb-4">
                                @foreach($update->tags as $tag)
                                    <span class="px-2 py-0.5 text-xs rounded bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('aura.updates.show', $update->slug) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300">
                                {{ __('Read more') }} →
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('No updates yet') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Check back later for system updates.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</x-aura::layout.app>

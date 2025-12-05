<div class="divide-y divide-gray-100 dark:divide-gray-800">
    @forelse($this->systemUpdates as $update)
        @php
            $isRead = $update->is_read ?? false;
            $categoryColors = [
                'release' => ['bg' => 'bg-purple-100 dark:bg-purple-900/50', 'text' => 'text-purple-600 dark:text-purple-400', 'badge' => 'bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300'],
                'maintenance' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900/50', 'text' => 'text-yellow-600 dark:text-yellow-400', 'badge' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300'],
                'announcement' => ['bg' => 'bg-blue-100 dark:bg-blue-900/50', 'text' => 'text-blue-600 dark:text-blue-400', 'badge' => 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300'],
            ];
            $colors = $categoryColors[$update->category] ?? ['bg' => 'bg-gray-100 dark:bg-gray-800', 'text' => 'text-gray-600 dark:text-gray-400', 'badge' => 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'];
        @endphp
        <div
            wire:key="update-{{ $update->id }}"
            class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors {{ !$isRead ? 'bg-purple-50/50 dark:bg-purple-900/10' : '' }}"
        >
            <div class="flex items-start space-x-3">
                {{-- Icon --}}
                <div class="flex-shrink-0">
                    <span class="flex justify-center items-center w-10 h-10 rounded-full {{ $colors['bg'] }} {{ $colors['text'] }}">
                        @if($update->category === 'release')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                        @elseif($update->category === 'maintenance')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                        @endif
                    </span>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-2">
                            @if($update->version)
                                <span class="px-1.5 py-0.5 text-xs font-semibold rounded {{ $colors['badge'] }}">
                                    v{{ $update->version }}
                                </span>
                            @endif
                            @if($update->is_pinned)
                                <svg class="w-3.5 h-3.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endif
                        </div>
                        @if(!$isRead)
                            <span class="flex-shrink-0 w-2 h-2 ml-2 mt-1.5 bg-purple-500 rounded-full"></span>
                        @endif
                    </div>

                    <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white {{ $isRead ? 'text-gray-600 dark:text-gray-400' : '' }}">
                        {{ $update->title }}
                    </p>

                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                        {{ Str::limit(strip_tags($update->body), 100) }}
                    </p>

                    <div class="flex items-center mt-2 space-x-3">
                        <span class="text-xs text-gray-400 dark:text-gray-500">
                            {{ $update->published_at?->diffForHumans() ?? $update->created_at->diffForHumans() }}
                        </span>

                        <a
                            href="{{ route('aura.updates.show', $update->slug) }}"
                            wire:click="markSystemUpdateAsRead({{ $update->id }})"
                            class="text-xs font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300"
                        >
                            {{ __('Read more') }}
                        </a>

                        @if(!$isRead)
                            <button
                                wire:click="markSystemUpdateAsRead({{ $update->id }})"
                                class="text-xs text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300"
                            >
                                {{ __('Mark read') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="flex flex-col justify-center items-center py-16 text-center px-6">
            <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-900 dark:text-white">
                {{ __('No updates') }}
            </p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __("You've read all the latest updates!") }}
            </p>
        </div>
    @endforelse
</div>

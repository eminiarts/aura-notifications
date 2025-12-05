<x-aura::layout.app>
    @section('title', $update->title)

    <div class="max-w-4xl mx-auto">
        {{-- Back link --}}
        <div class="mb-6">
            <a href="{{ route('aura.updates.index') }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('Back to Updates') }}
            </a>
        </div>

        {{-- Article --}}
        <article class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            {{-- Header --}}
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-2 mb-4">
                    @php
                        $categoryColors = [
                            'release' => 'purple',
                            'maintenance' => 'yellow',
                            'announcement' => 'blue',
                        ];
                        $color = $categoryColors[$update->category] ?? 'gray';
                    @endphp

                    @if($update->version)
                        <span class="px-3 py-1 text-sm font-semibold rounded bg-{{ $color }}-100 dark:bg-{{ $color }}-900 text-{{ $color }}-700 dark:text-{{ $color }}-300">
                            v{{ $update->version }}
                        </span>
                    @endif
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                        {{ ucfirst($update->category) }}
                    </span>
                    @if($update->is_pinned)
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-700 dark:text-yellow-300 bg-yellow-100 dark:bg-yellow-900 rounded">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            {{ __('Pinned') }}
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $update->title }}
                </h1>

                <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <time datetime="{{ $update->published_at?->toIso8601String() }}">
                        {{ __('Published') }} {{ $update->published_at?->format('F j, Y') ?? $update->created_at->format('F j, Y') }}
                    </time>
                    @if($update->creator)
                        <span class="mx-2">•</span>
                        <span>{{ __('By') }} {{ $update->creator->name }}</span>
                    @endif
                </div>

                {{-- Tags --}}
                @if(!empty($update->tags) && is_array($update->tags))
                    <div class="flex flex-wrap gap-2 mt-4">
                        @foreach($update->tags as $tag)
                            <span class="px-2 py-1 text-xs rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                #{{ $tag }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="p-6">
                <div class="prose dark:prose-invert max-w-none">
                    {!! Str::markdown($update->body) !!}
                </div>
            </div>
        </article>

        {{-- Navigation --}}
        <div class="mt-8 flex justify-between">
            @php
                $previousUpdate = \Aura\Notifications\Models\SystemUpdate::published()
                    ->where('published_at', '<', $update->published_at)
                    ->orderBy('published_at', 'desc')
                    ->first();

                $nextUpdate = \Aura\Notifications\Models\SystemUpdate::published()
                    ->where('published_at', '>', $update->published_at)
                    ->orderBy('published_at', 'asc')
                    ->first();
            @endphp

            <div>
                @if($previousUpdate)
                    <a href="{{ route('aura.updates.show', $previousUpdate->slug) }}" class="group flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                        <svg class="w-4 h-4 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span>
                            <span class="block text-xs uppercase tracking-wider">{{ __('Previous') }}</span>
                            <span class="block font-medium text-gray-900 dark:text-white">{{ Str::limit($previousUpdate->title, 30) }}</span>
                        </span>
                    </a>
                @endif
            </div>

            <div class="text-right">
                @if($nextUpdate)
                    <a href="{{ route('aura.updates.show', $nextUpdate->slug) }}" class="group inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                        <span>
                            <span class="block text-xs uppercase tracking-wider">{{ __('Next') }}</span>
                            <span class="block font-medium text-gray-900 dark:text-white">{{ Str::limit($nextUpdate->title, 30) }}</span>
                        </span>
                        <svg class="w-4 h-4 ml-2 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-aura::layout.app>

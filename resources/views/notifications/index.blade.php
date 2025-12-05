<x-aura::layout.app>
    @section('title', __('Notifications'))

    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('Notifications') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('View and manage your notifications.') }}
                </p>
            </div>
            <div class="flex items-center space-x-4">
                @if($unreadCount > 0)
                    <span class="px-3 py-1 text-sm font-medium text-white bg-primary-500 rounded-full">
                        {{ $unreadCount }} {{ __('unread') }}
                    </span>
                    <form action="{{ route('aura.notifications.mark-all-read') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300">
                            {{ __('Mark all as read') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Filters --}}
        <div class="flex space-x-2 mb-6">
            <a href="{{ route('aura.notifications.index', ['filter' => 'all']) }}"
               class="px-4 py-2 text-sm font-medium rounded-lg {{ $filter === 'all' ? 'bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                {{ __('All') }}
            </a>
            <a href="{{ route('aura.notifications.index', ['filter' => 'unread']) }}"
               class="px-4 py-2 text-sm font-medium rounded-lg {{ $filter === 'unread' ? 'bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                {{ __('Unread') }}
            </a>
            <a href="{{ route('aura.notifications.index', ['filter' => 'read']) }}"
               class="px-4 py-2 text-sm font-medium rounded-lg {{ $filter === 'read' ? 'bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                {{ __('Read') }}
            </a>
        </div>

        {{-- Statistics --}}
        @if($filter === 'all')
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statistics['total'] }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $statistics['unread'] }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Unread') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $statistics['read'] }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Read') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ count($statistics['by_category'] ?? []) }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Categories') }}</div>
                </div>
            </div>
        @endif

        {{-- Notifications List --}}
        <div class="space-y-4">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data;
                    $level = $data['level'] ?? 'info';
                @endphp
                <div class="relative bg-white dark:bg-gray-800 rounded-lg border {{ is_null($notification->read_at) ? 'border-primary-200 dark:border-primary-800' : 'border-gray-200 dark:border-gray-700' }} p-4 shadow-sm">
                    {{-- Unread indicator --}}
                    @if(is_null($notification->read_at))
                        <div class="absolute top-4 right-4 w-2 h-2 bg-primary-500 rounded-full"></div>
                    @endif

                    <div class="flex items-start space-x-4">
                        {{-- Icon --}}
                        <div class="flex-shrink-0">
                            <span class="flex justify-center items-center w-10 h-10 rounded-full
                                @switch($level)
                                    @case('success') bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400 @break
                                    @case('warning') bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400 @break
                                    @case('error') bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 @break
                                    @default bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400
                                @endswitch
                            ">
                                @switch($level)
                                    @case('success')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        @break
                                    @case('warning')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        @break
                                    @case('error')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        @break
                                    @default
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                @endswitch
                            </span>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $data['title'] ?? 'Notification' }}
                                </p>
                                <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap ml-2">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>

                            @if(!empty($data['body']))
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $data['body'] }}
                                </p>
                            @endif

                            {{-- Actions --}}
                            <div class="flex items-center mt-3 space-x-4">
                                @if(!empty($data['url']))
                                    <a href="{{ $data['url'] }}" class="text-sm font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300">
                                        {{ $data['action_text'] ?? __('View') }}
                                    </a>
                                @endif

                                @if(is_null($notification->read_at))
                                    <form action="{{ route('aura.notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                            {{ __('Mark as read') }}
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('aura.notifications.destroy', $notification->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                        {{ __('Delete') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('No notifications') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if($filter === 'unread')
                            {{ __("You're all caught up!") }}
                        @else
                            {{ __('No notifications to display.') }}
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->appends(['filter' => $filter])->links() }}
            </div>
        @endif
    </div>
</x-aura::layout.app>

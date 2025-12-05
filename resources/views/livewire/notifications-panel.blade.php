{{-- Slide-over panel from right side --}}
<div
    x-data="{ open: @entangle('open') }"
    @keydown.escape.window="open = false"
    @open-notifications-panel.window="open = true"
    @toggle-notifications-panel.window="open = !open"
>
    {{-- Blurred Backdrop --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-gray-900/20 backdrop-blur-sm"
        @click="open = false"
    ></div>

    {{-- Slide-over Panel (max-w-sm = 384px, roughly 30% on 1280px screen) --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-50 w-96 max-w-[30vw] min-w-[320px]"
        @click.stop
    >
        <div class="flex flex-col h-full bg-white dark:bg-gray-900 shadow-2xl border-l border-gray-200 dark:border-gray-700 overflow-hidden">
            {{-- Header --}}
            <div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center justify-center w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full">
                            <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <div class="flex items-center space-x-2">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ __('Notifications') }}
                            </h2>
                            @if($this->unreadCount + $this->unreadUpdatesCount > 0)
                                <span class="px-2 py-0.5 text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 rounded-full">
                                    {{ $this->unreadCount + $this->unreadUpdatesCount }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <button
                        @click="open = false"
                        class="p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Tabs --}}
                <div class="flex mt-4 space-x-1 bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
                    <button
                        wire:click="switchTab('notifications')"
                        class="flex-1 px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'notifications' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}"
                    >
                        {{ __('All') }}
                        @if($this->unreadCount > 0)
                            <span class="ml-1 text-xs {{ $activeTab === 'notifications' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400' }}">
                                {{ $this->unreadCount }}
                            </span>
                        @endif
                    </button>
                    <button
                        wire:click="switchTab('updates')"
                        class="flex-1 px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'updates' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}"
                    >
                        {{ __('Updates') }}
                        @if($this->unreadUpdatesCount > 0)
                            <span class="ml-1 text-xs {{ $activeTab === 'updates' ? 'text-purple-600 dark:text-purple-400' : 'text-gray-400' }}">
                                {{ $this->unreadUpdatesCount }}
                            </span>
                        @endif
                    </button>
                </div>
            </div>

            {{-- Mark all as read --}}
            @if(($activeTab === 'notifications' && $this->unreadCount > 0) || ($activeTab === 'updates' && $this->unreadUpdatesCount > 0))
                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-800 flex justify-end">
                    <button
                        wire:click="{{ $activeTab === 'notifications' ? 'markAllNotificationsAsRead' : '' }}"
                        class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 font-medium transition-colors"
                    >
                        {{ __('Mark all as read') }}
                    </button>
                </div>
            @endif

            {{-- Content --}}
            <div class="flex-1 overflow-y-auto">
                @if($activeTab === 'notifications')
                    @include('aura-notifications::livewire.partials.compact-notifications-list')
                @else
                    @include('aura-notifications::livewire.partials.compact-updates-list')
                @endif
            </div>

            {{-- Footer --}}
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <a
                    href="{{ $activeTab === 'notifications' ? route('aura.notifications.index') : route('aura.updates.index') }}"
                    class="flex items-center justify-center w-full text-sm font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 transition-colors"
                >
                    {{ __('View all') }} {{ $activeTab === 'notifications' ? __('notifications') : __('updates') }}
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

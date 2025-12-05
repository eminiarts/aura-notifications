<div class="relative" x-data="{ hover: false }">
    <button
        wire:click="togglePanel"
        @click="$dispatch('toggle-notifications-panel')"
        @mouseenter="hover = true"
        @mouseleave="hover = false"
        class="relative p-2 transition-colors rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500"
        aria-label="{{ __('Notifications') }}"
    >
        {{-- Bell Icon --}}
        <svg
            class="w-5 h-5 transition-colors"
            :class="hover ? 'text-gray-600' : 'text-white/90'"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>

        {{-- Badge --}}
        @if($this->unreadCount > 0)
            <span class="absolute top-0 right-0 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-xs font-bold text-white transform translate-x-1/4 -translate-y-1/4 bg-red-500 rounded-full">
                {{ $this->badgeText }}
            </span>
        @endif
    </button>
</div>

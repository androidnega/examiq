@props([
    'theme' => 'staff',
    'variant' => 'default',
])
@php
    $ring = $theme === 'admin' ? 'ring-blue-200' : 'ring-teal-200';
    $avatarPath = public_path(config('examiq.default_avatar'));
    $avatarUrl = is_string($avatarPath) && $avatarPath !== '' && is_file($avatarPath)
        ? asset(config('examiq.default_avatar'))
        : null;
    $isTopbar = $variant === 'topbar';
@endphp
<div class="relative" x-data="examiqProfileMenu()" @keydown.escape.window="close()">
    <button
        type="button"
        class="{{ $isTopbar ? 'rounded-full border border-slate-200/80 bg-white p-0.5 shadow-sm transition-colors hover:border-slate-300 hover:bg-slate-50' : 'rounded-full border border-transparent p-0.5 transition-colors hover:bg-slate-100' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500/30"
        @click="toggle()"
        :aria-expanded="open.toString()"
        aria-haspopup="true"
        aria-label="{{ __('Open account menu') }}"
        title="{{ __('Account') }}"
    >
        @if ($avatarUrl)
            <img
                src="{{ $avatarUrl }}"
                alt=""
                width="40"
                height="40"
                class="h-10 w-10 shrink-0 rounded-full object-cover ring-2 {{ $ring }}"
                loading="lazy"
                decoding="async"
            />
        @else
            <span
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-xs font-bold ring-2 {{ $theme === 'admin' ? 'bg-blue-50 text-blue-800 ring-blue-200' : 'bg-teal-50 text-teal-900 ring-teal-200' }}"
                aria-hidden="true"
            >{{ $initials }}</span>
        @endif
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        x-cloak
        class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-xl border border-slate-200/80 bg-white py-1 shadow-lg shadow-slate-200/60"
        @click.outside="close()"
    >
        <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-3">
            @if ($avatarUrl)
                <img
                    src="{{ $avatarUrl }}"
                    alt=""
                    width="40"
                    height="40"
                    class="h-10 w-10 shrink-0 rounded-full object-cover ring-2 {{ $ring }}"
                    loading="lazy"
                    decoding="async"
                />
            @else
                <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-xs font-bold ring-2 {{ $theme === 'admin' ? 'bg-blue-50 text-blue-800 ring-blue-200' : 'bg-teal-50 text-teal-900 ring-teal-200' }}"
                >{{ $initials }}</span>
            @endif
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                @if (filled($user->phone))
                    <p class="truncate text-xs text-slate-500">{{ $user->phone }}</p>
                @endif
            </div>
        </div>
        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50"
            >
                <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                </svg>
                {{ __('Log out') }}
            </button>
        </form>
    </div>
</div>

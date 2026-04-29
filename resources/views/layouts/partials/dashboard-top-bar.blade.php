@php
    use App\Enums\UserRole;

    $searchQ = old('q', request('q', ''));
    $pageTitle = $pageTitle ?? config('app.name');
    $rk = $roleKey ?? (auth()->user()->role instanceof UserRole ? auth()->user()->role->value : (string) auth()->user()->role);
    $mark = $appInitial ?? mb_strtoupper(mb_substr((string) config('app.name'), 0, 1));
    $accent = $accentMark ?? 'bg-teal-700';
@endphp

<div
    class="flex w-full min-w-0 flex-col gap-3 border-b border-slate-200 bg-slate-50/80 px-4 py-3 md:flex-row md:flex-nowrap md:items-center md:gap-4 md:px-6 md:py-3"
>
    <div class="flex min-w-0 shrink-0 items-center gap-3 md:max-w-[14rem]">
        @if (empty($showSidebarToggle))
            <a
                href="{{ $brandHref ?? route('dashboard') }}"
                class="flex shrink-0 items-center gap-2 md:hidden"
                title="{{ config('app.name') }}"
            >
                <span
                    class="flex h-9 w-9 items-center justify-center rounded-xl {{ $accent }} text-sm font-bold text-white"
                >{{ $mark }}</span>
            </a>
        @endif
        @if (! empty($showSidebarToggle))
            <button
                type="button"
                class="hidden shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white p-2.5 text-slate-700 shadow-sm transition-colors hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900 md:inline-flex"
                @click="toggle()"
                :aria-expanded="(!collapsed).toString()"
                aria-controls="dashboard-sidebar"
                title="{{ __('Toggle sidebar') }}"
            >
                <span class="sr-only">{{ __('Toggle sidebar') }}</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        @endif
        <div class="min-w-0">
            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-600">{{ __('Overview') }}</p>
            <h1 class="truncate text-base font-bold leading-tight text-slate-900 md:text-[1.05rem]">{{ $pageTitle }}</h1>
        </div>
    </div>

    <form
        method="get"
        action="{{ route('dashboard.search') }}"
        class="flex h-10 min-w-0 flex-1 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 shadow-sm transition-shadow focus-within:border-teal-400/60 focus-within:shadow-md focus-within:ring-2 focus-within:ring-teal-500/15"
        role="search"
    >
        <span class="shrink-0 text-slate-500" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
        </span>
        <label for="dashboard-global-search" class="sr-only">{{ __('Search') }}</label>
        <input
            id="dashboard-global-search"
            type="search"
            name="q"
            value="{{ $searchQ }}"
            autocomplete="off"
            placeholder="{{ __('Search submissions, courses…') }}"
            class="min-h-0 min-w-0 flex-1 border-0 bg-transparent py-0 text-sm font-medium leading-none text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:outline-none focus:ring-0"
        />
        <button
            type="submit"
            class="shrink-0 rounded-lg bg-teal-800 px-3 py-1.5 text-xs font-bold text-white transition-colors hover:bg-teal-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-600 focus-visible:ring-offset-2"
        >
            {{ __('Search') }}
        </button>
    </form>

    <div
        class="flex shrink-0 items-center gap-0.5 border-t border-slate-200 pt-3 md:border-l md:border-t-0 md:pl-4 md:pt-0"
    >
        <div class="-mx-0.5 flex max-w-[min(100%,28rem)] items-center gap-0.5 overflow-x-auto px-0.5 md:max-w-none md:overflow-visible">
            @include('layouts.partials.dashboard-quick-action-icons', ['roleKey' => $rk])
        </div>
        <div class="flex shrink-0 items-center border-l border-slate-200 pl-2 md:pl-3">
            @include('layouts.partials.dashboard-profile-dropdown', ['theme' => $profileTheme, 'variant' => 'topbar'])
        </div>
    </div>
</div>

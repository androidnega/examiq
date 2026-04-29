@php
    $searchQ = old('q', request('q', ''));
    $withProfile = $withProfile ?? false;
    $rk = $roleKey ?? null;
@endphp
<div
    class="flex min-w-0 flex-1 flex-col gap-3 sm:flex-row sm:flex-nowrap sm:items-center sm:gap-3 md:gap-4"
>
    <form
        method="get"
        action="{{ route('dashboard.search') }}"
        class="flex h-10 min-w-0 flex-1 items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 transition-colors focus-within:border-teal-300 focus-within:bg-white focus-within:ring-2 focus-within:ring-teal-500/15"
        role="search"
    >
        <span class="shrink-0 text-slate-400" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
        </span>
        <label for="dashboard-global-search-boxed" class="sr-only">{{ __('Search') }}</label>
        <input
            id="dashboard-global-search-boxed"
            type="search"
            name="q"
            value="{{ $searchQ }}"
            autocomplete="off"
            placeholder="{{ __('Search submissions, courses…') }}"
            class="min-h-0 min-w-0 flex-1 border-0 bg-transparent py-0 text-sm leading-none text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-0"
        />
        <button
            type="submit"
            class="shrink-0 rounded-lg bg-teal-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-teal-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2"
        >
            {{ __('Search') }}
        </button>
    </form>

    <div class="flex shrink-0 items-center gap-0.5 sm:border-l sm:border-slate-200 sm:pl-3">
        @if ($rk !== null)
            @include('layouts.partials.dashboard-quick-action-icons', ['roleKey' => $rk])
        @endif
        @if ($withProfile)
            <div class="flex shrink-0 items-center border-l border-slate-200 pl-2 sm:pl-3">
                @include('layouts.partials.dashboard-profile-dropdown', ['theme' => $profileTheme ?? 'staff', 'variant' => 'topbar'])
            </div>
        @endif
    </div>
</div>

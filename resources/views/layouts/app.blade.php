@php
    use App\Enums\UserRole;
    use Illuminate\Support\Facades\View;

    $user = auth()->user();
    $role = $user->role instanceof UserRole ? $user->role : UserRole::tryFrom((string) $user->role);
    $roleKey = $role?->value ?? (string) $user->role;

    $uiShell = match ($role ?? null) {
        UserRole::ExamOfficer => 'boxed',
        default => 'sidebar',
    };

    $navTheme = ($role ?? null) === UserRole::Admin ? 'admin' : 'staff';
    $profileTheme = $navTheme;

    $brandHref = route('dashboard');

    $initials = collect(preg_split('/\s+/', trim((string) $user->name)))
        ->filter()
        ->take(2)
        ->map(fn (string $w) => mb_strtoupper(mb_substr($w, 0, 1)))
        ->join('');
    if ($initials === '') {
        $initials = '?';
    }

    $statCardVariant = $statCardVariant ?? match ($roleKey) {
        'admin' => 'admin',
        'exam_officer' => 'boxed',
        default => 'staff',
    };
    View::share('statCardVariant', $statCardVariant);

    $accentMark = $navTheme === 'admin' ? 'bg-blue-600' : 'bg-teal-600';
    $sidebarSurface =
        $navTheme === 'admin'
            ? 'border-slate-200/80 bg-slate-100/95'
            : 'border-slate-200/80 bg-slate-50';
    $appInitial = mb_strtoupper(mb_substr((string) config('app.name'), 0, 1));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? $header ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    @stack('head')
    @include('layouts.partials.tailwind-cdn')
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/js/app.js'])
    @else
        <script>
            window.examiqSidebar = window.examiqSidebar || function () {
                return {
                    collapsed: false,
                    init() {
                        try {
                            this.collapsed = localStorage.getItem('examiq_sidebar_collapsed_v2') === '1';
                        } catch (_) {
                            this.collapsed = false;
                        }
                    },
                    toggle() {
                        this.collapsed = !this.collapsed;
                        try {
                            localStorage.setItem('examiq_sidebar_collapsed_v2', this.collapsed ? '1' : '0');
                        } catch (_) {
                            // ignore storage errors
                        }
                    },
                };
            };
            window.examiqProfileMenu = window.examiqProfileMenu || function () {
                return {
                    open: false,
                    toggle() {
                        this.open = !this.open;
                    },
                    close() {
                        this.open = false;
                    },
                };
            };
        </script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endif
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    @if ($uiShell === 'boxed')
        <div class="min-h-screen bg-slate-100">
            <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white shadow-sm shadow-slate-200/30">
                <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-x-4 gap-y-3 px-4 py-3 sm:px-6 md:flex-nowrap md:gap-4">
                    <div class="flex min-w-0 shrink-0 items-center gap-3">
                        <a href="{{ $brandHref }}" class="flex min-w-0 items-center gap-2.5">
                            <span
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $accentMark }} text-sm font-bold text-white"
                            >{{ $appInitial }}</span>
                            <span class="truncate text-lg font-bold tracking-tight text-slate-900">{{ config('app.name') }}</span>
                        </a>
                        <span class="hidden h-4 w-px shrink-0 bg-slate-200 sm:block" aria-hidden="true"></span>
                        <span class="hidden max-w-[14rem] truncate text-sm font-semibold text-slate-600 sm:inline">
                            {{ $header ?? config('app.name') }}
                        </span>
                    </div>
                    @include('layouts.partials.dashboard-header-toolbar', [
                        'profileTheme' => $profileTheme,
                        'roleKey' => $roleKey,
                        'withProfile' => true,
                    ])
                </div>
            </header>

            <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
                @include('layouts.partials.monitoring-banner')
                @if (session('status'))
                    <div class="mb-4 rounded-xl border border-emerald-100 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-900">
                        {{ session('status') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    @else
        <div class="flex h-screen min-h-0 overflow-hidden" x-data="examiqSidebar()">
            <aside
                id="dashboard-sidebar"
                class="{{ $sidebarSurface }} hidden h-full min-h-0 shrink-0 flex-col border-r transition-[width] duration-200 ease-out md:flex md:w-64"
                :class="collapsed ? 'md:!w-[4.5rem]' : ''"
                aria-label="{{ __('Sidebar') }}"
            >
                <div
                    class="flex h-14 shrink-0 items-center border-b border-slate-200/60 px-3 md:px-4"
                    :class="collapsed ? 'md:justify-center md:px-2' : ''"
                >
                    <a
                        href="{{ route('dashboard') }}"
                        class="flex min-w-0 items-center gap-2.5"
                        :class="collapsed ? 'md:justify-center' : ''"
                    >
                        <span
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $accentMark }} text-sm font-bold text-white"
                        >{{ $appInitial }}</span>
                        <span
                            class="truncate text-lg font-bold tracking-tight text-slate-900 max-md:hidden"
                            :class="collapsed ? 'hidden' : 'md:inline md:max-w-[10rem]'"
                        >{{ config('app.name') }}</span>
                    </a>
                </div>
                <div class="min-h-0 flex-1 overflow-y-auto overflow-x-hidden">
                    @include('layouts.partials.dashboard-sidebar-nav', ['navTheme' => $navTheme])
                </div>
            </aside>

            <div class="flex min-h-0 min-w-0 flex-1 flex-col overflow-hidden">
                <header class="z-40 shrink-0 bg-white shadow-sm shadow-slate-200/40">
                    @include('layouts.partials.dashboard-top-bar', [
                        'pageTitle' => $header ?? config('app.name'),
                        'roleKey' => $roleKey,
                        'profileTheme' => $profileTheme,
                        'showSidebarToggle' => true,
                        'appInitial' => $appInitial,
                        'accentMark' => $accentMark,
                        'brandHref' => $brandHref,
                    ])
                </header>

                <div class="shrink-0 border-b border-slate-200 bg-slate-50 md:hidden">
                    @include('layouts.partials.dashboard-sidebar-nav', ['navTheme' => $navTheme])
                </div>

                <main class="min-h-0 flex-1 overflow-y-auto overflow-x-hidden bg-slate-50/50 p-4 md:p-6">
                    @include('layouts.partials.monitoring-banner')
                    @if (session('status'))
                        <div class="mb-4 rounded-xl border border-emerald-100 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-900">
                            {{ session('status') }}
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
    @endif
</body>
</html>

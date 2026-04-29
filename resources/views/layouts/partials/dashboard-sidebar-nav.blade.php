@php
    use App\Enums\UserRole;

    $role = auth()->user()->role;
    $roleEnum = $role instanceof UserRole ? $role : UserRole::tryFrom((string) $role);
    $roleKey = $roleEnum?->value ?? (string) $role;

    $sections = config('dashboard-nav.'.$roleKey, []);
    $navTheme = $navTheme ?? 'staff';
    $activeStaff = 'bg-teal-100 text-teal-900';
    $activeAdmin = 'bg-blue-600 text-white shadow-sm';
    $inactive = $navTheme === 'admin'
        ? 'text-slate-700 hover:bg-slate-100 hover:text-slate-900'
        : 'text-slate-800 hover:bg-slate-100 hover:text-slate-950';
@endphp
<nav class="flex flex-col gap-1 px-2 pb-4 pt-3" aria-label="{{ __('Main navigation') }}">
    <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-700 md:hidden">
        {{ __('Main menu') }}
    </p>
    <p
        class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-700 max-md:hidden"
        :class="collapsed ? 'hidden' : 'block'"
    >
        {{ __('Main menu') }}
    </p>

    @foreach ($sections as $section)
        <div class="mt-1 first:mt-0">
            <p class="mb-1.5 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-700 md:hidden">
                {{ __($section['group']) }}
            </p>
            <p
                class="mb-1.5 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-700 max-md:hidden"
                :class="collapsed ? 'hidden' : 'block'"
            >
                {{ __($section['group']) }}
            </p>

            <ul class="space-y-0.5">
                @foreach ($section['items'] ?? [] as $item)
                    @php
                        $isDisabled = ! empty($item['disabled']);
                        if ($isDisabled) {
                            $active = false;
                        } else {
                            $active = request()->routeIs($item['pattern']);
                            if (! empty($item['pattern_unless'])) {
                                $active = $active && ! request()->routeIs($item['pattern_unless']);
                            }
                        }
                        $linkActive = $active ? ($navTheme === 'admin' ? $activeAdmin : $activeStaff) : $inactive;
                        $disabledClasses = 'cursor-not-allowed text-slate-400 hover:bg-transparent hover:text-slate-400';
                        $rowClass = 'flex items-center rounded-xl py-2.5 text-sm font-semibold transition-colors duration-150 px-3 max-md:gap-3 max-md:justify-start';
                    @endphp
                    <li>
                        @if ($isDisabled)
                            <span
                                class="{{ $disabledClasses }} {{ $rowClass }}"
                                :class="collapsed ? 'md:justify-center md:gap-0 md:px-2' : 'md:gap-3'"
                                title="{{ __('Coming soon') }}"
                            >
                                <x-dashboard-icon :name="$item['icon']" class="h-5 w-5 shrink-0 text-slate-400" />
                                <span class="min-w-0 flex-1 truncate md:hidden">{{ __($item['label']) }}</span>
                                <span
                                    class="min-w-0 flex-1 truncate max-md:hidden"
                                    :class="collapsed ? 'hidden' : 'block'"
                                >{{ __($item['label']) }}</span>
                            </span>
                        @else
                            <a
                                href="{{ route($item['route']) }}"
                                class="{{ $linkActive }} {{ $rowClass }}"
                                :class="collapsed ? 'md:justify-center md:gap-0 md:px-2' : 'md:gap-3'"
                                title="{{ __($item['label']) }}"
                                @if ($active) aria-current="page" @endif
                            >
                                <x-dashboard-icon
                                    :name="$item['icon']"
                                    class="h-5 w-5 shrink-0 {{ $active ? ($navTheme === 'admin' ? 'text-white' : 'text-teal-800') : 'text-slate-500' }}"
                                />
                                <span class="min-w-0 flex-1 truncate md:hidden">{{ __($item['label']) }}</span>
                                <span
                                    class="min-w-0 flex-1 truncate max-md:hidden"
                                    :class="collapsed ? 'hidden' : 'block'"
                                >{{ __($item['label']) }}</span>
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</nav>

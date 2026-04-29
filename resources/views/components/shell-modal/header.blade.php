@props([
    'title' => '',
    'subtitle' => null,
    'showLogo' => true,
])

<header class="flex shrink-0 items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-6 sm:py-5">
    <div class="flex min-w-0 flex-1 items-start gap-3 sm:gap-4">
        @if ($showLogo)
            <img
                src="{{ asset('images/brand/ttu-logo.png') }}"
                alt=""
                class="mt-0.5 hidden h-9 w-auto shrink-0 object-contain sm:block sm:h-10"
                width="256"
                height="256"
                loading="lazy"
                decoding="async"
            />
        @endif
        <div class="min-w-0 text-left">
            <h2 class="text-[15px] font-semibold leading-snug tracking-tight text-slate-900 sm:text-base">
                {{ $title }}
            </h2>
            @if ($subtitle)
                <p class="mt-0.5 text-sm font-normal leading-relaxed text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>
    </div>

    <div class="flex shrink-0 items-center gap-1">
        @isset($actions)
            <div class="mr-1 flex items-center gap-1">
                {{ $actions }}
            </div>
        @endisset

        @isset($close)
            {{ $close }}
        @else
            <button
                type="button"
                class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700"
                @click="$dispatch('shell-modal-close')"
                aria-label="{{ __('Close') }}"
            >
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path
                        fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                    />
                </svg>
            </button>
        @endisset
    </div>
</header>

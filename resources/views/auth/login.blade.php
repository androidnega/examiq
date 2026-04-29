@extends('layouts.guest')

@push('head')
    <link
        href="https://fonts.bunny.net/css?family=plus-jakarta-sans:500,600,700,800&display=swap"
        rel="stylesheet"
    />
@endpush

@section('content')
    <style>
        .login-ui {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
        }
        .login-bg {
            background-image:
                linear-gradient(
                    to bottom,
                    rgb(255 255 255 / 0.97),
                    rgb(255 255 255 / 0.97)
                ),
                url('https://img.freepik.com/premium-vector/geometric-memphis-design-minimal-seamless-cover_586360-1088.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        @keyframes login-pulse-ring {
            0%,
            100% {
                box-shadow: 0 0 0 0 rgb(20 184 166 / 0.45);
            }
            50% {
                box-shadow: 0 0 0 10px rgb(20 184 166 / 0);
            }
        }
        .login-plus-pulse {
            animation: login-pulse-ring 2s ease-out infinite;
        }
        @media (prefers-reduced-motion: reduce) {
            .login-plus-pulse {
                animation: none;
            }
        }
    </style>

    <div
        class="login-ui login-bg flex min-h-screen flex-col px-4 py-6 sm:px-6 sm:py-8"
        x-data="otpLogin()"
    >
        <div class="flex flex-1 flex-col items-center justify-center">
            <div
                class="grid w-full max-w-3xl overflow-hidden rounded-2xl border border-slate-200/80 bg-white/90 shadow-md shadow-slate-200/35 backdrop-blur-sm lg:grid-cols-2 lg:min-h-[22rem] lg:h-auto"
            >
                {{-- Image panel --}}
                <div class="relative min-h-32 shrink-0 overflow-hidden sm:min-h-36 lg:h-full lg:min-h-[22rem]">
                    <img
                        src="{{ config('examiq.login_hero_url') }}"
                        alt="{{ __('Student writing on paper with a pen in a classroom during an exam') }}"
                        width="800"
                        height="1000"
                        decoding="async"
                        fetchpriority="high"
                        class="absolute inset-0 h-full w-full object-cover object-[65%_center] sm:object-right"
                    />
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-teal-950/80 via-slate-900/35 to-transparent"
                        aria-hidden="true"
                    ></div>
                    <div
                        class="relative flex h-full min-h-32 flex-col justify-end p-5 text-white sm:min-h-36 sm:p-6 lg:min-h-0 lg:p-6"
                    >
                        <p
                            class="text-[10px] font-bold uppercase tracking-[0.22em] text-teal-200/95 sm:text-[11px]"
                        >
                            {{ __('Exam workflow') }}
                        </p>
                        <p class="mt-2 text-xl font-extrabold leading-tight tracking-tight text-white sm:text-2xl">
                            {{ config('app.name') }}
                        </p>
                        <p class="mt-2 max-w-xs text-sm font-semibold leading-snug text-white/90 sm:text-[0.95rem]">
                            {{ __('Coordinate exams, reviews, and secure sign-in in one place.') }}
                        </p>
                    </div>
                </div>

                {{-- Sign-in panel --}}
                <div
                    class="flex min-h-0 flex-col border-t border-slate-100 bg-white p-5 sm:p-6 lg:min-h-[22rem] lg:flex-1 lg:border-t-0 lg:border-l lg:border-slate-100 lg:p-6"
                >
                    <div class="shrink-0 text-center">
                        <h1 class="text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">
                            {{ __('Sign in') }}
                        </h1>
                        <p class="mx-auto mt-2 max-w-xs text-sm font-semibold leading-snug text-slate-600 sm:text-[0.95rem]">
                            {{ __('Use your phone number. Open the form, then enter the code we send you.') }}
                        </p>
                        <div class="mt-4 flex justify-center sm:mt-5">
                            <button
                                type="button"
                                class="relative flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-teal-500 to-cyan-600 text-xl font-light leading-none text-white shadow-lg shadow-teal-500/35 transition-all duration-200 hover:scale-105 hover:from-teal-400 hover:to-cyan-500 hover:shadow-xl hover:shadow-teal-500/40 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-400 focus-visible:ring-offset-2 active:scale-100 disabled:cursor-not-allowed disabled:opacity-50 disabled:shadow-none sm:h-12 sm:w-12"
                                @click="formOpen = !formOpen"
                                :disabled="step === 2"
                                :class="{ 'login-plus-pulse': step === 1 && !formOpen }"
                                :aria-expanded="formOpen || step === 2"
                                aria-controls="login-phone-panel"
                            >
                                <span class="select-none pb-0.5" x-show="step === 1" x-text="formOpen ? '−' : '+'"></span>
                                <span class="sr-only" x-show="step === 1">{{ __('Toggle phone form') }}</span>
                                <svg
                                    x-show="step === 2"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    class="h-5 w-5 sm:h-6 sm:w-6"
                                    aria-hidden="true"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3A5.25 5.25 0 0012 1.5zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span class="sr-only" x-show="step === 2">{{ __('Enter your code to continue') }}</span>
                            </button>
                        </div>
                    </div>

                    <div
                        class="relative mt-4 min-h-0 border-t border-slate-100 pt-4 lg:mt-3 lg:flex-1 lg:pt-3"
                    >
                        <div
                            id="login-phone-panel"
                            x-show="formOpen || step === 2"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="px-0.5 lg:px-0"
                            :class="
                                step === 2
                                    ? 'lg:absolute lg:inset-0 lg:flex lg:items-center lg:justify-center lg:overflow-hidden'
                                    : ''
                            "
                            x-cloak
                        >
                            <p
                                x-show="message.text && step === 1"
                                x-transition
                                class="mb-3 rounded-xl border px-3 py-2 text-sm font-semibold"
                                :class="message.type === 'error' ? 'border-red-200 bg-red-50 text-red-800' : 'border-teal-200 bg-teal-50 text-teal-900'"
                                x-text="message.text"
                            ></p>

                            <template x-if="step === 1">
                                <div class="space-y-3 lg:space-y-3.5">
                                    <div>
                                        <label
                                            for="phone"
                                            class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500 sm:text-xs"
                                        >
                                            {{ __('Phone number') }}
                                        </label>
                                        <input
                                            id="phone"
                                            type="text"
                                            x-model="phone"
                                            autocomplete="tel"
                                            inputmode="tel"
                                            placeholder="{{ __('+233 24 000 0000') }}"
                                            class="block w-full rounded-lg border border-slate-200/90 bg-slate-50/80 px-3.5 py-2.5 text-[15px] font-semibold tracking-tight text-slate-900 shadow-sm placeholder:font-medium placeholder:text-slate-400/90 transition-[border-color,box-shadow,background-color] focus:border-teal-500/80 focus:bg-white focus:shadow-[0_0_0_3px_rgb(20_184_166_/_12%)] focus:outline-none focus:ring-0 sm:px-4 sm:py-3 sm:text-base"
                                        />
                                    </div>
                                    <button
                                        type="button"
                                        class="w-full rounded-lg bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-teal-600/15 transition-[background-color,box-shadow] hover:bg-teal-700 hover:shadow-md hover:shadow-teal-600/20 focus:outline-none focus-visible:shadow-[0_0_0_3px_rgb(20_184_166_/_25%)] disabled:cursor-not-allowed disabled:opacity-50 sm:py-3"
                                        :disabled="loading.send || loading.verify || phone.trim().length === 0"
                                        @click="sendOtp"
                                    >
                                        <span x-show="!loading.send">{{ __('Send verification code') }}</span>
                                        <span x-show="loading.send" x-cloak>{{ __('Sending…') }}</span>
                                    </button>
                                </div>
                            </template>

                            <template x-if="step === 2">
                                <div class="flex flex-col items-center gap-3 lg:justify-center lg:gap-2 lg:pt-0">
                                    <p
                                        x-show="message.type === 'error' && message.text"
                                        class="w-full rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-center text-xs font-bold text-red-800"
                                        x-text="message.text"
                                    ></p>
                                    <p
                                        x-show="loading.verify"
                                        class="text-center text-xs font-bold text-slate-500"
                                    >
                                        {{ __('Verifying…') }}
                                    </p>
                                    <div
                                        class="flex w-full max-w-[17.5rem] justify-center gap-1.5 sm:max-w-xs sm:gap-2"
                                        role="group"
                                        aria-label="{{ __('Verification code') }}"
                                    >
                                        <template x-for="(_, i) in [0, 1, 2, 3, 4, 5]" :key="i">
                                            <input
                                                type="text"
                                                inputmode="numeric"
                                                pattern="[0-9]*"
                                                maxlength="1"
                                                autocomplete="one-time-code"
                                                :id="'otp-slot-' + i"
                                                :disabled="loading.verify"
                                                class="h-11 w-10 shrink-0 rounded-lg border border-slate-200 bg-white text-center text-lg font-extrabold tracking-tight text-slate-900 shadow-sm transition-colors focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/25 disabled:opacity-60 sm:h-12 sm:w-11 sm:text-xl"
                                                @input="onOtpInput($event, i)"
                                                @keydown="onOtpKeydown($event, i)"
                                                @paste="onOtpPaste($event)"
                                            />
                                        </template>
                                    </div>
                                    <button
                                        type="button"
                                        class="text-xs font-semibold text-teal-600 transition-colors hover:text-teal-800"
                                        :disabled="loading.verify"
                                        @click="backToPhone"
                                    >
                                        {{ __('Use a different number') }}
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <p
                        x-show="step === 1 && !formOpen"
                        class="shrink-0 pt-3 text-center text-[10px] font-medium leading-relaxed text-slate-400 sm:pt-4 sm:text-xs lg:pt-2"
                    >
                        {{ __('Protected sign-in. Codes are single-use and time-limited.') }}
                    </p>

                </div>
            </div>
        </div>

        <p class="mt-6 shrink-0 pb-2 text-center text-xs font-normal text-slate-400 sm:text-sm">
            {{ __('Built by') }}
            <a
                href="https://ausweblabs.com"
                target="_blank"
                rel="noopener noreferrer"
                class="font-medium text-slate-500 transition-colors hover:text-slate-600"
            >AusWebLabs</a>.
        </p>

        {{-- Post-OTP: TTU logo + calm horizontal progress (no spinners) --}}
        <div
            x-show="postAuthOverlay"
            x-transition:enter="transition ease-out duration-[480ms]"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-[360ms]"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-cloak
            class="fixed inset-0 z-[200] flex items-center justify-center bg-white px-10 py-20 sm:px-14"
            role="status"
            aria-live="polite"
            aria-busy="true"
        >
            <div class="flex w-full max-w-[18rem] flex-col items-center gap-8 text-center sm:max-w-[20rem]">
                <div class="flex flex-col items-center gap-5 sm:gap-6">
                    <img
                        src="{{ asset('images/brand/ttu-logo.png') }}"
                        alt="{{ __('Takoradi Technical University') }}"
                        class="h-[5.25rem] w-auto max-w-[14rem] object-contain select-none sm:h-28 sm:max-w-[16rem]"
                        width="256"
                        height="256"
                        loading="eager"
                        decoding="async"
                    />
                    <p class="max-w-[15rem] text-[13px] font-medium leading-relaxed text-neutral-500">
                        {{ __('Opening your workspace') }}
                    </p>
                </div>

                <div class="flex w-full flex-col items-stretch gap-4">
                    <div
                        class="h-2 w-full overflow-hidden rounded-full bg-neutral-200"
                        role="progressbar"
                        :aria-valuenow="loaderProgress"
                        aria-valuemin="0"
                        aria-valuemax="100"
                        aria-label="{{ __('Loading progress') }}"
                    >
                        <div
                            class="h-full rounded-full bg-[#5a938c]"
                            :style="'width: ' + loaderProgress + '%'"
                        ></div>
                    </div>
                    <p
                        class="font-sans text-[13px] font-medium tabular-nums text-neutral-500"
                        x-text="loaderProgress + '%'"
                    ></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function otpLogin() {
            return {
                step: 1,
                formOpen: false,
                phone: '',
                otpDigits: ['', '', '', '', '', ''],
                loading: { send: false, verify: false },
                message: { type: '', text: '' },
                _verifyStarted: false,
                postAuthOverlay: false,
                loaderProgress: 0,

                /** Subtle ease-out (gentler than cubic). */
                easeOutQuad(t) {
                    return 1 - (1 - t) * (1 - t);
                },

                async runProgressAnimation() {
                    const reduced =
                        typeof window.matchMedia === 'function' &&
                        window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    if (reduced) {
                        this.loaderProgress = 100;
                        return;
                    }

                    const durationMs = 920;
                    const start = performance.now();

                    await new Promise((resolve) => {
                        const step = (now) => {
                            const linear = Math.min(1, (now - start) / durationMs);
                            const eased = this.easeOutQuad(linear);
                            this.loaderProgress = Math.min(100, Math.round(eased * 100));
                            if (linear < 1) {
                                requestAnimationFrame(step);
                            } else {
                                this.loaderProgress = 100;
                                resolve();
                            }
                        };
                        requestAnimationFrame(step);
                    });
                },

                otpCode() {
                    return this.otpDigits.join('');
                },

                resetOtpSlots() {
                    this.otpDigits = ['', '', '', '', '', ''];
                    this._verifyStarted = false;
                    for (let i = 0; i < 6; i++) {
                        const el = document.getElementById('otp-slot-' + i);
                        if (el) {
                            el.value = '';
                        }
                    }
                },

                focusOtpSlot(index) {
                    const el = document.getElementById('otp-slot-' + index);
                    el?.focus();
                    el?.select();
                },

                onOtpInput(event, index) {
                    const raw = event.target.value.replace(/\D/g, '');
                    const digit = raw.slice(-1);
                    event.target.value = digit;
                    this.otpDigits[index] = digit;

                    if (digit && index < 5) {
                        this.focusOtpSlot(index + 1);
                    }

                    if (this.otpCode().length === 6 && !this._verifyStarted) {
                        this._verifyStarted = true;
                        this.verifyOtp();
                    }
                },

                onOtpKeydown(event, index) {
                    if (event.key === 'Backspace' && !event.target.value && index > 0) {
                        event.preventDefault();
                        this.focusOtpSlot(index - 1);
                    }
                },

                onOtpPaste(event) {
                    event.preventDefault();
                    const text = (event.clipboardData || window.clipboardData).getData('text');
                    const digits = text.replace(/\D/g, '').slice(0, 6).split('');
                    for (let i = 0; i < 6; i++) {
                        this.otpDigits[i] = digits[i] ?? '';
                        const el = document.getElementById('otp-slot-' + i);
                        if (el) {
                            el.value = this.otpDigits[i];
                        }
                    }
                    const nextEmpty = this.otpDigits.findIndex((d) => !d);
                    if (nextEmpty === -1) {
                        this.focusOtpSlot(5);
                        if (this.otpCode().length === 6 && !this._verifyStarted) {
                            this._verifyStarted = true;
                            this.verifyOtp();
                        }
                    } else {
                        this.focusOtpSlot(nextEmpty);
                    }
                },

                backToPhone() {
                    this.step = 1;
                    this.message = { type: '', text: '' };
                    this.resetOtpSlots();
                    this.formOpen = true;
                },

                async sendOtp() {
                    this.message = { type: '', text: '' };
                    this.loading.send = true;

                    const response = await fetch('{{ route('auth.send-otp') }}', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ phone: this.phone }),
                    });

                    const payload = await response.json();
                    this.loading.send = false;

                    if (!response.ok) {
                        this.message = { type: 'error', text: this.firstError(payload) };
                        return;
                    }

                    this.resetOtpSlots();
                    this.step = 2;
                    this.formOpen = true;
                    this.message = { type: '', text: '' };

                    this.$nextTick(() => {
                        this.focusOtpSlot(0);
                    });
                },

                async verifyOtp() {
                    const code = this.otpCode();
                    if (code.length !== 6) {
                        this._verifyStarted = false;
                        return;
                    }

                    this.message = { type: '', text: '' };
                    this.loading.verify = true;

                    const response = await fetch('{{ route('auth.verify-otp') }}', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ phone: this.phone, code }),
                    });

                    const payload = await response.json();
                    this.loading.verify = false;

                    if (!response.ok) {
                        this.message = { type: 'error', text: this.firstError(payload) };
                        this.resetOtpSlots();
                        this._verifyStarted = false;
                        this.$nextTick(() => this.focusOtpSlot(0));
                        return;
                    }

                    this.loaderProgress = 0;
                    this.postAuthOverlay = true;
                    const redirectUrl = payload.redirect ?? '/dashboard';
                    const settleAt100Ms = 160;

                    await this.$nextTick();
                    await new Promise((resolve) => requestAnimationFrame(() => resolve()));

                    await this.runProgressAnimation();
                    await new Promise((resolve) => setTimeout(resolve, settleAt100Ms));
                    // Keep the post-auth layer visible until navigation completes
                    // to avoid flashing back to the login view.
                    window.location.replace(redirectUrl);
                },

                firstError(payload) {
                    if (payload?.message) {
                        return payload.message;
                    }

                    const first = payload?.errors ? Object.values(payload.errors)[0] : null;
                    if (Array.isArray(first) && first[0]) {
                        return first[0];
                    }

                    return 'Something went wrong. Please try again.';
                },
            };
        }
    </script>
@endsection

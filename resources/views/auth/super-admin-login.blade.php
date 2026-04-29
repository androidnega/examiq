@extends('layouts.guest')

@section('content')
    <div class="flex min-h-screen items-center justify-center bg-slate-50 px-4">
        <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h1 class="text-xl font-bold tracking-tight text-slate-900">{{ __('Super Admin Login') }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ __('Use your super admin credentials to continue.') }}</p>

            @if ($errors->has('username'))
                <p class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-700">
                    {{ $errors->first('username') }}
                </p>
            @endif

            <form method="post" action="{{ route('auth.admin-login') }}" class="mt-4 space-y-3">
                @csrf
                <div>
                    <label for="username" class="mb-1 block text-sm font-medium text-slate-700">{{ __('Username') }}</label>
                    <input
                        id="username"
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-300/40"
                    />
                </div>
                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-slate-700">{{ __('Password') }}</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-300/40"
                    />
                </div>
                <button type="submit" class="w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                    {{ __('Login as Super Admin') }}
                </button>
            </form>
        </div>
    </div>
@endsection

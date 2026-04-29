@extends('layouts.app')

@section('content')
    <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800">{{ __('Dashboard') }}</h1>
        <p class="mt-2 text-sm text-gray-500">
            {{ __('Signed in as :name.', ['name' => auth()->user()->name]) }}
        </p>
        <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
            <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                <dt class="text-gray-500">{{ __('Role') }}</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ str_replace('_', ' ', auth()->user()->role->value) }}</dd>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                <dt class="text-gray-500">{{ __('Phone') }}</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ auth()->user()->phone }}</dd>
            </div>
        </dl>
    </div>
@endsection

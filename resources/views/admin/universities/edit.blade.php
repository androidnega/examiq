@extends('layouts.app', ['header' => __('Edit university')])

@section('content')
    <form method="post" action="{{ route('dashboard.universities.update', $university) }}" class="w-full space-y-5">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">{{ __('Name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name', $university->name) }}" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20" />
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">{{ __('Update') }}</x-button>
            <x-button href="{{ route('dashboard.universities.index') }}" variant="secondary">{{ __('Cancel') }}</x-button>
        </div>
    </form>
@endsection

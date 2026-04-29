@extends('layouts.app', ['header' => __('New department')])

@section('content')
    <form method="post" action="{{ route('dashboard.departments.store') }}" class="w-full space-y-5">
        @csrf
        <div>
            <label for="faculty_id" class="block text-sm font-medium text-slate-700">{{ __('Faculty') }}</label>
            <select id="faculty_id" name="faculty_id" required class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20">
                <option value="">{{ __('Select…') }}</option>
                @foreach ($faculties as $f)
                    <option value="{{ $f->id }}" @selected(old('faculty_id') === $f->id)>
                        {{ $f->name }} @if ($f->university) ({{ $f->university->name }}) @endif
                    </option>
                @endforeach
            </select>
            @error('faculty_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">{{ __('Name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20" />
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex flex-wrap gap-2">
            <x-button type="submit" variant="primary">{{ __('Save') }}</x-button>
            <x-button href="{{ route('dashboard.departments.index') }}" variant="secondary">{{ __('Cancel') }}</x-button>
        </div>
    </form>
@endsection

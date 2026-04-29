@extends('layouts.app', ['header' => __('Search results')])

@section('content')
    @php
        use App\Enums\UserRole;
    @endphp

    <div class="mb-6">
        @if ($q === '')
            <p class="text-sm text-slate-500">{{ __('Enter a term in the header search to find submissions and (for admins) people or courses.') }}</p>
        @else
            <p class="text-sm text-slate-600">
                {{ __('Results for “:term”', ['term' => $q]) }}
            </p>
        @endif
    </div>

    @if ($q !== '')
        @if ($submissions->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-sm font-bold text-slate-900">{{ __('Submissions') }}</h2>
                <ul class="mt-3 divide-y divide-slate-100 rounded-xl border border-slate-100 bg-white">
                    @foreach ($submissions as $sub)
                        @php
                            $href = $role === null
                                ? null
                                : match ($role) {
                                    UserRole::Lecturer => route('dashboard.submissions.show', $sub),
                                    UserRole::Hod => route('dashboard.department.show', $sub),
                                    UserRole::Moderator => route('dashboard.reviews.show', $sub),
                                    default => null,
                                };
                        @endphp
                        <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-3">
                            <div class="min-w-0">
                                <p class="font-medium text-slate-900">
                                    @if ($href)
                                        <a href="{{ $href }}" class="text-teal-700 hover:underline">{{ $sub->course?->code }} — {{ $sub->course?->name }}</a>
                                    @else
                                        {{ $sub->course?->code }} — {{ $sub->course?->name }}
                                    @endif
                                </p>
                                <p class="text-xs text-slate-500">{{ $sub->academic_year }}</p>
                            </div>
                            <x-badge :value="$sub->status" />
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($role === UserRole::Admin)
            @if ($users->isNotEmpty())
                <div class="mb-8">
                    <h2 class="text-sm font-bold text-slate-900">{{ __('Users') }}</h2>
                    <ul class="mt-3 divide-y divide-slate-100 rounded-xl border border-slate-100 bg-white">
                        @foreach ($users as $u)
                            <li class="px-4 py-3 text-sm text-slate-800">
                                <span class="font-medium">{{ $u->name }}</span>
                                <span class="text-slate-500"> · {{ $u->phone }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($courses->isNotEmpty())
                <div class="mb-8">
                    <h2 class="text-sm font-bold text-slate-900">{{ __('Courses') }}</h2>
                    <ul class="mt-3 divide-y divide-slate-100 rounded-xl border border-slate-100 bg-white">
                        @foreach ($courses as $course)
                            <li class="px-4 py-3 text-sm text-slate-800">
                                <span class="font-medium">{{ $course->code }}</span>
                                <span class="text-slate-600"> — {{ $course->name }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif

        @php
            $hasAny =
                $submissions->isNotEmpty()
                || ($role === UserRole::Admin && ($users->isNotEmpty() || $courses->isNotEmpty()));
        @endphp
        @if (! $hasAny)
            <p class="rounded-lg border border-slate-100 bg-white px-4 py-8 text-center text-sm text-slate-500">{{ __('No matches found.') }}</p>
        @endif
    @endif
@endsection

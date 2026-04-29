@extends('layouts.app', ['header' => __('Update submission')])

@section('content')
    <div class="w-full">
        <p class="mb-6 text-sm text-gray-500">
            {{ __('Uploading saves a new version before moderation is assigned. Previous files are kept.') }}
        </p>
        @include('lecturer.submissions.partials.form', ['submission' => $submission])
    </div>
@endsection

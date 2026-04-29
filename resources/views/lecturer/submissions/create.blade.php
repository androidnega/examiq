@extends('layouts.app', ['header' => __('New submission')])

@section('content')
    <div class="w-full">
        @include('lecturer.submissions.partials.form', ['courses' => $courses, 'submission' => null])
    </div>
@endsection

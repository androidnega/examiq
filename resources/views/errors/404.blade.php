<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 | {{ config('app.name') }}</title>
    @include('layouts.partials.tailwind-cdn', [
        'fontSans' => ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
    ])
</head>
<body class="min-h-screen bg-white text-slate-900">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden">
        <img
            src="https://cdn.prod.website-files.com/65ba70a5bb6f912baf0094a3/69b0118e738f6e5f54bca238_www.acolorbright.com_en_404(1440).avif"
            alt="404 background"
            class="absolute inset-0 h-full w-full object-cover"
        >
        <div class="absolute inset-0 bg-black/15"></div>
        <h1 class="relative text-3xl font-semibold tracking-wide text-white md:text-5xl">Lost</h1>
    </main>
</body>
</html>

@props(['wrapClass' => ''])
<div {{ $attributes->merge(['class' => 'overflow-x-auto rounded-xl border border-slate-100 bg-white '.$wrapClass]) }}>
    <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
        {{ $slot }}
    </table>
</div>

@props([
    'name' => 'grid',
])
@php
    $c = $attributes->get('class', 'h-5 w-5');
    $fa = [
        'grid' => 'fa-solid fa-table-cells',
        'folder' => 'fa-solid fa-folder',
        'plus' => 'fa-solid fa-plus',
        'users' => 'fa-solid fa-users',
        'check' => 'fa-solid fa-circle-check',
        'clipboard' => 'fa-solid fa-clipboard-list',
        'table' => 'fa-solid fa-table',
        'building' => 'fa-solid fa-building',
        'layers' => 'fa-solid fa-layer-group',
        'map-pin' => 'fa-solid fa-location-dot',
        'shield' => 'fa-solid fa-shield-halved',
        'activity' => 'fa-solid fa-wave-square',
        'file-text' => 'fa-solid fa-file-lines',
        'ban' => 'fa-solid fa-ban',
        'cog' => 'fa-solid fa-gear',
        'eye' => 'fa-solid fa-eye',
        'user-plus' => 'fa-solid fa-user-plus',
        'chart' => 'fa-solid fa-chart-column',
        'history' => 'fa-solid fa-clock-rotate-left',
        'chat' => 'fa-solid fa-comments',
        'inbox' => 'fa-solid fa-inbox',
        'print' => 'fa-solid fa-print',
        'calendar' => 'fa-solid fa-calendar-days',
        'calculator' => 'fa-solid fa-calculator',
        'search' => 'fa-solid fa-magnifying-glass',
        'tag' => 'fa-solid fa-tag',
        'arrow-up-tray' => 'fa-solid fa-arrow-up-from-bracket',
        'funnel' => 'fa-solid fa-filter',
        'pencil-square' => 'fa-solid fa-pen-to-square',
        'x-mark' => 'fa-solid fa-xmark',
        'user' => 'fa-solid fa-user',
        'book' => 'fa-solid fa-book',
    ];
    $iconClass = $fa[$name] ?? 'fa-solid fa-circle';
@endphp
<i {{ $attributes->merge(['class' => trim($iconClass.' '.$c)]) }} aria-hidden="true"></i>

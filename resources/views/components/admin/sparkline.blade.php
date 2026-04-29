@props([
    'values' => [],
    'stroke' => '#2563eb',
    'fill' => null,
    'fillOpacity' => '0.12',
])
@php
    $vals = array_values($values);
    $n = count($vals);
    $max = max($vals) > 0 ? max($vals) : 1;
    $w = 100;
    $h = 36;
    $pad = 2;
    $points = [];
    $areaBase = $h - $pad;
    foreach ($vals as $i => $v) {
        $x = $n <= 1 ? $w / 2 : $pad + ($i / max(1, $n - 1)) * ($w - 2 * $pad);
        $y = $pad + (1 - $v / $max) * ($h - 2 * $pad);
        $points[] = round($x, 2).','.round($y, 2);
    }
    $linePoints = implode(' ', $points);
    $firstX = $n <= 1 ? $w / 2 : $pad;
    $lastX = $n <= 1 ? $w / 2 : $w - $pad;
    $areaPoints = $firstX.','.$areaBase.' '.$linePoints.' '.$lastX.','.$areaBase;
@endphp
<svg
    viewBox="0 0 {{ $w }} {{ $h }}"
    class="{{ $attributes->get('class', 'h-12 w-full') }}"
    preserveAspectRatio="none"
    aria-hidden="true"
>
    @if ($fill)
        <polygon points="{{ $areaPoints }}" fill="{{ $fill }}" fill-opacity="{{ $fillOpacity }}" />
    @endif
    <polyline
        fill="none"
        stroke="{{ $stroke }}"
        stroke-width="1.75"
        stroke-linecap="round"
        stroke-linejoin="round"
        vector-effect="non-scaling-stroke"
        points="{{ $linePoints }}"
    />
</svg>

@props([
    'primary' => [],
    'secondary' => [],
    'max' => 1,
    'primaryColor' => '#0d9488',
    'secondaryColor' => '#e11d48',
])
@php
    $p = array_values($primary);
    $s = array_values($secondary);
    $top = max(1, (int) $max);
    $w = 100;
    $h = 40;
    $pad = 2;

    $coords = function (array $vals) use ($w, $h, $pad, $top): array {
        $out = [];
        $c = count($vals);
        if ($c === 0) {
            return $out;
        }
        foreach ($vals as $i => $v) {
            $x = $c <= 1 ? $w / 2 : $pad + ($i / max(1, $c - 1)) * ($w - 2 * $pad);
            $y = $pad + (1 - min($top, $v) / $top) * ($h - 2 * $pad);
            $out[] = [round($x, 2), round($y, 2)];
        }

        return $out;
    };

    $pc = $coords($p);
    $sc = $coords($s);
    $base = $h - $pad;

    $lineStr = function (array $coords): string {
        return collect($coords)->map(fn ($pt) => $pt[0].','.$pt[1])->implode(' ');
    };

    $areaStr = function (array $coords) use ($base, $lineStr): string {
        if ($coords === []) {
            return '';
        }
        $first = $coords[0][0].','.$base;
        $last = $coords[count($coords) - 1][0].','.$base;

        return $first.' '.$lineStr($coords).' '.$last;
    };
@endphp
<svg
    viewBox="0 0 {{ $w }} {{ $h }}"
    class="{{ $attributes->get('class', 'h-16 w-full') }}"
    preserveAspectRatio="none"
    aria-hidden="true"
>
    @if ($pc !== [])
        <polygon points="{{ $areaStr($pc) }}" fill="{{ $primaryColor }}" fill-opacity="0.18" />
    @endif
    @if ($sc !== [])
        <polygon points="{{ $areaStr($sc) }}" fill="{{ $secondaryColor }}" fill-opacity="0.14" />
    @endif
    @if ($sc !== [])
        <polyline
            fill="none"
            stroke="{{ $secondaryColor }}"
            stroke-width="1.75"
            stroke-linecap="round"
            stroke-linejoin="round"
            vector-effect="non-scaling-stroke"
            points="{{ $lineStr($sc) }}"
        />
    @endif
    @if ($pc !== [])
        <polyline
            fill="none"
            stroke="{{ $primaryColor }}"
            stroke-width="1.75"
            stroke-linecap="round"
            stroke-linejoin="round"
            vector-effect="non-scaling-stroke"
            points="{{ $lineStr($pc) }}"
        />
    @endif
</svg>

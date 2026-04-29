@php
    $fontSans = $fontSans ?? ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'];
@endphp
{{-- Tailwind Play CDN: scans the DOM and generates utilities client-side. --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: @json($fontSans),
                },
            },
        },
    };
</script>
<style>
    [x-cloak] {
        display: none !important;
    }

    /* Ensure off-screen text stays hidden if the Play CDN misses scanning `sr-only`. */
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border-width: 0;
    }
</style>

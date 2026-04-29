{{--
  Sleek modal backdrop. Pair with shell-modal.panel.

  Parent should control visibility, e.g. x-show="modalOpen" x-cloak, and close on:
  - @keydown.escape.window="modalOpen = false"
  - @click.self="modalOpen = false" (add on this element)
  - @shell-modal-close.window="modalOpen = false" (for header close button dispatch)

  Example:
  <div x-data="{ modalOpen: false }" @shell-modal-close.window="modalOpen = false">
    <x-shell-modal.backdrop
      x-show="modalOpen"
      x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-100"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="opacity-100"
      x-transition:leave-end="opacity-0"
      @click.self="modalOpen = false"
      @keydown.escape.window="modalOpen = false"
    >
      <x-shell-modal.panel>...</x-shell-modal.panel>
    </x-shell-modal.backdrop>
  </div>
--}}
<div
    role="dialog"
    aria-modal="true"
    {{ $attributes->merge([
        'class' => 'fixed inset-0 z-[70] flex items-end justify-center bg-slate-900/40 p-4 sm:items-center sm:p-8',
    ]) }}
>
    {{ $slot }}
</div>

@extends('layouts.app', ['header' => __('Users')])

@php
    $iconBtn = 'inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/30';
    $pillBase = 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium transition';
    $pillOn = 'border-blue-600 bg-blue-50 text-blue-800';
    $pillOff = 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50';
@endphp

@section('content')
    <div class="-mx-4 -mt-2 rounded-xl border border-slate-200 bg-white shadow-sm md:-mx-6 print:border-0 print:shadow-none" x-data="{ createModalOpen: false }" @shell-modal-close.window="createModalOpen = false">
        <div class="border-b border-slate-200 px-5 py-3">
            <h1 class="text-lg font-bold tracking-tight text-slate-900">{{ __('Users') }}</h1>
        </div>

        @if ($canManageUsers ?? false)
            <div class="border-b border-slate-200 bg-slate-50/60 px-5 py-3">
                <x-button type="button" variant="primary" @click="createModalOpen = true">{{ __('Create Account') }}</x-button>
            </div>
        @endif

        <div class="flex flex-col gap-2.5 border-b border-slate-200 px-5 py-2.5 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
            <div class="flex min-w-0 flex-1 flex-wrap items-center gap-2">
                <a
                    href="{{ $indexRoute }}"
                    class="{{ $pillBase }} {{ $filters['status'] === 'all' && $filters['role'] === 'all' ? $pillOn : $pillOff }}"
                >{{ __('All users') }}</a>
                <a
                    href="{{ $indexRoute.'?'.http_build_query(array_merge(request()->except('page'), ['status' => 'active', 'role' => 'all'])) }}"
                    class="{{ $pillBase }} {{ $filters['status'] === 'active' ? $pillOn : $pillOff }}"
                >{{ __('Active') }}</a>
                <a
                    href="{{ $indexRoute.'?'.http_build_query(array_merge(request()->except('page'), ['status' => 'blocked', 'role' => 'all'])) }}"
                    class="{{ $pillBase }} {{ $filters['status'] === 'blocked' ? $pillOn : $pillOff }}"
                >{{ __('Blocked') }}</a>
                <form method="get" action="{{ $indexRoute }}" class="inline-flex items-center">
                    <input type="hidden" name="q" value="{{ $filters['q'] }}" />
                    <input type="hidden" name="status" value="{{ $filters['status'] }}" />
                    <label class="sr-only" for="users-role-filter">{{ __('Role') }}</label>
                    <select
                        id="users-role-filter"
                        name="role"
                        onchange="this.form.submit()"
                        class="rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-xs text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                    >
                        <option value="all" @selected($filters['role'] === 'all')>{{ __('All roles') }}</option>
                        @foreach ($roleCases as $rc)
                            <option value="{{ $rc->value }}" @selected($filters['role'] === $rc->value)>
                                {{ ucfirst(str_replace('_', ' ', $rc->value)) }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <form
                method="get"
                action="{{ $indexRoute }}"
                class="flex w-full min-w-[12rem] max-w-md items-center gap-2 rounded-lg border border-slate-300 bg-white px-2.5 py-1 shadow-sm focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20 sm:w-auto"
            >
                <input type="hidden" name="status" value="{{ $filters['status'] }}" />
                <input type="hidden" name="role" value="{{ $filters['role'] }}" />
                <x-dashboard-icon name="search" class="h-4 w-4 shrink-0 text-slate-400" />
                <input
                    type="search"
                    name="q"
                    value="{{ $filters['q'] }}"
                    placeholder="{{ __('Search') }}"
                    class="min-w-0 flex-1 border-0 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0"
                />
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50/90">
                    <tr class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="whitespace-nowrap px-5 py-2">{{ __('User') }}</th>
                        <th class="whitespace-nowrap px-5 py-2">{{ __('Department') }}</th>
                        <th class="whitespace-nowrap px-5 py-2">{{ __('Joined') }}</th>
                        <th class="whitespace-nowrap px-5 py-2">{{ __('Status') }}</th>
                        <th class="whitespace-nowrap px-5 py-2 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach ($users as $row)
                        @php
                            $joined = $row->created_at?->format('d-m-Y') ?? '—';
                        @endphp
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="px-5 py-1.5">
                                <p class="text-sm font-semibold text-slate-900">{{ $row->name }}</p>
                                <p class="mt-0.5 text-xs text-slate-600">{{ $row->phone }}</p>
                                <div class="mt-1">
                                    <span
                                        class="inline-flex rounded-md border border-violet-200 bg-violet-50 px-1.5 py-0.5 text-[11px] font-medium text-violet-800"
                                    >{{ ucfirst(str_replace('_', ' ', $row->role instanceof \App\Enums\UserRole ? $row->role->value : (string) $row->role)) }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-1.5">
                                @if ($row->department)
                                    <span class="text-sm font-medium text-slate-800">{{ $row->department->name }}</span>
                                @else
                                    <span class="text-sm text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-1.5 text-sm text-slate-600">{{ $joined }}</td>
                            <td class="whitespace-nowrap px-5 py-1.5">
                                @if ($row->is_blocked)
                                    <span class="text-sm font-medium text-red-700">{{ __('Blocked') }}</span>
                                @else
                                    <span class="text-sm font-medium text-emerald-700">{{ __('Active') }}</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-1.5 text-right">
                                <div x-data="{ manageModalOpen: false }" @shell-modal-close.window="manageModalOpen = false">
                                    <button type="button" class="inline-flex cursor-pointer items-center rounded-md border border-slate-200 px-2 py-1 text-xs text-slate-700" @click="manageModalOpen = true">{{ __('Manage') }}</button>
                                    <template x-teleport="body">
                                        <x-shell-modal.backdrop
                                            x-show="manageModalOpen"
                                            x-cloak
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0"
                                            x-transition:enter-end="opacity-100"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100"
                                            x-transition:leave-end="opacity-0"
                                            @click.self="manageModalOpen = false"
                                            @keydown.escape.window="manageModalOpen = false"
                                        >
                                            <x-shell-modal.panel maxWidth="max-w-xl">
                                                <x-shell-modal.header :title="__('Manage Account')" :subtitle="$row->name" />
                                                <x-shell-modal.body-single>
                                                    @if ($canEditUsers ?? false)
                                                        <form id="manage-user-form-{{ $row->id }}" method="post" action="{{ route($updateRouteName, $row) }}" class="space-y-3 overflow-hidden">
                                                            @csrf
                                                            @method('PUT')
                                                            <div>
                                                                <label class="mb-1 block text-xs font-medium text-slate-600">{{ __('Name') }}</label>
                                                                <input type="text" name="name" value="{{ $row->name }}" class="w-full rounded border border-slate-200 px-2 py-1.5 text-xs" />
                                                            </div>
                                                            <div>
                                                                <label class="mb-1 block text-xs font-medium text-slate-600">{{ __('Phone') }}</label>
                                                                <input type="text" name="phone" value="{{ $row->phone }}" class="w-full rounded border border-slate-200 px-2 py-1.5 text-xs" />
                                                            </div>
                                                            <div>
                                                                <label class="mb-1 block text-xs font-medium text-slate-600">{{ __('Role') }}</label>
                                                                <select name="role" class="w-full rounded border border-slate-200 px-2 py-1.5 text-xs">
                                                                    @foreach (($creatableRoleCases ?? []) as $roleCase)
                                                                        <option value="{{ $roleCase->value }}" @selected((($row->role instanceof \App\Enums\UserRole ? $row->role->value : (string) $row->role) === $roleCase->value))>{{ ucfirst(str_replace('_', ' ', $roleCase->value)) }}</option>
                                                                    @endforeach
                                                                    @if (($isSuperAdmin ?? false) && (($row->role instanceof \App\Enums\UserRole ? $row->role->value : (string) $row->role) === 'admin'))
                                                                        <option value="admin" selected>{{ __('Admin') }}</option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="mb-1 block text-xs font-medium text-slate-600">{{ __('Department') }}</label>
                                                                <select name="department_id" class="w-full rounded border border-slate-200 px-2 py-1.5 text-xs">
                                                                    <option value="">{{ __('No department') }}</option>
                                                                    @foreach (($departments ?? []) as $department)
                                                                        <option value="{{ $department->id }}" @selected((string) $row->department_id === (string) $department->id)>{{ $department->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </form>
                                                    @endif

                                                    @if ($canResetPasswords ?? false)
                                                        <form id="reset-password-form-{{ $row->id }}" method="post" action="{{ route($resetPasswordRouteName, $row) }}" class="hidden">
                                                            @csrf
                                                        </form>
                                                    @endif

                                                    @if ($canBlockUsers ?? false && $row->getKey() !== auth()->id())
                                                        @if ($row->is_blocked)
                                                            <form id="block-toggle-form-{{ $row->id }}" method="post" action="{{ route('dashboard.users.unblock', $row) }}" class="hidden">
                                                                @csrf
                                                            </form>
                                                        @else
                                                            <form id="block-toggle-form-{{ $row->id }}" method="post" action="{{ route('dashboard.users.block', $row) }}" class="hidden" onsubmit="return confirm(@js(__('Block this user?')));">
                                                                @csrf
                                                            </form>
                                                        @endif
                                                    @endif

                                                    <div class="mt-3 flex flex-wrap items-center gap-2">
                                                        @if ($canEditUsers ?? false)
                                                            <button type="submit" form="manage-user-form-{{ $row->id }}" class="rounded bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white">{{ __('Save changes') }}</button>
                                                        @endif

                                                        @if ($canResetPasswords ?? false)
                                                            <button type="submit" form="reset-password-form-{{ $row->id }}" class="rounded border border-amber-300 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-800">{{ __('Reset password (SMS)') }}</button>
                                                        @endif

                                                        @if ($canBlockUsers ?? false && $row->getKey() !== auth()->id())
                                                            <button type="submit" form="block-toggle-form-{{ $row->id }}" class="rounded border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-500 transition hover:border-red-200 hover:text-red-700">
                                                                {{ $row->is_blocked ? __('Unblock') : __('Block') }}
                                                            </button>
                                                        @endif
                                                    </div>
                                                </x-shell-modal.body-single>
                                                <x-shell-modal.footer>
                                                    <x-button type="button" variant="secondary" @click="manageModalOpen = false">{{ __('Close') }}</x-button>
                                                </x-shell-modal.footer>
                                            </x-shell-modal.panel>
                                        </x-shell-modal.backdrop>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-3">
            <p class="text-xs text-slate-500">
                {{ __(':count users total', ['count' => $users->total()]) }}
            </p>
            <div>{{ $users->links() }}</div>
        </div>
        @if ($canManageUsers ?? false)
            <template x-teleport="body">
                <x-shell-modal.backdrop
                    x-show="createModalOpen"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click.self="createModalOpen = false"
                    @keydown.escape.window="createModalOpen = false"
                >
                    <x-shell-modal.panel maxWidth="max-w-2xl">
                        <x-shell-modal.header :title="__('Create Account')" :subtitle="__('Create lecturer, moderator, or exam officer account')" />
                        <x-shell-modal.body-single>
                            <form method="post" action="{{ $storeRoute }}" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-slate-600">{{ __('Full name') }}</label>
                                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-slate-600">{{ __('Phone number') }}</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-slate-600">{{ __('Role') }}</label>
                                    <select name="role" required class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs">
                                        <option value="">{{ __('Select role') }}</option>
                                        @foreach (($creatableRoleCases ?? []) as $roleCase)
                                            <option value="{{ $roleCase->value }}" @selected(old('role') === $roleCase->value)>{{ ucfirst(str_replace('_', ' ', $roleCase->value)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-slate-600">{{ __('Department') }}</label>
                                    <select name="department_id" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs">
                                        <option value="">{{ __('No department (exam officer)') }}</option>
                                        @foreach (($departments ?? []) as $department)
                                            <option value="{{ $department->id }}" @selected(old('department_id') === (string) $department->id)>{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-button type="submit" variant="primary">{{ __('Create') }}</x-button>
                            </form>
                        </x-shell-modal.body-single>
                        <x-shell-modal.footer>
                            <x-button type="button" variant="secondary" @click="createModalOpen = false">{{ __('Close') }}</x-button>
                        </x-shell-modal.footer>
                    </x-shell-modal.panel>
                </x-shell-modal.backdrop>
            </template>
        @endif
    </div>
@endsection

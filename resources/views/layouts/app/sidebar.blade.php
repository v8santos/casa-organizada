<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    @php
        $currentHousehold = request()->household();
    @endphp
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            @if ($currentHousehold)
                <flux:dropdown position="bottom" align="start">
                    <flux:profile
                        :name="$currentHousehold->name"
                        icon-trailing="chevron-up-down"
                    />

                    <flux:menu>
                        @foreach(auth()->user()->households as $household)
                            <form
                                method="POST"
                                action="{{ route('households.set', ['household_id' => $household->id]) }}"
                            >
                                @csrf

                                <flux:menu.item
                                    as="button"
                                    type="submit"
                                >
                                    <flux:avatar
                                        size="xs"
                                        :name="$household->name"
                                        class="mr-2"
                                    />

                                    {{ $household->name }}

                                    @if($household->is($currentHousehold))
                                        <flux:icon.check class="ml-auto size-4" />
                                    @endif
                                </flux:menu.item>
                            </form>
                        @endforeach

                        <flux:menu.separator />

                        <flux:menu.item href="{{ route('households.index') }}">
                            Gerenciar casas
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            @endif

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="clipboard-document-list" :href="route('shopping-lists.index')" :current="request()->routeIs('shopping-lists.*')" wire:navigate>
                        {{ __('Listas de compras') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="shopping-cart" :href="route('purchases.index')" :current="request()->routeIs('purchases.*')" wire:navigate>
                        {{ __('Compras') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>

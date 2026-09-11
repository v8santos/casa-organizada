<x-layouts::app :title="__('Listas de compras')">
    @php
        $totalItems = $lists->sum('items_count');
    @endphp

    <div class="flex h-full w-full flex-1 flex-col gap-6" x-data="{ search: '' }">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl" level="1">Listas de compras</flux:heading>
                <flux:subheading class="mt-1">Organize e acompanhe suas próximas compras.</flux:subheading>
            </div>

            <flux:button :href="route('shopping-lists.create')" variant="primary" icon="plus" wire:navigate>
                Nova lista
            </flux:button>
        </div>

        <flux:separator variant="subtle" />

        @if (session('status'))
            <div class="flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                <flux:icon.check-circle class="size-5 shrink-0" />
                {{ session('status') }}
            </div>
        @endif

        @if ($lists->isNotEmpty())
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between gap-6">
                        <div>
                            <flux:text class="text-sm">Total de listas</flux:text>
                            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $lists->count() }}</p>
                        </div>
                        <div class="grid size-10 place-items-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                            <flux:icon.clipboard-document-list class="size-5" />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between gap-6">
                        <div>
                            <flux:text class="text-sm">Itens planejados</flux:text>
                            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $totalItems }}</p>
                        </div>
                        <div class="grid size-10 place-items-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                            <flux:icon.shopping-bag class="size-5" />
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <flux:heading size="lg">Minhas listas</flux:heading>
                    <flux:text class="mt-1 text-sm">Selecione uma lista para visualizar ou editar.</flux:text>
                </div>

                <div class="w-full sm:w-72">
                    <flux:input x-model="search" type="search" icon="magnifying-glass" placeholder="Buscar listas..." clearable />
                </div>
            </div>

            <div class="flex flex-wrap items-start gap-4">
                @foreach ($lists as $list)
                    <article
                        x-show="!search || @js(str($list->name ?: 'Lista sem título')->lower()).includes(search.toLowerCase())"
                        x-transition.opacity.duration.150ms
                        class="group flex min-h-64 w-full flex-col rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-zinc-300 hover:shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600 sm:w-[calc(50%-0.5rem)] lg:w-80"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <flux:heading size="lg" class="truncate">{{ $list->name ?: 'Lista sem título' }}</flux:heading>
                                <flux:text class="mt-2 text-xs">
                                    Atualizada em {{ $list->updated_at->format('d/m/Y') }}
                                </flux:text>
                            </div>

                            <flux:button
                                :href="route('shopping-lists.edit', $list)"
                                variant="ghost"
                                size="sm"
                                icon="pencil-square"
                                wire:navigate
                                aria-label="Editar {{ $list->name ?: 'lista sem título' }}"
                            />
                        </div>

                        <div class="mt-6 flex-1">
                            @if ($list->items->isNotEmpty())
                                <ul class="space-y-3">
                                    @foreach ($list->items->take(4) as $item)
                                        <li class="flex min-w-0 items-center gap-2.5 text-sm text-zinc-600 dark:text-zinc-300">
                                            <span class="size-3.5 shrink-0 rounded border border-zinc-300 dark:border-zinc-600"></span>
                                            <span class="truncate">{{ $item->name }}</span>
                                            @if ($item->quantity > 1)
                                                <span class="ml-auto shrink-0 text-xs text-zinc-400">{{ $item->quantity }} {{ $item->unit }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="flex h-full min-h-20 items-center justify-center rounded-lg border border-dashed border-zinc-200 bg-zinc-50/50 px-4 text-center dark:border-zinc-700 dark:bg-zinc-800/30">
                                    <flux:text class="text-sm">Nenhum item adicionado</flux:text>
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 flex items-center border-t border-zinc-100 pt-4 dark:border-zinc-800">
                            <flux:badge size="sm" variant="pill">
                                {{ $list->items_count }} {{ $list->items_count === 1 ? 'item' : 'itens' }}
                            </flux:badge>

                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="flex min-h-96 flex-1 flex-col items-center justify-center rounded-xl border border-dashed border-zinc-300 bg-zinc-50/50 px-6 text-center dark:border-zinc-700 dark:bg-zinc-900/30">
                <div class="mb-4 grid size-12 place-items-center rounded-lg border border-zinc-200 bg-white text-zinc-500 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400">
                    <flux:icon.clipboard-document-list class="size-6" />
                </div>
                <flux:heading size="lg">Nenhuma lista criada</flux:heading>
                <flux:subheading class="mt-2 max-w-sm">Crie sua primeira lista para começar a organizar suas compras.</flux:subheading>
                <flux:button class="mt-6" :href="route('shopping-lists.create')" variant="primary" icon="plus" wire:navigate>
                    Criar lista
                </flux:button>
            </div>
        @endif
    </div>
</x-layouts::app>

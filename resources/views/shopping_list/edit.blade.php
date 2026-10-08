<x-layouts::app :title="__('Editar lista de compras')">
    @php
        $estimatedTotal = $list->items->sum(
            fn ($item) => round((float) $item->estimated_price * $item->quantity, 2)
        );
        $purchasedItemsTotal = $list->items->where('status', 2)->sum(
            fn ($item) => round((float) $item->estimated_price * $item->quantity, 2)
        );
    @endphp

    <div
        class="flex h-full w-full flex-1 flex-col gap-6"
        x-data="{
            copied: false,
            checklist: @js($clipboardContent),
            async copyChecklist() {
                try {
                    await navigator.clipboard.writeText(this.checklist)
                } catch (error) {
                    const textarea = document.createElement('textarea')
                    textarea.value = this.checklist
                    textarea.style.position = 'fixed'
                    textarea.style.opacity = '0'
                    document.body.appendChild(textarea)
                    textarea.select()
                    document.execCommand('copy')
                    textarea.remove()
                }

                this.copied = true
                setTimeout(() => this.copied = false, 2000)
            },
            finished: @js((bool) $list->purchased_at),
        }"
    >
        <div>
            <flux:button :href="route('shopping-lists.index')" variant="ghost" size="sm" icon="arrow-left" wire:navigate>
                Voltar para as listas
            </flux:button>
        </div>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="flex gap-4">
                    <livewire:update-shopping-list-title-form :shoppingList="$list"/>
                    @if($list->purchased_at)
                        <small>Comprado em: {{ $list->purchased_at->format('d-m-y H:i:s') }}</small>
                    @endif
                </div>
                <flux:subheading class="mt-1">Edite os dados e adicione os produtos da sua compra.</flux:subheading>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <flux:badge size="sm" variant="pill">
                    {{ $list->items->count() }} {{ $list->items->count() === 1 ? 'item' : 'itens' }}
                </flux:badge>

                <flux:button
                    :href="route('shopping-lists.share.whatsapp', $list)"
                    variant="filled"
                    size="sm"
                    icon="chat-bubble-left-right"
                    :disabled="$list->items->isEmpty()"
                    target="_blank"
                >
                    WhatsApp
                </flux:button>

                <flux:button
                    type="button"
                    variant="filled"
                    size="sm"
                    icon="clipboard"
                    :disabled="$list->items->isEmpty()"
                    x-on:click="copyChecklist()"
                >
                    <span x-show="!copied">Copiar lista</span>
                    <span x-show="copied" x-cloak>Copiada!</span>
                </flux:button>

                <form action="{{ route('shopping-lists.toggle-status', $list) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <flux:button
                        size="sm"
                        variant="filled"
                        type="submit"
                    >
                        <span x-show="!finished">Finalizar compra</span>
                        <span x-show="finished">Retomar compra</span>
                    </flux:button>
                </form>
            </div>
        </div>

        <flux:separator variant="subtle" />

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <section class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 p-6">
                <flux:heading>Quantidade de itens</flux:heading>
                <div class="font-bold text-2xl">{{ $list->items_count }}</div>
            </section>
            <section class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 p-6">
                <flux:heading>Valor estimado</flux:heading>
                <div class="font-bold text-2xl">R$ {{ number_format($estimatedTotal, 2, ',', '.') }}</div>
            </section>
            <section class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 p-6">
                <flux:heading>Quantidade comprada</flux:heading>
                <div class="font-bold text-2xl">{{ $list->purchased_items_count }}</div>
            </section>
            <section class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 p-6">
                <flux:heading>Valor comprado</flux:heading>
                <div class="font-bold text-2xl">R$ {{ number_format($purchasedItemsTotal, 2, ',', '.') }}</div>
            </section>
        </div>

        <flux:separator variant="subtle" />

        @if (session('status'))
            <div class="flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                <flux:icon.check-circle class="size-5 shrink-0" />
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 px-6 py-5 dark:border-zinc-700">
                <flux:heading size="lg">Itens da lista</flux:heading>
                <flux:text class="mt-1 text-sm">Adicione os produtos que pretende comprar.</flux:text>
            </div>

            @if (! $list->purchased_at)
                <form method="POST" action="{{ route('shopping-lists.items.store', ['listId' => $list]) }}" class="p-6">
                    @csrf

                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-12">
                        <div class="sm:col-span-2 lg:col-span-5">
                            <flux:input
                                name="item_name"
                                label="Item"
                                :value="old('item_name')"
                                placeholder="Ex.: Arroz"
                                required
                                autofocus
                            />
                        </div>

                        <div class="lg:col-span-2">
                            <flux:input
                                name="item_quantity"
                                type="text"
                                inputmode="decimal"
                                label="Quantidade"
                                :value="old('item_quantity', 1)"
                                placeholder="Ex.: 0,5"
                                required
                            />
                        </div>

                        <div class="lg:col-span-2">
                            <flux:select name="item_unit" label="Unidade" :value="old('item_unit', 'un')" required>
                                <flux:select.option value="un">Unidade</flux:select.option>
                                <flux:select.option value="kg">Quilograma</flux:select.option>
                                <flux:select.option value="g">Grama</flux:select.option>
                                <flux:select.option value="l">Litro</flux:select.option>
                                <flux:select.option value="ml">Mililitro</flux:select.option>
                                <flux:select.option value="pct">Pacote</flux:select.option>
                                <flux:select.option value="cx">Caixa</flux:select.option>
                            </flux:select>
                        </div>

                        <div class="lg:col-span-3">
                            <flux:input
                                name="item_estimated_price"
                                type="number"
                                min="0"
                                step="0.01"
                                label="Preço unitário"
                                :value="old('item_estimated_price')"
                                placeholder="0,00"
                                icon="currency-dollar"
                            />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end border-t border-zinc-100 pt-5 dark:border-zinc-800">
                        <flux:button type="submit" variant="primary" icon="plus">Adicionar item</flux:button>
                    </div>
                </form>
            @endif

            @if ($list->items->isEmpty())
                <div class="border-t border-zinc-200 px-6 py-12 text-center dark:border-zinc-700">
                    <div class="mx-auto mb-3 grid size-10 place-items-center rounded-lg bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                        <flux:icon.shopping-bag class="size-5" />
                    </div>
                    <flux:heading>Nenhum item adicionado</flux:heading>
                    <flux:text class="mt-1 text-sm">Use o formulário acima para começar a montar sua lista.</flux:text>
                </div>
            @else
                <div class="border-t border-zinc-200 dark:border-zinc-700">
                    <div class="hidden grid-cols-[minmax(0,1fr)_6rem_7rem_8rem_12rem] gap-4 border-b border-zinc-100 px-6 py-3 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:border-zinc-800 md:grid">
                        <span>Produto</span>
                        <span>Quantidade</span>
                        <span>Preço unitário</span>
                        <span class="text-right">Subtotal</span>
                        <span class="sr-only">Ações</span>
                    </div>

                    <livewire:shopping-list-item-table :list="$list"/>

                    <div class="flex items-center justify-between border-t border-zinc-200 bg-zinc-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800/40">
                        <flux:text class="text-sm">Total estimado</flux:text>
                        <span class="text-base font-semibold text-zinc-900 dark:text-white">R$ {{ number_format($estimatedTotal, 2, ',', '.') }}</span>
                    </div>
                </div>
            @endif
        </section>
    </div>
</x-layouts::app>

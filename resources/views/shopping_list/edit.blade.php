<x-layouts::app :title="__('Editar lista de compras')">
    @php
        $estimatedTotal = $list->items->sum(
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
        }"
    >
        <div>
            <flux:button :href="route('shopping-lists.index')" variant="ghost" size="sm" icon="arrow-left" wire:navigate>
                Voltar para as listas
            </flux:button>
        </div>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <flux:heading size="xl" level="1">{{ $list->name ?: 'Lista sem título' }}</flux:heading>
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


                <form method="POST" action="{{ route('purchases.store', ['shopping_list_id' => $list->id, 'name' => $list->name]) }}">
                    @csrf

                    <flux:button
                        type="submit"
                        variant="filled"
                        size="sm"
                        icon="shopping-cart"
                        :disabled="$list->items->isEmpty()"
                    >
                        Iniciar compra
                    </flux:button>
                </form>
            </div>
        </div>

        <flux:separator variant="subtle" />

        @if (session('status'))
            <div class="flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                <flux:icon.check-circle class="size-5 shrink-0" />
                {{ session('status') }}
            </div>
        @endif

        <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(19rem,1fr)]">
            <div class="space-y-6">
                <section class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="border-b border-zinc-200 px-6 py-5 dark:border-zinc-700">
                        <flux:heading size="lg">Itens da lista</flux:heading>
                        <flux:text class="mt-1 text-sm">Adicione os produtos que pretende comprar.</flux:text>
                    </div>

                    <form method="POST" action="{{ route('shopping-lists.items.store', $list) }}" class="p-6">
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
                            <div class="hidden grid-cols-[minmax(0,1fr)_6rem_7rem_8rem_5.5rem] gap-4 border-b border-zinc-100 px-6 py-3 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:border-zinc-800 md:grid">
                                <span>Produto</span>
                                <span>Quantidade</span>
                                <span>Preço unitário</span>
                                <span class="text-right">Subtotal</span>
                                <span class="sr-only">Ações</span>
                            </div>

                            <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                @foreach ($list->items as $item)
                                    <li class="grid grid-cols-[minmax(0,1fr)_auto] gap-2 px-6 py-4 text-sm md:grid-cols-[minmax(0,1fr)_6rem_7rem_8rem_5.5rem] md:items-center md:gap-4">
                                        <div class="flex min-w-0 items-center gap-3">
                                            <span class="size-4 shrink-0 rounded border border-zinc-300 dark:border-zinc-600"></span>
                                            <span class="truncate font-medium text-zinc-900 dark:text-white">{{ $item->name }}</span>
                                        </div>
                                        <span class="col-start-1 pl-7 text-zinc-600 dark:text-zinc-300 md:col-start-auto md:pl-0">{{ $item->quantity }} {{ $item->unit }}</span>
                                        <span class="col-start-1 pl-7 text-zinc-600 dark:text-zinc-300 md:col-start-auto md:pl-0">
                                            {{ $item->estimated_price !== null ? 'R$ '.number_format((float) $item->estimated_price, 2, ',', '.') : '—' }}
                                        </span>
                                        <span class="col-start-1 pl-7 font-medium text-zinc-900 dark:text-white md:col-start-auto md:pl-0 md:text-right">
                                            {{ $item->estimated_price !== null ? 'R$ '.number_format((float) $item->estimated_price * $item->quantity, 2, ',', '.') : '—' }}
                                        </span>

                                        <div class="col-start-2 row-start-1 row-end-5 flex items-start justify-end gap-1 md:col-start-auto md:row-auto md:items-center">
                                            <flux:modal.trigger name="edit-item-{{ $item->id }}">
                                                <flux:button variant="ghost" size="sm" icon="pencil-square" aria-label="Editar {{ $item->name }}" />
                                            </flux:modal.trigger>

                                            <flux:modal.trigger name="delete-item-{{ $item->id }}">
                                                <flux:button variant="ghost" size="sm" icon="trash" class="text-red-600! hover:bg-red-50! hover:text-red-700! dark:text-red-400! dark:hover:bg-red-950/50!" aria-label="Excluir {{ $item->name }}" />
                                            </flux:modal.trigger>
                                        </div>
                                    </li>

                                    <flux:modal
                                        name="edit-item-{{ $item->id }}"
                                        :show="($errors->has('edit_item_name') || $errors->has('edit_item_quantity') || $errors->has('edit_item_unit') || $errors->has('edit_item_estimated_price')) && (int) old('editing_item_id') === $item->id"
                                        focusable
                                        class="max-w-lg"
                                    >
                                        <form method="POST" action="{{ route('shopping-lists.items.update', [$list, $item]) }}" class="space-y-6">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="editing_item_id" value="{{ $item->id }}">

                                            <div>
                                                <flux:heading size="lg">Editar item</flux:heading>
                                                <flux:subheading class="mt-1">Atualize os dados do produto selecionado.</flux:subheading>
                                            </div>

                                            <flux:input
                                                name="edit_item_name"
                                                label="Item"
                                                :value="old('editing_item_id') == $item->id ? old('edit_item_name') : $item->name"
                                                required
                                                autofocus
                                            />

                                            <div class="grid gap-5 sm:grid-cols-2">
                                                <flux:input
                                                    name="edit_item_quantity"
                                                    type="text"
                                                    inputmode="decimal"
                                                    label="Quantidade"
                                                    :value="old('editing_item_id') == $item->id ? old('edit_item_quantity') : $item->quantity"
                                                    placeholder="Ex.: 0,5"
                                                    required
                                                />

                                                <flux:select
                                                    name="edit_item_unit"
                                                    label="Unidade"
                                                    :value="old('editing_item_id') == $item->id ? old('edit_item_unit') : $item->unit"
                                                    required
                                                >
                                                    <flux:select.option value="un">Unidade</flux:select.option>
                                                    <flux:select.option value="kg">Quilograma</flux:select.option>
                                                    <flux:select.option value="g">Grama</flux:select.option>
                                                    <flux:select.option value="l">Litro</flux:select.option>
                                                    <flux:select.option value="ml">Mililitro</flux:select.option>
                                                    <flux:select.option value="pct">Pacote</flux:select.option>
                                                    <flux:select.option value="cx">Caixa</flux:select.option>
                                                </flux:select>
                                            </div>

                                            <flux:input
                                                name="edit_item_estimated_price"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                label="Preço unitário"
                                                :value="old('editing_item_id') == $item->id ? old('edit_item_estimated_price') : $item->estimated_price"
                                                placeholder="0,00"
                                                icon="currency-dollar"
                                            />

                                            <div class="flex justify-end gap-3 border-t border-zinc-100 pt-5 dark:border-zinc-800">
                                                <flux:modal.close>
                                                    <flux:button variant="ghost">Cancelar</flux:button>
                                                </flux:modal.close>
                                                <flux:button type="submit" variant="primary">Salvar alterações</flux:button>
                                            </div>
                                        </form>
                                    </flux:modal>

                                    <flux:modal name="delete-item-{{ $item->id }}" focusable class="max-w-md">
                                        <form method="POST" action="{{ route('shopping-lists.items.destroy', [$list, $item]) }}" class="space-y-6">
                                            @csrf
                                            @method('DELETE')

                                            <div class="flex items-start gap-4">
                                                <div class="grid size-10 shrink-0 place-items-center rounded-full bg-red-100 text-red-600 dark:bg-red-950 dark:text-red-400">
                                                    <flux:icon.trash class="size-5" />
                                                </div>
                                                <div>
                                                    <flux:heading size="lg">Excluir item?</flux:heading>
                                                    <flux:subheading class="mt-2">
                                                        O item <strong class="font-medium text-zinc-900 dark:text-white">{{ $item->name }}</strong> será removido permanentemente da lista.
                                                    </flux:subheading>
                                                </div>
                                            </div>

                                            <div class="flex justify-end gap-3 border-t border-zinc-100 pt-5 dark:border-zinc-800">
                                                <flux:modal.close>
                                                    <flux:button variant="ghost">Cancelar</flux:button>
                                                </flux:modal.close>
                                                <flux:button type="submit" variant="danger">Excluir item</flux:button>
                                            </div>
                                        </form>
                                    </flux:modal>
                                @endforeach
                            </ul>

                            <div class="flex items-center justify-between border-t border-zinc-200 bg-zinc-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800/40">
                                <flux:text class="text-sm">Total estimado</flux:text>
                                <span class="text-base font-semibold text-zinc-900 dark:text-white">R$ {{ number_format($estimatedTotal, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif
                </section>
            </div>

            <aside class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-6 py-5 dark:border-zinc-700">
                    <flux:heading size="lg">Detalhes da lista</flux:heading>
                    <flux:text class="mt-1 text-sm">Informações gerais da compra.</flux:text>
                </div>

                <form method="POST" action="{{ route('shopping-lists.update') }}" class="space-y-5 p-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="list_id" value="{{ $list->id }}">
                    <flux:input name="name" label="Nome da lista" :value="old('name', $list->name)" required />

                    <div class="flex justify-end border-t border-zinc-100 pt-5 dark:border-zinc-800">
                        <flux:button type="submit" variant="primary">Salvar alterações</flux:button>
                    </div>
                </form>
            </aside>
        </div>
    </div>
</x-layouts::app>

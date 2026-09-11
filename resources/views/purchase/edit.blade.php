<x-layouts::app title="Compras">
    @php
        $priceTotal = $purchase->items->sum(
            fn ($item) => round((float) $item->price * $item->quantity, 2)
        );
        $totalItems = $purchase->items->sum('quantity');
        $shoppingListCount = $purchase->shoppingLists->count();
    @endphp

    <div class="flex h-full min-w-0 w-full max-w-full flex-1 flex-col gap-6" x-data="{ search: '' }">

        <div>
            <flux:button :href="route('purchases.index')" variant="ghost" size="sm" icon="arrow-left" wire:navigate>
                Voltar para as compras
            </flux:button>
        </div>

        <div>
            <flux:heading size="xl" level="1">{{ $purchase->name }}</flux:heading>
            <flux:subheading class="mt-1">Criado por: {{ $purchase->created_by }} </flux:subheading>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-6">
                    <div>
                        <flux:text class="text-sm">Valor total da compra</flux:text>
                        <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">R$ {{ $priceTotal }}</p>
                    </div>
                    <div class="grid size-10 place-items-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                        <flux:icon.banknotes class="size-5" />
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-6">
                    <div>
                        <flux:text class="text-sm">Itens comprados</flux:text>
                        <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $totalItems }}</p>
                    </div>
                    <div class="grid size-10 place-items-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                        <flux:icon.shopping-bag class="size-5" />
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-6 sm:col-span-2 dark:border-zinc-700 dark:bg-zinc-900 xl:col-span-1">
                <div class="flex items-center justify-between gap-6">
                    <div>
                        <flux:text class="text-sm">Listas de compras</flux:text>
                        <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-white">{{ $shoppingListCount }}</p>
                    </div>
                    <div class="grid size-10 place-items-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                        <flux:icon.calendar-days class="size-5" />
                    </div>
                </div>
            </div>
        </div>

        @if ($purchase->shoppingLists->isNotEmpty())
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <flux:heading size="lg">Minhas listas</flux:heading>
                    <flux:text class="mt-1 text-sm">Selecione uma lista para visualizar ou editar.</flux:text>
                </div>

                <div class="w-full sm:w-72">
                    <flux:input x-model="search" type="search" icon="magnifying-glass" placeholder="Buscar listas..." clearable />
                </div>
            </div>

            <div class="w-full min-w-0 max-w-full overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                <div class="w-full min-w-0 max-w-full">
                    <table class="block w-full max-w-full text-left text-sm xl:table">
                        <thead class="hidden border-b border-zinc-200 bg-zinc-50 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-400 xl:table-header-group">
                            <tr>
                                <th scope="col" class="px-6 py-3">Lista</th>
                                <th scope="col" class="px-6 py-3">Itens</th>
                                <th scope="col" class="px-6 py-3">Última atualização</th>
                                <th scope="col" class="px-6 py-3 text-right"><span class="sr-only">Ações</span></th>
                            </tr>
                        </thead>

                        <tbody class="block divide-y divide-zinc-200 dark:divide-zinc-700 xl:table-row-group xl:divide-y xl:divide-zinc-100 xl:dark:divide-zinc-800">
                            @foreach ($purchase->shoppingLists as $list)
                                @php
                                    $itemsCount = $list->items->count();
                                @endphp
                                <tr
                                    x-show="!search || @js(str($list->name ?: 'Lista sem título')->lower()).includes(search.toLowerCase())"
                                    x-transition.opacity.duration.150ms
                                    class="grid w-full min-w-0 grid-cols-2 gap-x-4 gap-y-3 p-4 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/40 xl:table-row xl:p-0"
                                >
                                    <td class="col-span-2 block min-w-0 pb-1 xl:table-cell xl:px-6 xl:py-4">
                                        <div class="truncate font-medium text-zinc-900 dark:text-white">{{ $list->name ?: 'Lista sem título' }}</div>
                                        @if ($list->items->isNotEmpty())
                                            <div class="mt-1 max-w-xs truncate text-xs text-zinc-500 dark:text-zinc-400">
                                                {{ $list->items->take(3)->pluck('name')->join(', ') }}
                                                @if ($itemsCount > 3)
                                                    e mais {{ $itemsCount - 3 }}
                                                @endif
                                            </div>
                                        @else
                                            <div class="mt-1 text-xs text-zinc-400">Nenhum item adicionado</div>
                                        @endif
                                    </td>

                                    <td class="block min-w-0 xl:table-cell xl:px-6 xl:py-4">
                                        <span class="mb-1 block text-[10px] font-medium uppercase tracking-wide text-zinc-400 xl:hidden">Itens</span>
                                        <flux:badge size="sm" variant="pill">
                                            {{ $itemsCount }} {{ $itemsCount === 1 ? 'item' : 'itens' }}
                                        </flux:badge>
                                    </td>

                                    <td class="block min-w-0 text-zinc-600 dark:text-zinc-300 xl:table-cell xl:whitespace-nowrap xl:px-6 xl:py-4">
                                        <span class="mb-1 block text-[10px] font-medium uppercase tracking-wide text-zinc-400 xl:hidden">Atualização</span>
                                        {{ $list->updated_at->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="block min-w-0 self-end xl:table-cell xl:px-6 xl:py-4">
                                        <div class="flex items-center gap-1 xl:flex-nowrap">
                                            <flux:modal.trigger name="sync-shopping-list-items-{{ $list->id }}">
                                                <flux:button variant="ghost" size="sm" icon="eye" aria-label="Visualizar {{ $list->name }}" />
                                            </flux:modal.trigger>
                                            <flux:button
                                                :href="route('shopping-lists.edit', $list)"
                                                variant="ghost"
                                                size="sm"
                                                icon="pencil-square"
                                                wire:navigate
                                                aria-label="Editar {{ $list->name ?: 'lista sem título' }}"
                                            >
                                                Editar
                                            </flux:button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @foreach ($purchase->shoppingLists as $list)
                <flux:modal
                    name="sync-shopping-list-items-{{ $list->id }}"
                    focusable
                    class="w-[calc(100vw-2rem)] max-w-lg"
                >
                    <form method="POST" action="{{ route('purchases.shopping-lists.items', ['purchase' => $purchase->id, 'shoppingList' => $list->id]) }}" class="min-w-0 space-y-6">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="shopping_list_id" value="{{ $list->id }}">

                        <flux:checkbox.group name="items">
                            @foreach ($list->items as $item)
                                <flux:checkbox :label="$item->name" :value="$item->id" :checked="$purchase->items->contains('shopping_list_item_id', $item->id)" />
                            @endforeach
                        </flux:checkbox.group>

                        <div class="flex flex-wrap justify-end gap-3 border-t border-zinc-100 pt-5 dark:border-zinc-800">
                            <flux:modal.close>
                                <flux:button variant="ghost">Cancelar</flux:button>
                            </flux:modal.close>
                            <flux:button type="submit" variant="primary">Salvar alterações</flux:button>
                        </div>
                    </form>
                </flux:modal>
            @endforeach
        @else
            <div class="flex min-h-96 flex-1 flex-col items-center justify-center rounded-xl border border-dashed border-zinc-300 bg-zinc-50/50 px-6 text-center dark:border-zinc-700 dark:bg-zinc-900/30">
                <div class="mb-4 grid size-12 place-items-center rounded-lg border border-zinc-200 bg-white text-zinc-500 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400">
                    <flux:icon.clipboard-document-list class="size-6" />
                </div>
                <flux:heading size="lg">Nenhuma lista de compras associada</flux:heading>
            </div>
        @endif

        <div class="mb-3">
            <section class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="border-b border-zinc-200 px-6 py-5 dark:border-zinc-700">
                        <flux:heading size="lg">Itens da lista</flux:heading>
                        <flux:text class="mt-1 text-sm">Adicione os produtos que pretende comprar.</flux:text>
                    </div>
            <form action="{{ route('purchases.items.store', $purchase) }}", method="post" class="p-6">
                @csrf
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-12">
                    <div class="sm:col-span-2 lg:col-span-5">
                        <flux:input name="name" label="Nome do produto" />
                    </div>
                    <div class="sm:col-span-2 lg:col-span-5">
                    <flux:input name="price" label="Preço" />
                    </div>
                    <div class="sm:col-span-2 lg:col-span-5">
                    <flux:input name="quantity" label="Quantidade" />
                    </div>
                    <div class="sm:col-span-2 lg:col-span-5">
                    <flux:select name="unit" label="Unidade" :value="old('unit', 'un')" required>
                        <flux:select.option value="un">Unidade</flux:select.option>
                        <flux:select.option value="kg">Quilograma</flux:select.option>
                        <flux:select.option value="g">Grama</flux:select.option>
                        <flux:select.option value="l">Litro</flux:select.option>
                        <flux:select.option value="ml">Mililitro</flux:select.option>
                        <flux:select.option value="pct">Pacote</flux:select.option>
                        <flux:select.option value="cx">Caixa</flux:select.option>
                    </flux:select>
                    </div>
                    </div>

                    <div class="pt-6">
                        <flux:button type="submit" variant="primary" icon="plus">Adicionar item</flux:button>
                    </div>
            </form>
            </section>
        </div>

        <div class="border-t border-zinc-200 dark:border-zinc-700">
            <div class="hidden grid-cols-[minmax(0,1fr)_6rem_7rem_8rem_5.5rem] gap-4 border-b border-zinc-100 px-6 py-3 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:border-zinc-800 md:grid">
                <span>Produto</span>
                <span>Quantidade</span>
                <span>Preço unitário</span>
                <span class="text-right">Subtotal</span>
                <span class="sr-only">Ações</span>
            </div>

            <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @foreach ($purchase->items as $item)
                    <li class="grid grid-cols-[minmax(0,1fr)_auto] gap-2 px-6 py-4 text-sm md:grid-cols-[minmax(0,1fr)_6rem_7rem_8rem_5.5rem] md:items-center md:gap-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="size-4 shrink-0 rounded border border-zinc-300 dark:border-zinc-600"></span>
                            <span class="truncate font-medium text-zinc-900 dark:text-white">{{ $item->name }}</span>
                        </div>
                        <span class="col-start-1 pl-7 text-zinc-600 dark:text-zinc-300 md:col-start-auto md:pl-0">{{ $item->quantity }} {{ $item->unit }}</span>
                        <span class="col-start-1 pl-7 text-zinc-600 dark:text-zinc-300 md:col-start-auto md:pl-0">
                            {{ 'R$ '.number_format((float) $item->price, 2, ',', '.') }}
                        </span>
                        <span class="col-start-1 pl-7 font-medium text-zinc-900 dark:text-white md:col-start-auto md:pl-0 md:text-right">
                            {{ 'R$ '.number_format((float) $item->price * $item->quantity, 2, ',', '.') }}
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
                    <!-- tem coisa errada aqui (a rota) -->
                        <form method="POST" action="{{ route('purchases.items.store', [$purchase, $item]) }}" class="space-y-6">
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
                                    :value="old('editing_item_id') == $item->id ? old('edit_item_unit') : $item->unit->value"
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
                        <form method="POST" action="{{ route('purchases.items.destroy', [$purchase, $item]) }}" class="space-y-6">
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

                <div class="flex items-center justify-between border-t border-zinc-200 bg-zinc-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800/40">
                    <flux:text class="text-sm">Total estimado</flux:text>
                    <span class="text-base font-semibold text-zinc-900 dark:text-white">R$ {{ number_format($priceTotal, 2, ',', '.') }}</span>
                </div>
            </ul>
        </div>
    </div>
</x-layouts::app>

<x-layouts::app title="Iniciar compra">
    <main class="mx-auto w-full max-w-2xl flex-1 px-4 py-6 sm:px-6 lg:py-10">
        <a href="{{ route('purchases.index') }}" wire:navigate class="mb-6 inline-flex items-center gap-2 text-sm text-zinc-600 transition hover:text-zinc-950 dark:text-zinc-400 dark:hover:text-white">
            <flux:icon.arrow-left class="size-4" />
            Voltar para a listagem de compras
        </a>

        <div class="flex flex-col gap-6 rounded-2xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 p-6 sm:p-8">
            <div>
                <flux:heading size="xl" level="1">Criar compra</flux:heading>
                <flux:subheading>Inicie sua compra com a opção de associar a várias listas de compras</flux:heading>
            </div>

            <form action="{{ route('purchases.store') }}" method="POST" class="flex flex-col gap-4">
                <flux:input name="name" label="Título para a compra"></flux:input>

                <ul>
                    @foreach ($shoppingLists as $shoppingList)
                        <li>
                            {{ $shoppingList->name }}
                        </li>
                    @endforeach
                </ul>

                <div class="flex justify-end">
                    <flux:button variant="ghost" :href="route('purchases.index')" wire:navigate>Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Criar compra</flux:button>
                </div>
            </form>
        </div>
    </main>
</x-layouts::app>
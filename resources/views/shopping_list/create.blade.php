<x-layouts::app :title="__('Nova lista de compras')">
    <main class="mx-auto w-full max-w-2xl flex-1 px-4 py-6 sm:px-6 lg:py-10">
        <a href="{{ route('shopping-lists.index') }}" wire:navigate class="mb-6 inline-flex items-center gap-2 text-sm text-zinc-600 transition hover:text-zinc-950 dark:text-zinc-400 dark:hover:text-white">
            <flux:icon.arrow-left class="size-4" />
            Voltar para as listas
        </a>

        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 sm:p-8">
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-950 dark:text-white">Criar lista</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Dê um nome para identificar sua nova lista de compras.</p>

            <form method="POST" action="{{ route('shopping-lists.store') }}" class="mt-8 space-y-6">
                @csrf
                <flux:input name="name" label="Nome da lista" :value="old('name')" placeholder="Ex.: Compras da semana" required autofocus />

                <div class="flex justify-end gap-3 border-t border-zinc-100 pt-6 dark:border-zinc-800">
                    <flux:button :href="route('shopping-lists.index')" variant="ghost" wire:navigate>Cancelar</flux:button>
                    <flux:button type="submit" variant="primary">Criar lista</flux:button>
                </div>
            </form>
        </div>
    </main>
</x-layouts::app>

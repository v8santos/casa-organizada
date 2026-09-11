<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div>
            <flux:heading size="xl" level="1">Dashboard</flux:heading>
            <flux:subheading class="mt-1">Acesse rapidamente os recursos do ambiente.</flux:subheading>
        </div>

        <flux:separator variant="subtle" />

        <div class="grid items-start gap-6 lg:grid-cols-2 xl:grid-cols-3">
            <section class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="grid size-10 place-items-center rounded-lg bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                    <flux:icon.clipboard-document-list class="size-5" />
                </div>
                <flux:heading size="lg" class="mt-5">Listas de compras</flux:heading>
                <flux:text class="mt-2 text-sm">Crie listas, adicione produtos e compartilhe pelo WhatsApp.</flux:text>
                <flux:button :href="route('shopping-lists.index')" variant="primary" size="sm" class="mt-5" wire:navigate>
                    Acessar listas
                </flux:button>
            </section>
        </div>
    </div>
</x-layouts::app>

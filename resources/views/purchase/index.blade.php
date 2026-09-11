<x-layouts::app title="Compras">
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl" level="1">
                    Compras
                </flux:heading>
                <flux:subheading class="mt-2">
                    Acompanhe suas compras iniciadas
                </flux:subheading>
            </div>
            <flux:button
                variant="primary"
                icon="plus"
                :href="route('purchases.create')"
                wire:navigate
            >
                Iniciar compra
            </flux:button>
        </div>

        <flux:separator variant="subtle"/>

        <div class="flex gap-4 flex-wrap">
            @if ($purchases->isEmpty())
                <span class="text-center w-full">Nenhuma compra encontrada</span>
            @else
                @foreach ($purchases as $purchase)
                    <article class="w-full sm:w-[calc(50%-0.5rem)] lg:w-80 border border-zinc-200 rounded-xl p-5 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                        <div class="flex justify-between items-start gap-4">
                            <div class="min-w-0">
                                <flux:heading size="lg" class="truncate">
                                    {{ $purchase->name }}
                                </flux:heading>
                                <flux:text class="text-xs">
                                    Criado por: {{ $purchase->created_by }} 
                                </flux:text>
                            </div>

                            <div>
                                <flux:button
                                size="sm"
                                icon="pencil-square"
                                variant="ghost"
                                :href="route('purchases.edit', $purchase)"
                                wire:navigate
                                />
                            </div>
                        </div>
                    </article>
                @endforeach
            @endif
        </div>
    </div>
</x-layouts::app>
<x-layouts::app title="Criar grupo familiar">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex justify-between">
            <div>
                <flux:heading size="xl" level="1">Grupo familiar</flux:heading>
                <flux:subheading class="mt-1">Gerencie e acesse seus grupos.</flux:subheading>
            </div>
            <flux:button
                variant="primary"
                class="cursor-pointer mt-4"
                :href="route('households.create')"
            >
                Adicionar grupo
            </flux:button>
        </div>
        <article>
            <livewire:select-household :households="$households"/>
        </article>
    </div>
</x-layouts::app>
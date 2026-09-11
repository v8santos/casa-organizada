<x-layouts::app title="Criar grupo familiar">
    <article class="flex">
        <form action="{{ route('households.set') }}" method="POST">
            @csrf
            <flux:radio.group name="household_id" label="Escolha com qual grupo familiar deseja acessar a plataforma">
                @foreach ($households as $household)
                    <flux:radio :value="$household->id" :label="$household->name" />
                @endforeach
            </flux:radio-group>

            <flux:button
                variant="primary"
                class="cursor-pointer mt-4"
                type="submit"
            >
                Acessar
            </flux:button>
        </form>
    </article>
</x-layouts::app>
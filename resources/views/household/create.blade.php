<x-layouts::app title="Criar grupo familiar">
    <form action="{{ route('households.store') }}" method="POST">
        @csrf
        <flux:input name="name" placeholder="Insira o nome do grupo familiar"/>
        <flux:button type="submit" variant="primary">Criar grupo</flux:button>
    </form>
</x-layouts::app>
<x-layouts::app.sidebar :title="$title ?? null">
    
    <flux:header class="flex justify-between">
        {{-- TODO: colocar isso aqui no padrão do usuário --}}
        <div>{{ now()->timezone('America/Sao_Paulo')->format('d-m-Y') }}</div>
        <livewire:balance-control />
    </flux:header>
    <flux:main class="min-w-0">
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>

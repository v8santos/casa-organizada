<x-layouts::app.sidebar :title="$title ?? null">
    @php
        $currentHousehold = request()->household();
    @endphp
    <flux:header class="flex justify-between">
        {{-- TODO: colocar isso aqui no padrão do usuário --}}
        <div>{{ now()->timezone('America/Sao_Paulo')->format('d-m-Y') }}</div>
        <div>Saldo: R$ {{ isset($currentHousehold) ? number_format($currentHousehold->balance, 2, ',', '.') : '--,--' }}</div>
    </flux:header>
    <flux:main class="min-w-0">
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>

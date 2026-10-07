<?php

use App\Models\Household;
use Livewire\Component;

new class extends Component
{
    public $households;

    public function setHousehold(int $householdId)
    {
        $household = Household::findOrFail($householdId);

        cookie()->queue(
            "household_id",
            $household->id,
            60 * 24
        );

        redirect()->route('dashboard');
    }
};
?>

<div class="flex justify-center gap-4">
    @if ($households->isNotEmpty())
        @foreach ($households as $household)
            <flux:button
                type="button"
                class="p-6 cursor-pointer"
                wire:click="setHousehold({{ $household->id }})"
            >
                {{ $household->name }}
            </flux:button>
        @endforeach
    @else
        {{-- TODO: [style] Da pra deixar isso aqui mais bonito --}}
        <div class="border p-4 rounded-lg">Nenhum grupo familiar encontrado!</div>
    @endif
</div>
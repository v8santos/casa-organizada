<?php

use App\Models\ShoppingList;
use Livewire\Component;

new class extends Component
{
    public ShoppingList $shoppingList;
    public string $name;
    public bool $editingTitle = false;

    public function mount(ShoppingList $shoppingList): void
    {
        $this->shoppingList = $shoppingList;
        $this->name = $shoppingList->name;
    }

    public function editTitle()
    {
        $this->editingTitle = true;
    }

    public function saveTitle()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $this->shoppingList->update([
            'name' => $this->name,
        ]);

        $this->editingTitle = false;
    }
};
?>

<div>
    @if ($editingTitle)
        <input
            wire:model="name"
            wire:blur="saveTitle"
            wire:keydown.enter="saveTitle"
            class="text-2xl font-bold border-b outline-none"
            autofocus
        />
    @else
        <flux:heading
            wire:click="editTitle"
            size="xl"
            level="1"
            class="border-b"
        >
            {{ $shoppingList->name ?: 'Lista sem título' }}
        </flux:heading>
    @endif
</div>

<?php

use App\Models\Household;
use Livewire\Component;

new class extends Component {
    public ?Household $currentHousehold;
    public string $balance;

    public function mount()
    {
        $this->currentHousehold = request()->household();
        if ($this->currentHousehold) {
            $this->balance = number_format($this->currentHousehold->balance, 2, ',', '.');
        }
    }

    public function updateBalance()
    {
        $balance = str_replace(['.', ','], ['', '.'], $this->balance);
        $this->currentHousehold->update([
            'balance' => $balance
        ]);
    }
};
?>

<div>
    @if ($currentHousehold)
        <flux:modal.trigger name="edit-household-balance">
            <flux:button class="cursor-pointer">
                Saldo: R$ {{ $balance }}
            </flux:button>
        </flux:modal.trigger>

        <flux:modal name="edit-household-balance" focusable class="max-w-md">
            <div class="flex items-center gap-4">
                <div
                    class="grid size-10 shrink-0 place-items-center rounded-full bg-red-100 text-green-600 dark:bg-green-950 dark:text-green-400">
                    <flux:icon.banknotes class="size-5" />
                </div>
                <div>
                    <flux:heading size="lg">Alterar o saldo da conta</flux:heading>
                </div>
            </div>

            <form wire:submit="updateBalance">
                <flux:input.group class="pt-3">
                    <flux:input.group.prefix>R$</flux:input.group.prefix>
                    <flux:input x-mask:dynamic="$money($input, ',')" wire:model="balance" />
                </flux:input.group>

                <div class="flex justify-end gap-3 border-t border-zinc-100 pt-5 dark:border-zinc-800">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancelar</flux:button>
                    </flux:modal.close>
                    <flux:modal.close>
                        <flux:button variant="primary" type="submit">Confirmar</flux:button>
                    </flux:modal.close>
                </div>
            </form>
        </flux:modal>
    @endif
</div>
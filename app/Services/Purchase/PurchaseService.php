<?php

namespace App\Services\Purchase;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class PurchaseService
{
    public function getAllPurchases(int $householdId): Collection
    {
        return Purchase::query()
            ->whereHas('shoppingList', function ($query) use ($householdId) {
                $query->where('household_id', $householdId);
            })
            ->latest('updated_at')
            ->get();
    }

    public function getPurchaseById(int $householdId, $purchaseId): Purchase
    {
        return Purchase::query()
            ->with('items', 'shoppingList', 'shoppingList.items')
            ->whereHas('shoppingList', function ($query) use ($householdId) {
                $query->where('household_id', $householdId);
            })
            ->withCount('items')
            ->find($purchaseId);
    }

    public function createPurchase(string $purchaseName, int $userId, int $shoppingListId): Purchase
    {
        $purchase = Purchase::create([
            'name' => $purchaseName,
            'owner_id' => $userId,
            'purchased_at' => null,
            'shopping_list_id' => $shoppingListId,
        ]);

        return $purchase;
    }

    public function addPurchaseItems(Purchase $purchase, array $items, User $user, ?int $shoppingListItemId): ?Collection
    {
        if ($purchase->owner_id !== $user->id) {
            logger()->info('erro id');
            return null;
        }

        foreach ($items as $item) {
            $this->addItem($purchase, $item, $shoppingListItemId);
        }

        $purchase->touch();

        return $purchase->items;
    }

    private function addItem(Purchase $purchase, array $data, ?int $shoppingListItemId): PurchaseItem
    {
        return $purchase->items()->create([
            'name' => $data['name'],
            'price'  => $data['price'],
            'quantity' => $data['quantity'],
            'unit' => $data['unit'],
            'shopping_list_item_id' => $shoppingListItemId,
        ]);
    }

    public function syncItems(Purchase $purchase, User $user, ShoppingList $shoppingList, ?array $itemIds): void
    {
        if (! $user->households()->find($shoppingList->household_id)) {
            throw new Exception('Usuário não autorizado', 403);
        }

        DB::transaction(function () use ($purchase, $shoppingList, $itemIds) {
            if (empty($itemIds)) {
                $purchase->items()
                    ->whereHas('shoppingListItem', fn ($query) => 
                        $query->where('shopping_list_id', $shoppingList->id)
                    )
                    ->delete();
                return ;
            }

            // pegamos os itens da lista de compras aberta que batem
            // com o que foi passado na requisição
            $shoppingListItems = $shoppingList->items()
                ->whereIn('id', $itemIds)
                ->get();

            $validIds = $shoppingListItems->pluck('id');

            // vamos deletar dentro dos seguintes requisitos:
            // onde: itens da compra que tenham relação com itens da lista de compras aberta
            // & que não estão na lista de itens verificada anteriormente
            $purchase->items()
                ->whereHas('shoppingListItem', fn ($query) => 
                    $query->where('shopping_list_id', $shoppingList->id)
                )
                ->whereNotIn('shopping_list_item_id', $validIds)
                ->delete();

            // pegamos os ids de itens que ainda restaram na lista
            // e estão na lista verifica
            $existingIds = $purchase->items()
                ->whereIn('shopping_list_item_id', $validIds)
                ->pluck('shopping_list_item_id');

            // filtramos para pegar apenas itens que ainda não estão atrelados
            $itemsToAdd = $shoppingListItems->whereNotIn('id', $existingIds);
            // aqui vamos rodar em cada item para gerar uma lista com colunas e valores (key, value)
            // adequado de shopping list items para purchase items
            $purchase->items()->createMany(
                $itemsToAdd->map(fn ($item) => [
                    'shopping_list_item_id' => $item->id,
                    'name' => $item->name,
                    'price'  => $item->estimated_price,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                ])->all()
            );

            $purchase->touch();
        });
    }

    public function deleteItem(Purchase $purchase, User $user, $itemId): void
    {
        if ($purchase->owner_id !== $user->id) {
            throw new Exception('Acesso negado', 403);
        }

        $itemToDelete = $purchase->items()->findOrFail($itemId);

        if (! $itemToDelete) {
            throw new Exception('Item não existe na base', 404);
        }

        $itemToDelete->delete();
    }
}
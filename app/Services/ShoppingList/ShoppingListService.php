<?php

namespace App\Services\ShoppingList;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Database\Eloquent\Collection;

final class ShoppingListService
{
    public function getAllShoppingLists(int $householdId): Collection
    {
        return ShoppingList::query()
            ->where('household_id', $householdId)
            ->with(['items' => fn ($query) => $query->latest()->limit(5)])
            ->withCount('items')
            ->latest('updated_at')
            ->get();
    }

    public function getShoppingListById(int $id, int $householdId): ?ShoppingList
    {
        return ShoppingList::query()
            ->where('household_id', $householdId)
            ->with('items')
            ->find($id);
    }

    public function createShoppingList(array $data): ShoppingList
    {
        return ShoppingList::create([
            'name' => $data['name'],
            'household_id' => $data['household_id'],
            'owner_id' => $data['owner_id'],
        ]);
    }

    public function addItemToShoppingList(ShoppingList $shoppingList, array $itemData): ShoppingListItem
    {
        $item = $shoppingList->items()->create([
            'name' => $itemData['item_name'],
            'estimated_price' => $itemData['item_estimated_price'] ?? null,
            'quantity' => $itemData['item_quantity'],
            'unit' => $itemData['item_unit'],
        ]);

        $shoppingList->touch();

        return $item;
    }

    public function updateShoppingList(ShoppingList $shoppingList, array $data): ShoppingList
    {
        $shoppingList->update([
            'name' => $data['name'],
        ]);

        return $shoppingList->refresh();
    }

    public function updateShoppingListItem(ShoppingList $shoppingList, int $itemId, array $data): ShoppingListItem
    {
        $item = $shoppingList->items()->findOrFail($itemId);

        $item->update([
            'name' => $data['edit_item_name'],
            'estimated_price' => $data['edit_item_estimated_price'] ?? null,
            'quantity' => $data['edit_item_quantity'],
            'unit' => $data['edit_item_unit'],
        ]);

        $shoppingList->touch();

        return $item->refresh();
    }

    public function exportAsPlainText(ShoppingList $shoppingList): string
    {
        $lines = $shoppingList->items->map(function (ShoppingListItem $item): string {
            $quantity = $item->quantity.' '.$item->unit;
            $price = $item->estimated_price !== null
                ? ' · R$ '.number_format((float) $item->estimated_price * $item->quantity, 2, ',', '.')
                : '';

            return $item->name.' — '.$quantity.$price;
        });

        $header = $shoppingList->name ?: 'Lista de compras';

        return $header.PHP_EOL.PHP_EOL.$lines->implode(PHP_EOL).PHP_EOL;
    }

    public function exportAsWhatsAppMessage(ShoppingList $shoppingList): string
    {
        $listText = $this->exportAsPlainText($shoppingList);
        $lines = explode(PHP_EOL, rtrim($listText));
        $lines[0] = '*'.$lines[0].'*';

        return implode(PHP_EOL, $lines);
    }

    public function removeItemFromShoppingList(ShoppingList $shoppingList, int $itemId): void
    {
        $item = $shoppingList->items()->findOrFail($itemId);
        $item->delete();
        $shoppingList->touch();
    }
}

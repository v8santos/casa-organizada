<?php

namespace App\Models;

use App\Enums\ItemUnitEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    protected $fillable = [
        'name',
        'price',
        'quantity',
        'unit',
        'purchase_id',
        'shopping_list_item_id',
    ];

    public function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'double',
            'unit' => ItemUnitEnum::class,
        ];
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function shoppingListItem(): BelongsTo
    {
        return $this->belongsTo(ShoppingListItem::class);
    }
}

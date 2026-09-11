<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoppingListItem extends Model
{
    protected $fillable = [
        'name',
        'estimated_price',
        'quantity',
        'unit',
        'shopping_list_id',
    ];

    protected function casts(): array
    {
        return [
            'estimated_price' => 'decimal:2',
            'quantity' => 'double',
        ];
    }

    public function shoppingList(): BelongsTo
    {
        return $this->belongsTo(ShoppingList::class);
    }
}

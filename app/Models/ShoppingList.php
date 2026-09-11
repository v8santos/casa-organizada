<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShoppingList extends Model
{
    protected $fillable = [
        'name',
        'household_id',
        'owner_id',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ShoppingListItem::class);
    }

    public function item(): HasMany
    {
        return $this->items();
    }

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }
}

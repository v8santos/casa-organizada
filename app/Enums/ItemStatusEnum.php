<?php

namespace App\Enums;

enum ItemStatusEnum: int
{
    case PENDING = 0;
    case IN_CART = 1;
    case PURCHASED = 2;
    case UNAVAILABLE = 3;
    case CANCELLED = 4;

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendente',
            self::IN_CART => 'No carrinho',
            self::PURCHASED => 'Comprado',
            self::UNAVAILABLE => 'Indisponível',
            self::CANCELLED => 'Cancelado',
        };
    }

    public function purchased(): bool
    {
        return $this === self::PURCHASED;
    }
}
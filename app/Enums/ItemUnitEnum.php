<?php

namespace App\Enums;

enum ItemUnitEnum: string
{
   case Unidade = 'un';
   case Quilograma = 'kg';
   case Grama = 'g';
   case Litro = 'l';
   case Mililitro = 'ml';
   case Pacote = 'pct';
   case Caixa = 'cx';

   public function label(float|int $quantity = 1): string
   {
      return match ($this) {
         self::Unidade => $quantity == 1 ? 'Unidade' : 'Unidades',
         self::Quilograma => 'Kg',
         self::Grama => 'g',
         self::Litro => $quantity == 1 ? 'Litro' : 'Litros',
         self::Mililitro => 'ml',
         self::Pacote => $quantity == 1 ? 'Pacote' : 'Pacotes',
         self::Caixa => $quantity == 1 ? 'Caixa' : 'Caixas',
      };
   }
}
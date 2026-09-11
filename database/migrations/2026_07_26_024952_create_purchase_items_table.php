<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->decimal('quantity', 6, 3);
            $table->string('unit');
            // item dependente de purchase, será deletado em cascata
            $table->foreignId('purchase_id')
                ->constrained()
                ->cascadeOnDelete();
            /**
             * item não dependente de shopping_list_items,
             * a tabela atual só atualiza esse valor para nulo na cascata
             * */
            $table->foreignId('shopping_list_item_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};

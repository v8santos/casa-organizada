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
        Schema::create('shopping_list_items', function (Blueprint $table) {
            $table->id();

            $table->string("name");
            $table->decimal("estimated_price", 8, 2)->nullable();
            $table->decimal("quantity", 6, 3);
            $table->string("unit");
            /**
             * aqui queremos que os itens sejam removidos
             * quando a lista for apagada.
             * Não precisamos de independência dos itens
             */
            $table->foreignId("shopping_list_id")
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_list_items');
    }
};

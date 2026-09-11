<?php

use App\Models\User;
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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            /**
             * mesmo que o criador da compra seja apagado da base
             * queremos que o as compras continuem existindo.
             * Aqui seguimos com o mesmo comportamento de shopping_lists
             */
            $table->foreignIdFor(User::class, 'creator_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('shopping_list_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->date('purchased_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};

<?php

use App\Models\Household;
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
        Schema::create('shopping_lists', function (Blueprint $table) {
            $table->id();
            
            $table->string("name")->nullable();
            $table->foreignIdFor(Household::class, "household_id")
                ->constrained()
                ->restrictOnDelete();
            /**
             * mesmo que o criador da lista seja apagado da base
             * queremos que o as listas continuem existindo
             */
            $table->foreignIdFor(User::class, "owner_id")
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
        Schema::dropIfExists('shopping_lists');
    }
};

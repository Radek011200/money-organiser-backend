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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nazwa wydatku
            $table->decimal('amount', 10, 2); // Kwota wydatku
            $table->string('category')->nullable(); // Kategoria wydatku
            $table->date('date'); // Data
            $table->timestamps(); // created_at, updated_at

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};

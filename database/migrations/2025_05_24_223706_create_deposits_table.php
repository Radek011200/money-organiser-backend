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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nazwa wpłaty
            $table->decimal('amount', 10, 2); // Kwota wpłaty
            $table->unsignedBigInteger('category_id')->nullable(); // Kategoria wpłaty
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->date('date'); // Data
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};

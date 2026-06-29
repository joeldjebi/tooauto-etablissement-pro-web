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
        Schema::create('communes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ville_id'); // Colonne ville_id
            $table->string('nom');
            $table->foreign('ville_id')
                  ->references('id')
                  ->on('villes') // Remplacer 'ville' par 'villes'
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communes');
    }
};
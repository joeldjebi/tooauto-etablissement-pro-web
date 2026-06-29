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
        Schema::create('etablissements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile');
            $table->string('email');
            $table->text('description');
            $table->string('logo')->nullable();
            $table->string('cover')->nullable();
            $table->integer('logo_where_is_create')->default(0)->comment("0=> crée sur le web, 1=> crée sur le mobile");
            $table->integer('cover_where_is_create')->default(0)->comment("0=> crée sur le web, 1=> crée sur le mobile");
            $table->string('adresse');
            $table->string('longitude');
            $table->string('latitude');
            $table->foreignId('professionnel_id')
                ->references('id')
                ->on('professionnels') 
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('pays_id')
                ->references('id')
                ->on('pays') 
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('ville_id')
                ->references('id')
                ->on('villes') 
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('commune_id')
                ->references('id')
                ->on('communes') 
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->integer('statut')->default(1)->comment("0=> desactiver, 1=> actif");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etablissements');
    }
};
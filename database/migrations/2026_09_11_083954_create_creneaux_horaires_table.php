<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creneaux_horaires', function (Blueprint $table) {
            $table->id();
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->string('libelle')->nullable(); // ex: "1ère heure"
            $table->unsignedTinyInteger('ordre'); // pour l'affichage dans l'ordre
            $table->enum('type', ['cours', 'recreation', 'pause'])->default('cours');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creneaux_horaires');
    }
};
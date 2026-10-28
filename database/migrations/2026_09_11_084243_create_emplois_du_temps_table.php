<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emplois_du_temps', function (Blueprint $table) {
            $table->id();
            $table->enum('jour', ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi']);
            $table->foreignId('creneau_horaire_id')->constrained('creneaux_horaires')->cascadeOnDelete();
            $table->foreignId('classe_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('professeur_id')->constrained('professeurs')->cascadeOnDelete();
            $table->string('matiere');
            $table->enum('type_seance', ['cours', 'td'])->default('cours');
            $table->timestamps();

            // une classe ne peut pas avoir 2 séances au même jour/créneau
            $table->unique(['jour', 'creneau_horaire_id', 'classe_id'], 'unique_classe_creneau');

            // un professeur ne peut pas être sur 2 séances au même jour/créneau
            $table->unique(['jour', 'creneau_horaire_id', 'professeur_id'], 'unique_prof_creneau');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emplois_du_temps');
    }
};

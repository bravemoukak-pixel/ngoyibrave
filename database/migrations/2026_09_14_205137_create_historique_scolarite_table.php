<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historique_scolarite', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained('eleves')->cascadeOnDelete();
            $table->foreignId('classe_id')->constrained('classes')->cascadeOnDelete();
            $table->string('annee_scolaire');
            $table->timestamps();

            $table->unique(['eleve_id', 'annee_scolaire'], 'unique_eleve_annee');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historique_scolarite');
    }
};

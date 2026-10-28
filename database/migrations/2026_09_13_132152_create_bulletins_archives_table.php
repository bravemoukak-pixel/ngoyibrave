<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulletins_archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained('eleves')->cascadeOnDelete();
            $table->unsignedTinyInteger('trimestre');
            $table->string('annee_scolaire'); // ex: "2026-2027"
            $table->string('chemin_fichier');
            $table->timestamps();

            $table->unique(['eleve_id', 'trimestre', 'annee_scolaire'], 'unique_bulletin_archive');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulletins_archives');
    }
};

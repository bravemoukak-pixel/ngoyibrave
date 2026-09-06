<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assiduites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('trimestre');
            $table->unsignedInteger('absences_justifiees')->default(0);
            $table->unsignedInteger('absences_injustifiees')->default(0);
            $table->unsignedInteger('retards_justifies')->default(0);
            $table->unsignedInteger('retards_injustifies')->default(0);
            $table->text('avertissements')->nullable();
            $table->text('blame')->nullable();
            $table->timestamps();

            $table->unique(['eleve_id', 'trimestre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assiduites');
    }
};

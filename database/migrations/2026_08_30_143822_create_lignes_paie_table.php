<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lignes_paie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professeur_id')->constrained()->cascadeOnDelete();
            $table->date('mois');
            $table->enum('type', ['prime', 'deduction']);
            $table->string('libelle');
            $table->decimal('montant', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lignes_paie');
    }
};

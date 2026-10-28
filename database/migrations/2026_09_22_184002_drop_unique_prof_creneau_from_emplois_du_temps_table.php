<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emplois_du_temps', function (Blueprint $table) {
            $table->dropUnique('unique_prof_creneau');
        });
    }

    public function down(): void
    {
        Schema::table('emplois_du_temps', function (Blueprint $table) {
            $table->unique(['jour', 'creneau_horaire_id', 'professeur_id'], 'unique_prof_creneau');
        });
    }
};

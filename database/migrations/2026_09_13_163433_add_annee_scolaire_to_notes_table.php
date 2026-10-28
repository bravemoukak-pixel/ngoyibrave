<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->string('annee_scolaire')->nullable()->after('trimestre');
        });

        // Attribue l'année scolaire actuelle à toutes les notes existantes
        $mois = now()->month;
        $annee = now()->year;
        if ($mois >= 10) {
            $anneeScolaire = $annee . '-' . ($annee + 1);
        } elseif ($mois <= 6) {
            $anneeScolaire = ($annee - 1) . '-' . $annee;
        } else {
            $anneeScolaire = $annee . '-' . ($annee + 1);
        }

        DB::table('notes')->whereNull('annee_scolaire')->update(['annee_scolaire' => $anneeScolaire]);
    }

    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('annee_scolaire');
        });
    }
};

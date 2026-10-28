<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creneaux_horaires', function (Blueprint $table) {
            $table->enum('grille', ['standard', 'primaire_a', 'primaire_b'])->default('standard')->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('creneaux_horaires', function (Blueprint $table) {
            $table->dropColumn('grille');
        });
    }
};

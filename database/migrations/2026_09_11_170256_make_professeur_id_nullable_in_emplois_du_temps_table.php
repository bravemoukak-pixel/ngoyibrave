<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emplois_du_temps', function (Blueprint $table) {
            $table->dropForeign(['professeur_id']);
            $table->unsignedBigInteger('professeur_id')->nullable()->change();
            $table->foreign('professeur_id')->references('id')->on('professeurs')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('emplois_du_temps', function (Blueprint $table) {
            $table->dropForeign(['professeur_id']);
            $table->unsignedBigInteger('professeur_id')->nullable(false)->change();
            $table->foreign('professeur_id')->references('id')->on('professeurs')->cascadeOnDelete();
        });
    }
};

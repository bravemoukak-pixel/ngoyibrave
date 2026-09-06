<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametres', function (Blueprint $table) {
            $table->id();
            $table->decimal('taux_horaire_college', 10, 2)->default(1600);
            $table->decimal('taux_horaire_lycee', 10, 2)->default(2000);
            $table->timestamps();
        });

        DB::table('parametres')->insert([
            'taux_horaire_college' => 1600,
            'taux_horaire_lycee' => 2000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('parametres');
    }
};

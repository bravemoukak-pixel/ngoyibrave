<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parametres', function (Blueprint $table) {
            $table->string('telephone_ecole_1')->nullable();
            $table->string('telephone_ecole_2')->nullable();
        });

        DB::table('parametres')->update([
            'telephone_ecole_1' => '06 685 74 88',
        ]);
    }

    public function down(): void
    {
        Schema::table('parametres', function (Blueprint $table) {
            $table->dropColumn(['telephone_ecole_1', 'telephone_ecole_2']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parent_eleves', function (Blueprint $table) {
            $table->decimal('montant_annuel_override', 10, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('parent_eleves', function (Blueprint $table) {
            $table->dropColumn('montant_annuel_override');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'secretaire', 'comptable', 'primaire', 'direction', 'parent') NOT NULL");

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('parent_eleve_id')->nullable()->after('role')->constrained('parent_eleves')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['parent_eleve_id']);
            $table->dropColumn('parent_eleve_id');
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'secretaire', 'comptable', 'primaire', 'direction') NOT NULL");
    }
};

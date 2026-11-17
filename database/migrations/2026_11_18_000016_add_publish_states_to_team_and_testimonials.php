<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        foreach (['team_members', 'testimonials'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('status', 20)->default('published')->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['team_members', 'testimonials'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        foreach (['posts', 'pages', 'services'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('focus_keyword', 160)->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['posts', 'pages', 'services'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('focus_keyword');
            });
        }
    }
};

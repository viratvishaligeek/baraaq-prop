<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            // Nullable unique slot permits many non-homepage pages and exactly one primary page.
            $table->string('homepage_key', 20)->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->dropUnique(['homepage_key']);
            $table->dropColumn('homepage_key');
        });
    }
};

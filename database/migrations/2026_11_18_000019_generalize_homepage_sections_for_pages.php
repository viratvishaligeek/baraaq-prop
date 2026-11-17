<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->foreignId('page_id')->nullable()->after('homepage_id')->constrained('pages')->cascadeOnDelete();
            $table->foreignId('homepage_id')->nullable()->change();
            $table->index(['page_id', 'sort_order']);
            $table->index(['page_id', 'is_enabled', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->dropIndex(['page_id', 'is_enabled', 'sort_order']);
            $table->dropIndex(['page_id', 'sort_order']);
            $table->dropConstrainedForeignId('page_id');
        });
    }
};

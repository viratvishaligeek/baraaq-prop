<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

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
        if (DB::table('homepage_sections')->whereNull('homepage_id')->exists()) {
            throw new RuntimeException(
                'Cannot roll back page-specific homepage sections automatically. Reassign or archive rows with a null homepage_id before rolling back.'
            );
        }

        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->dropIndex(['page_id', 'is_enabled', 'sort_order']);
            $table->dropIndex(['page_id', 'sort_order']);
            $table->dropConstrainedForeignId('page_id');
            $table->foreignId('homepage_id')->nullable(false)->change();
        });
    }
};

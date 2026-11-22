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
        if (! Schema::hasTable('homepage_sections')) {
            return;
        }

        $duplicate = DB::table('homepage_sections')
            ->select('homepage_id', 'section_key')
            ->groupBy('homepage_id', 'section_key')
            ->havingRaw('COUNT(*) > 1')
            ->first();

        if ($duplicate) {
            throw new RuntimeException(
                'Cannot enforce unique homepage section keys: duplicate rows exist for homepage_id '
                .$duplicate->homepage_id.' and section_key '.$duplicate->section_key.'. Reconcile these rows before migrating.'
            );
        }

        $indexes = collect(Schema::getIndexes('homepage_sections'));
        $uniqueIndex = $indexes->first(
            fn (array $index): bool => ($index['unique'] ?? false)
                && ($index['columns'] ?? []) === ['homepage_id', 'section_key']
        );

        if (! $uniqueIndex) {
            Schema::table('homepage_sections', function (Blueprint $table): void {
                $table->unique(['homepage_id', 'section_key'], 'homepage_sections_homepage_id_section_key_unique');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('homepage_sections')) {
            return;
        }

        $uniqueIndex = collect(Schema::getIndexes('homepage_sections'))->first(
            fn (array $index): bool => ($index['unique'] ?? false)
                && ($index['columns'] ?? []) === ['homepage_id', 'section_key']
        );

        if ($uniqueIndex) {
            Schema::table('homepage_sections', function (Blueprint $table) use ($uniqueIndex): void {
                $table->dropUnique($uniqueIndex['name']);
            });
        }
    }
};

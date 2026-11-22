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
        if (Schema::hasTable('homepage_sections')) {
            $duplicate = DB::table('homepage_sections')
                ->select('homepage_id', 'section_key')
                ->groupBy('homepage_id', 'section_key')
                ->havingRaw('COUNT(*) > 1')
                ->first();

            if ($duplicate) {
                throw new RuntimeException(
                    'Cannot enforce the fixed homepage section key constraint: duplicate rows exist for homepage_id '
                    .$duplicate->homepage_id.' and section_key '.$duplicate->section_key.'. Reconcile these rows before migrating.'
                );
            }

            $indexes = collect(Schema::getIndexes('homepage_sections'));
            $instanceIndex = $indexes->first(
                fn (array $index): bool => ($index['unique'] ?? false) && ($index['columns'] ?? []) === ['instance_key']
            );
            $sourceDesignIndex = $indexes->first(
                fn (array $index): bool => ($index['columns'] ?? []) === ['source_design']
            );
            $sectionKeyIndex = $indexes->first(
                fn (array $index): bool => ($index['columns'] ?? []) === ['section_key']
            );
            $sectionTypeIndex = $indexes->first(
                fn (array $index): bool => ($index['columns'] ?? []) === ['section_type']
            );
            $sectionKeyUnique = $indexes->first(
                fn (array $index): bool => ($index['unique'] ?? false)
                    && ($index['columns'] ?? []) === ['homepage_id', 'section_key']
            );

            $columnsToDrop = array_values(array_filter(
                ['instance_key', 'source_design'],
                fn (string $column): bool => Schema::hasColumn('homepage_sections', $column)
            ));

            if ($instanceIndex || $sourceDesignIndex || $sectionKeyIndex || $sectionTypeIndex || $columnsToDrop || ! $sectionKeyUnique) {
                Schema::table('homepage_sections', function (Blueprint $table) use ($instanceIndex, $sourceDesignIndex, $sectionKeyIndex, $sectionTypeIndex, $columnsToDrop, $sectionKeyUnique): void {
                    if ($instanceIndex) {
                        $table->dropUnique($instanceIndex['name']);
                    }

                    if ($sourceDesignIndex) {
                        $table->dropIndex($sourceDesignIndex['name']);
                    }

                    if ($sectionKeyIndex) {
                        $table->dropIndex($sectionKeyIndex['name']);
                    }

                    if ($sectionTypeIndex) {
                        $table->dropIndex($sectionTypeIndex['name']);
                    }

                    if ($columnsToDrop !== []) {
                        $table->dropColumn($columnsToDrop);
                    }

                    if (! $sectionKeyUnique) {
                        $table->unique(['homepage_id', 'section_key'], 'homepage_sections_homepage_id_section_key_unique');
                    }
                });
            }
        }

        if (Schema::hasTable('homepages')) {
            $indexes = collect(Schema::getIndexes('homepages'));
            $designKeyIndex = $indexes->first(
                fn (array $index): bool => ($index['columns'] ?? []) === ['design_key']
            );
            $defaultIndex = $indexes->first(
                fn (array $index): bool => ($index['columns'] ?? []) === ['is_default']
            );
            $statusIndex = $indexes->first(
                fn (array $index): bool => ($index['columns'] ?? []) === ['status']
            );

            $columnsToDrop = array_values(array_filter(
                ['design_key', 'is_default', 'is_composition_complete'],
                fn (string $column): bool => Schema::hasColumn('homepages', $column)
            ));

            if ($designKeyIndex || $defaultIndex || $statusIndex || $columnsToDrop !== []) {
                Schema::table('homepages', function (Blueprint $table) use ($designKeyIndex, $defaultIndex, $statusIndex, $columnsToDrop): void {
                    if ($designKeyIndex) {
                        $table->dropIndex($designKeyIndex['name']);
                    }

                    if ($defaultIndex) {
                        $table->dropIndex($defaultIndex['name']);
                    }

                    if ($statusIndex) {
                        $table->dropIndex($statusIndex['name']);
                    }

                    if ($columnsToDrop !== []) {
                        $table->dropColumn($columnsToDrop);
                    }
                });
            }
        }
    }

    public function down(): void
    {
        throw new RuntimeException('The fixed homepage schema cleanup is irreversible. Restore a tested database backup instead of rolling back.');
    }
};

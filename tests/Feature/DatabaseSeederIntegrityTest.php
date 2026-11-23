<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatabaseSeederIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_seed_is_repeatable_across_all_tables_and_preserves_foreign_key_integrity(): void
    {
        Storage::fake('public');

        $this->seed();

        $firstRunCounts = $this->databaseTableCounts();

        // A second complete run must neither duplicate records nor remove
        // administrator-owned or otherwise existing rows.
        $this->seed();

        $this->assertSame(
            $firstRunCounts,
            $this->databaseTableCounts(),
            'Running the complete database seeder twice must leave every table count unchanged.'
        );

        $this->assertSame(
            [],
            DB::select('PRAGMA foreign_key_check'),
            'The complete seed must not leave orphaned or invalid foreign-key references.'
        );

        // These are user-generated or evidence-dependent records. A fresh
        // installation must not invent reviews or service feedback.
        $this->assertDatabaseCount('comments', 0);
        $this->assertDatabaseCount('service_comments', 0);
        $this->assertDatabaseCount('newsletter_subscribers', 0);
        $this->assertDatabaseCount('enquiries', 0);
    }

    /**
     * Return deterministic row counts for every table in the SQLite test DB.
     *
     * @return array<string, int>
     */
    private function databaseTableCounts(): array
    {
        $tables = DB::select(
            "SELECT name FROM sqlite_master
             WHERE type = 'table'
               AND name NOT LIKE 'sqlite_%'
             ORDER BY name"
        );

        $counts = [];

        foreach ($tables as $table) {
            $counts[$table->name] = DB::table($table->name)->count();
        }

        return $counts;
    }
}

<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MigrationArchitectureTest extends TestCase
{
    #[Test]
    public function migration_files_do_not_create_the_same_table_more_than_once(): void
    {
        $createdTables = [];
        $migrationFiles = glob(database_path('migrations/*.php')) ?: [];

        $this->assertNotEmpty($migrationFiles, 'Migration files must be present.');

        foreach ($migrationFiles as $migrationFile) {
            $source = file_get_contents($migrationFile);
            $this->assertNotFalse($source, 'Could not read migration: '.$migrationFile);

            preg_match_all(
                '/Schema::create\s*\(\s*[\'"]([^\'"]+)[\'"]/m',
                $source,
                $matches
            );

            foreach ($matches[1] as $table) {
                if (isset($createdTables[$table])) {
                    $this->fail('Table '.$table.' is created more than once: '.$createdTables[$table].' and '.$migrationFile);
                }

                $createdTables[$table] = $migrationFile;
            }
        }

        $this->assertGreaterThan(0, count($createdTables));
    }
}

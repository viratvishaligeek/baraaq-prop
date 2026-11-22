<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Legacy builder tables and their contents are retained deliberately.
     *
     * The fixed Home 9 implementation no longer reads these tables, but a
     * production migration must not delete administrator-authored pages,
     * sections, widgets, media relationships, or settings without an explicit
     * export/retention decision. Application code can stop using the schema
     * independently; physical cleanup can be scheduled after a verified backup.
     */
    public function up(): void
    {
        // Intentionally non-destructive.
    }

    public function down(): void
    {
        // No schema or data was changed.
    }
};

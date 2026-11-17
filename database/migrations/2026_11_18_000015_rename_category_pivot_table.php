<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('categoryables') && ! Schema::hasTable('categorizables')) {
            Schema::rename('categoryables', 'categorizables');
        }
    }

    public function down(): void
    {
        // Keep the current Laravel morph pivot convention on rollback.
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reusable_sections', function (Blueprint $table) {
            $table->string('section_key', 100)->after('section_type')->index();
            $table->string('source_design', 30)->default('index')->after('section_key')->index();
        });
    }

    public function down(): void
    {
        Schema::table('reusable_sections', function (Blueprint $table) {
            $table->dropIndex(['section_key']);
            $table->dropIndex(['source_design']);
            $table->dropColumn(['section_key', 'source_design']);
        });
    }
};

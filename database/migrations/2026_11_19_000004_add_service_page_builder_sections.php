<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('page_id')->constrained('services')->cascadeOnDelete();
            $table->index(['service_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropIndex(['service_id', 'sort_order']);
            $table->dropColumn('service_id');
        });
    }
};

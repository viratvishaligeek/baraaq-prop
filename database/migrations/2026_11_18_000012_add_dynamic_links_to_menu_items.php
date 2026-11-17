<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('link_type', 30)->default('custom')->after('label');
            $table->unsignedBigInteger('link_id')->nullable()->after('link_type');
            $table->index(['link_type', 'link_id']);
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex(['link_type', 'link_id']);
            $table->dropColumn(['link_type', 'link_id']);
        });
    }
};

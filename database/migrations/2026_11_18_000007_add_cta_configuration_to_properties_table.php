<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('cta_mode', 30)->default('both')->after('is_featured');
            $table->string('whatsapp_number', 30)->nullable()->after('cta_mode');
            $table->string('whatsapp_message', 500)->nullable()->after('whatsapp_number');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['cta_mode', 'whatsapp_number', 'whatsapp_message']);
        });
    }
};

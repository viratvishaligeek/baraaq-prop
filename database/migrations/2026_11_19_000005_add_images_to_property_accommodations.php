<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_accommodations', function (Blueprint $table) {
            $table->foreignId('image_media_id')->nullable()->after('property_id')->constrained('media')->nullOnDelete();
            $table->index('image_media_id');
        });
    }

    public function down(): void
    {
        Schema::table('property_accommodations', function (Blueprint $table) {
            $table->dropForeign(['image_media_id']);
            $table->dropIndex(['image_media_id']);
            $table->dropColumn('image_media_id');
        });
    }
};

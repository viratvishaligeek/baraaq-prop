<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('media_attachments')) DB::table('media_attachments')->whereIn('mediable_type', ['App\\Models\\ReusableSection', 'App\\Models\\Widget'])->delete();
        if (Schema::hasTable('homepage_sections')) {
            Schema::table('homepage_sections', function (Blueprint $table) {
                foreach (['reusable_section_id', 'page_id', 'service_id'] as $column) {
                    if (Schema::hasColumn('homepage_sections', $column)) $table->dropConstrainedForeignId($column);
                }
            });
        }
        if (Schema::hasTable('pages') && Schema::hasColumn('pages', 'homepage_key')) Schema::table('pages', function (Blueprint $table) { $table->dropColumn('homepage_key'); });
        Schema::dropIfExists('widget_area_items');
        Schema::dropIfExists('widgets');
        Schema::dropIfExists('widget_areas');
        Schema::dropIfExists('reusable_sections');
    }
    public function down(): void { /* Retired builder/widget data is intentionally not reconstructed. */ }
};

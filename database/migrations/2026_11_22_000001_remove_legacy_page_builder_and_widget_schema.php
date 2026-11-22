<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            DB::table('settings')->where('key', 'dashboard_widgets')->delete();
        }

        if (Schema::hasTable('homepages')) {
            $fixedHomepageId = DB::table('homepages')->where('slug', 'home-9')->value('id');
            $obsoleteSections = DB::table('homepage_sections');
            if ($fixedHomepageId) {
                $obsoleteSections->where(function ($query) use ($fixedHomepageId) {
                    $query->whereNull('homepage_id')->orWhere('homepage_id', '<>', $fixedHomepageId);
                });
            } else {
                $obsoleteSections->whereNotNull('id');
            }
            $obsoleteSectionIds = $obsoleteSections->pluck('id');

            if ($obsoleteSectionIds->isNotEmpty() && Schema::hasTable('media_attachments')) {
                DB::table('media_attachments')
                    ->where('mediable_type', 'App\\Models\\HomepageSection')
                    ->whereIn('mediable_id', $obsoleteSectionIds)
                    ->delete();
            }

            if ($obsoleteSectionIds->isNotEmpty()) {
                DB::table('homepage_sections')->whereIn('id', $obsoleteSectionIds)->delete();
            }

            DB::table('homepages')->where('slug', '<>', 'home-9')->delete();
            if ($fixedHomepageId) {
                DB::table('homepages')->where('id', $fixedHomepageId)->update(['is_default' => true]);
            }
        }

        if (Schema::hasTable('media_attachments')) {
            DB::table('media_attachments')
                ->whereIn('mediable_type', ['App\\Models\\ReusableSection', 'App\\Models\\Widget'])
                ->delete();
        }

        if (Schema::hasTable('homepage_sections')) {
            $hasReusableSection = Schema::hasColumn('homepage_sections', 'reusable_section_id');
            $hasPage = Schema::hasColumn('homepage_sections', 'page_id');
            $hasService = Schema::hasColumn('homepage_sections', 'service_id');

            Schema::table('homepage_sections', function (Blueprint $table) use ($hasReusableSection, $hasPage, $hasService) {
                if ($hasPage) {
                    $table->dropIndex(['page_id', 'is_enabled', 'sort_order']);
                    $table->dropIndex(['page_id', 'sort_order']);
                    $table->dropConstrainedForeignId('page_id');
                }
                if ($hasService) {
                    $table->dropIndex(['service_id', 'sort_order']);
                    $table->dropConstrainedForeignId('service_id');
                }
                if ($hasReusableSection) {
                    $table->dropConstrainedForeignId('reusable_section_id');
                }
            });
        }

        if (Schema::hasTable('pages') && Schema::hasColumn('pages', 'homepage_key')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropUnique(['homepage_key']);
                $table->dropColumn('homepage_key');
            });
        }

        Schema::dropIfExists('widget_area_items');
        Schema::dropIfExists('widgets');
        Schema::dropIfExists('widget_areas');
        Schema::dropIfExists('reusable_sections');
    }

    public function down(): void
    {
        // Retired builder/widget data is intentionally not reconstructed.
    }
};

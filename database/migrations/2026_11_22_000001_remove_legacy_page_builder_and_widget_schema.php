<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('permissions')) {
            $widgetPermissionIds = DB::table('permissions')->where('slug', 'like', 'widgets.%')->pluck('id');
            if ($widgetPermissionIds->isNotEmpty() && Schema::hasTable('permission_role')) {
                DB::table('permission_role')->whereIn('permission_id', $widgetPermissionIds)->delete();
            }
            DB::table('permissions')->where('slug', 'like', 'widgets.%')->delete();
        }

        if (Schema::hasTable('settings')) {
            DB::table('settings')->where('key', 'dashboard_widgets')->delete();
        }

        if (Schema::hasTable('homepages')) {
            $fixedHomepageId = DB::table('homepages')->where('slug', 'home-9')->value('id');
            $obsoleteSections = Schema::hasTable('homepage_sections') ? DB::table('homepage_sections') : null;
            $fixedSectionKeys = [
                'hero.home-9', 'about.home-9', 'statistics.home-9', 'services.home-9',
                'properties.index', 'featured-gallery.home-9', 'home-details.home-9',
                'team.home-2', 'testimonials.home-3', 'blog.home-2',
            ];
            if ($obsoleteSections && $fixedHomepageId) {
                $obsoleteSections->where(function ($query) use ($fixedHomepageId, $fixedSectionKeys) {
                    $query->whereNull('homepage_id')
                        ->orWhere('homepage_id', '<>', $fixedHomepageId)
                        ->orWhereNotIn('section_key', $fixedSectionKeys);
                });
            } elseif ($obsoleteSections) {
                $obsoleteSections->whereNotNull('id');
            }
            $obsoleteSectionIds = $obsoleteSections ? $obsoleteSections->pluck('id') : collect();

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
                DB::table('homepages')->where('id', $fixedHomepageId)->update(['is_default' => true, 'deleted_at' => null, 'status' => 'published']);
                DB::table('homepages')->where('id', $fixedHomepageId)->whereNull('published_at')->update(['published_at' => now()]);
                foreach ($fixedSectionKeys as $position => $sectionKey) {
                    DB::table('homepage_sections')
                        ->where('homepage_id', $fixedHomepageId)
                        ->where('section_key', $sectionKey)
                        ->update(['sort_order' => $position + 1]);
                }
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

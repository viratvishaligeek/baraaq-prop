<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('circle_rate_districts', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 160);
            $table->string('slug', 180)->unique();
            $table->string('code', 40)->nullable()->unique();
            $table->string('state', 120)->default('Uttar Pradesh');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('circle_rate_tehsils', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('district_id')->constrained('circle_rate_districts')->restrictOnDelete();
            $table->string('name', 160);
            $table->string('slug', 180);
            $table->string('code', 40)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['district_id', 'slug']);
            $table->unique(['district_id', 'code']);
            $table->index(['district_id', 'is_active']);
        });

        Schema::create('circle_rate_area_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('description', 500)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('circle_rate_location_levels', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('description', 500)->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('circle_rate_locations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('district_id')->constrained('circle_rate_districts')->restrictOnDelete();
            $table->foreignId('tehsil_id')->nullable()->constrained('circle_rate_tehsils')->nullOnDelete();
            $table->foreignId('area_type_id')->nullable()->constrained('circle_rate_area_types')->nullOnDelete();
            $table->foreignId('level_id')->nullable()->constrained('circle_rate_location_levels')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('circle_rate_locations')->nullOnDelete();
            $table->string('name', 180);
            $table->string('slug', 200);
            $table->string('code', 60)->nullable();
            $table->json('attributes')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['district_id', 'parent_id', 'slug'], 'cr_locations_parent_slug_unique');
            $table->index(['district_id', 'tehsil_id', 'area_type_id']);
            $table->index(['parent_id', 'level_id', 'is_active']);
        });

        Schema::create('circle_rate_property_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 140);
            $table->string('slug', 160)->unique();
            $table->string('description', 1000)->nullable();
            $table->string('calculation_strategy', 60)->default('land_area');
            $table->json('field_configuration')->nullable();
            $table->json('formula_configuration')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('circle_rate_property_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 140);
            $table->string('slug', 160)->unique();
            $table->string('description', 1000)->nullable();
            $table->unsignedInteger('priority')->default(0);
            $table->json('applicability')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('circle_rate_category_property_type', function (Blueprint $table): void {
            $table->foreignId('category_id')->constrained('circle_rate_property_categories')->cascadeOnDelete();
            $table->foreignId('property_type_id')->constrained('circle_rate_property_types')->cascadeOnDelete();
            $table->primary(['category_id', 'property_type_id'], 'cr_category_property_type_pk');
        });

        Schema::create('circle_rate_category_location', function (Blueprint $table): void {
            $table->foreignId('category_id')->constrained('circle_rate_property_categories')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('circle_rate_locations')->cascadeOnDelete();
            $table->primary(['category_id', 'location_id'], 'cr_category_location_pk');
        });

        Schema::create('circle_rate_units', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->string('symbol', 24);
            $table->string('slug', 120)->unique();
            $table->string('dimension', 40)->default('area');
            $table->decimal('base_factor', 24, 12)->nullable();
            $table->boolean('is_base_unit')->default(false);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('circle_rate_unit_conversions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('from_unit_id')->constrained('circle_rate_units')->cascadeOnDelete();
            $table->foreignId('to_unit_id')->constrained('circle_rate_units')->cascadeOnDelete();
            $table->decimal('factor', 24, 12);
            $table->string('source_note', 500)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->unique(['from_unit_id', 'to_unit_id']);
        });

        Schema::create('circle_rate_road_width_ranges', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->foreignId('unit_id')->constrained('circle_rate_units')->restrictOnDelete();
            $table->decimal('minimum_width', 14, 4)->nullable();
            $table->decimal('maximum_width', 14, 4)->nullable();
            $table->decimal('exact_width', 14, 4)->nullable();
            $table->json('applicability')->nullable();
            $table->json('adjustment_configuration')->nullable();
            $table->date('effective_from')->nullable();
            $table->date('effective_until')->nullable();
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('circle_rate_financial_years', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 20)->unique();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->boolean('is_current')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('show_on_frontend')->default(true);
            $table->timestamps();
        });

        Schema::create('circle_rate_sources', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 220);
            $table->string('reference_number', 120)->nullable();
            $table->string('document_path', 500)->nullable();
            $table->string('document_disk', 40)->default('public');
            $table->string('source_url', 1000)->nullable();
            $table->date('published_on')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('circle_rate_import_batches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('financial_year_id')->nullable()->constrained('circle_rate_financial_years')->nullOnDelete();
            $table->foreignId('source_id')->nullable()->constrained('circle_rate_sources')->nullOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('original_filename', 255);
            $table->string('stored_path', 500)->nullable();
            $table->string('file_hash', 64)->nullable()->index();
            $table->string('file_type', 12);
            $table->string('status', 30)->default('uploaded')->index();
            $table->json('column_mapping')->nullable();
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('valid_rows')->default(0);
            $table->unsignedInteger('invalid_rows')->default(0);
            $table->unsignedInteger('conflict_rows')->default(0);
            $table->text('summary')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('circle_rate_import_rows', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('batch_id')->constrained('circle_rate_import_batches')->cascadeOnDelete();
            $table->unsignedInteger('row_number');
            $table->json('original_data');
            $table->json('normalized_data')->nullable();
            $table->json('validation_errors')->nullable();
            $table->string('status', 30)->default('pending')->index();
            $table->unsignedBigInteger('circle_rate_id')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('circle_rate_locations')->nullOnDelete();
            $table->timestamps();
            $table->unique(['batch_id', 'row_number']);
        });

        Schema::create('circle_rate_rules', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 180);
            $table->text('description')->nullable();
            $table->json('conditions');
            $table->json('actions');
            $table->foreignId('source_id')->nullable()->constrained('circle_rate_sources')->nullOnDelete();
            $table->boolean('is_verified')->default(false)->index();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('stop_processing')->default(true);
            $table->boolean('is_active')->default(true)->index();
            $table->date('effective_from')->nullable();
            $table->date('effective_until')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('circle_rate_calculator_fields', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->string('label', 160);
            $table->string('input_type', 40);
            $table->string('data_source', 60)->nullable();
            $table->string('placeholder', 180)->nullable();
            $table->text('help_text')->nullable();
            $table->json('validation_rules')->nullable();
            $table->json('visibility_conditions')->nullable();
            $table->json('applicability')->nullable();
            $table->string('default_value', 255)->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['name']);
        });

        Schema::create('circle_rate_calculator_field_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('field_id')->constrained('circle_rate_calculator_fields')->cascadeOnDelete();
            $table->string('label', 160);
            $table->string('value', 160);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['field_id', 'value']);
        });

        Schema::create('circle_rates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('district_id')->constrained('circle_rate_districts')->restrictOnDelete();
            $table->foreignId('tehsil_id')->nullable()->constrained('circle_rate_tehsils')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('circle_rate_locations')->nullOnDelete();
            $table->foreignId('area_type_id')->nullable()->constrained('circle_rate_area_types')->nullOnDelete();
            $table->foreignId('property_type_id')->constrained('circle_rate_property_types')->restrictOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('circle_rate_property_categories')->nullOnDelete();
            $table->foreignId('road_width_range_id')->nullable()->constrained('circle_rate_road_width_ranges')->nullOnDelete();
            $table->foreignId('financial_year_id')->constrained('circle_rate_financial_years')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('circle_rate_units')->restrictOnDelete();
            $table->foreignId('source_id')->nullable()->constrained('circle_rate_sources')->nullOnDelete();
            $table->decimal('rate', 20, 4);
            $table->unsignedInteger('priority')->default(0);
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->string('verification_status', 24)->default('pending')->index();
            $table->string('status', 24)->default('draft')->index();
            $table->json('conditions')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['district_id', 'tehsil_id', 'location_id', 'property_type_id'], 'circle_rates_location_type_idx');
            $table->index(['financial_year_id', 'effective_from', 'effective_until'], 'circle_rates_year_effective_idx');
            $table->index(['verification_status', 'status'], 'circle_rates_review_status_idx');
        });

        Schema::create('circle_rate_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('circle_rate_id')->constrained('circle_rates')->cascadeOnDelete();
            $table->unsignedInteger('version_number');
            $table->json('snapshot');
            $table->text('change_reason')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['circle_rate_id', 'version_number']);
        });

        Schema::table('circle_rate_import_rows', function (Blueprint $table): void {
            $table->foreign('circle_rate_id')->references('id')->on('circle_rates')->nullOnDelete();
        });

        Schema::create('circle_rate_valuation_records', function (Blueprint $table): void {
            $table->id();
            $table->uuid('reference')->unique();
            $table->foreignId('circle_rate_id')->nullable()->constrained('circle_rates')->nullOnDelete();
            $table->foreignId('previous_circle_rate_id')->nullable()->constrained('circle_rates')->nullOnDelete();
            $table->foreignId('financial_year_id')->nullable()->constrained('circle_rate_financial_years')->nullOnDelete();
            $table->json('input_data');
            $table->json('calculation_breakdown')->nullable();
            $table->decimal('property_area', 20, 6);
            $table->foreignId('area_unit_id')->nullable()->constrained('circle_rate_units')->nullOnDelete();
            $table->decimal('applicable_rate', 20, 4)->nullable();
            $table->decimal('estimated_value', 22, 2)->nullable();
            $table->decimal('previous_year_value', 22, 2)->nullable();
            $table->decimal('difference_amount', 22, 2)->nullable();
            $table->decimal('difference_percent', 12, 4)->nullable();
            $table->string('result_status', 30)->default('calculated')->index();
            $table->timestamp('valued_at');
            $table->timestamps();
            $table->index(['financial_year_id', 'valued_at']);
        });

        Schema::create('circle_rate_api_connections', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 160);
            $table->string('base_url', 1000)->nullable();
            $table->string('auth_type', 40)->default('none');
            $table->text('encrypted_credentials')->nullable();
            $table->json('configuration')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });

        Schema::create('circle_rate_api_sync_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('connection_id')->nullable()->constrained('circle_rate_api_connections')->nullOnDelete();
            $table->string('status', 30)->index();
            $table->string('endpoint', 1000)->nullable();
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->json('request_summary')->nullable();
            $table->json('response_summary')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });

        Schema::create('circle_rate_audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject_type', 120);
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('event', 60);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('source_id')->nullable()->constrained('circle_rate_sources')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['subject_type', 'subject_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        foreach ([
            'circle_rate_audit_logs',
            'circle_rate_api_sync_logs',
            'circle_rate_api_connections',
            'circle_rate_valuation_records',
            'circle_rate_versions',
            'circle_rate_import_rows',
            'circle_rates',
            'circle_rate_calculator_field_options',
            'circle_rate_calculator_fields',
            'circle_rate_rules',
            'circle_rate_import_batches',
            'circle_rate_sources',
            'circle_rate_financial_years',
            'circle_rate_road_width_ranges',
            'circle_rate_unit_conversions',
            'circle_rate_units',
            'circle_rate_category_location',
            'circle_rate_category_property_type',
            'circle_rate_property_categories',
            'circle_rate_property_types',
            'circle_rate_locations',
            'circle_rate_location_levels',
            'circle_rate_area_types',
            'circle_rate_tehsils',
            'circle_rate_districts',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};

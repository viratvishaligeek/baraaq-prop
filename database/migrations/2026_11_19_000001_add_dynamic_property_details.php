<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('country', 100)->nullable()->after('state');
            $table->string('availability_status', 30)->default('available')->index();
            $table->string('focus_keyword', 160)->nullable();
        });

        Schema::create('property_accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('name', 160);
            $table->string('category', 80)->nullable();
            $table->decimal('area', 12, 2)->nullable();
            $table->string('area_unit', 20)->default('sqft');
            $table->longText('description')->nullable();
            $table->boolean('is_included')->default(true);
            $table->json('attributes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['property_id', 'sort_order']);
        });

        Schema::create('property_faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('question', 500);
            $table->longText('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['property_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_faqs');
        Schema::dropIfExists('property_accommodations');
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['availability_status']);
            $table->dropColumn(['country', 'availability_status', 'focus_keyword']);
        });
    }
};

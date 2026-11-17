<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('homepage_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reusable_section_id')->nullable()->constrained('reusable_sections')->nullOnDelete();
            $table->uuid('instance_key')->unique();
            $table->string('section_key', 100)->index();
            $table->string('section_type', 80)->index();
            $table->string('source_design', 30)->default('index')->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->json('config')->nullable();
            $table->json('data_source')->nullable();
            $table->timestamps();
            $table->index(['homepage_id', 'sort_order']);
            $table->index(['homepage_id', 'is_enabled', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_sections');
    }
};

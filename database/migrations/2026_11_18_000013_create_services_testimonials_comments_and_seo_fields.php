<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('canonical_url', 2048)->nullable();
            $table->string('og_image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('company')->nullable();
            $table->text('quote');
            $table->string('photo')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('email');
            $table->text('body');
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();
            $table->index(['post_id', 'status', 'created_at']);
        });

        foreach (['posts', 'pages', 'properties'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('canonical_url', 2048)->nullable();
                $table->string('og_image')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach (['posts', 'pages', 'properties'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['canonical_url', 'og_image']);
            });
        }

        Schema::dropIfExists('comments');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('services');
    }
};

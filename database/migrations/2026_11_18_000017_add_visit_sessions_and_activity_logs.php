<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('page_views', function (Blueprint $table) {
            $table->char('visit_hash', 64)->nullable()->after('visitor_hash')->index();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('causer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject_type', 150)->index();
            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->string('event', 30)->index();
            $table->string('description', 255);
            $table->json('properties')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');

        Schema::table('page_views', function (Blueprint $table) {
            $table->dropColumn('visit_hash');
        });
    }
};

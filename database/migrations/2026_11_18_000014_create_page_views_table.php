<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->char('visitor_hash', 64)->index();
            $table->string('path', 2048);
            $table->string('route_name', 120)->nullable()->index();
            $table->string('content_type', 30)->default('page')->index();
            $table->unsignedBigInteger('content_id')->nullable()->index();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};

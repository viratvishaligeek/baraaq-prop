<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('email', 190);
            $table->text('body');
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();
            $table->index(['service_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_comments');
    }
};

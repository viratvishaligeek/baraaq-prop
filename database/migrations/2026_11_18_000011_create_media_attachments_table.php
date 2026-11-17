<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_attachments', function (Blueprint $table) {
            $table->id();
            $table->morphs('mediable');
            $table->string('field_key', 120);
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['mediable_type', 'mediable_id', 'field_key'], 'media_attachment_field_unique');
            $table->index('media_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_attachments');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiries', function (Blueprint $table): void {
            $table->string('phone', 30)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Preserve rows created while phone was optional before restoring the
        // historical NOT NULL constraint.
        DB::table('enquiries')->whereNull('phone')->update(['phone' => '']);

        Schema::table('enquiries', function (Blueprint $table): void {
            $table->string('phone', 30)->nullable(false)->change();
        });
    }
};

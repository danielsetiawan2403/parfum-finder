<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Index biasa untuk foreign key perfume_id, supaya index unik boleh dihapus.
        Schema::table('reviews', function (Blueprint $table) {
            $table->index('perfume_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique(['perfume_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unique(['perfume_id', 'user_id']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['perfume_id']);
        });
    }
};
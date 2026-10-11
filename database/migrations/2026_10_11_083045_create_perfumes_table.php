<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfumes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->default('Unisex');
            $table->unsignedBigInteger('price')->default(0);
            $table->string('image')->nullable();
            $table->text('top_notes')->nullable();
            $table->text('middle_notes')->nullable();
            $table->text('base_notes')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfumes');
    }
};
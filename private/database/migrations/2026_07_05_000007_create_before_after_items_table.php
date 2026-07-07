<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('before_after_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image_before')->nullable();
            $table->string('image_after')->nullable();
            $table->string('technique')->nullable();
            $table->string('material')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('before_after_items');
    }
};

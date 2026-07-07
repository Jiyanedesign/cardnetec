<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('whatsapp');
            $table->string('email')->nullable();
            $table->integer('qty')->nullable();
            $table->text('message')->nullable();
            $table->string('product_name')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('simulation_image_path')->nullable();
            $table->json('simulation_data')->nullable(); // Guardar coordenadas/textos
            $table->string('status')->default('new'); // new, seen, completed, archived
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_requests');
    }
};

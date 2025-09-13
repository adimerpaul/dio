<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fotografias', function (Blueprint $table) {
            $table->id();

            $table->morphs('caseable'); // polimórfico
            $table->foreignId('user_id')->constrained('users');

            $table->string('titulo')->nullable();
            $table->string('descripcion')->nullable();

            // info archivo
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('extension', 10);
            $table->string('mime');
            $table->unsignedBigInteger('size_bytes');

            // rutas
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('url');
            $table->string('thumb_path')->nullable();
            $table->string('thumb_url')->nullable();

            // dimensiones
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void {
        Schema::dropIfExists('fotografias');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();

            $table->morphs('caseable'); // polimórfico
            $table->foreignId('user_id')->constrained('users');

            $table->string('titulo')->nullable();
            $table->string('categoria', 40)->nullable();
            $table->text('descripcion')->nullable();

            // metadatos del archivo
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('extension', 16)->nullable();
            $table->string('mime', 80)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('disk', 32)->default('public');
            $table->string('path');
            $table->string('url')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void {
        Schema::dropIfExists('documentos');
    }
};

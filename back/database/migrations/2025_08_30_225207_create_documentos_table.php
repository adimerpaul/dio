<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('caso_id');
            $table->unsignedBigInteger('user_id');           // quién sube
            $table->string('titulo')->nullable();            // nombre visible opcional
            $table->string('categoria', 40)->nullable();     // p.ej. "oficio", "anexo", "evidencia"
            $table->text('descripcion')->nullable();

            // metadatos del archivo
            $table->string('original_name');                 // nombre original
            $table->string('stored_name');                   // nombre guardado
            $table->string('extension', 16)->nullable();
            $table->string('mime', 80)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('disk', 32)->default('public');
            $table->string('path');                          // ruta dentro del disk
            $table->string('url')->nullable();               // Storage::url(path)

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('caso_id')->references('id')->on('casos')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sesiones_psicologicas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('caso_id');
            $table->unsignedBigInteger('user_id'); // quién registra
            $table->date('fecha')->nullable();
            $table->string('titulo')->nullable();     // título visible en listado
            $table->unsignedInteger('duracion_min')->nullable(); // opcional
            $table->string('lugar')->nullable();      // opcional
            $table->string('tipo', 30)->nullable();   // individual / grupal / etc (opcional)
            $table->longText('contenido_html');       // WYSIWYG (base HTML del informe/sesión)
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('caso_id')->references('id')->on('casos')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones_psicologicas');
    }
};

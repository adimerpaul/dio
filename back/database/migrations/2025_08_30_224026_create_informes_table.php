<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('informes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('caso_id');
            $table->unsignedBigInteger('user_id');
            $table->date('fecha')->nullable();
            $table->string('titulo')->nullable();
            $table->string('area', 30)->nullable();     // psicologico | legal | social (u otros)
            $table->string('numero', 50)->nullable();   // correlativo interno opcional
            $table->longText('contenido_html');         // WYSIWYG
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('caso_id')->references('id')->on('casos')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('psicologicas', function (Blueprint $table) {
            $table->id();

            $table->morphs('caseable'); // polimórfico
            $table->foreignId('user_id')->constrained('users');

            $table->date('fecha')->nullable();
            $table->string('titulo')->nullable();
            $table->unsignedInteger('duracion_min')->nullable();
            $table->string('lugar')->nullable();
            $table->string('tipo', 30)->nullable(); // individual/grupal/etc.
            $table->longText('contenido_html');

            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void {
        Schema::dropIfExists('psicologicas');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('informes_legales', function (Blueprint $table) {
            $table->id();

            $table->morphs('caseable'); // polimórfico
            $table->foreignId('user_id')->constrained('users');

            $table->date('fecha')->nullable();
            $table->string('titulo')->nullable();
            $table->string('numero', 50)->nullable(); // correlativo interno
            $table->longText('contenido_html');
            $table->string('archivo')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void {
        Schema::dropIfExists('informes_legales');
    }
};

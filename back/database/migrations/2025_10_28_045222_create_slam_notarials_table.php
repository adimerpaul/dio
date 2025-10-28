<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('slam_notariales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->dateTime('fecha');
            $table->string('nombre_completo');
            $table->string('edad');
            $table->string('fecha_nacimiento');
            $table->string('estado_civil');
            $table->string('ci');
            $table->string('grado_instruccion');
            $table->string('ocupacion');
            $table->string('direccion_domicilio');
            $table->string('url');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slam_notariales');
    }
};

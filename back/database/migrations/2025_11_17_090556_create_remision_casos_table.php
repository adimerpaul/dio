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
        Schema::create('remision_casos', function (Blueprint $table) {
            $table->id();
//            codigo_ingreso fecha_hora objeto_ingreso cantidad remitente referencia dispocicion
            $table->string('codigo_ingreso')->nullable();
            $table->dateTime('fecha_hora')->nullable();
            $table->string('objeto_ingreso')->nullable();
            $table->integer('cantidad')->nullable();
            $table->string('remitente')->nullable();
//            remitente_otros
            $table->string('remitente_otros')->nullable();
//            organizacion
            $table->string('organizacion')->nullable();
//            $table->string('referencia')->nullable();
            $table->string('disposicion')->nullable();
            $table->string('archivo')->nullable();
            $table->string('estado')->nullable();
            $table->string('interno_externo')->nullable();
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remision_casos');
    }
};

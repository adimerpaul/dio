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
        Schema::create('caso_acogimientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('caso_id')->nullable();
            $table->string('tipo_de_acogida')->nullable();
            $table->string('centro_de_acogida')->nullable();
            $table->string('cuidadora_nombre_completo')->nullable();
            $table->string('cuidadora_celular')->nullable();
            $table->string('cuidadora_domicilio')->nullable();
            $table->string('cuidadora_ubicacion')->nullable();
            $table->string('cuidadora_lat')->nullable();
            $table->string('cuidadora_lng')->nullable();
            $table->foreign('caso_id')->references('id')->on('casos');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caso_acogimientos');
    }
};

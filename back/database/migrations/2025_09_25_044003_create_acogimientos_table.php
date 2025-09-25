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
        Schema::create('acogimientos', function (Blueprint $table) {
            $table->id();
            $table->string('adopcion')->nullable();
            $table->string('hogar')->nullable();
            //niños10  juzagado cuidado_nombre cuidadao_paterno cuidadora_meterno area_legal tipologia audiencia_evaluacion fecha fecha proximas audiencia
            for ($i = 1; $i <= 10; $i++) {
                $table->string("nino{$i}")->nullable();
            }
            $table->string('juzgado')->nullable();
            $table->string('cuidado_nombre')->nullable();
            $table->string('cuidado_paterno')->nullable();
            $table->string('cuidado_materno')->nullable();
            $table->string('area_legal')->nullable();
            $table->string('tipologia')->nullable();
            $table->date('audiencia_evaluacion')->nullable();
            $table->date('fecha')->nullable();
            $table->date('proximas_audiencia')->nullable();
            $table->unsignedBigInteger('caso_id');
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
        Schema::dropIfExists('acogimientos');
    }
};

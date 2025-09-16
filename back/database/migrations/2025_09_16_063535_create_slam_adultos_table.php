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
        Schema::create('slam_adultos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slam_id')->constrained('slams')->cascadeOnDelete();
            $table->string('nombre')->nullable();
            $table->string('paterno')->nullable();
            $table->string('materno')->nullable();
            $table->string('documento_tipo', 40)->nullable(); // CI / otro
            $table->string('documento_num', 40)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('lugar_nacimiento', 120)->nullable();
            $table->string('edad', 10)->nullable();
            $table->string('domicilio', 160)->nullable();
            $table->string('estado_civil', 40)->nullable();
            $table->string('ocupacion_1', 120)->nullable(); // p.ej. AGRICULTOR
            $table->string('ocupacion_2', 120)->nullable(); // p.ej. LABORES DE CASA
            $table->string('ingresos', 160)->nullable();    // p.ej. RENTA DIGNIDAD
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slam_adultos');
    }
};

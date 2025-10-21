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
        Schema::create('talleres', function (Blueprint $table) {
            $table->id();
            // solicitado: DIRECTOR, SECRETARIA, PERSONAL UE
            $table->string('solicitado');

            $table->string('dia', 20)->nullable(); // ej. LUNES/MARTES o fecha corta
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_final')->nullable();

            $table->string('lugar', 120)->nullable();
            $table->string('colegio', 120)->nullable();
            $table->string('curso', 120)->nullable();

            $table->unsignedInteger('numero_asistentes')->default(0);

            $table->string('tema', 160)->nullable();
            $table->text('descripcion')->nullable();

            $table->string('director_nombre', 120)->nullable();
            $table->string('director_telefono', 30)->nullable();

            $table->string('encargado_taller_nombre', 120)->nullable();
            $table->string('encargado_taller_telefono', 30)->nullable();

            $table->string('direccion_ubicacion_ue_colegio', 255)->nullable();
            $table->string('dirigido_a', 160)->nullable();

            $table->string('color', 7)->nullable();

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
        Schema::dropIfExists('talleres');
    }
};

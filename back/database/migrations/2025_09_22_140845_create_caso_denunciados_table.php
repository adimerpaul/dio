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
        Schema::create('caso_denunciados', function (Blueprint $table) {
            $table->id();
            $table->string('denunciado_nombres', 120)->nullable();
            $table->string('denunciado_paterno', 80)->nullable();
            $table->string('denunciado_materno', 80)->nullable();
            $table->string('denunciado_documento', 40)->nullable();
            $table->string('denunciado_documento_otro', 40)->nullable();
            $table->string('denunciado_nro', 30)->nullable();
            $table->string('denunciado_sexo', 15)->nullable();
            $table->string('denunciado_lugar_nacimiento', 120)->nullable();
            $table->date('denunciado_fecha_nacimiento')->nullable();
            $table->string('denunciado_edad')->nullable();
            $table->string('denunciado_telefono')->nullable();
            $table->string('denunciado_residencia', 120)->nullable();
            $table->string('denunciado_estado_civil', 40)->nullable();
            $table->string('denunciado_relacion', 60)->nullable();
            $table->string('denunciado_grado', 60)->nullable();
            $table->string('denunciado_trabaja', 10)->nullable();       // o boolean si sabes que es SI/NO
            $table->string('denunciado_prox', 120)->nullable();
            $table->string('denunciado_ocupacion', 80)->nullable();
            $table->string('denunciado_ocupacion_exacto', 120)->nullable();
            $table->string('denunciado_idioma', 60)->nullable();
            $table->string('denunciado_fijo', 30)->nullable();
            $table->string('denunciado_movil', 30)->nullable();
            $table->text('denunciado_domicilio_actual')->nullable();
            $table->string('denunciado_ingresos', 60)->nullable();
            $table->string('denunciado_relacion_victima', 60)->nullable();
            $table->string('denunciado_relacion_denunciado', 60)->nullable();
            $table->decimal('denunciado_latitud', 10, 7)->nullable();
            $table->decimal('denunciado_longitud', 10, 7)->nullable();
            $table->string('denunciado_cargo')->nullable();
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
        Schema::dropIfExists('caso_denunciados');
    }
};

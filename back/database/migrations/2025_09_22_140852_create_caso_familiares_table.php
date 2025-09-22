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
        Schema::create('caso_familiares', function (Blueprint $table) {
            $table->id();
            // ===== Denunciado =====
//            $table->string('denunciado_nombre_completo', 120)->nullable();
//            $table->string('denunciado_nombres', 120)->nullable();
//            $table->string('denunciado_paterno', 80)->nullable();
//            $table->string('denunciado_materno', 80)->nullable();
//            $table->string('denunciado_documento', 40)->nullable();
//            $table->string('denunciado_nro', 30)->nullable();
//            $table->string('denunciado_sexo', 15)->nullable();
//            $table->string('denunciado_lugar_nacimiento', 120)->nullable();
//            $table->date('denunciado_fecha_nacimiento')->nullable();
//            $table->string('denunciado_edad')->nullable();
//            $table->string('denunciado_telefono')->nullable();
//            $table->string('denunciado_residencia', 120)->nullable();
//            $table->string('denunciado_estado_civil', 40)->nullable();
//            $table->string('denunciado_relacion', 60)->nullable();
//            $table->string('denunciado_grado', 60)->nullable();
//            $table->string('denunciado_trabaja', 10)->nullable();       // o boolean si sabes que es SI/NO
//            $table->string('denunciado_prox', 120)->nullable();
//            $table->string('denunciado_ocupacion', 80)->nullable();
//            $table->string('denunciado_ocupacion_exacto', 120)->nullable();
//            $table->string('denunciado_idioma', 60)->nullable();
//            $table->string('denunciado_fijo', 30)->nullable();
//            $table->string('denunciado_movil', 30)->nullable();
//            $table->text('denunciado_domicilio_actual')->nullable();    // TEXT
//            $table->decimal('denunciado_latitud', 10, 7)->nullable();
//            $table->decimal('denunciado_longitud', 10, 7)->nullable();
            $table->string('familiar_nombre_completo', 120)->nullable();
            $table->string('familiar_nombres', 120)->nullable();
            $table->string('familiar_paterno', 80)->nullable();
            $table->string('familiar_materno', 80)->nullable();
            $table->string('familiar_documento', 40)->nullable();
            $table->string('familiar_nro', 30)->nullable();
            $table->string('familiar_sexo', 15)->nullable();
            $table->string('familiar_lugar_nacimiento', 120)->nullable();
            $table->date('familiar_fecha_nacimiento')->nullable();
            $table->string('familiar_edad')->nullable();
            $table->string('familiar_telefono', 30)->nullable();
            $table->string('familiar_residencia', 120)->nullable();
            $table->string('familiar_estado_civil', 40)->nullable();
            $table->string('familiar_parentesco', 60)->nullable();
            $table->string('familiar_grado', 60)->nullable();
            $table->string('familiar_trabaja', 10)->nullable();       //
            $table->string('familiar_prox', 120)->nullable();
            $table->string('familiar_ocupacion', 80)->nullable();
            $table->string('familiar_ocupacion_exacto', 120)->nullable();
            $table->string('familiar_idioma', 60)->nullable();
            $table->string('familiar_fijo', 30)->nullable();
            $table->string('familiar_movil', 30)->nullable();
            $table->text('familiar_domicilio_actual')->nullable();    // TEXT
            $table->decimal('familiar_latitud', 10, 7)->nullable();
            $table->decimal('familiar_longitud', 10, 7)->nullable();

            $table->unsignedBigInteger('caso_id');
            $table->foreign('caso_id')->references('id')->on('casos');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caso_familiares');
    }
};

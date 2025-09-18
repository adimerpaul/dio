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
        Schema::create('propremis', function (Blueprint $table) {
            $table->id();
            // ===== 1) DATOS DEL ADULTO MAYOR =====
            $table->string('area', 80)->nullable();
            $table->string('zona', 80)->nullable();
            $table->date('fecha_registro')->nullable();
            $table->string('numero_apoyo_integral', 80)->nullable();
            $table->string('numero_caso', 40)->nullable()->index();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
//            $table->string('numero_caso', 40)->nullable()->index();
//            colegio



            // datos del denunciado
            $table->string('denunciado_nombres', 120)->nullable();
            $table->string('denunciado_paterno', 80)->nullable();
            $table->string('denunciado_materno', 80)->nullable();
            $table->string('denunciado_ci', 40)->nullable();
            $table->string('denunciado_ciudad_nacimiento', 80)->nullable();
            $table->string('denunciado_sexo', 20)->nullable();
            $table->string('denunciado_lugar_nacimiento', 120)->nullable();
            $table->date('denunciado_fecha_nacimiento')->nullable();
            $table->string('denunciado_edad', 10)->nullable();
            $table->string('denunciado_direccion', 160)->nullable();
            $table->string('denunciado_estado_civil', 40)->nullable();
            $table->string('denunciado_idioma', 80)->nullable();
            $table->string('denunciado_grado_instruccion', 80)->nullable();
            $table->string('denunciado_ocupacion', 120)->nullable();
            $table->string('denunciado_celular1', 40)->nullable();
            $table->string('denunciado_celular2', 40)->nullable();
            $table->string('denunciado_telefono_fijo1', 40)->nullable();
            $table->string('denunciado_telefono_fijo2', 40)->nullable();
            $table->string('denunciado_direccion_actual', 160)->nullable();


//            dato de la denuncia
            $table->date('fecha_hecho')->nullable();
            $table->string('relacion_denunciante', 80)->nullable();
            $table->string('direccion_hecho', 160)->nullable();
            $table->text('descripcion_hecho')->nullable();



            // ===== 7) Documentos checks =====

            $table->boolean('doc_ci')->default(false);
            $table->boolean('doc_frontal_denunciado')->default(false);
            $table->boolean('doc_frontal_denunciante')->default(false);
            $table->boolean('doc_croquis')->default(false);


            // Metadatos
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('psicologica_user_id')->nullable();
            $table->foreign('psicologica_user_id')->references('id')->on('users');
            $table->unsignedBigInteger('trabajo_social_user_id')->nullable();
            $table->foreign('trabajo_social_user_id')->references('id')->on('users');
            $table->unsignedBigInteger('legal_user_id')->nullable();
            $table->foreign('legal_user_id')->references('id')->on('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propremis');
    }
};

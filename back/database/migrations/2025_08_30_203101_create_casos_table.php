<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('casos', function (Blueprint $table) {
            $table->id();

            // ===== Denunciante =====
            $table->string('area', 80)->nullable();
            $table->string('zona', 80)->nullable();
            $table->string('denunciante_nombre_completo', 120)->nullable();
            $table->string('denunciante_nombres', 120)->nullable();
            $table->string('denunciante_paterno', 80)->nullable();
            $table->string('denunciante_materno', 80)->nullable();
            $table->string('denunciante_documento', 40)->nullable();   // CI, RUMC, etc.
            $table->string('denunciante_nro', 30)->nullable();
            $table->string('denunciante_sexo', 15)->nullable();        // MASCULINO/FEMENINO/OTRO
            $table->string('denunciante_lugar_nacimiento', 120)->nullable();
            $table->date('denunciante_fecha_nacimiento')->nullable();
            $table->string('denunciante_edad')->nullable();
            $table->string('denunciante_telefono')->nullable();
            $table->string('denunciante_residencia', 120)->nullable();
            $table->string('denunciante_estado_civil', 40)->nullable();
            $table->string('denunciante_relacion', 60)->nullable();
            $table->string('denunciante_grado', 60)->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();

            $table->string('denunciante_trabaja')->nullable();        // true/false
            $table->string('denunciante_prox', 120)->nullable();        // referencia corta
            $table->string('denunciante_ocupacion', 80)->nullable();
            $table->string('denunciante_ocupacion_exacto', 120)->nullable();

            $table->string('denunciante_idioma', 60)->nullable();
            $table->string('denunciante_fijo', 30)->nullable();
            $table->string('denunciante_movil', 30)->nullable();
            $table->text('denunciante_domicilio_actual')->nullable();   // TEXT para direcciones largas

            // ===== Grupo Familiar rápido (si mantienes aquí) =====
            for ($i = 1; $i <= 5; $i++) {
                $table->string("familiar{$i}_nombre_completo", 120)->nullable();
                $table->tinyInteger("familiar{$i}_edad")->nullable();
                $table->string("familiar{$i}_parentesco", 40)->nullable();
                $table->string("familiar{$i}_celular", 30)->nullable();
            }

            // ===== Denunciado =====
            $table->string('denunciado_nombre_completo', 120)->nullable();
            $table->string('denunciado_nombres', 120)->nullable();
            $table->string('denunciado_paterno', 80)->nullable();
            $table->string('denunciado_materno', 80)->nullable();
            $table->string('denunciado_documento', 40)->nullable();
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
            $table->text('denunciado_domicilio_actual')->nullable();    // TEXT
            $table->decimal('denunciado_latitud', 10, 7)->nullable();
            $table->decimal('denunciado_longitud', 10, 7)->nullable();

            // ===== Caso =====
            $table->string('caso_numero', 40)->nullable()->index();
            $table->date('caso_fecha_hecho')->nullable();
            $table->string('caso_lugar_hecho', 160)->nullable();
            $table->string('caso_zona', 120)->nullable();
            $table->string('caso_direccion', 160)->nullable();
            $table->text('caso_descripcion')->nullable();               // TEXT (crucial)
            $table->string('caso_tipologia', 120)->nullable();
            $table->string('caso_modalidad', 120)->nullable();

            $table->string('violencia_fisica', 10)->nullable();
            $table->string('violencia_psicologica', 10)->nullable();
            $table->string('violencia_sexual', 10)->nullable();
            $table->string('violencia_economica', 10)->nullable();

            $table->string('seguimiento_area', 80)->nullable();
            $table->string('seguimiento_area_social', 120)->nullable();
            $table->string('seguimiento_area_legal', 120)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('casos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('autorizaciones_trabajo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();

            // Fecha de registro
            $table->dateTime('fecha')->nullable();

            // ===== DATOS DEL ADOLESCENTE =====
            $table->string('primer_apellido', 120)->nullable();
            $table->string('segundo_apellido', 120)->nullable();
            $table->string('primer_nombre', 120)->nullable();
            $table->string('segundo_nombre', 120)->nullable();

            $table->string('ci', 50)->nullable();
            $table->string('edad', 10)->nullable();

            $table->string('direccion', 255)->nullable();
            $table->string('ciudad', 120)->nullable();
            $table->string('municipio', 120)->nullable();

            $table->string('telefono_1', 60)->nullable();
            $table->string('telefono_2', 60)->nullable();

            $table->string('nombre_padre', 180)->nullable();
            $table->string('nombre_madre', 180)->nullable();
            $table->string('nombre_tutor', 180)->nullable();

            $table->string('ocupacion', 180)->nullable();
            $table->string('grado_parentesco', 120)->nullable();
            $table->string('grado_instruccion', 120)->nullable();

            // ===== DATOS DE LA EMPRESA / LUGAR DE TRABAJO =====
            $table->string('razon_social', 200)->nullable();
            $table->string('representante_legal', 200)->nullable();

            // "EMPRESA" / "PERSONA_NATURAL" / "COOPERATIVA"
            $table->string('tipo', 40)->nullable();

            $table->string('direccion_empresa', 255)->nullable();
            $table->string('ciudad_empresa', 120)->nullable();
            $table->string('nit', 80)->nullable();
            $table->string('telefono_empresa', 60)->nullable();

            // ===== TIPO Y CONDICIONES DE TRABAJO =====
            $table->string('tipo_actividad', 180)->nullable();
            $table->string('remuneracion_bs', 40)->nullable();
            $table->string('forma_pago', 80)->nullable();

            // frecuencia pago (check)
            $table->boolean('pago_diario')->default(false);
            $table->boolean('pago_semanal')->default(false);
            $table->boolean('pago_quincenal')->default(false);
            $table->boolean('pago_anual')->default(false);

            $table->string('cargo_ocupar', 180)->nullable();
            $table->string('duracion_trabajo', 120)->nullable();

            $table->text('descripcion_lugar_actividad')->nullable();

            // archivo (foto/pdf escaneado)
            $table->string('url', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autorizaciones_trabajo');
    }
};

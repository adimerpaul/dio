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
            $table->string('nurej', 80)->nullable();
            $table->string('numero_juzgado', 80)->nullable();
            $table->string('numero_juzgado_padre', 80)->nullable();
            $table->string('responsable_fiscalia', 120)->nullable();
            $table->string('estado_caso', 80)->nullable();
            $table->string('estado_caso_otro', 80)->nullable();
            $table->string('respaldo', 255)->nullable();
            $table->text('observaciones')->nullable();
            $table->string('respaldo', 255)->nullable();
            $table->string('archivo_caso', 255)->nullable();
            $table->string('cud', 80)->nullable();
            $table->string('numero_apoyo_integral', 80)->nullable();
            $table->string('tipo', 80)->nullable();
            $table->string('principal', 255)->nullable();

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
            $table->string('violencia_patrimonial', 10)->nullable();
            $table->string('violencia_simbolica', 10)->nullable();
            $table->string('violencia_institucional', 10)->nullable();
            $table->string('violencia_cibernetica', 10)->nullable();

            $table->unsignedBigInteger('psicologica_user_id')->nullable();
            $table->foreign('psicologica_user_id')->references('id')->on('users');
            $table->unsignedBigInteger('trabajo_social_user_id')->nullable();
            $table->foreign('trabajo_social_user_id')->references('id')->on('users');
            $table->unsignedBigInteger('legal_user_id')->nullable();
            $table->foreign('legal_user_id')->references('id')->on('users');


            $table->string('documento_fotocopia_carnet_denunciante', 10)->nullable();
            $table->string('documento_fotocopia_carnet_denunciado', 10)->nullable();
            $table->string('documento_placas_fotograficas_domicilio_denunciante', 10)->nullable();
            $table->string('documento_croquis_direccion_denunciado', 10)->nullable();
            $table->string('documento_placas_fotograficas_domicilio_denunciado', 10)->nullable();
            $table->string('documento_ciudadania_digital', 10)->nullable();
            $table->string('documento_otros', 160)->nullable();
            $table->string('documento_otros_detalle', 160)->nullable();
            $table->string('documento_certificado_nacimiento', 160)->nullable();
            $table->string('documento_certificado_matrimonio', 160)->nullable();
            $table->string('documento_tres_testigos', 160)->nullable();
            $table->string('documento_contrato_pago', 160)->nullable();
            $table->string('documento_libreta_notas', 160)->nullable();
            $table->string('documento_numero_cuenta', 160)->nullable();
//• INICIA EL PROCESO CON HOJA DE RUTA EN SECRETARIA, CON UNA  NOTA DE DENUNCIA DEL DIRECTOR (HOJA DE RUTA:XXXX)
//•	FOTOCOPIA DE CI. DE LA VICTIMA Y DENUNCIANTE
//•	NOTA DEL DIRECTOR
//•	NOTA DEL DISTRITAL
//•	NOTA DEL DEFENSOR DEL PUEBLO
            $table->string('documento_fotocopia_ci_victima', 160)->nullable();
            $table->string('documento_fotocopia_ci_denunciante', 160)->nullable();
            $table->string('documento_nota_director', 160)->nullable();
            $table->string('documento_nota_distrital', 160)->nullable();
            $table->string('documento_nota_defensor_pueblo', 160)->nullable();

            $table->dateTime('fecha_apertura_caso')->nullable();
            $table->dateTime('fecha_derivacion_psicologica')->nullable();
            $table->dateTime('fecha_informe_area_social')->nullable();
            $table->dateTime('fecha_informe_area_psicologica')->nullable();
            $table->dateTime('fecha_informe_trabajo_social')->nullable();
            $table->dateTime('fecha_derivacion_area_legal')->nullable();
            $table->dateTime('fecha_derivacion_area_psicologica')->nullable();
            $table->dateTime('fecha_aceptacion_area_psicologica')->nullable();
            $table->dateTime('fecha_derivacion_area_social')->nullable();
            $table->dateTime('fecha_aceptacion_area_social')->nullable();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('casos');
    }
};

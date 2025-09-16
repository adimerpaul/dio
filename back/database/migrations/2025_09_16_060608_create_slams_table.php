<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('slams', function (Blueprint $table) {
            $table->id();

            // ===== 1) DATOS DEL ADULTO MAYOR =====
            $table->date('fecha_registro')->nullable();
            $table->string('numero_apoyo_integral', 80)->nullable();
            $table->string('numero_caso', 40)->nullable()->index();
            $table->decimal('am_latitud', 10, 7)->nullable();
            $table->decimal('am_longitud', 10, 7)->nullable();
            $table->string('am_extravio')->nullable();
            $table->string('am_medicina')->nullable();
            $table->string('am_fisioterapia')->nullable();

//            for ($i = 1; $i <= 6; $i++) {
//                $table->string('am_nombres{$i}', 120)->nullable(); // Permitir hasta 6 nombres
//                $table->string('am_paterno{$i}', 80)->nullable();
//                $table->string('am_materno{$i}', 80)->nullable();
//            }

            // Documentos de identidad (en el formulario hay 2 filas, dejamos campos "A" y "B")
//            $table->string('am_doc_tipo_a', 40)->nullable();       // CI / otro
//            $table->string('am_doc_num_a', 40)->nullable();
//            $table->unsignedSmallInteger('am_doc_dia_a')->nullable();
//            $table->unsignedSmallInteger('am_doc_mes_a')->nullable();
//            $table->unsignedSmallInteger('am_doc_anio_a')->nullable();
//            $table->string('am_doc_lugar_a', 120)->nullable();
//
//            $table->string('am_doc_tipo_b', 40)->nullable();
//            $table->string('am_doc_num_b', 40)->nullable();
//            $table->unsignedSmallInteger('am_doc_dia_b')->nullable();
//            $table->unsignedSmallInteger('am_doc_mes_b')->nullable();
//            $table->unsignedSmallInteger('am_doc_anio_b')->nullable();
//            $table->string('am_doc_lugar_b', 120)->nullable();
//
//            $table->string('am_lugar_nacimiento', 120)->nullable();
//            $table->string('am_edad', 10)->nullable();
//            $table->string('am_domicilio', 160)->nullable();
//            $table->string('am_estado_civil', 40)->nullable();
//
//            // Ocupación e ingresos
//            $table->string('am_ocupacion_1', 120)->nullable();     // p.ej. AGRICULTOR
//            $table->string('am_ocupacion_2', 120)->nullable();     // p.ej. LABORES DE CASA
//            $table->string('am_ingresos', 160)->nullable();        // p.ej. RENTA DIGNIDAD

            // Idiomas (checks)
            $table->boolean('am_idioma_castellano')->default(false);
            $table->boolean('am_idioma_quechua')->default(false);
            $table->boolean('am_idioma_aymara')->default(false);
            $table->string('am_idioma_otros', 120)->nullable();

            // Teléfonos de referencia
            $table->string('ref_tel_fijo', 40)->nullable();
            $table->string('ref_tel_movil', 40)->nullable();
            $table->string('ref_tel_movil_alt', 40)->nullable();

            // ===== 3) GRUPO FAMILIAR RÁPIDO =====
            for ($i = 1; $i <= 6; $i++) {
                $table->string("fam{$i}_nombres", 120)->nullable();    // Nombres y apellidos
                $table->string("fam{$i}_edad", 10)->nullable();
                $table->string("fam{$i}_parentesco", 40)->nullable();
                $table->string("fam{$i}_estado_civil", 40)->nullable();
                $table->string("fam{$i}_ocupacion", 120)->nullable();
            }

            // ===== 4) DATOS DEL DENUNCIADO/A =====
            $table->string('den_nombres', 120)->nullable();
            $table->string('den_paterno', 80)->nullable();
            $table->string('den_materno', 80)->nullable();
            $table->string('den_edad', 10)->nullable();
            $table->string('den_domicilio', 160)->nullable();
            $table->string('den_estado_civil', 40)->nullable();

            $table->string('den_idioma', 80)->nullable();          // p.ej. CASTELLANO
            $table->string('den_grado_instruccion', 80)->nullable(); // p.ej. TÉCNICO
            $table->string('den_ocupacion', 120)->nullable();        // p.ej. MECÁNICO

            // ===== 5) BREVE CIRCUNSTANCIA DEL HECHO =====
            $table->text('hecho_descripcion')->nullable();

            // ===== 6) TIPOLOGÍA (checks) =====
            $table->boolean('tip_violencia_fisica')->default(false);
            $table->boolean('tip_violencia_psicologica')->default(false);
            $table->boolean('tip_abandono')->default(false);
            $table->boolean('tip_apoyo_integral')->default(false);

            // ===== 7) SEGUIMIENTO DEL CASO (checks) =====
            $table->boolean('seg_trabajo_legal')->default(false);
            $table->boolean('seg_trabajo_social')->default(false);
            $table->boolean('seg_psicologico')->default(false);

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

    public function down(): void {
        Schema::dropIfExists('slams');
    }
};

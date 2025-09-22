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
        Schema::create('caso_denunciantes', function (Blueprint $table) {
            $table->id();
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
        Schema::dropIfExists('caso_denunciantes');
    }
};

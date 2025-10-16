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
        Schema::create('victimas', function (Blueprint $table) {
            $table->id();
            $table->string('nombres_apellidos')->nullable();
            $table->string('ci')->nullable();
            $table->string('tipo_documento')->nullable();
            $table->string('tipo_documento_otro')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('lugar_nacimiento')->nullable();
            $table->integer('edad')->nullable();
            $table->string('sexo')->nullable();
            $table->string('estado_civil')->nullable();
            $table->string('ocupacion')->nullable();
            $table->string('idioma')->nullable();
            $table->string('domicilio')->nullable();
            $table->string('telefono')->nullable();

            // Campos específicos según área
            $table->boolean('gestante')->nullable(); // DNA
            $table->boolean('estudia')->nullable();
            $table->string('lugar_estudio')->nullable();
            $table->string('grado_curso')->nullable();
            $table->boolean('trabaja')->nullable();
            $table->string('lugar_trabajo')->nullable();

            $table->boolean('es_denunciante')->default(false);

            $table->unsignedBigInteger('caso_id')->nullable();
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
        Schema::dropIfExists('victimas');
    }
};

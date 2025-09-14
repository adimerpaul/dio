<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dnas', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 50)->nullable();
            $table->date('fecha_atencion')->nullable();
            $table->enum('tipo_proceso', [
                'PROCESO_PENAL','PROCESO_FAMILIAR','PROCESO_NNA','APOYO_INTEGRAL'
            ]);
            $table->string('principal')->nullable();
            $table->string('zona', 120)->nullable();
            $table->string('area', 50)->default('DNA');

            $table->string('domicilio')->nullable();
            $table->string('telefono', 100)->nullable();

            // Denunciado
            $table->string('denunciado_nombre')->nullable();
            $table->string('denunciado_sexo', 30)->nullable();
            $table->unsignedTinyInteger('denunciado_edad')->nullable();
            $table->string('denunciado_relacion')->nullable();
            $table->string('denunciado_ci', 100)->nullable();
            $table->string('denunciado_domicilio')->nullable();
            $table->string('denunciado_telefono', 100)->nullable();
            $table->string('denunciado_lugar_trabajo')->nullable();
            $table->string('denunciado_ocupacion')->nullable();

            // Denunciante
            $table->string('denunciante_nombre')->nullable();
            $table->string('denunciante_sexo', 30)->nullable();
            $table->unsignedTinyInteger('denunciante_edad')->nullable();
            $table->string('denunciante_ci', 100)->nullable();
            $table->string('denunciante_domicilio')->nullable();
            $table->string('denunciante_telefono', 100)->nullable();
            $table->string('denunciante_lugar_trabajo')->nullable();
            $table->string('denunciante_ocupacion')->nullable();

            $table->text('descripcion')->nullable();

            // Relaciones
            $table->foreignId('abogado_user_id')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dnas');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dna_menores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dna_id')->constrained('dnas')->cascadeOnDelete();

            $table->string('nombre')->nullable();
            $table->boolean('gestante')->default(false);
            $table->unsignedTinyInteger('edad_anios')->nullable();
            $table->unsignedTinyInteger('edad_meses')->nullable();
            $table->string('sexo', 30)->nullable();
            $table->boolean('cert_nac')->default(false);
            $table->boolean('estudia')->default(false);
            $table->string('ultimo_curso')->nullable();
            $table->string('tipo_trabajo')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dna_menores');
    }
};

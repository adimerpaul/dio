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
        Schema::create('caso_menors', function (Blueprint $table) {
            $table->id();
            $table->string('nombres', 120)->nullable();
            $table->string('paterno', 80)->nullable();
            $table->string('materno', 80)->nullable();
            $table->boolean('gestante')->nullable();
            $table->integer('edad_anios')->nullable();
            $table->integer('edad_meses')->nullable();
            $table->string('sexo', 20)->nullable();
            $table->string('ciudad_nacimiento', 80)->nullable();
            $table->string('estudia', 20)->nullable();
            $table->string('ultimo_curso', 80)->nullable();
            $table->string('tipo_trabajo', 120)->nullable();
            $table->string('ci', 40)->nullable()->index();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('lugar_nacimiento', 120)->nullable();
            $table->string('edad', 10)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('caso_id')->nullable();
            $table->foreign('caso_id')->references('id')->on('casos')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caso_menors');
    }
};

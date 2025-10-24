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
        Schema::create('juventud_talleres', function (Blueprint $table) {
            $table->id();
            $table->string('actividad', 160);
            $table->string('area', 120)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('poblacion', 120)->nullable();
            $table->string('lugar', 160)->nullable();
            $table->string('responsables', 160)->nullable();
            $table->string('colaboracion', 160)->nullable();
            $table->text('observaciones')->nullable();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_final')->nullable();
            $table->string('color', 7)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juventud_talleres');
    }
};

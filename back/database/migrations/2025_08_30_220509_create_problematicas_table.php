<?php

// database/migrations/2025_01_01_000000_create_problematicas_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('problematicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caso_id')->constrained('casos');
            $table->foreignId('user_id')->constrained('users'); // quien registró
            $table->date('fecha')->nullable();                 // fecha del reporte
            $table->string('titulo');                          // problemática principal
            $table->text('detalle')->nullable();               // descripción / antecedentes
            $table->text('acciones_inmediatas')->nullable();   // "Acciones inmediatas y realizadas"
            $table->text('observaciones')->nullable();         // notas/observ.
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void {
        Schema::dropIfExists('problematicas');
    }
};

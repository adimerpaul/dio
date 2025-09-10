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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            // Opcional: vincular a un Caso
            $table->foreignId('caso_id')->nullable()->constrained('casos')->nullOnDelete();

            // Dueña/o del evento (psicóloga/o u otro profesional)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Datos del evento
            $table->string('title');               // Título visible en calendario
            $table->text('notes')->nullable();     // Notas
            $table->string('location')->nullable();// Lugar/consulta
            $table->dateTime('start');             // Inicio
            $table->dateTime('end')->nullable();   // Fin (nullable => citas “slot”)
            $table->boolean('all_day')->default(false);

            // Estado y estilo
            $table->enum('status', ['Programado','Reprogramado','Atendido','Cancelado'])->default('Programado');
            $table->string('color')->nullable();   // Personalización (opcional)

            // Metadatos
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
//            $table->timestamps();
            $table->softDeletes();

            // Índices útiles
            $table->index(['user_id', 'start']);
            $table->index(['status']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};

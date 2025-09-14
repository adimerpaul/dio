<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dna_familiares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dna_id')->constrained('dnas')->cascadeOnDelete();

            $table->string('nombre')->nullable();
            $table->string('parentesco')->nullable();
            $table->unsignedTinyInteger('edad')->nullable();
            $table->string('sexo', 30)->nullable();
            $table->string('instruccion')->nullable();
            $table->string('ocupacion')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dna_familiares');
    }
};

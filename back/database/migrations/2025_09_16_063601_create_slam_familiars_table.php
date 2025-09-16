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
        Schema::create('slam_familiars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slam_id')->constrained('slams')->cascadeOnDelete();
            $table->string('nombre')->nullable();
            $table->string('paterno')->nullable();
            $table->string('materno')->nullable();
            $table->string('parentesco', 60)->nullable();
            $table->unsignedTinyInteger('edad')->nullable();
            $table->string('sexo', 15)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slam_familiars');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turnos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->enum('tipo', ['diurno', 'nocturno', 'rotativo', 'flexible'])->default('diurno');
            $table->time('hora_entrada');
            $table->time('hora_salida');
            $table->unsignedSmallInteger('tolerancia_minutos')->default(0);
            $table->boolean('cruza_medianoche')->default(false);
            $table->json('patron_rotativo')->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turnos');
    }
};

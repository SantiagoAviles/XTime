<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reglas_horas_extra', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->default('Ley N° 27671');
            $table->decimal('recargo_primeras_horas', 5, 2)->default(25.00);
            $table->unsignedTinyInteger('umbral_primeras_horas')->default(2);
            $table->decimal('recargo_adicional', 5, 2)->default(35.00);
            $table->boolean('aplica_nocturno')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reglas_horas_extra');
    }
};

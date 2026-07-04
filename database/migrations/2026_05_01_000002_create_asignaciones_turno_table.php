<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones_turno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->foreignId('turno_id')->constrained('turnos')->restrictOnDelete();
            $table->date('vigente_desde');
            $table->date('vigente_hasta')->nullable();
            $table->foreignId('asignada_por_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->index(['empleado_id', 'vigente_desde']);
            $table->index(['turno_id', 'vigente_desde']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones_turno');
    }
};

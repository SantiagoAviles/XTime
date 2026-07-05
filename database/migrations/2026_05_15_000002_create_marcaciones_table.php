<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marcaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->foreignId('turno_id')->nullable()->constrained('turnos')->nullOnDelete();
            $table->foreignId('metodo_marcacion_id')->constrained('metodos_marcacion')->restrictOnDelete();
            $table->date('fecha');
            $table->dateTime('hora_entrada')->nullable();
            $table->dateTime('hora_salida')->nullable();
            $table->decimal('horas_trabajadas', 5, 2)->default(0);
            $table->decimal('horas_extra', 5, 2)->default(0);
            $table->enum('estado', ['normal', 'tardanza', 'ausencia', 'justificada'])->default('normal');
            $table->foreignId('registrada_por_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('motivo_manual')->nullable();
            $table->string('ip_origen', 45)->nullable();
            $table->timestamps();

            $table->unique(['empleado_id', 'fecha']);
            $table->index(['fecha', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marcaciones');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('justificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->foreignId('marcacion_id')->nullable()->constrained('marcaciones')->nullOnDelete();
            $table->date('fecha');
            $table->enum('tipo', ['ausencia', 'tardanza'])->default('ausencia');
            $table->text('motivo');
            $table->string('adjunto_path')->nullable();
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->foreignId('revisada_por_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('comentario_revisor')->nullable();
            $table->dateTime('revisada_at')->nullable();
            $table->timestamps();

            $table->unique(['empleado_id', 'fecha', 'tipo']);
            $table->index(['estado', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('justificaciones');
    }
};

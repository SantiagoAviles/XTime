<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->string('nombres');
            $table->string('apellidos');
            $table->char('dni', 8)->unique();
            $table->string('cargo')->nullable();
            $table->char('telefono', 9)->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->string('qr_code')->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};

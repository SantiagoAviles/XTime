<?php

namespace Tests\Feature\Horarios;

use App\Models\Turno;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TurnoCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function admin(): User
    {
        $u = User::factory()->create(['is_active' => true]);
        $u->assignRole('Administrador');
        return $u;
    }

    public function test_admin_puede_crear_turno_diurno(): void
    {
        $resp = $this->actingAs($this->admin())->post('/turnos', [
            'nombre' => 'Test diurno',
            'tipo' => 'diurno',
            'hora_entrada' => '08:00',
            'hora_salida' => '17:00',
            'tolerancia_minutos' => 10,
        ]);

        $resp->assertRedirect();
        $this->assertDatabaseHas('turnos', ['nombre' => 'Test diurno']);
    }

    public function test_turno_nocturno_calcula_cruce_medianoche(): void
    {
        $resp = $this->actingAs($this->admin())->post('/turnos', [
            'nombre' => 'Nocturno test',
            'tipo' => 'nocturno',
            'hora_entrada' => '22:00',
            'hora_salida' => '06:00',
            'cruza_medianoche' => 1,
        ]);

        $resp->assertRedirect();
        $turno = Turno::where('nombre', 'Nocturno test')->first();
        $this->assertTrue((bool) $turno->cruza_medianoche);
        $this->assertEqualsWithDelta(8.0, $turno->duracionEnHoras(), 0.01);
    }

    public function test_nombre_turno_unico(): void
    {
        Turno::create([
            'nombre' => 'X', 'tipo' => 'diurno',
            'hora_entrada' => '08:00', 'hora_salida' => '17:00',
        ]);

        $resp = $this->actingAs($this->admin())->post('/turnos', [
            'nombre' => 'X', 'tipo' => 'diurno',
            'hora_entrada' => '08:00', 'hora_salida' => '17:00',
        ]);

        $resp->assertSessionHasErrors('nombre');
    }
}

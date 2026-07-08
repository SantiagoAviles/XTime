<?php

namespace Tests\Feature\Asistencias;

use App\Domain\Asistencias\MarcacionService;
use App\Domain\Asistencias\QrService;
use App\Models\Area;
use App\Models\AsignacionTurno;
use App\Models\Empleado;
use App\Models\MetodoMarcacion;
use App\Models\Turno;
use Carbon\CarbonImmutable;
use Database\Seeders\MetodoMarcacionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarcacionQrTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(MetodoMarcacionSeeder::class);
    }

    private function empleadoConTurno(string $entrada = '08:00', string $salida = '17:00', int $tolerancia = 10): Empleado
    {
        $area = Area::create(['nombre' => 'Op', 'is_active' => true]);
        $emp  = Empleado::create([
            'area_id' => $area->id, 'nombres' => 'Ana', 'apellidos' => 'Soto',
            'dni' => '12340000', 'estado' => 'activo',
        ]);
        $turno = Turno::create([
            'nombre' => 'T1', 'tipo' => 'diurno',
            'hora_entrada' => $entrada, 'hora_salida' => $salida,
            'tolerancia_minutos' => $tolerancia,
        ]);
        AsignacionTurno::create([
            'empleado_id' => $emp->id, 'turno_id' => $turno->id,
            'vigente_desde' => now()->subDays(30)->toDateString(),
        ]);
        return $emp->fresh();
    }

    public function test_genera_qr_codigo_unico_para_empleado(): void
    {
        $emp = $this->empleadoConTurno();
        $qr = app(QrService::class);

        $codigo = $qr->asegurarCodigo($emp);
        $this->assertNotEmpty($codigo);
        $this->assertSame($codigo, $qr->asegurarCodigo($emp->fresh()), 'Es idempotente');
    }

    public function test_entrada_normal_dentro_de_tolerancia(): void
    {
        $emp = $this->empleadoConTurno('08:00', '17:00', 10);

        /** @var MarcacionService $svc */
        $svc = app(MarcacionService::class);
        $r = $svc->registrar(
            empleado: $emp,
            codigoMetodo: MetodoMarcacion::CODIGO_QR,
            momento: CarbonImmutable::today()->setTime(8, 5)
        );

        $this->assertTrue($r['ok']);
        $this->assertSame(MarcacionService::OK_ENTRADA, $r['codigo']);
        $this->assertSame('normal', $r['marcacion']->estado);
    }

    public function test_entrada_fuera_de_tolerancia_se_marca_tardanza(): void
    {
        $emp = $this->empleadoConTurno('08:00', '17:00', 10);

        $svc = app(MarcacionService::class);
        $r = $svc->registrar(
            empleado: $emp,
            codigoMetodo: MetodoMarcacion::CODIGO_QR,
            momento: CarbonImmutable::today()->setTime(8, 30)
        );

        $this->assertTrue($r['ok']);
        $this->assertSame('tardanza', $r['marcacion']->estado);
    }

    public function test_segunda_marcacion_registra_salida_y_calcula_horas(): void
    {
        $emp = $this->empleadoConTurno('08:00', '17:00', 10);

        $svc = app(MarcacionService::class);
        $svc->registrar($emp, MetodoMarcacion::CODIGO_QR, CarbonImmutable::today()->setTime(8, 0));
        $r2 = $svc->registrar($emp, MetodoMarcacion::CODIGO_QR, CarbonImmutable::today()->setTime(17, 0));

        $this->assertTrue($r2['ok']);
        $this->assertSame(MarcacionService::OK_SALIDA, $r2['codigo']);
        $this->assertEqualsWithDelta(9.0, (float) $r2['marcacion']->horas_trabajadas, 0.01);
    }

    public function test_tercera_marcacion_es_rechazada_como_duplicado(): void
    {
        $emp = $this->empleadoConTurno();

        $svc = app(MarcacionService::class);
        $svc->registrar($emp, MetodoMarcacion::CODIGO_QR, CarbonImmutable::today()->setTime(8, 0));
        $svc->registrar($emp, MetodoMarcacion::CODIGO_QR, CarbonImmutable::today()->setTime(17, 0));
        $r3 = $svc->registrar($emp, MetodoMarcacion::CODIGO_QR, CarbonImmutable::today()->setTime(18, 0));

        $this->assertFalse($r3['ok']);
        $this->assertSame(MarcacionService::ERR_DUPLICADO, $r3['codigo']);
    }

    public function test_turno_nocturno_cierra_salida_al_dia_siguiente_sin_duplicar_entrada(): void
    {
        $area = Area::create(['nombre' => 'Logística', 'is_active' => true]);
        $emp  = Empleado::create([
            'area_id' => $area->id, 'nombres' => 'Lucia', 'apellidos' => 'Vargas',
            'dni' => '55550000', 'estado' => 'activo',
        ]);
        $turno = Turno::create([
            'nombre' => 'Nocturno test', 'tipo' => 'nocturno',
            'hora_entrada' => '22:00', 'hora_salida' => '06:00',
            'tolerancia_minutos' => 10, 'cruza_medianoche' => true,
        ]);
        AsignacionTurno::create([
            'empleado_id' => $emp->id, 'turno_id' => $turno->id,
            'vigente_desde' => now()->subDays(30)->toDateString(),
        ]);

        $svc = app(MarcacionService::class);

        $dia1 = CarbonImmutable::today();
        $entrada = $svc->registrar($emp->fresh(), MetodoMarcacion::CODIGO_QR, $dia1->setTime(22, 10));
        $this->assertSame(MarcacionService::OK_ENTRADA, $entrada['codigo']);
        $this->assertSame($dia1->toDateString(), $entrada['marcacion']->fecha->toDateString());

        $salida = $svc->registrar($emp->fresh(), MetodoMarcacion::CODIGO_QR, $dia1->addDay()->setTime(6, 5));

        $this->assertTrue($salida['ok']);
        $this->assertSame(MarcacionService::OK_SALIDA, $salida['codigo']);
        $this->assertSame($entrada['marcacion']->id, $salida['marcacion']->id, 'Debe cerrar la misma marcación, no crear una nueva entrada');
        $this->assertEqualsWithDelta(7.92, (float) $salida['marcacion']->horas_trabajadas, 0.05);
    }

    public function test_empleado_inactivo_no_puede_marcar(): void
    {
        $emp = $this->empleadoConTurno();
        $emp->update(['estado' => 'inactivo']);

        $svc = app(MarcacionService::class);
        $r = $svc->registrar($emp->fresh(), MetodoMarcacion::CODIGO_QR);

        $this->assertFalse($r['ok']);
        $this->assertSame(MarcacionService::ERR_NO_ACTIVO, $r['codigo']);
    }
}

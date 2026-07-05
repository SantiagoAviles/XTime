<?php

namespace App\Console\Commands;

use App\Models\Empleado;
use App\Models\Feriado;
use App\Models\Justificacion;
use App\Models\Marcacion;
use App\Models\MetodoMarcacion;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class GenerarAusenciasDiariasCommand extends Command
{
    protected $signature = 'altermec:ausencias-diarias {--fecha= : YYYY-MM-DD (default: hoy)}';
    protected $description = 'Genera marcaciones de ausencia para empleados activos con turno vigente que no marcaron.';

    public function handle(): int
    {
        $fecha = $this->option('fecha')
            ? CarbonImmutable::parse($this->option('fecha'))
            : CarbonImmutable::today();

        $fechaStr = $fecha->toDateString();

        if (Feriado::esFeriado($fecha)) {
            $this->info("[{$fechaStr}] Es feriado — no se generan ausencias.");
            return self::SUCCESS;
        }

        $metodoManual = MetodoMarcacion::porCodigo(MetodoMarcacion::CODIGO_MANUAL);
        if (! $metodoManual) {
            $this->error('No existe el método MANUAL en la BD. Ejecuta MetodoMarcacionSeeder.');
            return self::FAILURE;
        }

        $empleados = Empleado::query()
            ->whereNull('deleted_at')
            ->where('estado', 'activo')
            ->with(['asignacionesTurno' => fn ($q) => $q->latest('vigente_desde')])
            ->get();

        $creadas = 0;
        $omitidas = 0;

        foreach ($empleados as $empleado) {
            $turno = $empleado->turnoVigente($fecha);
            if (! $turno) {
                $omitidas++;
                continue;
            }

            $tieneMarcacion = Marcacion::where('empleado_id', $empleado->id)
                ->whereDate('fecha', $fechaStr)
                ->exists();

            if ($tieneMarcacion) {
                $omitidas++;
                continue;
            }

            $estaJustificado = Justificacion::where('empleado_id', $empleado->id)
                ->whereDate('fecha', $fechaStr)
                ->where('estado', Justificacion::ESTADO_APROBADA)
                ->exists();

            $estado = $estaJustificado ? Marcacion::ESTADO_JUSTIFICADA : Marcacion::ESTADO_AUSENCIA;

            Marcacion::create([
                'empleado_id'          => $empleado->id,
                'turno_id'             => $turno->id,
                'metodo_marcacion_id'  => $metodoManual->id,
                'fecha'                => $fechaStr,
                'estado'               => $estado,
                'motivo_manual'        => 'Generada automáticamente por scheduler',
            ]);
            $creadas++;
        }

        activity('marcaciones')
            ->withProperties(['fecha' => $fechaStr, 'creadas' => $creadas, 'omitidas' => $omitidas])
            ->event('scheduler_ausencias_ejecutado')
            ->log('scheduler_ausencias_ejecutado');

        $this->info("[{$fechaStr}] Creadas: {$creadas}, omitidas: {$omitidas}.");

        return self::SUCCESS;
    }
}

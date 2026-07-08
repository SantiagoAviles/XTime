<?php

namespace App\Domain\Asistencias;

use App\Domain\Horarios\CalculadoraHorasExtraService;
use App\Models\Empleado;
use App\Models\Marcacion;
use App\Models\MetodoMarcacion;
use App\Models\Turno;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class MarcacionService
{
    public function __construct(
        private readonly CalculadoraHorasExtraService $calculadora,
    ) {}

    /**
     * Excepciones de negocio.
     */
    public const ERR_NO_ACTIVO       = 'EMPLEADO_NO_ACTIVO';
    public const ERR_SIN_TURNO       = 'SIN_TURNO_VIGENTE';
    public const ERR_DUPLICADO       = 'MARCACION_DUPLICADA';
    public const OK_ENTRADA          = 'ENTRADA_REGISTRADA';
    public const OK_SALIDA           = 'SALIDA_REGISTRADA';

    /**
     * Punto de entrada principal. Detecta automáticamente si corresponde
     * registrar entrada o salida según la marcación previa del día.
     *
     * @return array{
     *     ok: bool,
     *     codigo: string,
     *     mensaje: string,
     *     marcacion: ?Marcacion,
     *     empleado: ?Empleado,
     * }
     */
    public function registrar(
        Empleado $empleado,
        string $codigoMetodo,
        ?CarbonImmutable $momento = null,
        ?User $registradoPor = null,
        ?string $motivoManual = null,
        ?string $ip = null,
    ): array {
        $momento = $momento ?? CarbonImmutable::now();
        $fecha   = $momento->toDateString();

        if (! $empleado->estaActivo()) {
            return $this->err(self::ERR_NO_ACTIVO, 'El empleado no se encuentra activo.', $empleado);
        }

        $turno = $empleado->turnoVigente($momento);
        $esManual = $codigoMetodo === MetodoMarcacion::CODIGO_MANUAL;

        if (! $turno && ! $esManual) {
            return $this->err(self::ERR_SIN_TURNO, 'El empleado no tiene turno vigente para hoy.', $empleado);
        }

        $metodo = MetodoMarcacion::porCodigo($codigoMetodo);
        if (! $metodo) {
            return $this->err('METODO_INVALIDO', 'Método de marcación no reconocido.', $empleado);
        }

        return DB::transaction(function () use ($empleado, $turno, $metodo, $momento, $fecha, $registradoPor, $motivoManual, $ip) {
            // Busca una marcación abierta (sin salida) de las últimas 24h: cubre
            // turnos que cruzan medianoche, donde la salida ocurre en la fecha siguiente
            // a la de la entrada.
            $marcacionAbierta = Marcacion::query()
                ->where('empleado_id', $empleado->id)
                ->whereNull('hora_salida')
                ->where('fecha', '>=', $momento->subDay()->toDateString())
                ->orderByDesc('fecha')
                ->lockForUpdate()
                ->first();

            if ($marcacionAbierta) {
                $this->registrarSalida($marcacionAbierta, $turno, $momento, $registradoPor, $motivoManual, $ip);
                $this->auditar('marcacion_salida', $marcacionAbierta, $registradoPor, $ip);
                return $this->ok(self::OK_SALIDA, 'Salida registrada correctamente.', $empleado, $marcacionAbierta);
            }

            $marcacionHoy = Marcacion::query()
                ->where('empleado_id', $empleado->id)
                ->whereDate('fecha', $fecha)
                ->lockForUpdate()
                ->first();

            if ($marcacionHoy) {
                return $this->err(self::ERR_DUPLICADO, 'Ya tiene entrada y salida registradas hoy.', $empleado, $marcacionHoy);
            }

            $marcacion = $this->crearEntrada($empleado, $turno, $metodo, $momento, $registradoPor, $motivoManual, $ip);
            $this->auditar('marcacion_entrada', $marcacion, $registradoPor, $ip);
            return $this->ok(self::OK_ENTRADA, 'Entrada registrada correctamente.', $empleado, $marcacion);
        });
    }

    private function crearEntrada(
        Empleado $empleado,
        ?Turno $turno,
        MetodoMarcacion $metodo,
        CarbonImmutable $momento,
        ?User $registradoPor,
        ?string $motivoManual,
        ?string $ip,
    ): Marcacion {
        $estado = Marcacion::ESTADO_NORMAL;

        if ($turno) {
            $estado = $this->esTardanza($turno, $momento) ? Marcacion::ESTADO_TARDANZA : Marcacion::ESTADO_NORMAL;
        }

        return Marcacion::create([
            'empleado_id'            => $empleado->id,
            'turno_id'               => $turno?->id,
            'metodo_marcacion_id'    => $metodo->id,
            'fecha'                  => $momento->toDateString(),
            'hora_entrada'           => $momento->toDateTimeString(),
            'estado'                 => $estado,
            'registrada_por_user_id' => $registradoPor?->id,
            'motivo_manual'          => $motivoManual,
            'ip_origen'              => $ip,
        ]);
    }

    private function registrarSalida(
        Marcacion $marcacion,
        ?Turno $turno,
        CarbonImmutable $momento,
        ?User $registradoPor,
        ?string $motivoManual,
        ?string $ip,
    ): void {
        $entrada = CarbonImmutable::parse($marcacion->hora_entrada);
        $horas   = max(0, round($entrada->diffInMinutes($momento) / 60, 2));

        $turnoEvaluar = $turno ?? $marcacion->turno;
        $extras = 0.0;
        if ($turnoEvaluar) {
            $extras = (float) $this->calculadora->calcular($turnoEvaluar, $horas)['horas_extra_total'];
        }

        $marcacion->update([
            'hora_salida'      => $momento->toDateTimeString(),
            'horas_trabajadas' => $horas,
            'horas_extra'      => $extras,
            'motivo_manual'    => $motivoManual ?? $marcacion->motivo_manual,
            'ip_origen'        => $ip ?? $marcacion->ip_origen,
        ]);
    }

    private function esTardanza(Turno $turno, CarbonImmutable $momento): bool
    {
        $teorica = CarbonImmutable::parse(
            $momento->toDateString() . ' ' . \Carbon\Carbon::parse($turno->hora_entrada)->format('H:i:s')
        );

        return $momento->diffInMinutes($teorica, false) < -$turno->tolerancia_minutos;
    }

    private function auditar(string $evento, Marcacion $marcacion, ?User $autor, ?string $ip): void
    {
        activity('marcaciones')
            ->causedBy($autor)
            ->performedOn($marcacion)
            ->withProperties([
                'empleado_id' => $marcacion->empleado_id,
                'fecha'       => $marcacion->fecha?->toDateString(),
                'estado'      => $marcacion->estado,
                'metodo_id'   => $marcacion->metodo_marcacion_id,
                'ip'          => $ip,
                'manual'      => ! is_null($marcacion->registrada_por_user_id),
            ])
            ->event($evento)
            ->log($evento);
    }

    private function ok(string $codigo, string $mensaje, Empleado $empleado, ?Marcacion $marcacion = null): array
    {
        return [
            'ok'        => true,
            'codigo'    => $codigo,
            'mensaje'   => $mensaje,
            'marcacion' => $marcacion,
            'empleado'  => $empleado,
        ];
    }

    private function err(string $codigo, string $mensaje, ?Empleado $empleado = null, ?Marcacion $marcacion = null): array
    {
        return [
            'ok'        => false,
            'codigo'    => $codigo,
            'mensaje'   => $mensaje,
            'marcacion' => $marcacion,
            'empleado'  => $empleado,
        ];
    }
}

<?php

namespace App\Domain\Justificaciones;

use App\Models\Empleado;
use App\Models\Justificacion;
use App\Models\Marcacion;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JustificacionService
{
    public function crear(Empleado $empleado, array $data, ?UploadedFile $adjunto = null): Justificacion
    {
        return DB::transaction(function () use ($empleado, $data, $adjunto) {
            $marcacion = Marcacion::where('empleado_id', $empleado->id)
                ->whereDate('fecha', $data['fecha'])
                ->first();

            $adjuntoPath = null;
            if ($adjunto) {
                $adjuntoPath = $adjunto->store("justificaciones/{$empleado->id}", 'public');
            }

            $j = Justificacion::create([
                'empleado_id'  => $empleado->id,
                'marcacion_id' => $marcacion?->id,
                'fecha'        => $data['fecha'],
                'tipo'         => $data['tipo'],
                'motivo'       => $data['motivo'],
                'adjunto_path' => $adjuntoPath,
                'estado'       => Justificacion::ESTADO_PENDIENTE,
            ]);

            activity('justificaciones')
                ->causedBy($empleado->user)
                ->performedOn($j)
                ->event('justificacion_creada')
                ->log('justificacion_creada');

            return $j->fresh();
        });
    }

    public function aprobar(Justificacion $j, User $revisor, ?string $comentario = null): void
    {
        DB::transaction(function () use ($j, $revisor, $comentario) {
            $j->update([
                'estado'                => Justificacion::ESTADO_APROBADA,
                'revisada_por_user_id'  => $revisor->id,
                'comentario_revisor'    => $comentario,
                'revisada_at'           => now(),
            ]);

            if ($j->marcacion) {
                $j->marcacion->update(['estado' => Marcacion::ESTADO_JUSTIFICADA]);
            }

            Notificacion::create([
                'empleado_id' => $j->empleado_id,
                'titulo'      => 'Tu justificación fue APROBADA',
                'mensaje'     => "Tu solicitud del " . $j->fecha->format('d/m/Y') . " fue aprobada.",
                'enlace'      => route('autogestion.justificaciones.index'),
            ]);

            activity('justificaciones')
                ->causedBy($revisor)
                ->performedOn($j)
                ->withProperties(['estado_anterior' => 'pendiente', 'estado_nuevo' => 'aprobada'])
                ->event('justificacion_aprobada')
                ->log('justificacion_aprobada');
        });
    }

    public function rechazar(Justificacion $j, User $revisor, string $comentario): void
    {
        DB::transaction(function () use ($j, $revisor, $comentario) {
            $j->update([
                'estado'                => Justificacion::ESTADO_RECHAZADA,
                'revisada_por_user_id'  => $revisor->id,
                'comentario_revisor'    => $comentario,
                'revisada_at'           => now(),
            ]);

            Notificacion::create([
                'empleado_id' => $j->empleado_id,
                'titulo'      => 'Tu justificación fue RECHAZADA',
                'mensaje'     => "Motivo: {$comentario}",
                'enlace'      => route('autogestion.justificaciones.index'),
            ]);

            activity('justificaciones')
                ->causedBy($revisor)
                ->performedOn($j)
                ->withProperties(['estado_anterior' => 'pendiente', 'estado_nuevo' => 'rechazada', 'motivo' => $comentario])
                ->event('justificacion_rechazada')
                ->log('justificacion_rechazada');
        });
    }
}

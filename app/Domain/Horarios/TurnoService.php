<?php

namespace App\Domain\Horarios;

use App\Models\AsignacionTurno;
use App\Models\Turno;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class TurnoService
{
    public function listar(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        return Turno::query()
            ->withCount(['asignaciones'])
            ->when($filtros['busqueda'] ?? null, fn (Builder $q, $b) => $q->where('nombre', 'like', "%{$b}%"))
            ->when($filtros['tipo'] ?? null, fn (Builder $q, $t) => $q->where('tipo', $t))
            ->orderBy('nombre')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function crear(array $data): Turno
    {
        return Turno::create([
            'nombre'             => $data['nombre'],
            'tipo'               => $data['tipo'],
            'hora_entrada'       => $data['hora_entrada'],
            'hora_salida'        => $data['hora_salida'],
            'tolerancia_minutos' => $data['tolerancia_minutos'] ?? 0,
            'cruza_medianoche'   => (bool) ($data['cruza_medianoche'] ?? $this->esCruceMedianoche($data['hora_entrada'], $data['hora_salida'])),
            'descripcion'        => $data['descripcion'] ?? null,
            'is_active'          => $data['is_active'] ?? true,
        ]);
    }

    public function actualizar(Turno $turno, array $data): Turno
    {
        $turno->update([
            'nombre'             => $data['nombre'],
            'tipo'               => $data['tipo'],
            'hora_entrada'       => $data['hora_entrada'],
            'hora_salida'        => $data['hora_salida'],
            'tolerancia_minutos' => $data['tolerancia_minutos'] ?? 0,
            'cruza_medianoche'   => (bool) ($data['cruza_medianoche'] ?? $turno->cruza_medianoche),
            'descripcion'        => $data['descripcion'] ?? null,
            'is_active'          => $data['is_active'] ?? $turno->is_active,
        ]);

        return $turno->refresh();
    }

    public function eliminar(Turno $turno): void
    {
        $empleadosActivos = AsignacionTurno::query()
            ->where('turno_id', $turno->id)
            ->where(function ($q) {
                $q->whereNull('vigente_hasta')
                  ->orWhere('vigente_hasta', '>=', now()->toDateString());
            })
            ->exists();

        if ($empleadosActivos) {
            throw new \DomainException('No se puede eliminar un turno con asignaciones vigentes.');
        }

        $turno->delete();
    }

    private function esCruceMedianoche(string $entrada, string $salida): bool
    {
        return strtotime($salida) <= strtotime($entrada);
    }
}

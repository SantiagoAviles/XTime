<?php

namespace App\Domain\Empleados;

use App\Models\Area;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class AreaService
{
    public function listar(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        return Area::query()
            ->withCount(['empleados' => fn ($q) => $q->whereNull('deleted_at')])
            ->when($filtros['busqueda'] ?? null, fn (Builder $q, $b) => $q->where('nombre', 'like', "%{$b}%"))
            ->when(isset($filtros['estado']), function (Builder $q) use ($filtros) {
                if ($filtros['estado'] === 'activo') {
                    $q->where('is_active', true);
                } elseif ($filtros['estado'] === 'inactivo') {
                    $q->where('is_active', false);
                }
            })
            ->orderBy('nombre')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function crear(array $data): Area
    {
        return Area::create([
            'nombre'      => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'is_active'   => $data['is_active'] ?? true,
        ]);
    }

    public function actualizar(Area $area, array $data): Area
    {
        $area->update([
            'nombre'      => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'is_active'   => $data['is_active'] ?? $area->is_active,
        ]);

        return $area->refresh();
    }

    public function eliminar(Area $area): void
    {
        if ($area->empleados()->whereNull('deleted_at')->exists()) {
            throw new \DomainException('No se puede eliminar un área con empleados activos asignados.');
        }

        $area->delete();
    }
}

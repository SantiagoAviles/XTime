<?php

namespace App\Domain\Empleados;

use App\Models\Empleado;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmpleadoService
{
    public function listar(array $filtros = [], int $perPage = 15): LengthAwarePaginator
    {
        return Empleado::query()
            ->with(['area', 'user.roles'])
            ->when($filtros['busqueda'] ?? null, function (Builder $q, string $busqueda) {
                $q->where(function ($qq) use ($busqueda) {
                    $qq->where('nombres', 'like', "%{$busqueda}%")
                       ->orWhere('apellidos', 'like', "%{$busqueda}%")
                       ->orWhere('dni', 'like', "%{$busqueda}%");
                });
            })
            ->when($filtros['area_id'] ?? null, fn (Builder $q, $id) => $q->where('area_id', $id))
            ->when($filtros['estado'] ?? null, fn (Builder $q, $e) => $q->where('estado', $e))
            ->when(isset($filtros['incluir_inactivos']) && $filtros['incluir_inactivos'], fn ($q) => $q->withTrashed())
            ->orderBy('apellidos')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function crear(array $data, ?User $creador = null): Empleado
    {
        return DB::transaction(function () use ($data, $creador) {
            $user = null;

            if (! empty($data['email'])) {
                $user = User::create([
                    'name'      => trim($data['nombres'] . ' ' . $data['apellidos']),
                    'email'     => $data['email'],
                    'password'  => Hash::make($data['password'] ?? Str::random(12)),
                    'is_active' => true,
                ]);

                if (! empty($data['rol'])) {
                    $user->assignRole($data['rol']);
                }
            }

            $empleado = Empleado::create([
                'user_id'       => $user?->id,
                'area_id'       => $data['area_id'],
                'nombres'       => $data['nombres'],
                'apellidos'     => $data['apellidos'],
                'dni'           => $data['dni'],
                'cargo'         => $data['cargo'] ?? null,
                'telefono'      => $data['telefono'] ?? null,
                'fecha_ingreso' => $data['fecha_ingreso'] ?? now()->toDateString(),
                'estado'        => $data['estado'] ?? 'activo',
            ]);

            activity('empleados')
                ->causedBy($creador)
                ->performedOn($empleado)
                ->withProperties(['dni' => $empleado->dni, 'area_id' => $empleado->area_id])
                ->event('empleado_creado')
                ->log('empleado_creado');

            return $empleado->fresh(['area', 'user']);
        });
    }

    public function actualizar(Empleado $empleado, array $data, ?User $editor = null): Empleado
    {
        return DB::transaction(function () use ($empleado, $data, $editor) {
            $empleado->fill([
                'nombres'       => $data['nombres'],
                'apellidos'     => $data['apellidos'],
                'dni'           => $data['dni'],
                'cargo'         => $data['cargo'] ?? $empleado->cargo,
                'telefono'      => $data['telefono'] ?? $empleado->telefono,
                'area_id'       => $data['area_id'],
                'fecha_ingreso' => $data['fecha_ingreso'] ?? $empleado->fecha_ingreso,
                'estado'        => $data['estado'] ?? $empleado->estado,
            ])->save();

            if ($empleado->user && ! empty($data['rol'])) {
                $empleado->user->syncRoles([$data['rol']]);
                $empleado->user->update(['name' => $empleado->nombreCompleto()]);
            }

            activity('empleados')
                ->causedBy($editor)
                ->performedOn($empleado)
                ->withProperties(['dni' => $empleado->dni])
                ->event('empleado_actualizado')
                ->log('empleado_actualizado');

            return $empleado->fresh(['area', 'user']);
        });
    }

    public function desactivar(Empleado $empleado, ?User $autor = null): void
    {
        DB::transaction(function () use ($empleado, $autor) {
            $empleado->update(['estado' => 'inactivo']);

            if ($empleado->user) {
                $empleado->user->update(['is_active' => false]);
            }

            $empleado->delete();

            activity('empleados')
                ->causedBy($autor)
                ->performedOn($empleado)
                ->withProperties(['dni' => $empleado->dni])
                ->event('empleado_dado_de_baja')
                ->log('empleado_dado_de_baja');
        });
    }

    public function reactivar(Empleado $empleado, ?User $autor = null): void
    {
        DB::transaction(function () use ($empleado, $autor) {
            $empleado->restore();
            $empleado->update(['estado' => 'activo']);

            if ($empleado->user) {
                $empleado->user->update(['is_active' => true]);
            }

            activity('empleados')
                ->causedBy($autor)
                ->performedOn($empleado)
                ->withProperties(['dni' => $empleado->dni])
                ->event('empleado_reactivado')
                ->log('empleado_reactivado');
        });
    }
}

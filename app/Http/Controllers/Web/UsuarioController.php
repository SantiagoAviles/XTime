<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $usuarios = User::query()
            ->with(['roles', 'empleado.area'])
            ->when($request->busqueda, function ($q, $busqueda) {
                $q->where(function ($qq) use ($busqueda) {
                    $qq->where('name', 'like', "%{$busqueda}%")
                       ->orWhere('email', 'like', "%{$busqueda}%");
                });
            })
            ->when($request->rol, function ($q, $rol) {
                $q->whereHas('roles', fn ($qq) => $qq->where('name', $rol));
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('seguridad.usuarios.index', [
            'usuarios' => $usuarios,
            'roles'    => Role::orderBy('name')->get(),
            'filtros'  => $request->only(['busqueda', 'rol']),
        ]);
    }

    public function edit(User $usuario): View
    {
        return view('seguridad.usuarios.edit', [
            'usuario' => $usuario->load(['roles', 'empleado']),
            'roles'   => Role::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $data = $request->validate([
            'rol'       => ['required', 'string', Rule::exists('roles', 'name')],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'rol.required' => 'Debe seleccionar un rol.',
            'rol.exists'   => 'El rol seleccionado no existe.',
        ]);

        $rolesPrevios = $usuario->roles->pluck('name')->all();
        $usuario->syncRoles([$data['rol']]);
        $usuario->update(['is_active' => (bool) ($data['is_active'] ?? $usuario->is_active)]);

        activity('usuarios')
            ->causedBy($request->user())
            ->performedOn($usuario)
            ->withProperties([
                'roles_previos' => $rolesPrevios,
                'rol_nuevo'     => $data['rol'],
                'is_active'     => $usuario->is_active,
            ])
            ->event('rol_actualizado')
            ->log('rol_actualizado');

        return redirect()
            ->route('seguridad.usuarios.index')
            ->with('success', 'Rol y estado del usuario actualizados.');
    }
}

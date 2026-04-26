<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $email = $request->validated('email');
        $throttleKey = $this->throttleKey($request, $email);

        // Verificar si está bloqueado por intentos fallidos
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'credentials' => 'Acceso bloqueado temporalmente por intentos fallidos. Intenta en 15 minutos.',
                ]);
        }

        // Intentar autenticación
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 15 * 60);

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'credentials' => 'Las credenciales no coinciden con nuestros registros.',
                ]);
        }

        $user = Auth::user();

        // Validar que el usuario esté activo
        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'credentials' => 'Usuario inactivo. Contacte al administrador.',
                ]);
        }

        // Validar que el usuario tenga al menos un rol asignado
        if ($user->roles()->count() === 0) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'credentials' => 'Usuario sin rol asignado. Contacte al administrador.',
                ]);
        }

        // Limpiar intentos fallidos al login exitoso
        RateLimiter::clear($throttleKey);

        // Regenerar sesión
        $request->session()->regenerate();

        // TODO: Registrar login en activity_log

        // Redirigir según rol principal
        return $this->redirectByRole($user);
    }

    public function logout(Request $request): RedirectResponse
    {
        // TODO: Registrar logout en activity_log

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function throttleKey(Request $request, string $email): string
    {
        return 'login-attempts:' . strtolower($email) . ':' . $request->ip();
    }

    private function redirectByRole($user): RedirectResponse
    {
        $roleName = strtolower($user->roles()->first()?->name ?? '');

        return match ($roleName) {
            'administrador', 'admin'           => redirect()->route('dashboard'),
            'rrhh'                              => redirect()->route('dashboard'),
            'supervisor'                        => redirect()->route('dashboard'),
            'jefe de operaciones'               => redirect()->route('dashboard'),
            'empleado'                          => redirect()->route('dashboard'),
            default                             => redirect()->route('dashboard'),
        };
    }
}

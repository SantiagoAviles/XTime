<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        // Lógica real de autenticación se implementa en el siguiente bloque.
        return back();
    }

    public function logout(Request $request): RedirectResponse
    {
        // Lógica real de logout se implementa en el siguiente bloque.
        return redirect()->route('login');
    }
}

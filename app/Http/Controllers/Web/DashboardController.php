<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user    = Auth::user();
        $role    = $user->roles()->first();

        return view('dashboard.index', [
            'user' => $user,
            'role' => $role,
        ]);
    }
}

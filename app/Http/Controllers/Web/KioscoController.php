<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class KioscoController extends Controller
{
    public function vista(): View
    {
        return view('asistencias.kiosko');
    }
}

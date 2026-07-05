<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class AuditoriaController extends Controller
{
    public function index(Request $request): View
    {
        $logs = Activity::query()
            ->with(['causer', 'subject'])
            ->when($request->filled('log_name'), fn ($q) => $q->where('log_name', $request->log_name))
            ->when($request->filled('event'), fn ($q) => $q->where('event', 'like', '%' . $request->event . '%'))
            ->when($request->filled('causer_email'), function ($q) use ($request) {
                $q->whereHas('causer', fn ($qq) => $qq->where('email', 'like', '%' . $request->causer_email . '%'));
            })
            ->when($request->filled('desde'), fn ($q) => $q->where('created_at', '>=', $request->desde))
            ->when($request->filled('hasta'), fn ($q) => $q->where('created_at', '<=', $request->hasta . ' 23:59:59'))
            ->latest('id')
            ->paginate(50)
            ->withQueryString();

        $logNames = Activity::query()
            ->select('log_name')
            ->distinct()
            ->orderBy('log_name')
            ->pluck('log_name');

        return view('auditoria.index', [
            'logs'      => $logs,
            'log_names' => $logNames,
            'filtros'   => $request->only(['log_name', 'event', 'causer_email', 'desde', 'hasta']),
        ]);
    }
}

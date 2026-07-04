<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

#[Fillable([
    'empleado_id', 'turno_id', 'vigente_desde', 'vigente_hasta',
    'asignada_por_user_id', 'observacion',
])]
class AsignacionTurno extends Model
{
    use LogsActivity;

    protected $table = 'asignaciones_turno';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('asignaciones_turno')
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected function casts(): array
    {
        return [
            'vigente_desde' => 'date',
            'vigente_hasta' => 'date',
        ];
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class);
    }

    public function asignadaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignada_por_user_id');
    }

    public function estaVigenteEn(\DateTimeInterface $fecha): bool
    {
        $f = $fecha instanceof \Carbon\CarbonInterface ? $fecha : \Carbon\Carbon::parse($fecha);

        if ($f->lt($this->vigente_desde)) {
            return false;
        }

        if ($this->vigente_hasta && $f->gt($this->vigente_hasta)) {
            return false;
        }

        return true;
    }
}

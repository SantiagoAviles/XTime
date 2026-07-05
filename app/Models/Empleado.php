<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

#[Fillable(['user_id', 'area_id', 'nombres', 'apellidos', 'dni', 'cargo', 'telefono', 'fecha_ingreso', 'estado', 'qr_code'])]
class Empleado extends Model
{
    use SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('empleados')
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected function casts(): array
    {
        return [
            'fecha_ingreso' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function areasSupervisadas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'area_supervisor')
            ->withTimestamps();
    }

    public function asignacionesTurno(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AsignacionTurno::class);
    }

    public function marcaciones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Marcacion::class);
    }

    public function justificaciones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Justificacion::class);
    }

    public function notificaciones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Notificacion::class);
    }

    public function nombreCompleto(): string
    {
        return trim($this->nombres . ' ' . $this->apellidos);
    }

    public function estaActivo(): bool
    {
        return $this->estado === 'activo' && is_null($this->deleted_at);
    }

    public function turnoVigente(?\DateTimeInterface $fecha = null): ?Turno
    {
        $fecha = $fecha ? \Carbon\Carbon::parse($fecha) : \Carbon\Carbon::today();

        $asignacion = $this->asignacionesTurno()
            ->where('vigente_desde', '<=', $fecha->toDateString())
            ->where(function ($q) use ($fecha) {
                $q->whereNull('vigente_hasta')
                  ->orWhere('vigente_hasta', '>=', $fecha->toDateString());
            })
            ->latest('vigente_desde')
            ->first();

        return $asignacion?->turno;
    }
}

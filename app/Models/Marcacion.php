<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

#[Fillable([
    'empleado_id', 'turno_id', 'metodo_marcacion_id', 'fecha',
    'hora_entrada', 'hora_salida', 'horas_trabajadas', 'horas_extra',
    'estado', 'registrada_por_user_id', 'motivo_manual', 'ip_origen',
])]
class Marcacion extends Model
{
    use LogsActivity;

    protected $table = 'marcaciones';

    public const ESTADO_NORMAL      = 'normal';
    public const ESTADO_TARDANZA    = 'tardanza';
    public const ESTADO_AUSENCIA    = 'ausencia';
    public const ESTADO_JUSTIFICADA = 'justificada';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('marcaciones')
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected function casts(): array
    {
        return [
            'fecha'            => 'date',
            'hora_entrada'     => 'datetime',
            'hora_salida'      => 'datetime',
            'horas_trabajadas' => 'decimal:2',
            'horas_extra'      => 'decimal:2',
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

    public function metodo(): BelongsTo
    {
        return $this->belongsTo(MetodoMarcacion::class, 'metodo_marcacion_id');
    }

    public function registradaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrada_por_user_id');
    }

    public function justificaciones(): HasMany
    {
        return $this->hasMany(Justificacion::class);
    }

    public function tieneEntrada(): bool
    {
        return ! is_null($this->hora_entrada);
    }

    public function tieneSalida(): bool
    {
        return ! is_null($this->hora_salida);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

#[Fillable([
    'empleado_id', 'marcacion_id', 'fecha', 'tipo', 'motivo',
    'adjunto_path', 'estado', 'revisada_por_user_id',
    'comentario_revisor', 'revisada_at',
])]
class Justificacion extends Model
{
    use LogsActivity;

    protected $table = 'justificaciones';

    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_APROBADA  = 'aprobada';
    public const ESTADO_RECHAZADA = 'rechazada';

    public const TIPO_AUSENCIA = 'ausencia';
    public const TIPO_TARDANZA = 'tardanza';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('justificaciones')
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected function casts(): array
    {
        return [
            'fecha'        => 'date',
            'revisada_at'  => 'datetime',
        ];
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function marcacion(): BelongsTo
    {
        return $this->belongsTo(Marcacion::class);
    }

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisada_por_user_id');
    }

    public function estaPendiente(): bool
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }
}

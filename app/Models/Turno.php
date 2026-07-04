<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

#[Fillable([
    'nombre', 'tipo', 'hora_entrada', 'hora_salida',
    'tolerancia_minutos', 'cruza_medianoche', 'patron_rotativo',
    'descripcion', 'is_active',
])]
class Turno extends Model
{
    use LogsActivity;

    protected $table = 'turnos';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('turnos')
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected function casts(): array
    {
        return [
            'hora_entrada'      => 'datetime:H:i',
            'hora_salida'       => 'datetime:H:i',
            'cruza_medianoche'  => 'boolean',
            'is_active'         => 'boolean',
            'patron_rotativo'   => 'array',
        ];
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionTurno::class);
    }

    public function marcaciones(): HasMany
    {
        return $this->hasMany(Marcacion::class);
    }

    public function duracionEnHoras(): float
    {
        $entrada = strtotime($this->hora_entrada);
        $salida  = strtotime($this->hora_salida);

        if ($this->cruza_medianoche || $salida <= $entrada) {
            $salida += 24 * 3600;
        }

        return round(($salida - $entrada) / 3600, 2);
    }
}

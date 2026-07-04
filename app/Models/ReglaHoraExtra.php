<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

#[Fillable([
    'nombre', 'recargo_primeras_horas', 'umbral_primeras_horas',
    'recargo_adicional', 'aplica_nocturno', 'is_active',
])]
class ReglaHoraExtra extends Model
{
    use LogsActivity;

    protected $table = 'reglas_horas_extra';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('reglas_horas_extra')
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected function casts(): array
    {
        return [
            'recargo_primeras_horas' => 'decimal:2',
            'recargo_adicional'      => 'decimal:2',
            'aplica_nocturno'        => 'boolean',
            'is_active'              => 'boolean',
        ];
    }

    public static function vigente(): ?self
    {
        return self::query()->where('is_active', true)->latest('id')->first();
    }
}

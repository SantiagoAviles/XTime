<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

#[Fillable(['fecha', 'descripcion', 'nacional', 'is_active'])]
class Feriado extends Model
{
    use LogsActivity;

    protected $table = 'feriados';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('feriados')
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected function casts(): array
    {
        return [
            'fecha'     => 'date',
            'nacional'  => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public static function esFeriado(\DateTimeInterface $fecha): bool
    {
        $fechaStr = \Carbon\Carbon::parse($fecha)->toDateString();

        return self::query()
            ->where('fecha', $fechaStr)
            ->where('is_active', true)
            ->exists();
    }
}

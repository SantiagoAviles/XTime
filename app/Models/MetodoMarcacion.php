<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['codigo', 'descripcion', 'is_active'])]
class MetodoMarcacion extends Model
{
    protected $table = 'metodos_marcacion';

    public const CODIGO_QR     = 'QR';
    public const CODIGO_DNI    = 'DNI';
    public const CODIGO_MANUAL = 'MANUAL';

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function marcaciones(): HasMany
    {
        return $this->hasMany(Marcacion::class);
    }

    public static function porCodigo(string $codigo): ?self
    {
        return self::query()->where('codigo', $codigo)->first();
    }
}

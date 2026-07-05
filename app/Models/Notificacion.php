<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['empleado_id', 'titulo', 'mensaje', 'enlace', 'leida_at'])]
class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected function casts(): array
    {
        return [
            'leida_at' => 'datetime',
        ];
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function marcarComoLeida(): void
    {
        $this->update(['leida_at' => now()]);
    }

    public function noLeida(): bool
    {
        return is_null($this->leida_at);
    }
}

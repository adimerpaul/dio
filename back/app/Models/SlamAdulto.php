<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class SlamAdulto extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    protected $table = 'slam_adultos';

    protected $fillable = [
        'slam_id',
        'nombre','paterno','materno',
        'documento_tipo','documento_num',
        'fecha_nacimiento','lugar_nacimiento','edad',
        'domicilio','estado_civil',
        'ocupacion_1','ocupacion_2','ingresos',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    protected $hidden = ['created_at','updated_at','deleted_at'];

    /* ===== Relaciones ===== */

    public function slam()
    {
        return $this->belongsTo(Slam::class, 'slam_id');
    }

    /* ===== Helpers opcionales ===== */

    public function getNombreCompletoAttribute(): string
    {
        return trim(($this->nombre ?? '') . ' ' . ($this->paterno ?? '') . ' ' . ($this->materno ?? ''));
    }
}

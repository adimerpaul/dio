<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class SlamFamiliar extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    protected $table = 'slam_familiars';

    protected $fillable = [
        'slam_id',
        'nombre','paterno','materno',
        'parentesco','edad','sexo','telefono',
        'estado_civil',
        'ocupacion'
    ];

    protected $casts = [
        'edad' => 'integer',
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

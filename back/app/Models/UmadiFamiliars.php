<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class UmadiFamiliars extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    protected $table = 'umadi_familiars';

    protected $fillable = [
        'umadi_id',
        'nombre','paterno','materno',
        'parentesco','edad','sexo','telefono',
    ];

    protected $casts = [
        'edad' => 'integer',
    ];

    protected $hidden = ['created_at','updated_at','deleted_at'];

    /* ===== Relaciones ===== */

    public function umadi()
    {
        return $this->belongsTo(Umadi::class, 'umadi_id');
    }

    /* ===== Helpers opcionales ===== */

    public function getNombreCompletoAttribute(): string
    {
        return trim(($this->nombre ?? '') . ' ' . ($this->paterno ?? '') . ' ' . ($this->materno ?? ''));
    }
}

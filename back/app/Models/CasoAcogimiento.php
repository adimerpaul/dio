<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
class CasoAcogimiento extends Model implements AuditableContract
{
    use SoftDeletes, AuditableTrait;

    protected $table = 'caso_acogimientos';

    protected $fillable = [
        'caso_id',
        'tipo_de_acogida',
        'centro_de_acogida',
        'cuidadora_nombre_completo',
        'cuidadora_celular',
        'cuidadora_domicilio',
        'cuidadora_ubicacion',
        'cuidadora_lat',
        'cuidadora_lng',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function caso()
    {
        return $this->belongsTo(Caso::class);
    }
}

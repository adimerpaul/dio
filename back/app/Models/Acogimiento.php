<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
class Acogimiento extends Model implements AuditableContract
{
    use AuditableTrait, SoftDeletes;
    protected $fillable = [
        'adopcion',
        'hogar',
        'nino1',
        'nino2',
        'nino3',
        'nino4',
        'nino5',
        'nino6',
        'nino7',
        'nino8',
        'nino9',
        'nino10',
        'juzgado',
        'cuidado_nombre',
        'cuidado_paterno',
        'cuidado_materno',
        'area_legal',
        'tipologia',
        'audiencia_evaluacion',
        'fecha',
        'proximas_audiencia',
        'caso_id'
    ];

    // Relaciones
    public function caso()
    {
//        return $this->belongsTo(Caso::class); one
    }
}

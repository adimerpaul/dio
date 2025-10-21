<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
class Taller extends Model implements AuditableContract
{
    use SoftDeletes, AuditableTrait;
    protected $table = 'talleres';

    protected $fillable = [
        'solicitado',
        'dia',
        'fecha_inicio',
        'fecha_final',
        'lugar',
        'colegio',
        'curso',
        'numero_asistentes',
        'tema',
        'descripcion',
        'director_nombre',
        'director_telefono',
        'encargado_taller_nombre',
        'encargado_taller_telefono',
        'direccion_ubicacion_ue_colegio',
        'dirigido_a',
        'color',
        'user_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    function user(){
        return $this->belongsTo(User::class);
    }
}

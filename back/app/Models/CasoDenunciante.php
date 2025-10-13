<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
class CasoDenunciante extends Model implements AuditableContract
{
    use SoftDeletes, AuditableTrait;
    protected $fillable=[
        'denunciante_nombres',
        'denunciante_paterno',
        'denunciante_materno',
        'denunciante_documento',
        'denunciante_nro',
        'denunciante_sexo',
        'denunciante_lugar_nacimiento',
        'denunciante_fecha_nacimiento',
        'denunciante_edad',
        'denunciante_telefono',
        'denunciante_residencia',
        'denunciante_estado_civil',
        'denunciante_relacion',
        'denunciante_grado',
        'denunciante_domicilio_actual',
        'latitud',
        'longitud',
        'caso_id',
        'denunciante_idioma',
        'denunciante_trabaja',
        'denunciante_ocupacion',
        'denunciante_ingresos',
        'denunciante_relacion_victima',
        'denunciante_relacion_denunciado',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    public function caso(){ return $this->belongsTo(Caso::class); }
}

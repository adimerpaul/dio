<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
class CasoDenunciado extends Model implements AuditableContract
{
    use SoftDeletes, AuditableTrait;
    protected $fillable = [
        'denunciado_nombres',
        'denunciado_paterno',
        'denunciado_materno',
        'denunciado_documento',
        'denunciado_nro',
        'denunciado_sexo',
        'denunciado_lugar_nacimiento',
        'denunciado_fecha_nacimiento',
        'denunciado_edad',
        'denunciado_telefono',
        'denunciado_residencia',
        'denunciado_estado_civil',
        'denunciado_relacion',
        'denunciado_grado',
        'denunciado_trabaja',
        'denunciado_prox',
        'denunciado_ocupacion',
        'denunciado_ocupacion_exacto',
        'denunciado_idioma',
        'denunciado_fijo',
        'denunciado_movil',
        'denunciado_domicilio_actual',
        'denunciado_latitud',
        'denunciado_longitud',
        'caso_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    public function caso(){ return $this->belongsTo(Caso::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Umadi extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    protected $table = 'umadis';

    protected $fillable = [
        // ===== 1) DATOS DEL ADULTO MAYOR / DENUNCIANTE =====
        'area',
        'zona',
        'fecha_registro',
        'numero_apoyo_integral',
        'numero_caso',
        'latitud',
        'longitud',
        'nombres',
        'paterno',
        'materno',
        'tipo_documento',
        'numero_documento',
        'sexo',
        'lugar_nacimiento',
        'fecha_nacimiento',
        'edad',
        'direccion',
        'estado_civil',
        'relacion_denunciado',
        'grado_instruccion',
        'trabaja',
        'ocupacion',
        'edad_aprox',
        'edada_exacto', // <- así está en tu migración
        'idioma',
        'celular1',
        'celular2',
        'telefono_fijo1',
        'telefono_fijo2',
        'direccion_actual',

        // ===== DATOS DEL DENUNCIADO =====
        'denunciado_nombres',
        'denunciado_paterno',
        'denunciado_materno',
        'denunciado_ci',
        'denunciado_ciudad_nacimiento',
        'denunciado_sexo',
        'denunciado_lugar_nacimiento',
        'denunciado_fecha_nacimiento',
        'denunciado_edad',
        'denunciado_direccion',
        'denunciado_estado_civil',
        'denunciado_idioma',
        'denunciado_grado_instruccion',
        'denunciado_ocupacion',
        'denunciado_celular1',
        'denunciado_celular2',
        'denunciado_telefono_fijo1',
        'denunciado_telefono_fijo2',
        'denunciado_direccion_actual',

        // ===== DATOS DEL HECHO / DENUNCIA =====
        'fecha_hecho',
        'relacion_denunciante',
        'direccion_hecho',
        'descripcion_hecho',

        // ===== Documentos (checks) =====
        'doc_ci',
        'doc_frontal_denunciado',
        'doc_frontal_denunciante',
        'doc_croquis',

        // Metadatos (responsables)
        'user_id',
        'psicologica_user_id',
        'trabajo_social_user_id',
        'legal_user_id',
    ];

    protected $hidden = ['created_at','updated_at','deleted_at'];


    // Familiares asociados (N)
    public function familiares()
    {
        return $this->hasMany(UmadiFamiliars::class, 'umadi_id');
    }

    // Usuarios/Responsables
    public function user()                 { return $this->belongsTo(User::class); }
    public function psicologica_user()     { return $this->belongsTo(User::class, 'psicologica_user_id'); }
    public function trabajo_social_user()  { return $this->belongsTo(User::class, 'trabajo_social_user_id'); }
    public function legal_user()           { return $this->belongsTo(User::class, 'legal_user_id'); }
    public function psicologicas()   { return $this->morphMany(Psicologica::class,  'caseable'); }
    public function informesLegales(){ return $this->morphMany(InformeLegal::class,'caseable'); }
    public function documentos()     { return $this->morphMany(Documento::class,    'caseable'); }
    public function fotografias()    { return $this->morphMany(Fotografia::class,   'caseable'); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Slam extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    protected $table = 'slams';

    protected $fillable = [
        // ===== 1) DATOS DEL ADULTO MAYOR =====
        'zona',
        'area',
        'fecha_registro',
        'numero_apoyo_integral',
        'numero_caso',
        'am_latitud',
        'am_longitud',
        'am_extravio',
        'am_medicina',
        'am_fisioterapia',

        // Idiomas (checks)
        'am_idioma_castellano',
        'am_idioma_quechua',
        'am_idioma_aymara',
        'am_idioma_otros',

        // Teléfonos de referencia
        'ref_tel_fijo',
        'ref_tel_movil',
        'ref_tel_movil_alt',

        // ===== 4) DATOS DEL DENUNCIADO/A =====
        'den_nombres',
        'den_paterno',
        'den_materno',
        'den_edad',
        'den_domicilio',
        'den_estado_civil',

        'den_idioma',          // p.ej. CASTELLANO
        'den_grado_instruccion', // p.ej. TÉCNICO
        'den_ocupacion',        // p.ej. MECÁNICO

        // ===== 5) BREVE CIRCUNSTANCIA DEL HECHO =====
        'hecho_descripcion',

        // ===== 6) TIPOLOGÍA (checks) =====
        'tip_violencia_fisica',
        'tip_violencia_psicologica',
        'tip_abandono',
        'tip_apoyo_integral',

        // ===== 7) Documentos checks =====
        'doc_ci',
        'doc_frontal_denunciado',
        'doc_frontal_denunciante',
        'doc_croquis',

        // Metadatos
        'user_id',
        'psicologica_user_id',
        'trabajo_social_user_id',
        'legal_user_id'
    ];

    protected $hidden = ['created_at','updated_at','deleted_at'];

    /* ===== Relaciones ===== */

    // Adultos mayores registrados en el caso (N)
    public function adultos()
    {
        return $this->hasMany(SlamAdulto::class, 'slam_id');
    }

    // Familiares asociados (N)
    public function familiares()
    {
        return $this->hasMany(SlamFamiliar::class, 'slam_id');
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

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
        'fecha_registro',
        'numero_apoyo_integral',
        'numero_caso',
        'am_latitud','am_longitud',
        'am_extravio','am_medicina','am_fisioterapia',

        // Idiomas y teléfonos
        'am_idioma_castellano','am_idioma_quechua','am_idioma_aymara','am_idioma_otros',
        'ref_tel_fijo','ref_tel_movil','ref_tel_movil_alt',

        // Tipología
        'tip_violencia_fisica','tip_violencia_psicologica','tip_abandono','tip_apoyo_integral',

        // Seguimiento
        'seg_trabajo_legal','seg_trabajo_social','seg_psicologico',

        // Responsables
        'user_id','psicologica_user_id','trabajo_social_user_id','legal_user_id',

        // Texto libre del hecho (si lo pones aquí)
        'hecho_descripcion',
    ];

    protected $casts = [
//        'fecha_registro' => 'date',

        // Booleans
        'am_idioma_castellano' => 'boolean',
        'am_idioma_quechua'    => 'boolean',
        'am_idioma_aymara'     => 'boolean',
        'tip_violencia_fisica'      => 'boolean',
        'tip_violencia_psicologica' => 'boolean',
        'tip_abandono'              => 'boolean',
        'tip_apoyo_integral'        => 'boolean',
        'seg_trabajo_legal'         => 'boolean',
        'seg_trabajo_social'        => 'boolean',
        'seg_psicologico'           => 'boolean',
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
}

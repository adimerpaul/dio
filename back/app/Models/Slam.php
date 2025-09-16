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
        'numero_caso',
        'am_latitud','am_longitud',

        // Adulto mayor (hasta 6 registros)
        'am_nombres1','am_paterno1','am_materno1',
        'am_nombres2','am_paterno2','am_materno2',
        'am_nombres3','am_paterno3','am_materno3',
        'am_nombres4','am_paterno4','am_materno4',
        'am_nombres5','am_paterno5','am_materno5',
        'am_nombres6','am_paterno6','am_materno6',

        // Documentos
        'am_doc_tipo_a','am_doc_num_a','am_doc_dia_a','am_doc_mes_a','am_doc_anio_a','am_doc_lugar_a',
        'am_doc_tipo_b','am_doc_num_b','am_doc_dia_b','am_doc_mes_b','am_doc_anio_b','am_doc_lugar_b',

        'am_lugar_nacimiento','am_edad','am_domicilio','am_estado_civil',
        'am_ocupacion_1','am_ocupacion_2','am_ingresos',

        // Idiomas
        'am_idioma_castellano','am_idioma_quechua','am_idioma_aymara','am_idioma_otros',

        // Teléfonos
        'ref_tel_fijo','ref_tel_movil','ref_tel_movil_alt',

        // Grupo familiar rápido
        'fam1_nombres','fam1_edad','fam1_parentesco','fam1_estado_civil','fam1_ocupacion',
        'fam2_nombres','fam2_edad','fam2_parentesco','fam2_estado_civil','fam2_ocupacion',
        'fam3_nombres','fam3_edad','fam3_parentesco','fam3_estado_civil','fam3_ocupacion',
        'fam4_nombres','fam4_edad','fam4_parentesco','fam4_estado_civil','fam4_ocupacion',
        'fam5_nombres','fam5_edad','fam5_parentesco','fam5_estado_civil','fam5_ocupacion',
        'fam6_nombres','fam6_edad','fam6_parentesco','fam6_estado_civil','fam6_ocupacion',

        // Denunciado
        'den_nombres','den_paterno','den_materno','den_edad','den_domicilio','den_estado_civil',
        'den_idioma','den_grado_instruccion','den_ocupacion',

        // Hecho
        'hecho_descripcion',

        // Tipología
        'tip_violencia_fisica','tip_violencia_psicologica','tip_abandono','tip_apoyo_integral',

        // Seguimiento
        'seg_trabajo_legal','seg_trabajo_social','seg_psicologico',

        // Relación usuario
        'user_id',
        'am_medicina','am_fisioterapia','am_extravio',
        'psicologica_user_id','trabajo_social_user_id','legal_user_id',
    ];

    // Relación con usuario responsable
    public function user()               { return $this->belongsTo(User::class); }
    public function psicologica_user()   { return $this->belongsTo(User::class, 'psicologica_user_id'); }
    public function trabajo_social_user(){ return $this->belongsTo(User::class, 'trabajo_social_user_id'); }
    public function legal_user()         { return $this->belongsTo(User::class, 'legal_user_id'); }
    protected $hidden = ['created_at','updated_at','deleted_at'];
}

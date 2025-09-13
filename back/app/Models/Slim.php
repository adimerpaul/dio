<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Slim extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'slims';

    protected $fillable = [
        'numero_apoyo_integral','area','zona',
        'denunciante_nombre_completo','denunciante_nombres','denunciante_paterno','denunciante_materno',
        'denunciante_documento','denunciante_nro','denunciante_sexo','denunciante_lugar_nacimiento',
        'denunciante_fecha_nacimiento','denunciante_edad','denunciante_telefono','denunciante_residencia',
        'denunciante_estado_civil','denunciante_relacion','denunciante_grado','latitud','longitud',
        'denunciante_trabaja','denunciante_prox','denunciante_ocupacion','denunciante_ocupacion_exacto',
        'denunciante_idioma','denunciante_fijo','denunciante_movil','denunciante_domicilio_actual',
        'familiar1_nombre_completo','familiar1_edad','familiar1_parentesco','familiar1_celular',
        'familiar2_nombre_completo','familiar2_edad','familiar2_parentesco','familiar2_celular',
        'familiar3_nombre_completo','familiar3_edad','familiar3_parentesco','familiar3_celular',
        'familiar4_nombre_completo','familiar4_edad','familiar4_parentesco','familiar4_celular',
        'familiar5_nombre_completo','familiar5_edad','familiar5_parentesco','familiar5_celular',
        'denunciado_nombre_completo','denunciado_nombres','denunciado_paterno','denunciado_materno',
        'denunciado_documento','denunciado_nro','denunciado_sexo','denunciado_lugar_nacimiento',
        'denunciado_fecha_nacimiento','denunciado_edad','denunciado_telefono','denunciado_residencia',
        'denunciado_estado_civil','denunciado_relacion','denunciado_grado','denunciado_trabaja',
        'denunciado_prox','denunciado_ocupacion','denunciado_ocupacion_exacto','denunciado_idioma',
        'denunciado_fijo','denunciado_movil','denunciado_domicilio_actual','denunciado_latitud','denunciado_longitud',
        'caso_numero','caso_fecha_hecho','caso_lugar_hecho','caso_zona','caso_direccion','caso_descripcion',
        'caso_tipologia','caso_modalidad',
        'violencia_fisica','violencia_psicologica','violencia_sexual','violencia_economica',
        'psicologica_user_id','trabajo_social_user_id','legal_user_id',
        'documento_fotocopia_carnet_denunciante','documento_fotocopia_carnet_denunciado',
        'documento_placas_fotograficas_domicilio_denunciante','documento_croquis_direccion_denunciado',
        'documento_placas_fotograficas_domicilio_denunciado','documento_ciudadania_digital',
        'fecha_apertura_caso','fecha_derivacion_psicologica','fecha_informe_area_psicologica',
        'fecha_informe_trabajo_social','fecha_derivacion_area_legal','user_id',
    ];

    protected $hidden = ['created_at','updated_at','deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->denunciante_nombre_completo = trim(
                "{$model->denunciante_nombres} {$model->denunciante_paterno} {$model->denunciante_materno}"
            );
        });
    }

    // Asignados
    public function user()               { return $this->belongsTo(User::class); }
    public function psicologica_user()   { return $this->belongsTo(User::class, 'psicologica_user_id'); }
    public function trabajo_social_user(){ return $this->belongsTo(User::class, 'trabajo_social_user_id'); }
    public function legal_user()         { return $this->belongsTo(User::class, 'legal_user_id'); }

    // Relaciones polimórficas (hijas)
    public function problematicas()  { return $this->morphMany(Problematica::class, 'caseable'); }
    public function psicologicas()   { return $this->morphMany(Psicologica::class,  'caseable'); }
    public function informesLegales(){ return $this->morphMany(InformeLegal::class,'caseable'); }
    public function documentos()     { return $this->morphMany(Documento::class,    'caseable'); }
    public function fotografias()    { return $this->morphMany(Fotografia::class,   'caseable'); }
}

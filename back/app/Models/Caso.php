<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Caso extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'casos';

    protected $fillable = [
        'tipo',
        'area',
        'zona',
        'numero_apoyo_integral',
        'caso_numero',
        'caso_fecha_hecho',
        'caso_lugar_hecho',
        'caso_zona',
        'caso_direccion',
        'caso_descripcion',
        'caso_tipologia',
        'caso_modalidad',
        'violencia_fisica',
        'violencia_psicologica',
        'violencia_sexual',
        'violencia_economica',
        'violencia_patrimonial',
        'violencia_simbolica',
        'violencia_institucional',
        'psicologica_user_id',
        'trabajo_social_user_id',
        'legal_user_id',
        'documento_fotocopia_carnet_denunciante',
        'documento_fotocopia_carnet_denunciado',
        'documento_placas_fotograficas_domicilio_denunciante',
        'documento_croquis_direccion_denunciado',
        'documento_placas_fotograficas_domicilio_denunciado',
        'documento_ciudadania_digital',
        'documento_otros',
        'documento_otros_detalle',
        'fecha_apertura_caso',
        'fecha_derivacion_psicologica',
        'fecha_informe_area_psicologica',
        'fecha_informe_trabajo_social',
        'fecha_derivacion_area_legal',
        'user_id',
    ];

    /**
     * Ocultar en respuestas JSON.
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $casts = [
//        'caso_fecha_hecho'      => 'date',
        'denunciante_trabaja'   => 'boolean',
        'violencia_fisica'      => 'boolean',
        'violencia_psicologica' => 'boolean',
        'violencia_sexual'      => 'boolean',
        'violencia_economica'   => 'boolean',
    ];
    public function problematicas(){ return $this->hasMany(Problematica::class); }
    public function user(){ return $this->belongsTo(User::class); }
//    denunciantes
    public function denunciantes(){ return $this->hasMany(CasoDenunciante::class); }
    public function denunciado(){ return $this->hasOne(CasoDenunciado::class); }
    public function familiares(){ return $this->hasMany(CasoFamiliares::class); }
    public function psicologica_user(){ return $this->belongsTo(User::class, 'psicologica_user_id'); }
    public function trabajo_social_user(){ return $this->belongsTo(User::class, 'trabajo_social_user_id'); }
    public function legal_user(){ return $this->belongsTo(User::class, 'legal_user_id');}

    public function sesionesPsicologicas(){ return $this->hasMany(SesionPsicologica::class); }
    public function informes(){ return $this->hasMany(Informe::class); }
    public function documentos(){ return $this->hasMany(Documento::class); }
    public function fotografias(){ return $this->hasMany(Fotografia::class); }
}

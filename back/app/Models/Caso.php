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
        'nurej',
        'cud',
        'tipo',
        'area',
        'zona',
        'principal',
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
        'documento_certificado_nacimiento',
        'documento_certificado_matrimonio',
        'documento_tres_testigos',
        'documento_contrato_pago',
        'documento_libreta_notas',
        'documento_numero_cuenta',
        'documento_otros',
        'documento_otros_detalle',
        'fecha_apertura_caso',
        'fecha_derivacion_psicologica',
        'fecha_informe_area_psicologica',
        'fecha_informe_area_social',
        'fecha_informe_trabajo_social',
        'fecha_derivacion_area_legal',
        'fecha_aceptacion_area_legal',
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
    public function denunciados(){ return $this->hasMany(CasoDenunciado::class); }
    public function familiares(){ return $this->hasMany(CasoFamiliares::class); }
    public function menores(){ return $this->hasMany(CasoMenor::class); }
    public function psicologica_user(){ return $this->belongsTo(User::class, 'psicologica_user_id'); }
    public function trabajo_social_user(){ return $this->belongsTo(User::class, 'trabajo_social_user_id'); }
    public function legal_user(){ return $this->belongsTo(User::class, 'legal_user_id');}
    public function psicologicas()   { return $this->morphMany(Psicologica::class,  'caseable'); }
    public function informesLegales(){ return $this->morphMany(InformeLegal::class,'caseable'); }
    public function documentos()     { return $this->morphMany(Documento::class,    'caseable'); }
    public function fotografias()    { return $this->morphMany(Fotografia::class,   'caseable'); }
    public function informesSociales(){ return $this->morphMany(InformesSocial::class,'caseable'); }
    public function acogimientos()  { return $this->hasMany(Acogimiento::class);  }
    public function victimas(){ return $this->hasMany(Victima::class);  }
}

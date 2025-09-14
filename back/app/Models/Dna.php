<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
class Dna extends Model implements AuditableContract
{
    use SoftDeletes,AuditableTrait;
    protected $fillable = [
        'codigo','fecha_atencion','tipo_proceso','principal','zona','area',
        'domicilio','telefono',
        'denunciado_nombre','denunciado_sexo','denunciado_edad','denunciado_relacion','denunciado_ci','denunciado_domicilio','denunciado_telefono','denunciado_lugar_trabajo','denunciado_ocupacion',
        'denunciante_nombre','denunciante_sexo','denunciante_edad','denunciante_ci','denunciante_domicilio','denunciante_telefono','denunciante_lugar_trabajo','denunciante_ocupacion',
        'descripcion','abogado_user_id','user_id'
    ];

    protected $casts = [
        'fecha_atencion' => 'date',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function abogado() { return $this->belongsTo(User::class, 'abogado_user_id'); }

    public function menores()    { return $this->hasMany(DnaMenor::class); }
    public function familiares() { return $this->hasMany(DnaFamiliar::class); }


    public function psicologicas()    { return $this->morphMany(\App\Models\Psicologica::class, 'caseable'); }
    public function informesLegales() { return $this->morphMany(\App\Models\InformeLegal::class,'caseable'); }
    public function documentos()      { return $this->morphMany(\App\Models\Documento::class,    'caseable'); }
    public function fotografias()     { return $this->morphMany(\App\Models\Fotografia::class,   'caseable'); }
}

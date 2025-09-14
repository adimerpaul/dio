<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
class DnaMenor extends Model implements AuditableContract
{
    use AuditableTrait, SoftDeletes;
    protected $table = 'dna_menores';

    protected $fillable = [
        'dna_id','nombre','gestante','edad_anios','edad_meses','sexo','cert_nac','estudia','ultimo_curso','tipo_trabajo'
    ];

    protected $casts = [
        'gestante' => 'boolean',
        'cert_nac' => 'boolean',
        'estudia'  => 'boolean',
    ];

    public function dna() { return $this->belongsTo(Dna::class); }
}

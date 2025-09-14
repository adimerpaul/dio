<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
class DnaFamiliar extends Model implements AuditableContract
{
    use AuditableTrait, SoftDeletes;
    protected $table = 'dna_familiares';

    protected $fillable = [
        'dna_id','nombre','parentesco','edad','sexo','instruccion','ocupacion'
    ];

    public function dna() { return $this->belongsTo(Dna::class); }
}

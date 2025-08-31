<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Problematica extends Model implements AuditableContract
{
    use SoftDeletes, AuditableTrait;

    protected $fillable = [
        'caso_id','user_id','fecha','titulo','detalle','acciones_inmediatas','observaciones'
    ];

    protected $hidden = ['created_at','updated_at','deleted_at'];

    // Relaciones
    public function caso(){ return $this->belongsTo(Caso::class); }
    public function user(){ return $this->belongsTo(User::class); }
}

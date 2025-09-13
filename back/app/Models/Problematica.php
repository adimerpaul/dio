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
        'caseable_id','caseable_type',
        'user_id','fecha','titulo','detalle','acciones_inmediatas','observaciones'
    ];

    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function caseable() { return $this->morphTo(); }
    public function user()     { return $this->belongsTo(User::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class InformesSocial extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $fillable = [
        'caseable_id','caseable_type',
        'user_id','fecha','titulo','numero','contenido_html',
        'archivo'
    ];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
    ];

    public function caseable() { return $this->morphTo(); }
    public function user()     { return $this->belongsTo(User::class); }
}

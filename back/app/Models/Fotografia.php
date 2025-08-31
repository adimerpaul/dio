<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Fotografia extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $fillable = [
        'caso_id','user_id','titulo','descripcion',
        'original_name','stored_name','extension','mime','size_bytes',
        'disk','path','url','thumb_path','thumb_url',
        'width','height'
    ];

    public function caso() { return $this->belongsTo(Caso::class); }
    public function user() { return $this->belongsTo(\App\Models\User::class); }
}

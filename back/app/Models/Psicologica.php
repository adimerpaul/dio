<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Psicologica extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'psicologicas';

    protected $fillable = [
        'caseable_id','caseable_type',
        'user_id','fecha','titulo','duracion_min','lugar','tipo','contenido_html'
    ];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
        'duracion_min' => 'integer',
    ];

    public function caseable() { return $this->morphTo(); }
    public function user()     { return $this->belongsTo(User::class); }
}

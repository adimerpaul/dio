<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class SesionPsicologica extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'sesiones_psicologicas';

    protected $fillable = [
        'caso_id',
        'user_id',
        'fecha',
        'titulo',
        'duracion_min',
        'lugar',
        'tipo',
        'contenido_html',
    ];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
        'duracion_min' => 'integer',
    ];

    public function caso()  { return $this->belongsTo(Caso::class); }
    public function user()  { return $this->belongsTo(\App\Models\User::class); }
}

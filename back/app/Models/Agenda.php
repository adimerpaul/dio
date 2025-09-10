<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Agenda extends Model implements AuditableContract
{
    use SoftDeletes, AuditableTrait;
    protected $fillable = [
        'caso_id',
        'user_id',
        'title',
        'notes',
        'location',
        'start',
        'end',
        'all_day',
        'status',
        'color',
        'created_by',
    ];

    protected $casts = [
        'start'   => 'datetime',
        'end'     => 'datetime',
        'all_day' => 'boolean',
    ];

    // Relaciones
    public function user()   { return $this->belongsTo(User::class); }
    public function caso()   { return $this->belongsTo(Caso::class); }
    public function creator(){ return $this->belongsTo(User::class, 'created_by'); }
}

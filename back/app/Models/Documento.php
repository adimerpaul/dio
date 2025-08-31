<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Documento extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $fillable = [
        'caso_id','user_id','titulo','categoria','descripcion',
        'original_name','stored_name','extension','mime','size_bytes',
        'disk','path','url'
    ];

    protected $appends = ['size_human'];

    public function caso() { return $this->belongsTo(Caso::class); }
    public function user() { return $this->belongsTo(\App\Models\User::class); }

    public function getSizeHumanAttribute(): string
    {
        $bytes = (int) ($this->size_bytes ?? 0);
        if ($bytes >= 1073741824) return number_format($bytes/1073741824, 2).' GB';
        if ($bytes >= 1048576)    return number_format($bytes/1048576, 2).' MB';
        if ($bytes >= 1024)       return number_format($bytes/1024, 2).' KB';
        return $bytes.' B';
    }
}

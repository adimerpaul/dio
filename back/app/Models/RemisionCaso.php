<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
class RemisionCaso extends Model implements AuditableContract
{
    use SoftDeletes, AuditableTrait;
//$table->id();
////            codigo_ingreso fecha_hora objeto_ingreso cantidad remitente referencia dispocicion
//$table->string('codigo_ingreso')->nullable();
//$table->dateTime('fecha_hora')->nullable();
//$table->string('objeto_ingreso')->nullable();
//$table->integer('cantidad')->nullable();
//$table->string('remitente')->nullable();
//$table->string('referencia')->nullable();
//$table->string('disposicion')->nullable();
//$table->string('estado')->nullable();
//$table->softDeletes();
//$table->timestamps();
    protected $fillable = [
        'codigo_ingreso',
        'fecha_hora',
        'objeto_ingreso',
        'cantidad',
        'remitente',
        'remitente_otros',
//        'referencia',
        'organizacion',
        'disposicion',
        'estado',
        'archivo',
        'user_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
//    user
    function user(){
        return $this->belongsTo(User::class);
    }
}

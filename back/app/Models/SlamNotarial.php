<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
class SlamNotarial extends Model implements Auditable
{
    use AuditableTrait,SoftDeletes;
//Schema::create('slam_notariales', function (Blueprint $table) {
//    $table->id();
//    $table->unsignedBigInteger('user_id');
//    $table->foreign('user_id')->references('id')->on('users');
//    $table->date('fecha');
//    $table->string('nombre_completo');
//    $table->string('edad');
//    $table->string('fecha_nacimiento');
//    $table->string('estado_civil');
//    $table->string('ci');
//    $table->string('grado_instruccion');
//    $table->string('ocupacion');
//    $table->string('direccion_domicilio');
//    $table->string('url');
//    $table->softDeletes();
//    $table->timestamps();
//});

    protected $table = 'slam_notariales';

    protected $fillable = [
        'user_id',
        'fecha',
        'nombre_completo',
        'edad',
        'fecha_nacimiento',
        'estado_civil',
        'ci',
        'grado_instruccion',
        'ocupacion',
        'direccion_domicilio',
        'url'
    ];

    protected $hidden = ['created_at','updated_at','deleted_at'];
}

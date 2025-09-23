<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class CasoMenor extends Model implements AuditableContract
{

    use SoftDeletes, AuditableTrait;
    //$table->id();
    //            $table->string('nombres', 120)->nullable();
    //            $table->string('paterno', 80)->nullable();
    //            $table->string('materno', 80)->nullable();
    //            $table->boolean('gestante')->nullable();
    //            $table->integer('edad_anios')->nullable();
    //            $table->integer('edad_meses')->nullable();
    //            $table->string('sexo', 20)->nullable();
    //            $table->string('ciudad_nacimiento', 80)->nullable();
    //            $table->string('estudia', 20)->nullable();
    //            $table->string('ultimo_curso', 80)->nullable();
    //            $table->string('tipo_trabajo', 120)->nullable();
    //            $table->string('ci', 40)->nullable()->index();
    //            $table->date('fecha_nacimiento')->nullable();
    //            $table->string('lugar_nacimiento', 120)->nullable();
    //            $table->string('edad', 10)->nullable();
    //            $table->timestamps();
    //            $table->softDeletes();
    protected $fillable =[
        'nombres',
        'paterno',
        'materno',
        'gestante',
        'edad_anios',
        'edad_meses',
        'sexo',
        'ciudad_nacimiento',
        'estudia',
        'ultimo_curso',
        'tipo_trabajo',
        'ci',
        'fecha_nacimiento',
        'lugar_nacimiento',
        'edad',
        'user_id',
        'caso_id',
    ];
    protected $hidden= [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

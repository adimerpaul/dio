<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class CasoFamiliares extends Model implements AuditableContract
{
    //            $table->id();
    //            $table->string('familiar_nombre_completo', 120)->nullable();
    //            $table->string('familiar_nombres', 120)->nullable();
    //            $table->string('familiar_paterno', 80)->nullable();
    //            $table->string('familiar_materno', 80)->nullable();
    //            $table->string('familiar_documento', 40)->nullable();
    //            $table->string('familiar_nro', 30)->nullable();
    //            $table->string('familiar_sexo', 15)->nullable();
    //            $table->string('familiar_lugar_nacimiento', 120)->nullable();
    //            $table->date('familiar_fecha_nacimiento')->nullable();
    //            $table->string('familiar_edad')->nullable();
    //            $table->string('familiar_telefono', 30)->nullable();
    //            $table->string('familiar_residencia', 120)->nullable();
    //            $table->string('familiar_estado_civil', 40)->nullable();
    //            $table->string('familiar_parentesco', 60)->nullable();
    //            $table->string('familiar_grado', 60)->nullable();
    //            $table->string('familiar_trabaja', 10)->nullable();       //
    //            $table->string('familiar_prox', 120)->nullable();
    //            $table->string('familiar_ocupacion', 80)->nullable();
    //            $table->string('familiar_ocupacion_exacto', 120)->nullable();
    //            $table->string('familiar_idioma', 60)->nullable();
    //            $table->string('familiar_fijo', 30)->nullable();
    //            $table->string('familiar_movil', 30)->nullable();
    //            $table->text('familiar_domicilio_actual')->nullable();    // TEXT
    //            $table->decimal('familiar_latitud', 10, 7)->nullable();
    //            $table->decimal('familiar_longitud', 10, 7)->nullable();
    //
    //            $table->unsignedBigInteger('caso_id');
    //            $table->foreign('caso_id')->references('id')->on('casos');
    //            $table->timestamps();
    //            $table->softDeletes();

    use SoftDeletes, AuditableTrait;

    protected $fillable=[
        'familiar_nombre_completo',
        'familiar_nombres',
        'familiar_paterno',
        'familiar_materno',
        'familiar_documento',
        'familiar_nro',
        'familiar_sexo',
        'familiar_lugar_nacimiento',
        'familiar_fecha_nacimiento',
        'familiar_edad',
        'familiar_telefono',
        'familiar_residencia',
        'familiar_estado_civil',
        'familiar_parentesco',
        'familiar_grado',
        'familiar_trabaja',
        'familiar_prox',
        'familiar_ocupacion',
        'familiar_ocupacion_exacto',
        'familiar_idioma',
        'familiar_fijo',
        'familiar_movil',
        'familiar_domicilio_actual',
        'familiar_latitud',
        'familiar_longitud',
        'caso_id',
    ];
    protected $hidden= [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

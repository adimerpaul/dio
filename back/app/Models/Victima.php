<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
class Victima extends Model implements Auditable{
    use AuditableTrait, SoftDeletes;
    //            $table->string('nombres_apellidos');
    //            $table->string('ci')->nullable();
    //            $table->date('fecha_nacimiento')->nullable();
    //            $table->string('lugar_nacimiento')->nullable();
    //            $table->integer('edad')->nullable();
    //            $table->enum('sexo', ['M', 'F', 'OTRO'])->nullable();
    //            $table->string('estado_civil')->nullable();
    //            $table->string('ocupacion')->nullable();
    //            $table->string('idioma')->nullable();
    //            $table->string('domicilio')->nullable();
    //            $table->string('telefono')->nullable();
    //
    //            // Campos específicos según área
    //            $table->boolean('gestante')->nullable(); // DNA
    //            $table->boolean('estudia')->nullable();
    //            $table->string('lugar_estudio')->nullable();
    //            $table->string('grado_curso')->nullable();
    //            $table->boolean('trabaja')->nullable();
    //            $table->string('lugar_trabajo')->nullable();
    //
    //            $table->boolean('es_denunciante')->default(false);
    //
    //            $table->unsignedBigInteger('caso_id')->nullable();
    //            $table->foreign('caso_id')->references('id')->on('casos');
    protected $fillable = [
        'nombres_apellidos',
        'ci',
        'fecha_nacimiento',
        'lugar_nacimiento',
        'edad',
        'sexo',
        'estado_civil',
        'ocupacion',
        'ingreso_economico',
        'idioma',
        'domicilio',
        'telefono',
        'gestante',
        'estudia',
        'lugar_estudio',
        'grado_curso',
        'trabaja',
        'lugar_trabajo',
        'es_denunciante',
        'caso_id'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public function caso(){
        return $this->belongsTo(Caso::class);
    }
}

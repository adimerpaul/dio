<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutorizacionesTrabajo extends Model
{
    protected $table = 'autorizaciones_trabajo';

    protected $fillable = [
        'user_id','fecha',

        'primer_apellido','segundo_apellido','primer_nombre','segundo_nombre',
        'ci','edad','direccion','ciudad','municipio',
        'telefono_1','telefono_2',
        'nombre_padre','nombre_madre','nombre_tutor',
        'ocupacion','grado_parentesco','grado_instruccion',

        'razon_social','representante_legal','tipo',
        'direccion_empresa','ciudad_empresa','nit','telefono_empresa',

        'tipo_actividad','remuneracion_bs','forma_pago',
        'pago_diario','pago_semanal','pago_quincenal','pago_anual',
        'cargo_ocupar','duracion_trabajo','descripcion_lugar_actividad',

        'url'
    ];

//    protected $casts = [
//        'pago_diario' => 'boolean',
//        'pago_semanal' => 'boolean',
//        'pago_quincenal' => 'boolean',
//        'pago_anual' => 'boolean',
//        'fecha' => 'datetime',
//    ];
}

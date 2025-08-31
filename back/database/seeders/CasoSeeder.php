<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Caso;

class CasoSeeder extends Seeder
{
    public function run(): void
    {
        Caso::create([
            // === Datos del denunciante ===
            'denunciante_nombre_completo'    => 'JOCELYN SANDRA MORALES PATANA',
            'denunciante_nombres'            => 'JOCELYN SANDRA',
            'denunciante_paterno'            => 'MORALES',
            'denunciante_materno'            => 'PATANA',
            'denunciante_documento'          => 'CARNET DE IDENTIDAD',
            'denunciante_nro'                => '9081769',
            'denunciante_sexo'               => 'FEMENINO',
            'denunciante_lugar_nacimiento'   => 'LA PAZ-MURILLO-NUESTRA SRA DE LA PAZ',
            'denunciante_fecha_nacimiento'   => '1997-06-19',
            'denunciante_edad'               => 27,
            'denunciante_residencia'         => 'ORURO',
            'denunciante_estado_civil'       => 'SOLTERA',
            'denunciante_relacion'           => 'CONOCIDO',
            'denunciante_grado'              => 'MEDIO',
            'denunciante_trabaja'            => 'SI',
            'denunciante_ocupacion'          => 'MÉDICO',
            'denunciante_ocupacion_exacto'   => 'MÉDICO GENERAL',
            'denunciante_idioma'             => 'CASTELLANO',
            'denunciante_fijo'               => '78328937',
            'denunciante_movil'              => '78888760',
            'denunciante_domicilio_actual'   => 'CAMACHO ENTRE BOLIVAR 1635',

            // Grupo Familiar
            'familiar1_nombre_completo'      => 'AMILCAR VELASCO HERRERA',
            'familiar1_edad'                 => 34,
            'familiar1_parentesco'           => 'PAREJA',
            'familiar1_celular'              => '78328937',

            // === Datos del denunciado ===
            'denunciado_nombre_completo'     => 'MARTIN RODRIGUEZ',
            'denunciado_nombres'             => 'MARTIN',
            'denunciado_paterno'             => 'RODRIGUEZ',
            'denunciado_documento'           => 'C.I.',
            'denunciado_nro'                 => 'Ninguno',
            'denunciado_sexo'                => 'MASCULINO',
            'denunciado_lugar_nacimiento'    => 'LA PAZ',
            'denunciado_fecha_nacimiento'    => '1998-01-01',
            'denunciado_edad'                => 27,
            'denunciado_residencia'          => 'EL ALTO',
            'denunciado_estado_civil'        => 'SOLTERO',
            'denunciado_ocupacion'           => 'OBRERO',
            'denunciado_ocupacion_exacto'    => 'CONSTRUCTOR',
            'denunciado_idioma'              => 'CASTELLANO',
            'denunciado_domicilio_actual'    => 'PLAN 561 C 1A - 1228 ZONA CIUDAD SATÉLITE EL ALTO',

            // === Datos del caso ===
            'caso_numero'                    => '09/25',
            'caso_fecha_hecho'               => '2025-03-08',
            'caso_lugar_hecho'               => 'AV. CÍVICA A LA ENTRADA DEL CARNAVAL',
            'caso_zona'                      => 'ORURO CENTRAL',
            'caso_direccion'                 => 'CERCADO, ORURO',
            'caso_descripcion'               => 'El día sábado 8 de marzo la Sra fue a la Av. Cívica, a la entrada del carnaval...',
            'caso_tipologia'                 => 'ABUSO SEXUAL',
            'caso_modalidad'                 => 'TOCAMIENTO INDEBIDO',
            'violencia_fisica'               => 'NO',
            'violencia_psicologica'          => 'SÍ',
            'violencia_sexual'               => 'SÍ',
            'violencia_economica'            => 'NO',

            // Seguimiento
            'seguimiento_area'               => 'AREA SOCIAL',
            'seguimiento_area_social'        => 'LIC. DORIS PORTILLO Z.',
            'seguimiento_area_legal'         => 'ABG. CAMILO SORIA GUTIÉRREZ',
        ]);
        Caso::withoutEvents(function () {
            $total = 10000;
            $chunk = 500; // baja a 500 por iteración
            $loops = (int) ceil($total / $chunk);

            for ($i = 0; $i < $loops; $i++) {
                $size = ($i === $loops - 1) ? ($total - $i * $chunk) : $chunk;
                Caso::factory()->count($size)->create();
            }
        });
    }
}

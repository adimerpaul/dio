<?php

namespace Database\Factories;

use App\Models\Caso;
use Illuminate\Database\Eloquent\Factories\Factory;

class CasoFactory extends Factory
{
    protected $model = Caso::class;

    public function definition(): array
    {
        // Faker en español ayuda con nombres/dirs realistas
        $this->faker = \Faker\Factory::create('es_ES');

        // Helpers
        $siNo = fn() => $this->faker->boolean(60) ? 'SI' : 'NO';  // <-- usa esto si tus columnas son VARCHAR
        $bool = fn() => $this->faker->boolean();                  // <-- usa este si migraste a TINYINT(1)/BOOLEAN
        $areas = ['DNA', 'SLIM', 'SLAM', 'UMAGUIS', 'PROCENI'];
        $zonas = ['CENTRAL', 'NORTE', 'SUR', 'ESTE', 'OESTE'];

        // Familiares: a veces vacíos
        $fam = [];
        for ($i = 1; $i <= 5; $i++) {
            $use = $this->faker->boolean(30);
            $fam["familiar{$i}_nombre_completo"] = $use ? $this->faker->name() : null;
            $fam["familiar{$i}_edad"]            = $use ? $this->faker->numberBetween(1, 90) : null;
            $fam["familiar{$i}_parentesco"]      = $use ? $this->faker->randomElement(['MADRE','PADRE','HIJO/A','HERMANO/A','PAREJA','TUTOR/A']) : null;
            $fam["familiar{$i}_celular"]         = $use ? $this->faker->numerify('7########') : null;
        }

        return array_merge([
            // ===== DENUNCIANTE =====
            'area'                         => $this->faker->randomElement($areas),
            'zona'                         => $this->faker->randomElement($zonas),
            'denunciante_nombre_completo'  => $this->faker->name(),
            'denunciante_nombres'          => $this->faker->firstName().' '.$this->faker->firstName(),
            'denunciante_paterno'          => $this->faker->lastName(),
            'denunciante_materno'          => $this->faker->lastName(),
            'denunciante_documento'        => $this->faker->randomElement(['CARNET DE IDENTIDAD','PASAPORTE','R.U.N.']),
            'denunciante_nro'              => (string)$this->faker->numberBetween(1000000, 99999999),
            'denunciante_sexo'             => $this->faker->randomElement(['FEMENINO','MASCULINO','OTRO']),
            'denunciante_lugar_nacimiento' => $this->faker->city(),
            'denunciante_fecha_nacimiento' => $this->faker->date('Y-m-d','2006-12-31'),
            'denunciante_edad'             => $this->faker->numberBetween(18, 85),
            'denunciante_residencia'       => $this->faker->city(),
            'denunciante_estado_civil'     => $this->faker->randomElement(['SOLTERA/O','CASADA/O','DIVORCIADA/O','UNIÓN LIBRE','VIUDA/O']),
            'denunciante_relacion'         => $this->faker->randomElement(['CONOCIDO','PAREJA','EX PAREJA','FAMILIAR','DESCONOCIDO']),
            'denunciante_grado'            => $this->faker->randomElement(['PRIMARIO','SECUNDARIO','TÉCNICO','SUPERIOR','NINGUNO']),
            'latitud'                      => $this->faker->optional()->latitude(-22, -9),
            'longitud'                     => $this->faker->optional()->longitude(-69, -63),
            // Si tu migración usa VARCHAR('SI'/'NO'):
            'denunciante_trabaja'          => $siNo(),
            // Si tu migración usa BOOLEAN/TINYINT(1), reemplaza la línea anterior por:
            // 'denunciante_trabaja'       => $bool(),
            'denunciante_prox'             => $this->faker->optional()->word(),
            'denunciante_ocupacion'        => $this->faker->jobTitle(),
            'denunciante_ocupacion_exacto'=> $this->faker->jobTitle(),
            'denunciante_idioma'           => $this->faker->randomElement(['CASTELLANO','AIMARA','QUECHUA','GUARANÍ','OTRO']),
            'denunciante_fijo'             => $this->faker->optional()->numerify('2######'),
            'denunciante_movil'            => $this->faker->numerify('7########'),
            'denunciante_domicilio_actual' => $this->faker->streetAddress(),

            // ===== DENUNCIADO =====
            'denunciado_nombre_completo'   => $this->faker->name(),
            'denunciado_nombres'           => $this->faker->firstName(),
            'denunciado_paterno'           => $this->faker->lastName(),
            'denunciado_materno'           => $this->faker->lastName(),
            'denunciado_documento'         => $this->faker->randomElement(['C.I.','PASAPORTE','NINGUNO']),
            'denunciado_nro'               => $this->faker->boolean(80) ? (string)$this->faker->numberBetween(1000000, 99999999) : 'Ninguno',
            'denunciado_sexo'              => $this->faker->randomElement(['MASCULINO','FEMENINO','OTRO']),
            'denunciado_lugar_nacimiento'  => $this->faker->city(),
            'denunciado_fecha_nacimiento'  => $this->faker->date('Y-m-d','2006-12-31'),
            'denunciado_edad'              => $this->faker->numberBetween(18, 90),
            'denunciado_residencia'        => $this->faker->city(),
            'denunciado_estado_civil'      => $this->faker->randomElement(['SOLTERO','CASADO','DIVORCIADO','UNIÓN LIBRE','VIUDO']),
            'denunciado_relacion'          => $this->faker->randomElement(['CONOCIDO','PAREJA','EX PAREJA','FAMILIAR','DESCONOCIDO']),
            'denunciado_grado'             => $this->faker->randomElement(['PRIMARIO','SECUNDARIO','TÉCNICO','SUPERIOR','NINGUNO']),
            // idem comentario de boolean:
            'denunciado_trabaja'           => $siNo(),
            // 'denunciado_trabaja'        => $bool(),
            'denunciado_prox'              => $this->faker->optional()->word(),
            'denunciado_ocupacion'         => $this->faker->jobTitle(),
            'denunciado_ocupacion_exacto'  => $this->faker->jobTitle(),
            'denunciado_idioma'            => $this->faker->randomElement(['CASTELLANO','AIMARA','QUECHUA','GUARANÍ','OTRO']),
            'denunciado_fijo'              => $this->faker->optional()->numerify('2######'),
            'denunciado_movil'             => $this->faker->numerify('7########'),
            'denunciado_domicilio_actual'  => $this->faker->streetAddress(),
            'denunciado_latitud'           => $this->faker->optional()->latitude(-22, -9),
            'denunciado_longitud'          => $this->faker->optional()->longitude(-69, -63),

            // ===== CASO =====
            'caso_numero'                  => str_pad((string)$this->faker->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT).'/'.date('y'),
            'caso_fecha_hecho'             => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'caso_lugar_hecho'             => $this->faker->streetName(),
            'caso_zona'                    => $this->faker->randomElement(['CENTRAL','NORTE','SUR','ESTE','OESTE']),
            'caso_direccion'               => $this->faker->address(),
            'caso_descripcion' => $this->faker->paragraph(3),
            'caso_tipologia'               => $this->faker->randomElement(['VIOLENCIA FÍSICA','VIOLENCIA PSICOLÓGICA','VIOLENCIA SEXUAL','VIOLENCIA ECONÓMICA','ABUSO SEXUAL']),
            'caso_modalidad'               => $this->faker->randomElement(['TOCAMIENTO INDEBIDO','AGRESIÓN','AMENAZA','ACOSO','OTRO']),

            // Tipos de violencia (si son VARCHAR usa 'SI'/'NO'; si son boolean, cambia a $bool())
            'violencia_fisica'             => $siNo(),
            'violencia_psicologica'        => $siNo(),
            'violencia_sexual'             => $siNo(),
            'violencia_economica'          => $siNo(),
            // 'violencia_fisica'          => $bool(),
            // 'violencia_psicologica'     => $bool(),
            // 'violencia_sexual'          => $bool(),
            // 'violencia_economica'       => $bool(),

            // Seguimiento
            'seguimiento_area'             => $this->faker->randomElement(['ÁREA SOCIAL','ÁREA LEGAL','MULTIDISCIPLINARIO']),
            'seguimiento_area_social'      => 'LIC. '.$this->faker->name(),
            'seguimiento_area_legal'       => 'ABG. '.$this->faker->name(),
        ], $fam);
    }
}

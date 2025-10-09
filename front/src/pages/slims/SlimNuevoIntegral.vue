<template>
  <SlimNuevo :showNumeroApoyoIntegral="true" :tipologias="tipologias"/>
</template>

<script>
import SlimNuevo from 'pages/slims/SlimNuevo.vue'
export default {
  name: 'SlimNuevoIntegral',
  components: { SlimNuevo },
  data: () => ({
    tipologias: [
      'Violencia Física',
      'Violencia Feminicida',
      'Violencia Psicológica',
      'Violencia Mediática',
      'Violencia Simbólica y/o Encubierta',
      'Violencia Contra la Dignidad, la Honra y el Nombre',
      'Violencia Sexual',
      'Violencia Contra los Derechos Reproductivos',
      'Violencia en Servicios de Salud',
      'Violencia Patrimonial y Económica',
      'Violencia Laboral',
      'Violencia en el Sistema Educativo Plurinacional',
      'Violencia en el Ejercicio Político y de Liderazgo de la Mujer',
      'Violencia Institucional',
      'Violencia en la Familia',
      'Violencia Contra los Derechos y la Libertad Sexual',
      'Tipologias Multiples',
      'Otra'
    ],
  }),
}
</script>

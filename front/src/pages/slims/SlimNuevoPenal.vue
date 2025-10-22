<template>
  <CasoNuevo
    v-if="ready"
    :showNumeroApoyoIntegral="false"
    :tipologias="tipologias"
    :titulo="'Registrar Nuevo Caso Penal'"
    :tipo="tipo"
  />
</template>

<script>
import { nextTick } from 'vue'
import CasoNuevo from 'pages/casos/CasoNuevo.vue'

export default {
  name: 'SlimNuevoPenal',
  components: { CasoNuevo },
  data () {
    return {
      ready: false,
      tipo: null,
      tipologias: []
    }
  },
  async created () {
    // 1) Asignas variables
    this.tipo = 'SLIM'
    this.tipologias = [
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
    ]

    // 2) Esperas a que el DOM procese los cambios y luego habilitas el render
    await nextTick()
    this.ready = true
  }
}
</script>

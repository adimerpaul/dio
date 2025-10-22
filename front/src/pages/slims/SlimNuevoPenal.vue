<template>
  <CasoNuevo
    v-if="ready"
    :showNumeroApoyoIntegral="false"
    :titulo="tipo"
    :subtitulo="'Nuevo proceso penal'"
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
      tipo: 'SLIM',
    }
  },
  async created () {
    this.tipo = 'SLIM'
    console.log('SlimNuevoPenal created, tipo set to:', this.tipo)
    await nextTick()
    this.ready = true
  }
}
</script>

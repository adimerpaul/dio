<template>
  <CasoNuevo
    v-if="ready"
    :tipo="tipo"
    :showNumeroApoyoIntegral="false"
    :titulo="'Registrar Nuevo Caso Penal yyyyy'"
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
    await nextTick()
    this.ready = true
  }
}
</script>

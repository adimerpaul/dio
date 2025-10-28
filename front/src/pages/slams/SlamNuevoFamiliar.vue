<template>
  <CasoNuevo
    :showNumeroApoyoIntegral="false"
    titulo="Proceso Familiar SLAM"
    :tipo="'SLAM'"
  />
</template>

<script>
import CasoNuevo from "pages/casos/CasoNuevo.vue";
export default {
  name: 'SlamNuevoFamiliar',
  components: {CasoNuevo}
}
</script>

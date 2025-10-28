<template>
  <CasoNuevo
    :showNumeroApoyoIntegral="false"
    titulo="Proceso Penal SLAM"
    :tipo="'SLAM'"
  />
</template>

<script>
import CasoNuevo from "pages/casos/CasoNuevo.vue";
export default {
  name: 'SlamNuevoPenal',
  components: {CasoNuevo}
}
</script>

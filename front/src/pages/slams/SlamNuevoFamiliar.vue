<template>
  <CasoNuevo
    :showNumeroApoyoIntegral="false"
    titulo="Familiar SLAM"
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

<template>
  <!--  <SlimNuevo :showNumeroApoyoIntegral="true" />-->
  <CasoNuevo
    :showNumeroApoyoIntegral="true"
    titulo="Apoyo Integral SLAM"
    :tipo="'SLAM'"
  />
</template>

<script>
import CasoNuevo from "pages/casos/CasoNuevo.vue";
export default {
  name: 'SlamNuevoApoyo',
  components: {CasoNuevo}
}
</script>

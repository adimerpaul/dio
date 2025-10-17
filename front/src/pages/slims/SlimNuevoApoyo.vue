<template>
<!--  <SlimNuevo :showNumeroApoyoIntegral="true" />-->
  <CasoNuevo
    :showNumeroApoyoIntegral="true"
    titulo="Apoyo Integral"
    :tipo="'SLIM'"
  />
</template>

<script>
import SlimNuevo from 'pages/slims/SlimNuevo.vue'
import CasoNuevo from "pages/casos/CasoNuevo.vue";
export default {
  name: 'SlimNuevoApoyo',
  components: {CasoNuevo, SlimNuevo }
}
</script>

<template>
  <CasoNuevoPROPREMI :showNumeroApoyoIntegral="true" />
</template>

<script>
import SlimNuevo from 'pages/slims/SlimNuevo.vue'
import CasoNuevoPROPREMI from "pages/propremis/PropremisNuevo.vue";
export default {
  name: 'SlimNuevoIntegral',
  components: {CasoNuevoPROPREMI, SlimNuevo }
}
</script>

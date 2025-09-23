<template>
  <CasoNuevoPROPREMI :showNumeroApoyoIntegral="false" />
</template>

<script>
import SlimNuevo from 'pages/slims/SlimNuevo.vue'
import CasoNuevoPROPREMI from "pages/propremis/PropremisNuevo.vue";
export default {
  name: 'SlimNuevoFisico',
  components: {CasoNuevoPROPREMI, SlimNuevo }
}
</script>

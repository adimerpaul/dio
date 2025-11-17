<template>
  <!--  <SlimNuevo :showNumeroApoyoIntegral="false" :tipologias="tipologias"/>-->
  <CasoNuevo
    :showNumeroApoyoIntegral="false"
    :titulo="'Registrar Nuevo Caso Hechos de Fragancia'"
    :tipo="'SLIM'"
  />
</template>

<script>
// import SlimNuevo from 'pages/slims/SlimNuevo.vue'
import CasoNuevo from "pages/casos/CasoNuevo.vue";

export default {
  name: 'SlimNuevoHechosFragancia',
  components: {CasoNuevo },
  data () {
    return {
    }
  }
}
</script>

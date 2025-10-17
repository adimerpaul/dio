<template>
<!--  <SlimNuevo :showNumeroApoyoIntegral="false" :tipologias="tipologias"/>-->
  <CasoNuevo
    :showNumeroApoyoIntegral="false"
    :tipologias="tipologias"
    titulo="Nuevo Proceso Familiar"
    :tipo="'SLIM'"
  />
</template>

<script>
import SlimNuevo from 'pages/slims/SlimNuevo.vue'
import CasoNuevo from "pages/casos/CasoNuevo.vue";
export default {
  name: 'SlimNuevoFamiliar',
  components: {CasoNuevo, SlimNuevo },
  data: () => ({
    tipologias: [
      'Asistencia Familiar',
      'Otros'
    ],
  })
}
</script>

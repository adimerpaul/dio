<template>
  <SlimNuevo :showNumeroApoyoIntegral="false" :tipologias="tipologias"/>
</template>

<script>
import SlimNuevo from 'pages/slims/SlimNuevo.vue'
export default {
  name: 'SlimNuevoFamiliar',
  components: { SlimNuevo },
  data: () => ({
    tipologias: [
      'Asistencia Familiar',
      'Otros'
    ],
  })
}
</script>

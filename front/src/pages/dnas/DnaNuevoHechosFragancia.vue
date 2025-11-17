<template>
  <CasoNuevo
    :showNumeroApoyoIntegral="false"
    titulo="DNA Nuevo Hechos de Fragancia"
    :tipo="'DNA'"
  />
</template>
<script>
import CasoNuevo from "pages/casos/CasoNuevo.vue";
export default {
  name: 'DnaNuevoHechosFragancia',
  components: {CasoNuevo }
}
</script>

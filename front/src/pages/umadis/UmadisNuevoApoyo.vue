<template>
  <CasoNuevo
    :showNumeroApoyoIntegral="true"
    titulo="Nuevo Apoyo Integral UMADIS"
    :tipo="'UMADIS'"
  />
</template>

<script>
import CasoNuevo from "pages/casos/CasoNuevo.vue";
export default {
  name: 'UmadisNuevoApoyo',
  components: {CasoNuevo },
}
</script>

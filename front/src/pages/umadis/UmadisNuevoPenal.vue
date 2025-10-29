<template>
  <CasoNuevo
    :showNumeroApoyoIntegral="false"
    titulo="Nuevo Caso Penal UMADIS"
    :tipo="'UMADIS'"
  />
</template>

<script>
import CasoNuevo from "pages/casos/CasoNuevo.vue";
export default {
  name: 'UmadisNuevoPenal',
  components: {CasoNuevo },
}
</script>

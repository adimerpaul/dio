<template>
  <CasoNuevo
    :showNumeroApoyoIntegral="false"
    titulo="Nuevo Caso Social UMADIS"
    :tipo="'UMADIS'"
  />
</template>

<script>
import CasoNuevo from "pages/casos/CasoNuevo.vue";
export default {
  name: 'UmadisNuevoSocial',
  components: {CasoNuevo },
}
</script>

<template>
  <CasoNuevo
    :showNumeroApoyoIntegral="false"
    titulo="Nuevo Familiar UMADIS"
    :tipo="'UMADIS'"
  />
</template>

<script>
import CasoNuevo from "pages/casos/CasoNuevo.vue";
export default {
  name: 'UmadisNuevoFamiliar',
  components: {CasoNuevo },
}
</script>

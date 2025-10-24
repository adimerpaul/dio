<template>
  <CasoNuevo
    :showNumeroApoyoIntegral="false"
    titulo="Nuevo Proceso Juventudes"
    :tipo="'JUVENTUDES'"
  />
</template>

<script>
import CasoNuevo from "pages/casos/CasoNuevo.vue";
export default {
  name: 'SlimNuevoFamiliar',
  components: {CasoNuevo },
  data: () => ({
  })
}
</script>

<template>
  <NuevoUmadi :showNumeroApoyoIntegral="false" />
</template>

<script>
import SlamNuevo from "pages/slams/SlamNuevo.vue";
import NuevoUmadi from "pages/umadis/UmadisNuevo.vue";

export default {
  name: 'SlamNuevoFisico',
  components: {NuevoUmadi, SlamNuevo, },
}
</script>

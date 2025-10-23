<template>
<!--  <DnaNuevo tipo-proceso="PROCESO_PENAL" /></template>-->
    <CasoNuevo
    :showNumeroApoyoIntegral="false"
    titulo="DNA Proceso Penal"
    :tipo="'DNA'"
  />
</template>
<script>
import DnaNuevo from 'pages/dnas/DnaNuevo.vue'
import CasoNuevo from "pages/casos/CasoNuevo.vue";
export default { name: 'DnaNuevoPenal', components: {CasoNuevo, DnaNuevo } }
</script>

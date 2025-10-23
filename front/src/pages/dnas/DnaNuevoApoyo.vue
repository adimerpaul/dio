<template>
  <!--  <DnaNuevo tipo-proceso="PROCESO_PENAL" /></template>-->
  <CasoNuevo
    :showNumeroApoyoIntegral="true"
    titulo="DNA Nuevo Apoyo Integral"
    :tipo="'DNA'"
  />
</template>
<script>
import DnaNuevo from 'pages/dnas/DnaNuevo.vue'
import CasoNuevo from "pages/casos/CasoNuevo.vue";
export default { name: 'DnaNuevoApoyo', components: {CasoNuevo, DnaNuevo } }
</script>

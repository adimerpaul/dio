<template>
<!--  <div>-->
<!--    <h1>Remisión de Casos Externos</h1>-->
  <q-page class="q-pa-md">
    <div class="text-h5 q-mb-md">
      Remisión de Casos Externos
    </div>
    <remision-casos-page interoExterno="EXTERNO"/>
  </q-page>

<!--  </div>-->
</template>
<script>
import RemisionCasosPage from "pages/RemisionCasos/RemisionCasosPage.vue";

export default {
  name: 'RemisionCasosExterno',
  components: {RemisionCasosPage},
};
</script>

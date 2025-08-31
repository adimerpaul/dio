<template>
  <q-card flat bordered class="q-pa-md">
    <div class="text-subtitle1 text-weight-medium q-mb-sm">Informes (Legal)</div>
    <div class="text-grey-7">Carga/gestión de informes legales (ID: {{ caseId }})</div>
  </q-card>
</template>
<script>
export default { name: 'InformesLegal', props: { caseId: { type: [String, Number], required: true } } }
</script>

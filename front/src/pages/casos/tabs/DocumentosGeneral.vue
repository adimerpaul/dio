<template>
  <q-card flat bordered class="q-pa-md">
    <div class="text-subtitle1 text-weight-medium q-mb-sm">Documentos (General)</div>
    <div class="text-grey-7">Gestión y adjuntos de documentos del caso (ID: {{ caseId }})</div>
  </q-card>
</template>
<script>
export default { name: 'DocumentosGeneral', props: { caseId: { type: [String, Number], required: true } } }
</script>

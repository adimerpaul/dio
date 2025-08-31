<template>
  <q-card flat bordered class="q-pa-md">
    <div class="text-subtitle1 text-weight-medium q-mb-sm">Sesiones (Psicológico)</div>
    <div class="text-grey-7">Listado/registro de sesiones psicológicas (ID: {{ caseId }})</div>
  </q-card>
</template>
<script>
export default { name: 'SesionesPsicologico', props: { caseId: { type: [String, Number], required: true } } }
</script>

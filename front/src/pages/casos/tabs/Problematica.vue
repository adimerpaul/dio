<template>
  <q-card flat bordered class="q-pa-md">
    <div class="text-subtitle1 text-weight-medium q-mb-sm">Problemática</div>
    <div class="text-grey-7">Aquí irá el formulario/visor de la problemática del caso (ID: {{ caseId }})</div>
  </q-card>
</template>
<script>
export default { name: 'Problematica', props: { caseId: { type: [String, Number], required: true } } }
</script>

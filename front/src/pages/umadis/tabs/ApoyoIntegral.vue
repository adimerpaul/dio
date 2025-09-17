<template>
  <q-card flat bordered class="q-pa-md">
    <div class="text-subtitle1 text-weight-medium q-mb-sm">Apoyo integral</div>
    <div class="text-grey-7">DIO personal, descripción, impresión (ID: {{ caseId }})</div>
  </q-card>
</template>
<script>
export default { name: 'ApoyoIntegral', props: { caseId: { type: [String, Number], required: true } } }
</script>

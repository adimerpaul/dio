<template>
  <q-card flat bordered class="q-pa-md">
    <div class="text-subtitle1 text-weight-medium q-mb-sm">Fotografías</div>
    <div class="text-grey-7">Sube/ver fotos relacionadas al caso (ID: {{ caseId }})</div>
  </q-card>
</template>
<script>
export default { name: 'Fotografias', props: { caseId: { type: [String, Number], required: true } } }
</script>

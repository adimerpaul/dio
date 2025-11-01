<template>
  <template v-for="documento in caso.documentos" :key="documento.id">
    <tr v-if="documento.user?.role === role">
      <td>Archivo</td>
      <td>{{ documento.titulo || documento.original_name }}</td>
      <td>{{ (documento.created_at || '').slice(0,10) || '—' }}</td>
      <td>{{ documento.user?.name || '—' }}</td>
      <td>
        <a @click="openDocumento(documento)"
           v-if="documento.url">
          Ver archivo
        </a>
        <span v-else>—</span>
      </td>
    </tr>
  </template>
  <template v-for="foto in caso.fotografias" :key="foto.id">
    <tr v-if="foto.user?.role === role">
      <td>Fotografía</td>
      <td>{{ foto.titulo || foto.original_name }}</td>
      <td>{{ (foto.created_at || '').slice(0,10) || '—' }}</td>
      <td>{{ foto.user?.name || '—' }}</td>
      <td>
        <a @click="openFotografia(foto)"
           v-if="foto.url">
          Ver archivo
        </a>
        <span v-else>—</span>
      </td>
    </tr>
  </template>
</template>
<script>
export default {
  props: {
    caso: {
      type: Object,
      required: true
    },
    role: {
      type: String,
      required: true
    }
  },
  methods: {
    openDocumento(documento) {
      const id = documento.id
      const base = this.$axios?.defaults?.baseURL || ''
      const url = `${base}/documentos/${id}/download`
      this.open(url)
    },
    openFotografia(fotografia) {
      const base = this.$axios?.defaults?.baseURL || ''
      const url = `${base}/../storage/${fotografia.path}`
      this.open(url)
    },
  }
}
</script>

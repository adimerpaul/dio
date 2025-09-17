<template>
  <q-card flat bordered class="q-pa-md">
    <div class="row items-center q-mb-sm">
      <div class="col">
        <div class="text-subtitle1 text-weight-medium">Fotografías</div>
        <div class="text-caption text-grey-7">Sube/ver fotos del caso #{{ caseId }}</div>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="add_photo_alternate"
          no-caps
          label="Subir"
          @click="pickFile"
          :loading="uploading"
        />
        <input type="file" ref="file" accept="image/*" class="hidden" @change="upload" />
      </div>
    </div>

    <q-separator />

    <div class="row q-col-gutter-md q-mt-sm">
      <div v-for="f in caso.fotografias" :key="f.id" class="col-6 col-sm-4 col-md-3">
        <q-card flat bordered>
          <q-img
            :src="toPublicUrl(f.thumb_url || f.url)"
            :ratio="4/3"
            @click="view(toPublicUrl(f.url))"
            style="cursor:pointer"
          >
            <div class="absolute-bottom text-subtitle2 bg-black bg-opacity-40 q-pa-xs ellipsis">
              {{ f.titulo }}
            </div>
          </q-img>
          <q-card-actions align="between">
            <q-btn dense flat icon="delete" color="negative" @click="remove(f)" />
          </q-card-actions>
        </q-card>
      </div>
    </div>

  </q-card>
</template>

<script>
export default {
  name: 'Fotografias',
  props: {
    caseId: { type: [String, Number], required: true },
    caso: { type: Object, required: true }
  },
  data () {
    return {
      rows: { data: [], last_page: 1 },
      page: 1,
      perPage: 12,
      loading: false,
      uploading: false
    }
  },
  methods: {
    toPublicUrl (url) {
      console.log('url', url)
      if (!url) return ''
      if (/^https?:\/\//i.test(url)) return url
      const baseApi = this.$axios?.defaults?.baseURL || ''
      const basePublic = baseApi.replace(/\/api\/?$/, '')
      return `${basePublic}${url}`
    },

    pickFile () { this.$refs.file.click() },

    async upload (e) {
      const file = e.target.files?.[0]
      if (!file) return
      this.uploading = true
      try {
        const fd = new FormData()
        fd.append('file', file)
        await this.$axios.post(`/umadis/${this.caseId}/fotografias`, fd, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
        this.$q.notify({ type: 'positive', message: 'Foto subida' })
        this.$emit('refresh')
      } catch (e) {
        this.$q.notify({ type: 'negative', message: e?.response?.data?.message || 'No se pudo subir' })
      } finally {
        this.uploading = false
        if (this.$refs.file) this.$refs.file.value = ''
      }
    },

    async remove (f) {
      if (!confirm('¿Eliminar foto?')) return
      try {
        await this.$axios.delete(`/umadis/fotografias/${f.id}`)
        this.$q.notify({ type: 'positive', message: 'Eliminada' })
        this.$emit('refresh')
      } catch (e) {
        this.$q.notify({ type: 'negative', message: e?.response?.data?.message || 'No se pudo eliminar' })
      }
    },

    view (url) {
      if (!url) return
      window.open(url, '_blank')
    }
  }
}
</script>

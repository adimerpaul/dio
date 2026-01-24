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
          @click="openDialog"
          :loading="uploading"
        />
      </div>
    </div>

    <q-separator />

    <div class="row q-col-gutter-md q-mt-sm">
      <template v-for="it in caso.documentos" :key="it.id">
      <div v-if="it.user_id === $store.user.id" class="col-6 col-sm-4 col-md-3">
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
      </template>
    </div>

    <!-- Diálogo para título + archivo -->
    <q-dialog v-model="dialog.show" persistent>
      <q-card style="min-width: 350px; max-width: 500px">
        <q-card-section class="row items-center q-gutter-sm">
          <div class="text-h6">Subir fotografía</div>
        </q-card-section>

        <q-card-section class="q-gutter-md">
          <q-input
            v-model="dialog.titulo"
            label="Título de la foto"
            dense
            outlined
            autofocus
            :rules="[val => !!val || 'El título es obligatorio']"
          />

          <div>
            <q-btn
              flat
              icon="attach_file"
              label="Seleccionar archivo"
              @click="triggerFile"
            />
            <div v-if="dialog.fileName" class="text-caption q-mt-xs ellipsis">
              Archivo seleccionado: <strong>{{ dialog.fileName }}</strong>
            </div>
            <div v-else class="text-caption text-grey-7 q-mt-xs">
              Aún no se ha seleccionado ningún archivo
            </div>

            <!-- input real oculto -->
            <input
              type="file"
              ref="file"
              accept="image/*"
              class="hidden"
              @change="onFileChosen"
            />
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" color="grey" @click="closeDialog" :disable="uploading" />
          <q-btn
            color="primary"
            label="Subir"
            :loading="uploading"
            @click="upload"
            :disable="!dialog.file || !dialog.titulo.trim()"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-card>
</template>

<script>
export default {
  name: 'Fotografias',
  props: {
    caseId: { type: [String, Number], required: true },
    caso: { type: Object, required: false }
  },
  data () {
    return {
      rows: { data: [], last_page: 1 },
      page: 1,
      perPage: 12,
      loading: false,
      uploading: false,
      dialog: {
        show: false,
        titulo: '',
        file: null,
        fileName: ''
      }
    }
  },
  methods: {
    // Convierte '/storage/...' a 'http://localhost:8000/storage/...'
    toPublicUrl (url) {
      if (!url) return ''
      if (/^https?:\/\//i.test(url)) return url
      const basePublic = (this.$url || '').replace(/\/api\/?$/, '')
      return `${basePublic}${url}`
    },

    // === Diálogo ===
    openDialog () {
      this.dialog.show = true
      this.dialog.titulo = ''
      this.dialog.file = null
      this.dialog.fileName = ''
      if (this.$refs.file) {
        this.$refs.file.value = ''
      }
    },
    closeDialog () {
      if (this.uploading) return
      this.dialog.show = false
    },

    triggerFile () {
      this.$refs.file && this.$refs.file.click()
    },

    onFileChosen (e) {
      const file = e.target.files?.[0]
      if (!file) {
        this.dialog.file = null
        this.dialog.fileName = ''
        return
      }
      this.dialog.file = file
      this.dialog.fileName = file.name
    },

    async upload () {
      const file = this.dialog.file
      const titulo = this.dialog.titulo.trim()

      if (!file) {
        this.$q.notify({ type: 'negative', message: 'Selecciona un archivo de imagen' })
        return
      }
      if (!titulo) {
        this.$q.notify({ type: 'negative', message: 'Escribe un título para la fotografía' })
        return
      }

      this.uploading = true
      try {
        const fd = new FormData()
        fd.append('file', file)
        fd.append('titulo', titulo)

        await this.$axios.post(`/casos/${this.caseId}/fotografias`, fd, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
        this.$q.notify({ type: 'positive', message: 'Foto subida' })
        this.dialog.show = false
        this.$emit('refresh')
      } catch (e) {
        this.$q.notify({
          type: 'negative',
          message: e?.response?.data?.message || 'No se pudo subir'
        })
      } finally {
        this.uploading = false
        if (this.$refs.file) this.$refs.file.value = ''
        this.dialog.file = null
        this.dialog.fileName = ''
      }
    },

    async remove (f) {
      if (!confirm('¿Eliminar foto?')) return
      try {
        await this.$axios.delete(`/fotografias/${f.id}`)
        this.$q.notify({ type: 'positive', message: 'Eliminada' })
        this.$emit('refresh')
      } catch (e) {
        this.$q.notify({
          type: 'negative',
          message: e?.response?.data?.message || 'No se pudo eliminar'
        })
      }
    },

    view (url) {
      if (!url) return
      window.open(url, '_blank')
    }
  }
}
</script>

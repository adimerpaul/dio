<template>
  <q-card flat bordered class="q-pa-md">
    <div class="row items-center q-gutter-sm q-mb-md">
      <q-avatar icon="qr_code_2" color="primary" text-color="white" />
      <div>
        <div class="text-h6 text-weight-bold">Códigos del Caso</div>
        <div class="text-caption text-grey-7">NUREJ y CUD</div>
      </div>
      <q-space />
      <q-btn
        :label="editMode ? 'Cancelar' : 'Editar'"
        :icon="editMode ? 'close' : 'edit'"
        flat
        color="primary"
        @click="toggleEdit"
      />
      <q-btn
        v-if="editMode"
        color="primary"
        icon="save"
        label="Guardar"
        :loading="loading"
        @click="guardar"
      />
    </div>

    <q-separator spaced />

    <div class="row q-col-gutter-md">
      <div class="col-12 col-md-6">
        <q-input
          v-model="form.nurej"
          :readonly="!editMode"
          filled
          label="NUREJ"
          :dense="$q.screen.lt.md"
        >
          <template #prepend><q-icon name="tag" /></template>
        </q-input>
      </div>
      <div class="col-12 col-md-6">
        <q-input
          v-model="form.cud"
          :readonly="!editMode"
          filled
          label="CUD"
          :dense="$q.screen.lt.md"
        >
          <template #prepend><q-icon name="confirmation_number" /></template>
        </q-input>
      </div>
      <div class="col-12 col-md-6">
        <q-select v-model="form.numero_juzgado"
                  :options="juzgados"
                  :readonly="!editMode"
                  filled
                  label="Número de Juzgado"
                  :dense="$q.screen.lt.md">
          <template #prepend><q-icon name="gavel" /></template>
        </q-select>
      </div>
<!--      responsable_fiscalia-->
      <div class="col-12 col-md-6">
        <q-input
          v-model="form.responsable_fiscalia"
          :readonly="!editMode"
          filled
          label="Responsable Fiscalía"
          :dense="$q.screen.lt.md"
        >
          <template #prepend><q-icon name="person" /></template>
        </q-input>
      </div>
    </div>
  </q-card>
</template>

<script>
import axios from 'axios'

export default {
  name: 'CodigoCaso',
  props: {
    caso: { type: Object, required: true },
  },
  data () {
    return {
      editMode: false,
      loading: false,
      form: {
        nurej: null,
        cud: null,
      },
      juzgados: [
        'Juzgado Publico de la familia 1',
        'Juzgado Publico de la familia 2',
        'Juzgado Publico de la familia 3',
        'Juzgado Publico de la familia 4',
        'Juzgado Publico de la familia 5',
        'Juzgado Publico de la familia 6',
        'Juzgado Publico de la familia 7',
        'Juzgado Publico de la familia 8',
        'Juzgado Publico de la familia 9',
      ],
    }
  },
  watch: {
    // sincroniza cuando cambie 'caso'
    caso: {
      immediate: true,
      deep: true,
      handler (v) {
        this.form.nurej = v?.nurej ?? null
        this.form.cud   = v?.cud   ?? null
        this.form.numero_juzgado = v?.numero_juzgado ?? null
        this.form.responsable_fiscalia = v?.responsable_fiscalia ?? null
      },
    },
  },
  methods: {
    toggleEdit () {
      if (this.editMode) {
        // al cancelar, restaurar desde props
        this.form.nurej = this.caso?.nurej ?? null
        this.form.cud   = this.caso?.cud   ?? null
      }
      this.editMode = !this.editMode
    },
    async guardar () {
      this.loading = true
      try {
        const payload = {
          nurej: this.form.nurej,
          cud: this.form.cud,
          numero_juzgado: this.form.numero_juzgado,
          responsable_fiscalia: this.form.responsable_fiscalia,
        }
        await this.$axios.put(`casos/${this.caso.id}/codigos`, payload)
        this.$emit('actualizado') // notificar al padre
        this.editMode = false
        this.$q.notify({ type: 'positive', message: 'Códigos actualizados correctamente.' })
      } catch (error) {
        console.error(error)
        this.$q.notify({ type: 'negative', message: 'Error al actualizar los códigos.' })
      } finally {
        this.loading = false
      }
    },
  },
}
</script>

<style scoped>
.q-card { border-radius: 16px; }
</style>

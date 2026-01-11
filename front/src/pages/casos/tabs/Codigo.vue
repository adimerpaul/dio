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
        v-if="$store.user.role === 'Abogado' || $store.user.role === 'Administrador'"
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
        <q-select v-model="form.numero_juzgado_padre"
                  :options="juzgados_padre"
                  @update:model-value="changeJuzgadoPadre"
                  :readonly="!editMode"
                  filled
                  label="Tipo Juzgado"
                  :dense="$q.screen.lt.md">
          <template #prepend><q-icon name="balance" /></template>
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
      <div class="col-12 col-md-6">
        <q-select v-model="form.numero_juzgado"
                  :options="juzgados"
                  :readonly="!editMode"
                  filled
                  label="Número de Juzgado"
                  :dense="$q.screen.lt.md">
          <template #prepend><q-icon name="gavel" /></template>
        </q-select>
<!--        ver archivo-->
<!--        href ver archivo-->
<!--        <a :href="`${$url}/..${caso.codigo_file}`" target="_blank">-->
<!--          Ver Archivo de Código-->
<!--        </a>-->
        <div v-if="caso.codigo_file" class="q-mt-md">
          <q-btn
            :href="`${$url}/..${caso.codigo_file}`"
            target="_blank"
            rel="noopener"
            color="secondary"
            icon="visibility"
            label="Ver Archivo de Código"
            :dense="$q.screen.lt.md"
            no-caps
          />
        </div>

<!--        subir archivo -->
        <!--          v-if="solo para Abogado"-->
        <q-file
          v-model="form.archivo_codigo"
          :readonly="!editMode"
          outlined
          label="Subir Archivo de Código"
          accept=".pdf,.doc,.docx"
          @update:model-value="uploadFile"
          dense
          v-if="$store.user.role === 'Abogado'"
        >
          <template #prepend><q-icon name="upload_file" /></template>
        </q-file>
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
      juzgados_padre: [
        'Juzgado Tribunal Sala',
        'Juzgado Familia',
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
        this.form.numero_juzgado_padre = v?.numero_juzgado_padre ?? null
        this.form.responsable_fiscalia = v?.responsable_fiscalia ?? null
      },
    },
  },
  methods: {
    uploadFile(file) {
      this.form.archivo_codigo = file;
      const form = new FormData();
      form.append('file', file);
      this.$axios.post(`casos/${this.caso.id}/upload-codigo-file`, form, {
        headers: { 'Content-Type': 'multipart/form-data' }
      }).then(() => {
        this.$q.notify({ type: 'positive', message: 'Archivo de código subido correctamente.' });
        this.$emit('refresh');
      }).catch((error) => {
        console.error(error);
        this.$q.notify({ type: 'negative', message: 'Error al subir el archivo de código.' });
      });
    },
    changeJuzgadoPadre(value) {
      this.form.numero_juzgado = null; // resetear el valor seleccionado
      if(value === 'Juzgado Familia') {
        this.juzgados = [
          'Juzgado Publico de la familia 1',
          'Juzgado Publico de la familia 2',
          'Juzgado Publico de la familia 3',
          'Juzgado Publico de la familia 4',
          'Juzgado Publico de la familia 5',
          'Juzgado Publico de la familia 6',
          'Juzgado Publico de la familia 7',
          'Juzgado Publico de la familia 8',
          'Juzgado Publico de la familia 9',
        ]
      }else{
        this.juzgados = [
          "JUZGADO - TRIBUNAL - SALA",
          "JUZGADO DE LA NIÑEZ Y ADOLESCENCIA N° 1",
          "JUZGADO DE LA NIÑEZ Y ADOLESCENCIA N° 2",
          "JUZGADO DE INSTRUCCIÓN PENAL ANTICORRUPCIÓN Y CONTRA LA VIOLENCIA HACIA LAS MUJERES N° 1",
          "JUZGADO DE INSTRUCCIÓN PENAL ANTICORRUPCIÓN Y CONTRA LA VIOLENCIA HACIA LAS MUJERES N° 2",
          "JUZGADO DE INSTRUCCIÓN PENAL ANTICORRUPCIÓN Y CONTRA LA VIOLENCIA HACIA LAS MUJERES N° 3",
          "JUZGADO DE INSTRUCCIÓN PENAL ANTICORRUPCIÓN Y CONTRA LA VIOLENCIA HACIA LAS MUJERES N° 4",
          "JUZGADO DE INSTRUCCIÓN PENAL ANTICORRUPCIÓN Y CONTRA LA VIOLENCIA HACIA LAS MUJERES N° 5",
          "JUZGADO DE INSTRUCCIÓN PENAL ANTICORRUPCIÓN Y CONTRA LA VIOLENCIA HACIA LAS MUJERES N° 6",
          "JUZGADO DE INSTRUCCIÓN PENAL ANTICORRUPCIÓN Y CONTRA LA VIOLENCIA HACIA LAS MUJERES N° 7",
          "JUZGADO DE INSTRUCCIÓN PENAL ANTICORRUPCIÓN Y CONTRA LA VIOLENCIA HACIA LAS MUJERES N° 8",
          "TRIBUNAL DE SENTENCIA PENAL N° 1",
          "TRIBUNAL DE SENTENCIA PENAL N° 2",
          "TRIBUNAL DE SENTENCIA PENAL N° 3",
          "JUZGADO DE SENTENCIA PENAL N° 1",
          "JUZGADO DE SENTENCIA PENAL N° 2",
          "JUZGADO DE SENTENCIA PENAL N° 3",
          "JUZGADO DE SENTENCIA PENAL N° 4",
          "JUZGADO DE SENTENCIA PENAL N° 5",
          "JUZGADO DE SENTENCIA PENAL N° 6",
          "JUZGADO DE SENTENCIA PENAL N° 7",
          "SALA PENAL N° 1",
          "SALA PENAL N° 2",
          "SALA PENAL N° 3",
          "JUZGADO DE EJECUCIÓN PENAL 1",
          "JUZGADO CIVIL COMERCIAL",
          "SALA CIVIL, COMERCIAL, DE FAMILIA 1",
          "SALA CIVIL, COMERCIAL, DE FAMILIA 2",
          "SALA CONSTITUCIONAL N° 1",
          "SALA CONSTITUCIONAL N° 2",
          "JUZGADO PÚBLICO DE FAMILIA N° 1",
          "JUZGADO PÚBLICO DE FAMILIA N° 2",
          "JUZGADO PÚBLICO DE FAMILIA N° 3",
          "JUZGADO PÚBLICO DE FAMILIA N° 4",
          "JUZGADO PÚBLICO DE FAMILIA N° 5",
          "JUZGADO PÚBLICO DE FAMILIA N° 6",
          "JUZGADO PÚBLICO DE FAMILIA N° 7",
          "JUZGADO PÚBLICO DE FAMILIA N° 8",
          "JUZGADO PÚBLICO DE FAMILIA N° 9"
        ];
      }
    },
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
          numero_juzgado_padre: this.form.numero_juzgado_padre,
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

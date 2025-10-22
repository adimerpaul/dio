<template>
  <div class="row  q-col-gutter-md">
<!--    {{ caseId }}-->
<!--    tipo de caso -->
<!--    respaldo-->
<!--    observaciones-->
    <div class="col-12 col-md-6">
      <q-select
        v-model="caso.estado_caso"
        :options="estado_casos"
        outlined
        label="Estado del Caso"
        :dense="$q.screen.lt.md"
      >
        <template #prepend><q-icon name="folder_open" /></template>
      </q-select>
    </div>
    <div class="col-12 col-md-6" v-if="caso.estado_caso === 'Otros'">
      <q-input
        v-model="caso.estado_caso_otro"
        outlined
        label="Especifique otro estado"
        :dense="$q.screen.lt.md"
      >
        <template #prepend><q-icon name="edit" /></template>
      </q-input>
    </div>
    <div class="col-12 col-md-12">
<!--      <q-input-->
<!--        v-model="caso.respaldo"-->
<!--        outlined-->
<!--        label="Respaldo"-->
<!--        :dense="$q.screen.lt.md"-->
<!--      >-->
<!--        <template #prepend><q-icon name="backup" /></template>-->
<!--      </q-input>-->
<!--      file-->
<!--      ver achico esta lleno-->
      <div v-if="caso.respaldo">
        <q-icon name="attachment" />
        <a :href="caso.respaldo" target="_blank">{{ caso.respaldo.split('/').pop() }}</a>
      </div>
      <q-file
        v-model="file"
        label="Subir Archivo Caso"
        outlined
        :dense="$q.screen.lt.md"
        prepend-icon="backup"
        counter
        :hide-upload-button="true"
        @update:model-value="onFileChange"
      />
    </div>
    <div class="col-12">
      <q-input
        v-model="caso.observaciones"
        outlined
        label="Observaciones"
        type="textarea"
        :dense="$q.screen.lt.md"
      >
        <template #prepend><q-icon name="notes" /></template>
      </q-input>
    </div>
    <div class="col-12">
      <q-btn
        no-caps
        icon="save"
        class="full-width"
        label="Guardar"
        color="primary"
        @click="guardar"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: "EstadoCaso",
  props: {
    caso: { type: Object, required: true },
    caseId: { type: [Number, String], required: true },
  },
  data: function () {
    return {
      casoNew: {},
      file: null,
      estado_casos:[
        "Concluido",
        "Con Representacion",
        "Con Sentencia",
        "En Jugado",
        "En Fiscalia",
        "Archivo",
        "Otros",
      ]
    };
  },
  mounted() {
    this.casoNew.estado_caso = this.caso.estado_caso;
    this.casoNew.estado_caso_otro = this.caso.estado_caso_otro;
    this.casoNew.respaldo = this.caso.respaldo;
    this.casoNew.observaciones = this.caso.observaciones;
  },
  emits: ["refresh"],
  methods: {
    onFileChange (file) {
      if (!file) return;                 // file es un File, no array
      const formData = new FormData();
      formData.append('file', file);      // 👈 directo

      this.$axios.post(`/casos/${this.caseId}/upload_respaldo/`, formData)
        .then(({ data }) => {
          this.caso.respaldo = data.respaldo;
          this.$q.notify({ type: 'positive', message: 'Archivo subido correctamente' });
        })
        .catch(err => {
          this.$q.notify({ type: 'negative', message: `Error al subir: ${err?.response?.data?.detail || err.message}` });
        });
    },
    refresh() {
      this.$emit("refresh");
    },
    guardar() {
      this.$axios
        .put(`/casos/${this.caseId}/`, {
          estado_caso: this.caso.estado_caso,
          estado_caso_otro: this.caso.estado_caso_otro,
          respaldo: this.caso.respaldo,
          observaciones: this.caso.observaciones,
        })
        .then(() => {
          this.$q.notify({
            type: "positive",
            message: "Datos guardados correctamente",
          });
          this.refresh();
        })
        .catch((error) => {
          this.$q.notify({
            type: "negative",
            message: `Error al guardar los datos: ${error.response.data.detail || error.message}`,
          });
        });
    },
  },
};
</script>

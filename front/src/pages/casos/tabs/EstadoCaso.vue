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
    <div class="col-12">
    </div>
<!--    Etapas-->
    <div class="col-12 col-md-3">
      <q-select v-model="caso.etapa_procesal"  outlined label="Etapa Procesal" :dense="$q.screen.lt.md"
                :options="['Denuncia / Querella', 'Etapa Preparatoria', 'Etapa Intermedia', 'Jucio Oral','Etapa Impugnatoria/Recursos']"
                @update:model-value="caso.etapa = ''"
      >
        <template #prepend><q-icon name="gavel" /></template>
      </q-select>
    </div>
    <div class="col-12 col-md-3">
      <q-select v-model="caso.etapa" :options="['Preliminar', 'Investigacion Formalizada', 'Actos Conclusivos', 'Archivado']" outlined label="Inputacion formal" :dense="$q.screen.lt.md"
                v-if="caso.etapa_procesal === 'Etapa Preparatoria'"
      >
        <template #prepend><q-icon name="hourglass_empty" /></template>
      </q-select>
    </div>
    <div class="col-12 col-md-12">
      <div v-if="caso.respaldo">
        <q-icon name="attachment" />
        <q-btn flat @click="fileDownload" target="_blank">{{ caso.respaldo.split('/').pop() }}</q-btn>
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
        :loading="loading"
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
      loading: false,
      estado_casos:[
        "Apertura Denuncia",
        "Notificacion",
        "Respuesta",
        "En Audiencia",
        "En Jugado",
        "En Fiscalia",
        "Con Sentencia",
        "Con Representacion",
        "Concluido",
        "Archivado",
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
    fileDownload() {
      console.log(this.caso.respaldo);
      window.open(this.$axios.defaults.baseURL +'/..'+ this.caso.respaldo, '_blank');
      // const link = document.createElement('a');
      // link.href = this.$axios.defaults.baseURL + this.caso.respaldo;
      // link.download = this.caso.respaldo.split('/').pop();
      // document.body.appendChild(link);
      // link.click();
      // document.body.removeChild(link);
    },
    onFileChange (file) {
      if (!file) return;
      const formData = new FormData();
      formData.append('file', file);
      this.loading = true;
      this.$axios.post(`/casos/${this.caseId}/upload_respaldo/`, formData)
        .then(({ data }) => {
          // this.caso.respaldo = data.respaldo;
          this.$q.notify({ type: 'positive', message: 'Archivo subido correctamente' });
          this.$emit("refresh");
        })
        .catch(err => {
          this.$q.notify({ type: 'negative', message: `Error al subir: ${err?.response?.data?.detail || err.message}` });
        }).finally(() => {
          this.loading = false;
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
          etapa_procesal: this.caso.etapa_procesal,
          etapa: this.caso.etapa,
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

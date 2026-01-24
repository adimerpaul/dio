<template>
  <div class="row  q-col-gutter-md">
<!--    {{ caseId }}-->
<!--    tipo de caso -->
<!--    respaldo-->
<!--    observaciones-->
    <div class="col-12">
    </div>
<!--    Etapas-->
    <div class="col-12 col-md-3">
      <q-select v-model="casoNew.etapa_procesal"  outlined label="Etapa Procesal" dense
                :options="['Denuncia / Querella', 'Etapa Preparatoria', 'Etapa Intermedia', 'Jucio Oral','Etapa Impugnatoria/Recursos']"
                @update:model-value="casoNew.etapa = ''"
                :disable="!['Abogado','Administrador'].includes($store.user.role)"
      >
        <template #prepend><q-icon name="gavel" /></template>
      </q-select>
    </div>
    <div class="col-12 col-md-3">
      <q-select v-model="casoNew.etapa" :options="['Preliminar', 'Investigacion Formalizada', 'Actos Conclusivos', 'Archivado']" outlined label="Inputacion formal" dense
                v-if="casoNew.etapa_procesal === 'Etapa Preparatoria'"
      >
        <template #prepend><q-icon name="hourglass_empty" /></template>
      </q-select>
    </div>
    <div class="col-12"></div>
    <div class="col-12 col-md-3">
      <q-select
        v-model="casoNew.estado_caso"
        :options="estado_casos"
        outlined
        label="Estado del Caso"
        dense
        :disable="!['Abogado','Administrador'].includes($store.user.role)"
      >
        <template #prepend><q-icon name="folder_open" /></template>
      </q-select>
    </div>
    <div class="col-12 col-md-6" v-if="casoNew.estado_caso === 'Otros'">
      <q-input
        v-model="casoNew.estado_caso_otro"
        outlined
        label="Especifique otro estado"
        dense
      >
        <template #prepend><q-icon name="edit" /></template>
      </q-input>
    </div>
    <div class="col-12 col-md-12">
      <div v-if="casoNew.respaldo">
        <q-icon name="attachment" />
        <q-btn flat @click="fileDownload" target="_blank">{{ casoNew.respaldo.split('/').pop() }}</q-btn>
      </div>
      <q-file
        v-model="file"
        label="Subir Archivo Caso"
        outlined
        dense
        prepend-icon="backup"
        counter
        :hide-upload-button="true"
        @update:model-value="onFileChange"
        :disable="!['Abogado','Administrador'].includes($store.user.role)"
      />
    </div>
    <div class="col-12">
      <q-input
        v-model="casoNew.observaciones"
        outlined
        label="Observaciones"
        type="textarea"
        dense
        :disable="!['Abogado','Administrador'].includes($store.user.role)"
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
        // "Notificacion",
        // "Respuesta",
        // "En Audiencia",
        // "En Jugado",
        // "En Fiscalia",
        // "Con Sentencia",
        // "Con Representacion",
        // "Concluido",
        // "Archivado",
        // "Otros",
        "En Seguimiento",
        "En Proceso",
        "Concluido",
        "Archivado",
        "Con Representacion",
        "Otros",
      ]
    };
  },
  mounted() {
    this.casoNew = { ...this.caso };
    // this.casoNew.estado_caso = this.casoNew.estado_caso;
    // this.casoNew.estado_caso_otro = this.casoNew.estado_caso_otro;
    // this.casoNew.respaldo = this.casoNew.respaldo;
    // this.casoNew.observaciones = this.casoNew.observaciones;
  },
  emits: ["refresh"],
  methods: {
    fileDownload() {
      console.log(this.casoNew.respaldo);
      window.open(this.$axios.defaults.baseURL +'/..'+ this.casoNew.respaldo, '_blank');
      // const link = document.createElement('a');
      // link.href = this.$axios.defaults.baseURL + this.casoNew.respaldo;
      // link.download = this.casoNew.respaldo.split('/').pop();
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
          // this.casoNew.respaldo = data.respaldo;
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
      this.loading = true;
      this.$axios
        .put(`/casos/${this.caseId}/`, {
          estado_caso: this.casoNew.estado_caso,
          estado_caso_otro: this.casoNew.estado_caso_otro,
          respaldo: this.casoNew.respaldo,
          observaciones: this.casoNew.observaciones,
          etapa_procesal: this.casoNew.etapa_procesal,
          etapa: this.casoNew.etapa,
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
        }).finally(() => {
          this.loading = false;
        });
    },
  },
};
</script>

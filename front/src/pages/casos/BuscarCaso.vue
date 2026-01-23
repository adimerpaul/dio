<script setup>
</script>
<template>
<q-page>
  <q-card class="my-card">
    <q-card-section>
      <div class="text-h6">Buscar Caso</div>
    </q-card-section>

    <q-card-section>
      <div class="row">
        <div class="col-12 col-md-6">
          <q-input
            v-model="searchQuery"
            label="Ingrese el Numero de caso ,CI nombre completo del caso, NUREJ, CUD"
            outlined
            dense
            clearable
          ></q-input>
        </div>
        <div class="col-12 col-md-2 flex flex-center">
          <q-btn
            icon="search"
            label="Buscar"
            color="primary"
            :loading="loading"
            @click="buscarCaso"
          ></q-btn>
        </div>
      </div>
    </q-card-section>

    <q-card-section v-if="casos.length > 0">
<!--      <pre>{{casos}}</pre>-->
<!--      [-->
<!--      {-->
<!--      "id": 19,-->
<!--      "caso": "SLAM 001/26",-->
<!--      "tipo": "SLAM",-->
<!--      "caso_numero": "001/26",-->
<!--      "titulo": "Proceso Penal SLAM",-->
<!--      "fecha_apertura": null,-->
<!--      "creado": "2026-01-11 05:25",-->
<!--      "participa": [-->
<!--      {-->
<!--      "rol": "Víctima",-->
<!--      "nombre": "AAAAAAAAAA",-->
<!--      "ci": "9081769"-->
<!--      },-->
<!--      {-->
<!--      "rol": "Denunciante",-->
<!--      "nombre": "BBBBBBBBBBB",-->
<!--      "ci": "9081769"-->
<!--      }-->
<!--      ]-->
<!--      },-->
<!--      {-->
<!--      "id": 4,-->
<!--      "caso": "SLIM 001/25",-->
<!--      "tipo": "SLIM",-->
<!--      "caso_numero": "001/25",-->
<!--      "titulo": null,-->
<!--      "fecha_apertura": null,-->
<!--      "creado": "2025-11-09 17:27",-->
<!--      "participa": [-->
<!--      {-->
<!--      "rol": "Víctima",-->
<!--      "nombre": "JOCELYN SANDRA MORALES PATANA",-->
<!--      "ci": "9081769"-->
<!--      },-->
<!--      {-->
<!--      "rol": "Denunciante",-->
<!--      "nombre": "JOCELYN SANDRA MORALES PATANA",-->
<!--      "ci": "9081769"-->
<!--      }-->
<!--      ]-->
<!--      }-->
<!--      ]-->
      <q-item>
        <q-item-section>
          <div class="text-subtitle1">Casos encontrados:</div>
          <q-list bordered separator>
            <q-item v-for="caso in casos" :key="caso.id" clickable @click="irCaso(caso)">
              <q-item-section>
                <div><strong>ID:</strong> {{ caso.id }}</div>
                <div><strong>Caso:</strong> {{ caso.caso }}</div>
                <div><strong>Título:</strong> {{ caso.titulo || 'N/A' }}</div>
                <div><strong>Creado:</strong> {{ caso.creado }}</div>
                <div>
                  <strong>Participantes:</strong>
                  <ul>
                    <li v-for="participante in caso.participa" :key="participante.ci">
                      {{ participante.rol }}: {{ participante.nombre }} (CI: {{ participante.ci }})
                    </li>
                  </ul>
                </div>
              </q-item-section>
            </q-item>
          </q-list>
        </q-item-section>
      </q-item>
    </q-card-section>
    <q-card-section v-else-if="!loading && buscado && casos.length === 0">
      <div class="text-subtitle1">
        No se encontró ningún caso
      </div>
    </q-card-section>

  </q-card>
</q-page>
</template>
<script>
export default {
  data() {
    return {
      loading: false,
      searchQuery: '',
      casos: [],
      buscado: false
    };
  },
  mounted() {
    this.searchQuery = '9081769'
  },
  methods: {
    irCaso(caso) {
      this.$router.push('/casos/' + caso.id);
    },
    buscarCaso() {
      // const { data } = await this.$axios.get('/casosHistorialDocumentos', {
      //   params: { ci }
      // });
      this.loading = true;
      this.$axios
        .get('/casosHistorialDocumentos', {
          params: { ci: this.searchQuery }
        })
        .then((response) => {
          this.casos = response.data.casos;
          this.buscado = true;
        })
        .catch(() => {
          this.casos = [];
          this.buscado = true;
        })
        .finally(() => {
          this.loading = false;
        });
    }
  }
};
</script>

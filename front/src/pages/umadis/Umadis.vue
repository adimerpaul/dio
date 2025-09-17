<template>
  <q-page class="q-pa-md">
    <div class="row items-center q-col-gutter-md q-mb-md">
      <div class="col-12 col-md">
        <q-input
          v-model="search"
          outlined dense debounce="400"
          placeholder="Buscar por N° SLAM, denunciante, denunciado, tipología, zona…"
          @update:model-value="onSearch"
        >
          <template #prepend><q-icon name="search" /></template>
          <template #append>
            <q-btn flat dense round icon="close" @click="clearSearch" v-if="search"/>
          </template>
        </q-input>
      </div>

      <div class="col-auto">
        <q-toggle
          v-model="onlyPendientes"
          color="primary"
          label="Solo mis pendientes"
          @update:model-value="goFirstPage"
        />
      </div>

      <div class="col-auto">
        <q-select
          v-model="perPage"
          :options="[10, 20, 50]"
          dense outlined style="width: 120px"
          label="Por página"
          @update:model-value="goFirstPage"
        />
      </div>

      <div class="col-auto">
        <q-btn color="primary" icon="refresh" label="Actualizar" no-caps @click="fetchSlims()" :loading="loading"/>
      </div>
    </div>
<!--    [-->
<!--    {-->
<!--    "id": 2,-->
<!--    "fecha_registro": "2025-09-17",-->
<!--    "numero_apoyo_integral": null,-->
<!--    "numero_caso": "111",-->
<!--    "am_latitud": "-17.9378541",-->
<!--    "am_longitud": "-67.1175964",-->
<!--    "am_extravio": null,-->
<!--    "am_medicina": null,-->
<!--    "am_fisioterapia": null,-->
<!--    "am_idioma_castellano": 0,-->
<!--    "am_idioma_quechua": 0,-->
<!--    "am_idioma_aymara": 0,-->
<!--    "am_idioma_otros": "asd",-->
<!--    "ref_tel_fijo": "2321",-->
<!--    "ref_tel_movil": "123",-->
<!--    "ref_tel_movil_alt": "12312",-->
<!--    "den_nombres": "asdsa",-->
<!--    "den_paterno": "asd",-->
<!--    "den_materno": "asd",-->
<!--    "den_edad": "232",-->
<!--    "den_domicilio": "asda",-->
<!--    "den_estado_civil": "asd",-->
<!--    "den_idioma": "asd",-->
<!--    "den_grado_instruccion": "asd",-->
<!--    "den_ocupacion": "asd",-->
<!--    "hecho_descripcion": "asdsa",-->
<!--    "tip_violencia_fisica": 1,-->
<!--    "tip_violencia_psicologica": 1,-->
<!--    "tip_abandono": 1,-->
<!--    "tip_apoyo_integral": 1,-->
<!--    "doc_ci": 0,-->
<!--    "doc_frontal_denunciado": 1,-->
<!--    "doc_frontal_denunciante": 1,-->
<!--    "doc_croquis": 0,-->
<!--    "user_id": 1,-->
<!--    "psicologica_user_id": 3,-->
<!--    "trabajo_social_user_id": 5,-->
<!--    "legal_user_id": 4,-->
<!--    "mi_estado": {-->
<!--    "me_asignado": false,-->
<!--    "rol": null,-->
<!--    "primer_hecho": false,-->
<!--    "label_listo": "Primer informe listo",-->
<!--    "deadline": "2025-09-27",-->
<!--    "dias_restantes": 9,-->
<!--    "atrasado": false-->
<!--    },-->
<!--    "adultos": [-->
<!--    {-->
<!--    "id": 2,-->
<!--    "slam_id": 2,-->
<!--    "nombre": "asda",-->
<!--    "paterno": "232",-->
<!--    "materno": "asda",-->
<!--    "documento_tipo": null,-->
<!--    "documento_num": "232",-->
<!--    "fecha_nacimiento": "2000-10-03T04:00:00.000000Z",-->
<!--    "lugar_nacimiento": "23232",-->
<!--    "edad": "23",-->
<!--    "domicilio": "232",-->
<!--    "estado_civil": "a2312",-->
<!--    "ocupacion_1": "12312",-->
<!--    "ocupacion_2": "123",-->
<!--    "ingresos": "1231"-->
<!--    }-->
<!--    ],-->
<!--    "familiares": [-->
<!--    {-->
<!--    "id": 2,-->
<!--    "slam_id": 2,-->
<!--    "nombre": "1231",-->
<!--    "paterno": "123",-->
<!--    "materno": "123",-->
<!--    "parentesco": "123",-->
<!--    "edad": 23,-->
<!--    "sexo": "232",-->
<!--    "telefono": "2232"-->
<!--    }-->
<!--    ],-->
<!--    "psicologica_user": {-->
<!--    "id": 3,-->
<!--    "name": "Lic. Ana  Calle"-->
<!--    },-->
<!--    "trabajo_social_user": {-->
<!--    "id": 5,-->
<!--    "name": "Lic. Tania Calizaya"-->
<!--    },-->
<!--    "legal_user": {-->
<!--    "id": 4,-->
<!--    "name": "Abo. Anita Calle"-->
<!--    },-->
<!--    "user": {-->
<!--    "id": 1,-->
<!--    "name": "Ing Evelin Ramirez Cube"-->
<!--    }-->
<!--    },-->
<!--    {-->
<!--    "id": 1,-->
<!--    "fecha_registro": null,-->
<!--    "numero_apoyo_integral": null,-->
<!--    "numero_caso": "12321",-->
<!--    "am_latitud": "-17.9660150",-->
<!--    "am_longitud": "-67.1079018",-->
<!--    "am_extravio": null,-->
<!--    "am_medicina": null,-->
<!--    "am_fisioterapia": null,-->
<!--    "am_idioma_castellano": 0,-->
<!--    "am_idioma_quechua": 1,-->
<!--    "am_idioma_aymara": 1,-->
<!--    "am_idioma_otros": null,-->
<!--    "ref_tel_fijo": "1232",-->
<!--    "ref_tel_movil": "1231",-->
<!--    "ref_tel_movil_alt": "12312",-->
<!--    "den_nombres": "asd",-->
<!--    "den_paterno": "asd",-->
<!--    "den_materno": "asd",-->
<!--    "den_edad": "232",-->
<!--    "den_domicilio": "asd",-->
<!--    "den_estado_civil": "asd",-->
<!--    "den_idioma": "as",-->
<!--    "den_grado_instruccion": "asd",-->
<!--    "den_ocupacion": "asda",-->
<!--    "hecho_descripcion": "asdsada",-->
<!--    "tip_violencia_fisica": 1,-->
<!--    "tip_violencia_psicologica": 0,-->
<!--    "tip_abandono": 1,-->
<!--    "tip_apoyo_integral": 0,-->
<!--    "doc_ci": 0,-->
<!--    "doc_frontal_denunciado": 1,-->
<!--    "doc_frontal_denunciante": 1,-->
<!--    "doc_croquis": 1,-->
<!--    "user_id": 1,-->
<!--    "psicologica_user_id": 3,-->
<!--    "trabajo_social_user_id": 5,-->
<!--    "legal_user_id": 4,-->
<!--    "mi_estado": {-->
<!--    "me_asignado": false,-->
<!--    "rol": null,-->
<!--    "primer_hecho": false,-->
<!--    "label_listo": "Primer informe listo",-->
<!--    "deadline": "2025-09-27",-->
<!--    "dias_restantes": 9,-->
<!--    "atrasado": false-->
<!--    },-->
<!--    "adultos": [-->
<!--    {-->
<!--    "id": 1,-->
<!--    "slam_id": 1,-->
<!--    "nombre": "asdas",-->
<!--    "paterno": "asdas",-->
<!--    "materno": "asdas",-->
<!--    "documento_tipo": null,-->
<!--    "documento_num": "2321",-->
<!--    "fecha_nacimiento": "2025-09-12T04:00:00.000000Z",-->
<!--    "lugar_nacimiento": "asdsa",-->
<!--    "edad": "232",-->
<!--    "domicilio": "asdsa",-->
<!--    "estado_civil": "asda",-->
<!--    "ocupacion_1": "232",-->
<!--    "ocupacion_2": "asda",-->
<!--    "ingresos": "asda"-->
<!--    }-->
<!--    ],-->
<!--    "familiares": [-->
<!--    {-->
<!--    "id": 1,-->
<!--    "slam_id": 1,-->
<!--    "nombre": "adsa",-->
<!--    "paterno": "asdas",-->
<!--    "materno": "asd",-->
<!--    "parentesco": "asdas",-->
<!--    "edad": 232,-->
<!--    "sexo": "as",-->
<!--    "telefono": "asda"-->
<!--    }-->
<!--    ],-->
<!--    "psicologica_user": {-->
<!--    "id": 3,-->
<!--    "name": "Lic. Ana  Calle"-->
<!--    },-->
<!--    "trabajo_social_user": {-->
<!--    "id": 5,-->
<!--    "name": "Lic. Tania Calizaya"-->
<!--    },-->
<!--    "legal_user": {-->
<!--    "id": 4,-->
<!--    "name": "Abo. Anita Calle"-->
<!--    },-->
<!--    "user": {-->
<!--    "id": 1,-->
<!--    "name": "Ing Evelin Ramirez Cube"-->
<!--    }-->
<!--    }-->
<!--    ]-->

    <q-markup-table flat bordered dense>
      <thead>
      <tr>
        <th style="width: 60px">#</th>
        <th>Nº SLAM</th>
        <th>Fecha</th>
        <th>Denunciante</th>
        <th>Denunciado</th>
        <th>Tipología</th>
        <th>Zona</th>
        <th>Tipo</th>
        <th>Alerta</th>
      </tr>
      </thead>

      <tbody v-if="!loading && umadis.length">
      <tr
        v-for="(c, idx) in umadis"
        :key="c.id"
        @click="$router.push('/umadis/' + c.id)"
        class="cursor-pointer"
      >
        <td>{{ rowIndex(idx) }}</td>
        <td class="text-no-wrap">{{ c.numero_caso || '—' }}</td>
        <td class="text-no-wrap">{{ $filters.date(c.fecha_registro) }}</td>
        <td>
          <div class="text-weight-medium">{{ c.nombres + ' ' + c.paterno + ' ' + c.materno }}</div>
        </td>
        <td>
          <div class="text-weight-medium">{{ c.denunciado_nombres + ' ' + c.denunciado_paterno + ' ' + c.denunciado_materno }}</div>
<!--          <div class="text-caption text-grey-7" v-if="c.denunciado_nro">CI: {{ c.denunciado_nro }}</div>-->
        </td>
        <td>
<!--          // ===== 6) TIPOLOGÍA (checks) =====-->
<!--          'tip_violencia_fisica',-->
<!--          'tip_violencia_psicologica',-->
<!--          'tip_abandono',-->
<!--          'tip_apoyo_integral',-->
          <q-chip v-if="c.tip_violencia_fisica" color="red" text-color="white" size="sm" class="q-mr-xs">
            Física
          </q-chip>
          <q-chip v-if="c.tip_violencia_psicologica" color="orange" text-color="white" size="sm" class="q-mr-xs">
            Psicológica
          </q-chip>
          <q-chip v-if="c.tip_abandono" color="blue" text-color="white" size="sm" class="q-mr-xs">
            Abandono
          </q-chip>
          <q-chip v-if="c.tip_apoyo_integral" color="purple" text-color="white" size="sm" class="q-mr-xs">
            Apoyo Integral
          </q-chip>
          <div v-if="!c.tip_violencia_fisica && !c.tip_violencia_psicologica && !c.tip_abandono && !c.tip_apoyo_integral" class="text-grey-6">—</div>
        </td>
        <td>{{ c.zona || '—' }}</td>
        <td>
          <q-badge
            :color="c.numero_apoyo_integral ? 'green' : 'grey-5'"
            :text-color="c.numero_apoyo_integral ? 'white' : 'black'"
            :label="c.numero_apoyo_integral ? 'Apoyo Integral' : 'Regular'"
            rounded
            dense
          />
        </td>
        <td>
          <template v-if="c.mi_estado?.me_asignado">
            <template v-if="c.mi_estado.primer_informe_hecho">
              <q-badge color="green" text-color="white" class="q-mr-xs">
                {{ c.mi_estado.label_listo || 'Primer informe listo' }}
              </q-badge>
            </template>
            <template v-else>
              <q-badge
                :color="c.mi_estado.atrasado ? 'red' : 'orange'"
                :text-color="c.mi_estado.atrasado ? 'white' : 'black'"
                class="q-mr-xs"
              >
                <template v-if="c.mi_estado.atrasado">
                  Atrasado {{ Math.abs(c.mi_estado.dias_restantes) }} d
                </template>
                <template v-else>
                  Faltan {{ c.mi_estado.dias_restantes }} d
                </template>
              </q-badge>
              <q-tooltip transition-show="jump-down" transition-hide="jump-up">
                Debes cargar tu
                <b>{{ c.mi_estado.rol === 'Psicologo' ? 'primera sesión' : 'primer informe' }}</b>.<br>
                Fecha límite: <b>{{ c.mi_estado.deadline }}</b>.
              </q-tooltip>
            </template>
          </template>
          <template v-else>
            <span class="text-grey-6">—</span>
          </template>
        </td>
      </tr>
      </tbody>

      <tbody v-else-if="!loading && !umadis.length">
      <tr>
        <td colspan="9" class="text-center text-grey">
          No hay resultados para tu búsqueda.
        </td>
      </tr>
      </tbody>

      <tbody v-else>
      <tr>
        <td colspan="9" class="text-center">
          <q-spinner-dots size="24px" class="q-mr-sm" /> Cargando…
        </td>
      </tr>
      </tbody>
    </q-markup-table>
<!--    <pre>-->
<!--      {{umadis}}-->
<!--    </pre>-->

    <div class="row items-center justify-between q-mt-md">
      <div class="text-caption text-grey-7">
        Mostrando
        <b>{{ meta.from || 0 }}</b> – <b>{{ meta.to || 0 }}</b>
        de <b>{{ meta.total || 0 }}</b> registros
      </div>

      <q-pagination
        v-model="page"
        :max="meta.last_page || 1"
        max-pages="10"
        boundary-numbers
        direction-links
        @update:model-value="fetchSlims"
      />
    </div>
  </q-page>
</template>

<script>
export default {
  name: 'SlimsPage',
  data () {
    return {
      umadis: [],
      loading: false,
      // server meta
      page: 1,
      perPage: 10,
      meta: { current_page: 1, last_page: 1, total: 0, from: 0, to: 0 },
      // search
      search: '',
      // filtro pendientes
      onlyPendientes: false,
    }
  },
  mounted () {
    // Si llega ?only_pendientes=1 desde el layout, lo activamos
    this.onlyPendientes = String(this.$route.query.only_pendientes || '') === '1'
    this.fetchSlims()
  },
  watch: {
    '$route.query.only_pendientes'(v) {
      this.onlyPendientes = String(v || '') === '1'
      this.goFirstPage()
    }
  },
  methods: {
    fetchSlims () {
      this.loading = true
      this.$axios.get('/umadis', {
        params: {
          q: this.search || '',
          page: this.page,
          per_page: this.perPage,
          only_pendientes: this.onlyPendientes ? 1 : 0
        }
      })
        .then(res => {
          const r = res.data || {}
          this.umadis = r.data || []
          this.meta  = {
            current_page: r.current_page || 1,
            last_page:    r.last_page || 1,
            total:        r.total || 0,
            from:         r.from || 0,
            to:           r.to || 0
          }
          this.page = this.meta.current_page
        })
        .catch(e => this.$alert.error(e.response?.data?.message || 'Error cargando SLAMs'))
        .finally(() => { this.loading = false })
    },
    onSearch () {
      this.page = 1
      this.fetchSlims()
    },
    clearSearch () {
      this.search = ''
      this.onSearch()
    },
    goFirstPage () {
      this.page = 1
      this.fetchSlims()
    },
    rowIndex (idx) {
      return (this.meta.from || 0) + idx
    }
  }
}
</script>

<style scoped>
.ellipsis-2-lines {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>

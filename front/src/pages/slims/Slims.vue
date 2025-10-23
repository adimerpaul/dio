<template>
  <q-page class="q-pa-md">
    <div class="row items-center q-col-gutter-md q-mb-md">
      <div class="col-12 col-md">
        <q-input
          v-model="search"
          outlined dense debounce="400"
          placeholder="Buscar por N° SLIM, denunciante, denunciado, tipología, zona…"
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
        <q-btn color="primary" icon="refresh" label="Actualizar" no-caps @click="fetchSlims()" :loading="loading" size="10px"/>
<!--        <q-btn color="secondary" icon="print" label="Imprimir" no-caps @click="imprimir()" />-->
        <q-btn-dropdown label="Visualizar Casos" color="green" icon="visibility" no-caps size="10px">
          <q-list>
<!--            <q-item clickable v-ripple @click="imprimir()">-->
<!--              <q-item-section>Imprimir SLIMs</q-item-section>-->
<!--            </q-item>-->
<!--            descrgar excel-->
            <q-item clickable v-ripple @click="visibleShow" v-close-popup>
              <q-item-section avatar>
                <q-icon name="visibility" />
              </q-item-section>
              <q-item-section>Vizualizar Casos</q-item-section>
            </q-item>
<!--            <q-item clickable v-ripple @click="excelDownload" v-close-popup>-->
<!--              <q-item-section avatar>-->
<!--                <q-icon name="file_download" />-->
<!--              </q-item-section>-->
<!--              <q-item-section>Descargar Excel</q-item-section>-->
<!--            </q-item>-->
<!--            <q-item clickable v-ripple @click="$router.push('/slims/imprimir-etiquetas')">-->
<!--              <q-item-section>Imprimir etiquetas</q-item-section>-->
<!--            </q-item>-->
          </q-list>
        </q-btn-dropdown>
      </div>
    </div>

    <q-markup-table flat bordered dense>
      <thead>
      <tr>
        <th style="width: 60px">#</th>
        <th>Nº </th>
        <th>Fecha</th>
        <th>Denunciante</th>
        <th>Denunciado</th>
        <th>Tipología</th>
        <th>Zona</th>
        <th>Estado</th>
        <th>Alerta</th>

        <!-- ⬇️ NUEVAS COLUMNAS (formato planilla) -->
        <th v-if="showExtra">Víctima (NNA)</th>
        <th v-if="showExtra">Resp. Legal</th>
        <th v-if="showExtra">Resp. Psicológico</th>
        <th v-if="showExtra">Resp. T. Social</th>
        <th v-if="showExtra">CUD</th>
        <th v-if="showExtra">NUREJ</th>
        <th v-if="showExtra">Juzgado</th>
        <th v-if="showExtra">Fiscalía a Resp.</th>
        <th v-if="showExtra">Estado actual del proceso</th>
        <th v-if="showExtra">Fecha Sentencia</th>
        <th v-if="showExtra">Observación</th>
        <!-- (Opcional) columna extra si es Apoyo Integral -->
        <th v-if="showExtra">F. Entrega al Juzgado</th>
      </tr>
      </thead>


      <tbody v-if="!loading && slims.length">
      <tr
        v-for="(c, idx) in slims"
        :key="c.id"
        @click="$router.push('/casos/' + c.id)"
        class="cursor-pointer"
      >
        <td>{{ rowIndex(idx) }}</td>
        <td class="text-no-wrap">{{c.tipo}} {{ c.caso_numero || '—' }}</td>
        <td class="text-no-wrap">{{ $filters.date(c.fecha_apertura_caso) }}</td>
        <td>
<!--          <div class="text-weight-medium">{{ c.denunciante_nombre_completo }}</div>-->
<!--          <div class="text-caption text-grey-7" v-if="c.denunciante_nro">CI: {{ c.denunciante_nro }}</div>-->
<!--          "denunciados": [-->
<!--          {-->
<!--          "id": 3,-->
<!--          "denunciado_nombres": null,-->
<!--          "denunciado_paterno": null,-->
<!--          "denunciado_materno": null,-->
<!--          "denunciado_documento": "Carnet de identidad",-->
<!--          "denunciado_nro": null,-->
<!--          "denunciado_sexo": null,-->
<!--          "denunciado_lugar_nacimiento": null,-->
<!--          "denunciado_fecha_nacimiento": null,-->
<!--          "denunciado_edad": null,-->
<!--          "denunciado_telefono": null,-->
<!--          "denunciado_residencia": null,-->
<!--          "denunciado_estado_civil": null,-->
<!--          "denunciado_relacion": null,-->
<!--          "denunciado_grado": null,-->
<!--          "denunciado_trabaja": "1",-->
<!--          "denunciado_prox": null,-->
<!--          "denunciado_ocupacion": null,-->
<!--          "denunciado_ocupacion_exacto": null,-->
<!--          "denunciado_idioma": null,-->
<!--          "denunciado_fijo": null,-->
<!--          "denunciado_movil": null,-->
<!--          "denunciado_domicilio_actual": null,-->
<!--          "denunciado_latitud": null,-->
<!--          "denunciado_longitud": null,-->
<!--          "caso_id": 4-->
<!--          }-->
<!--          <template v-if="c.denunciantes">-->
            <div class="text-weight-medium">{{ c.denunciantes[0].denunciante_nombres == null ? '—' : (c.denunciantes[0].denunciante_nombres + ' ' + (c.denunciantes[0].denunciante_paterno || '') + ' ' + (c.denunciantes[0].denunciante_materno || '') ) }}</div>
            <div class="text-caption text-grey-7" v-if="c.denunciantes[0].denunciante_nro">CI: {{ c.denunciantes[0].denunciante_nro }}</div>
<!--          </template>-->
        </td>
        <td>
<!--          <div class="text-weight-medium">{{ c.denunciado_nombre_completo || '—' }}</div>-->
<!--          <div class="text-caption text-grey-7" v-if="c.denunciado_nro">CI: {{ c.denunciado_nro }}</div>-->
<!--          "denunciados": [-->
<!--          {-->
<!--          "id": 4,-->
<!--          "denunciado_nombres": null,-->
<!--          "denunciado_paterno": null,-->
<!--          "denunciado_materno": null,-->
<!--          "denunciado_documento": "Carnet de identidad",-->
<!--          "denunciado_nro": null,-->
<!--          "denunciado_sexo": null,-->
<!--          "denunciado_lugar_nacimiento": null,-->
<!--          "denunciado_fecha_nacimiento": null,-->
<!--          "denunciado_edad": null,-->
<!--          "denunciado_telefono": null,-->
<!--          "denunciado_residencia": null,-->
<!--          "denunciado_estado_civil": null,-->
<!--          "denunciado_relacion": null,-->
<!--          "denunciado_grado": null,-->
<!--          "denunciado_trabaja": "1",-->
<!--          "denunciado_prox": null,-->
<!--          "denunciado_ocupacion": null,-->
<!--          "denunciado_ocupacion_exacto": null,-->
<!--          "denunciado_idioma": null,-->
<!--          "denunciado_fijo": null,-->
<!--          "denunciado_movil": null,-->
<!--          "denunciado_domicilio_actual": null,-->
<!--          "denunciado_latitud": null,-->
<!--          "denunciado_longitud": null,-->
<!--          "caso_id": 5-->
<!--          }-->
<!--          ]-->
<!--          <template v-if="c.denunciados">-->
            <div class="text-weight-medium">{{ c.denunciados[0]?.denunciado_nombres == null ? '—' : (c.denunciados[0]?.denunciado_nombres + ' ' + (c.denunciados[0]?.denunciado_paterno || '') + ' ' + (c.denunciados[0]?.denunciado_materno || '') ) }}</div>
            <div class="text-caption text-grey-7" v-if="c.denunciados[0]?.denunciado_nro">CI: {{ c.denunciados[0]?.denunciado_nro }}</div>
        </td>
        <td>{{ c.caso_tipologia || '—' }}</td>
        <td>{{ c.zona || '—' }}</td>
        <td>
<!--          <q-badge-->
<!--            :color="c.numero_apoyo_integral ? 'green' : 'grey-5'"-->
<!--            :text-color="c.numero_apoyo_integral ? 'white' : 'black'"-->
<!--            :label="c.numero_apoyo_integral ? 'Apoyo Integral' : 'Regular'"-->
<!--            rounded-->
<!--            dense-->
<!--          />-->
          {{c.estado_caso || '—'}}
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
        <td v-if="showExtra">—</td> <!-- Víctima (NNA) si luego la expones -->
        <td v-if="showExtra">{{ c.legal_user?.name ?? '—' }}</td>
        <td v-if="showExtra">{{ c.psicologica_user?.name ?? '—' }}</td>
        <td v-if="showExtra">{{ c.trabajo_social_user?.name ?? '—' }}</td>
        <td v-if="showExtra">{{ fmt(c.cud) }}</td>
        <td v-if="showExtra">{{ fmt(c.nurej) }}</td>
        <td v-if="showExtra">{{ fmt(c.numero_juzgado) }}</td>
        <td v-if="showExtra">{{ fmt(c.responsable_fiscalia) }}</td>
        <td v-if="showExtra">{{ fmt(c.estado_caso || c.estado_caso_otro) }}</td>
        <td v-if="showExtra">{{ fmtFecha(c.fecha_sentencia) }}</td>
        <td v-if="showExtra" class="ellipsis-2-lines">{{ fmt(c.observaciones) }}</td>
        <td v-if="showExtra">{{ c.numero_apoyo_integral ? fmtFecha(c.fecha_entrega_juzgado || c.fecha_derivacion_area_legal) : '—' }}</td>
      </tr>
      </tbody>

      <tbody v-else-if="!loading && !slims.length">
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
      showExtra: false,
      slims: [],
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
    visibleShow () {
      this.showExtra = !this.showExtra
    },
    nombrePersona (p) {
      if (!p) return '—'
      const n = [
        p.denunciante_nombres ?? p.denunciado_nombres ?? p.nombres,
        p.denunciante_paterno ?? p.paterno,
        p.denunciante_materno ?? p.materno
      ].filter(Boolean).join(' ')
      return n || '—'
    },
    fmt (v) { return (v ?? '') !== '' ? v : '—' },
    fmtFecha (v) {
      if (!v) return '—'
      try {
        const d = new Date(String(v).replace(' ', 'T'))
        const dd = String(d.getDate()).padStart(2, '0')
        const mm = String(d.getMonth()+1).padStart(2, '0')
        const yyyy = d.getFullYear()
        return `${dd}/${mm}/${yyyy}`
      } catch { return v }
    },
    imprimir(){
      this.$axios.get('/slimsImprimir',)
        .then(res => {
          // pdf
          const blob = new Blob([res.data], { type: 'application/pdf' })
          const link = document.createElement('a')
          link.href = window.URL.createObjectURL(blob)
          link.download = 'slims.pdf'
          link.click()
          window.URL.revokeObjectURL(link.href)
        })
        .catch(e => this.$alert.error(e.response?.data?.message || 'Error imprimiendo SLIMs'))
    },
    fetchSlims () {
      this.loading = true
      this.$axios.get('/casos', {
        params: {
          q: this.search || '',
          tipo : 'SLIM',
          page: this.page,
          per_page: this.perPage,
          only_pendientes: this.onlyPendientes ? 1 : 0
        }
      })
        .then(res => {
          const r = res.data || {}
          this.slims = r.data || []
          this.meta  = {
            current_page: r.current_page || 1,
            last_page:    r.last_page || 1,
            total:        r.total || 0,
            from:         r.from || 0,
            to:           r.to || 0
          }
          this.page = this.meta.current_page
        })
        .catch(e => this.$alert.error(e.response?.data?.message || 'Error cargando SLIMs'))
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

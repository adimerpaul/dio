<template>
  <q-page class="q-pa-md">
    <q-table
      :rows="remisiones"
      :columns="columns"
      row-key="id"
      dense
      wrap-cells
      flat
      bordered
      :rows-per-page-options="[0]"
      title="Remisiones de caso"
      :filter="filter"
    >
      <template #top-right>
        <q-btn
          color="positive"
          label="Nuevo"
          no-caps
          icon="add_circle_outline"
          class="q-mr-sm"
          :loading="loading"
          @click="nuevo"
        />
        <q-btn
          color="primary"
          label="Actualizar"
          no-caps
          icon="refresh"
          class="q-mr-sm"
          :loading="loading"
          @click="getRemisiones"
        />
        <q-input v-model="filter" label="Buscar" dense outlined>
          <template #append><q-icon name="search" /></template>
        </q-input>
      </template>

      <!-- Columna de acciones -->
      <template #body-cell-actions="props">
        <q-td :props="props">
          <q-btn-dropdown label="Opciones" no-caps size="10px" dense color="primary">
            <q-list>
              <q-item clickable v-close-popup @click="editar(props.row)">
                <q-item-section avatar><q-icon name="edit" /></q-item-section>
                <q-item-section><q-item-label>Editar</q-item-label></q-item-section>
              </q-item>

              <q-item clickable v-close-popup @click="eliminar(props.row.id)">
                <q-item-section avatar><q-icon name="delete" /></q-item-section>
                <q-item-section><q-item-label>Eliminar</q-item-label></q-item-section>
              </q-item>

              <q-item clickable v-close-popup @click="imprimir(props.row)">
                <q-item-section avatar><q-icon name="print" /></q-item-section>
                <q-item-section><q-item-label>Imprimir hoja</q-item-label></q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </q-td>
      </template>
    </q-table>

    <!-- Dialogo Crear / Editar -->
    <q-dialog v-model="dialog" persistent>
      <q-card style="width: 500px; max-width: 90vw">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1">{{ accion }} remisión</div>
          <q-space /><q-btn icon="close" flat round dense @click="dialog = false" />
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-form @submit.prevent="guardar">
            <!-- Objeto / Cantidad -->
            <q-select
              v-model="remision.objeto_ingreso"
              label="Objeto"
              :options="['Sobre', 'Hojas', 'Carpetas']"
              dense
              outlined
            />
            <q-input
              v-model.number="remision.cantidad"
              label="Cantidad / Hojas"
              type="number"
              dense
              outlined
              class="q-mt-sm"
            />

            <!-- REMITENTE (USUARIO) -->
            <q-input
              v-model="remision.remitente"
              label="Remitente"
              dense
              outlined
              class="q-mt-sm"
            />
            <q-select
              v-model="remision.organizaciones"
              label="Organización"
              dense
              :options="organizaciones"
              outlined
              class="q-mt-sm"
            />

            <!-- REMITENTE OTROS (opcional) -->
            <q-input
              v-if="remision.organizacion === 'Otros'"
              v-model="remision.remitente_otros"
              label="Remitente otros (si corresponde)"
              dense
              outlined
              class="q-mt-sm"
            />
            <q-select
              v-model="remision.user_id"
              :options="usuarios"
              option-value="id"
              :option-label="u => `${u.name} (${u.role})`"
              emit-value
              map-options
              label="Referencia"
              dense
              outlined
              class="q-mt-sm"
            />
            <!--            <pre>{{usuarios}}</pre>-->

            <!-- REFERENCIA (solo para hoja/imprimir, si la sigues usando) -->
<!--            <q-input-->
<!--              v-model="remision.referencia"-->
<!--              label="Referencia"-->
<!--              type="textarea"-->
<!--              dense-->
<!--              outlined-->
<!--              class="q-mt-sm"-->
<!--            />-->

            <!-- DISPOSICIÓN -->
            <q-select
              v-model="remision.disposicion"
              :options="dispociciones"
              label="Disposición / Proveído"
              dense
              outlined
              class="q-mt-sm"
            />

            <div class="text-right q-mt-md">
              <q-btn
                flat
                color="negative"
                label="Cancelar"
                no-caps
                @click="dialog = false"
                :loading="loading"
              />
              <q-btn
                color="primary"
                label="Guardar"
                no-caps
                type="submit"
                class="q-ml-sm"
                :loading="loading"
              />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  name: 'RemisionCasosPage',
  data () {
    return {
      remisiones: [],
      remision: {},
      dialog: false,
      accion: '',
      loading: false,
      filter: '',
      usuarios: [], // <-- usuarios remitentes
      dispociciones:[
        'Urgente',
        'Atender Solicitud',
        'Para su conocimiento',
        'Preparar Informe',
        'Hacer seguimiento',
        'Archivo',
        'Invitación'
      ],
      organizaciones: [
        'Secretario Municipal de Desarrollo Humano',
        'Alcalde Municipal GAMO',
        'DIO',
        'Fiscal de Materia',
        'Defensor del Pueblo',
        'Otros'
  ],
      columns: [
        { name: 'actions', label: 'Acciones', align: 'center' },
        { name: 'codigo_ingreso', label: 'N° Ingreso', field: 'codigo_ingreso', align: 'left' },
        {
          name: 'fecha_hora',
          label: 'Fecha/Hora',
          align: 'left',
          field: row => row.fecha_hora
        },
        { name: 'objeto_ingreso', label: 'Objeto ingreso', field: 'objeto_ingreso', align: 'left' },
        { name: 'cantidad', label: 'Hojas', field: 'cantidad', align: 'center' },
        {
          name: 'remitente',
          label: 'Remitente',
          align: 'left',
          // Nombre del usuario remitente; si no existe, mostramos remitente_otros
          field: row => (row.user ? row.user.name : (row.remitente_otros || ''))
        },
        { name: 'referencia', label: 'Referencia', field: 'referencia', align: 'left' },
        { name: 'disposicion', label: 'Disposición', field: 'disposicion', align: 'left' },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' }
      ]
    }
  },
  mounted () {
    this.getRemisiones()
    this.getUsuarios()
  },
  methods: {
    async getRemisiones () {
      this.loading = true
      try {
        const res = await this.$axios.get('remision-casos')
        this.remisiones = res.data
      } catch (e) {
        this.$alert && this.$alert.error(e.response?.data?.message || 'Error cargando remisiones')
      } finally {
        this.loading = false
      }
    },
    async getUsuarios () {
      try {
        // Endpoint basado en tu UserController::usuariosRole
        const res = await this.$axios.get('users')
        this.usuarios = res.data
      } catch (e) {
        console.error(e)
        this.$alert && this.$alert.error('Error cargando usuarios remitentes')
      }
    },
    nuevo () {
      this.remision = {
        codigo_ingreso: '',
        fecha_hora: '',
        objeto_ingreso: '',
        cantidad: null,
        user_id: null,
        remitente_otros: '',
        referencia: '',
        disposicion: '',
        estado: 'ACTIVO'
      }
      this.accion = 'Nueva'
      this.dialog = true
    },
    editar (row) {
      this.remision = {
        ...row,
        user_id: row.user_id || null,
        remitente_otros: row.remitente_otros || ''
      }
      this.accion = 'Editar'
      this.dialog = true
    },
    async eliminar (id) {
      this.$alert
        ? this.$alert.dialog('¿Desea eliminar la remisión?')
          .onOk(async () => {
            this.loading = true
            try {
              await this.$axios.delete(`remision-casos/${id}`)
              this.$alert.success('Remisión eliminada')
              this.getRemisiones()
            } catch (e) {
              this.$alert.error(e.response?.data?.message || 'No se pudo eliminar')
            } finally {
              this.loading = false
            }
          })
        : console.log('Confirm delete', id)
    },
    async guardar () {
      this.loading = true
      try {
        if (this.remision.id) {
          await this.$axios.put(`remision-casos/${this.remision.id}`, this.remision)
          this.$alert && this.$alert.success('Remisión actualizada')
        } else {
          await this.$axios.post('remision-casos', this.remision)
          this.$alert && this.$alert.success('Remisión creada')
        }
        this.dialog = false
        this.getRemisiones()
      } catch (e) {
        this.$alert && this.$alert.error(e.response?.data?.message || 'No se pudo guardar')
      } finally {
        this.loading = false
      }
    },

    // --- Imprimir hoja de ruta ---
    imprimir (row) {
      const fechaHora = row.fecha_hora || ''
      const fecha = fechaHora.substring(0, 10)          // yyyy-mm-dd
      const hora = fechaHora.substring(11, 16) || ''    // hh:mm

      const remitenteTexto = row.user
        ? row.user.name
        : (row.remitente_otros || '')

      const win = window.open('', '_blank', 'width=800,height=1000')
      win.document.write(`
        <html>
        <head>
          <title>Hoja de Ruta</title>
          <style>
            * { box-sizing: border-box; font-family: Arial, sans-serif; }
            body { margin: 20px; }
            .hoja {
              width: 100%;
              border: 1px solid #000;
              padding: 16px;
              font-size: 12px;
            }
            .titulo {
              text-align: center;
              font-weight: bold;
              font-size: 18px;
              margin-bottom: 10px;
            }
            .fila {
              display: flex;
              margin-bottom: 6px;
            }
            .campo {
              border: 1px solid #000;
              padding: 4px;
              flex: 1;
              margin-right: 4px;
            }
            .campo:last-child { margin-right: 0; }
            .label {
              font-weight: bold;
              font-size: 11px;
              display: block;
            }
            .valor {
              min-height: 18px;
            }
            .texto-largo {
              min-height: 40px;
            }
            .seccion-titulo {
              font-weight: bold;
              margin-top: 8px;
              margin-bottom: 4px;
            }
            .lista-proveido {
              margin-top: 6px;
              font-size: 11px;
            }
          </style>
        </head>
        <body onload="window.print();window.close();">
          <div class="hoja">
            <div class="titulo">HOJA DE RUTA</div>

            <div class="fila">
              <div class="campo">
                <span class="label">N° DE INGRESO</span>
                <div class="valor">${row.codigo_ingreso || ''}</div>
              </div>
              <div class="campo">
                <span class="label">HOJAS</span>
                <div class="valor">${row.cantidad || ''}</div>
              </div>
              <div class="campo">
                <span class="label">FECHA</span>
                <div class="valor">${fecha}</div>
              </div>
              <div class="campo">
                <span class="label">HORA</span>
                <div class="valor">${hora}</div>
              </div>
            </div>

            <div class="campo" style="margin-bottom:6px;">
              <span class="label">REMITENTE FIRMADO POR</span>
              <div class="texto-largo">${remitenteTexto}</div>
            </div>

            <div class="seccion-titulo">OBJETO / INGRESO</div>
            <div class="campo texto-largo" style="margin-bottom:6px;">
              <div>${row.objeto_ingreso || ''}</div>
            </div>

            <div class="seccion-titulo">REFERENCIA</div>
            <div class="campo texto-largo" style="margin-bottom:6px;">
              <div>${row.referencia || ''}</div>
            </div>

            <div class="seccion-titulo">PROVEÍDO / DISPOSICIÓN</div>
            <div class="campo texto-largo">
              <div>${row.disposicion || ''}</div>
            </div>

            <div class="lista-proveido">
              <div>☐ Urgente &nbsp;&nbsp; ☐ Atender solicitud &nbsp;&nbsp; ☐ Para su conocimiento</div>
              <div>☐ Preparar informe &nbsp;&nbsp; ☐ Hacer seguimiento &nbsp;&nbsp; ☐ Archivo &nbsp;&nbsp; ☐ Invitación</div>
            </div>
          </div>
        </body>
        </html>
      `)
      win.document.close()
    }
  }
}
</script>

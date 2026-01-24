<template>
  <q-card flat bordered class="q-pa-md">
    <!-- Header -->
    <div class="row items-center q-mb-sm">
      <div class="col">
        <div class="text-subtitle1 text-weight-medium">Documentos (General)</div>
        <div class="text-caption text-grey-7">Gestión de adjuntos del caso #{{ caseId }}</div>
      </div>
      <div class="col-auto row items-center q-gutter-sm">
<!--        <q-input dense outlined v-model="search" placeholder="Buscar..." style="width:260px">-->
<!--          <template #append><q-icon name="search" /></template>-->
<!--        </q-input>-->
        <q-btn flat color="primary" icon="refresh" :loading="loading" @click="$emit('refresh')"/>
        <q-btn color="green" icon="upload" no-caps label="Subir archivo" @click="openUpload"/>
      </div>
    </div>

    <q-separator/>

<!--    <pre>{{caso}}</pre>-->
    <!-- Tabla -->
    <q-markup-table dense flat bordered wrap-cells class="q-mt-sm">
      <thead>
      <tr class="bg-primary text-white">
        <th style="width:70px">ID</th>
        <th style="width:160px">Acciones</th>
        <th>Título</th>
<!--        <th style="width:130px">Categoría</th>-->
        <th style="width:120px">Tamaño</th>
        <th style="width:160px">Tipo</th>
        <th style="width:180px">Usuario</th>
      </tr>
      </thead>
      <tbody>
      <template v-for="it in caso.documentos" :key="it.id">
        <tr  v-if="it.user_id === $store.user.id">
          <td>#{{ it.id }}</td>
          <td>
            <q-btn-dropdown dense color="primary" size="sm" label="Opciones" no-caps>
              <q-item clickable v-close-popup @click="viewDoc(it)">
                <q-item-section avatar><q-icon name="visibility"/></q-item-section>
                <q-item-section>Ver</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="downloadDoc(it)">
                <q-item-section avatar><q-icon name="download"/></q-item-section>
                <q-item-section>Descargar</q-item-section>
              </q-item>
              <q-separator/>
              <q-item clickable v-close-popup @click="editMeta(it)">
                <q-item-section avatar><q-icon name="edit"/></q-item-section>
                <q-item-section>Editar</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="removeDoc(it)">
                <q-item-section avatar><q-icon name="delete" color="negative"/></q-item-section>
                <q-item-section class="text-negative">Eliminar</q-item-section>
              </q-item>
            </q-btn-dropdown>
          </td>
          <td class="text-weight-medium">{{ it.titulo }}</td>
          <!--        <td>{{ it.categoria || '—' }}</td>-->
          <td>{{ it.size_human }}</td>
          <td>{{ it.mime || it.extension }}</td>
          <td>{{ it.user?.name || it.user?.username || '—' }}</td>
        </tr>
      </template>
      <tr v-if="!caso.documentos.length && !loading">
        <td colspan="7" class="text-center text-grey">Sin registros</td>
      </tr>
      </tbody>
    </q-markup-table>

<!--    <div class="row justify-end q-mt-sm">-->
<!--      <q-pagination v-model="page" :max="rows.last_page || 1" boundary-numbers direction-links @input="fetchRows"/>-->
<!--    </div>-->

    <!-- Subida -->
    <q-dialog v-model="dlgUpload" persistent>
      <q-card style="max-width: 700px; width: 95vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1">Subir archivo</div>
          <q-space/><q-btn flat dense round icon="close" v-close-popup/>
        </q-card-section>
        <q-card-section class="q-gutter-md">
          <q-input v-model="meta.titulo" label="Título" outlined dense/>
<!--          <q-input v-model="meta.categoria" label="Categoría" outlined dense/>-->
          <q-input v-model="meta.descripcion" type="textarea" label="Descripción" outlined dense autogrow/>
          <q-file v-model="file" outlined dense use-chips :clearable="true"
                  :accept="accept"
                  hint="Hasta 20MB. PDF, Word, Excel, imágenes, etc."/>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Cancelar" v-close-popup/>
          <q-btn color="primary" label="Subir" :loading="saving" @click="upload"/>
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Editar metadatos -->
    <q-dialog v-model="dlgEdit">
      <q-card style="max-width: 600px; width: 95vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1">Editar metadatos</div>
          <q-space/><q-btn flat dense round icon="close" v-close-popup/>
        </q-card-section>
        <q-card-section class="q-gutter-md">
          <q-input v-model="edit.titulo" label="Título" outlined dense/>
<!--          <q-input v-model="edit.categoria" label="Categoría" outlined dense/>-->
          <q-input v-model="edit.descripcion" type="textarea" label="Descripción" outlined dense autogrow/>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Cancelar" v-close-popup/>
          <q-btn color="primary" label="Guardar" :loading="saving" @click="saveMeta"/>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-card>
</template>

<script>
export default {
  name: 'DocumentosGeneral',
  props: {
    caseId: { type:[String,Number], required:true },
    caso: { type:Object, default:()=>({}) },
  },
  data(){
    return {
      loading:false, saving:false,
      search:'', page:1, perPage:10,
      rows:{ data:[], total:0, last_page:1 },

      // upload
      dlgUpload:false, file:null,
      meta:{ titulo:'', categoria:'', descripcion:'' },
      accept: '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.odt,.ods,.txt,.zip,.jpg,.jpeg,.png,.webp',

      // edit
      dlgEdit:false, edit:{}, editingId:null,
    }
  },
  // watch:{
  //   caseId(){ this.page=1; this.fetchRows() },
  //   search(){ this.page=1; this.fetchRows() }
  // },
  // created(){ this.fetchRows() },
  methods:{
    // async fetchRows(){
    //   if(!this.caseId) return
    //   this.loading=true
    //   try{
    //     const res = await this.$axios.get(`/casos/${this.caseId}/documentos`, {
    //       params:{ q:this.search, page:this.page, per_page:this.perPage }
    //     })
    //     this.rows = res.data || { data:[], last_page:1 }
    //   }catch(e){
    //     this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'Error cargando documentos' })
    //   }finally{ this.loading=false }
    // },

    openUpload(){ this.file=null; this.meta={titulo:'',categoria:'',descripcion:''}; this.dlgUpload=true },

    async upload(){
      if(!this.file){ this.$q.notify({type:'negative', message:'Seleccione un archivo'}); return }
      this.saving=true
      try{
        const fd = new FormData()
        fd.append('file', this.file)
        if(this.meta.titulo)      fd.append('titulo', this.meta.titulo)
        if(this.meta.categoria)   fd.append('categoria', this.meta.categoria)
        if(this.meta.descripcion) fd.append('descripcion', this.meta.descripcion)

        await this.$axios.post(`/casos/${this.caseId}/documentos`, fd, {
          headers:{ 'Content-Type':'multipart/form-data' }
        })
        this.$q.notify({ type:'positive', message:'Archivo subido' })
        this.dlgUpload=false
        // this.fetchRows()
        this.$emit('refresh')
      }catch(e){
        this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'No se pudo subir' })
      }finally{ this.saving=false }
    },

    viewDoc(it){
      const base = this.$url || ''
      window.open(`${base}/documentos/${it.id}/view`, '_blank')
    },
    downloadDoc(it){
      const base = this.$url || ''
      // window.open(`${base}/documentos/${it.id}/download`, '_blank')
      console.log('url', `${base}/documentos/${it.id}/download`)
    },

    editMeta(it){
      this.editingId = it.id
      this.edit = { titulo: it.titulo, categoria: it.categoria, descripcion: it.descripcion }
      this.dlgEdit = true
    },
    async saveMeta(){
      this.saving = true
      try{
        await this.$axios.put(`/documentos/${this.editingId}`, this.edit)
        this.$q.notify({ type:'positive', message:'Actualizado' })
        this.dlgEdit=false
        // this.fetchRows()
        this.$emit('refresh')
      }catch(e){
        this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'No se pudo actualizar' })
      }finally{ this.saving=false }
    },

    removeDoc(it){
      const go = async () => {
        try{
          await this.$axios.delete(`/documentos/${it.id}`)
          this.$q.notify({ type:'positive', message:'Eliminado' })
          // this.fetchRows()
          this.$emit('refresh')
        }catch(e){
          this.$q.notify({ type:'negative', message: e?.response?.data?.message || 'No se pudo eliminar' })
        }
      }
      if(this.$alert?.dialog) this.$alert.dialog('¿Eliminar el documento?').onOk(go)
      else if(confirm('¿Eliminar el documento?')) go()
    }
  }
}
</script>

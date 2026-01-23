<template>
  <q-page class="q-pa-xs bg-grey-2">
    <div class="toolbar q-pa-sm bg-white row items-center shadow-1" v-if="accion==='nuevo'">
      <div class="col">
        <div class="text-subtitle1 text-weight-bold"> {{ tipo }}</div>
        <div class="text-caption text-grey-7">{{ titulo }}</div>
      </div>
      <div class="col-auto row q-gutter-sm">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm" no-caps dense/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save" no-caps dense/>
      </div>
    </div>
    <div class="" v-else-if="accion==='modificar'">
      <div class="text-right">
<!--        btn cambiar estado de sidabled-->
        <q-btn
          flat color="primary" :icon="disabled ? 'lock_open' : 'lock'" :label="disabled ? 'Habilitar edición' : 'Deshabilitar edición'" @click="disabled = !disabled" no-caps dense
          v-if="disabled && ($store.user.role==='Asistente' || $store.user.role==='Administrador' || $store.user.role==='Auxiliar')"
        />
        <q-btn color="primary" icon="save" label="Guardar Cambios" :loading="loading" @click="update" no-caps dense v-if="disabled===false"/>
      </div>
    </div>
    <q-form class="q-mt-xs" @submit.prevent="save">
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center q-pa-none">
          <q-item class="full-width" dense>
            <q-item-section avatar>
              <q-icon name="assignment_ind"/>
            </q-item-section>
            <q-item-section>
              <div class="text-subtitle1 text-weight-medium" v-if="tipo==='PROPREMI' || tipo==='DNA'">
                1) DATOS DE LA VICTIMA (MENOR/ES)
              </div>
              <div class="text-subtitle1 text-weight-medium" v-else-if="tipo==='JUVENTUDES'">
                1) DATOS DE LA VICTIMA (ADOLECENTE/JOVEN)
              </div>
              <div class="text-subtitle1 text-weight-medium" v-else-if="tipo==='SLAM'">
                1) DATOS DE LA VICTIMA (ADULTOS MAYORES)
              </div>
              <div class="text-subtitle1 text-weight-medium" v-else-if="tipo==='UMADIS'">
                1) DATOS DE LA VICTIMA (PERSONAS CON DISCAPACIDAD)
              </div>
              <div class="text-subtitle1 text-weight-medium" v-else>
                1) DATOS DE LA VICTIMA
              </div>
            </q-item-section>
            <q-item-section side>
              <q-badge color="blue-2" text-color="blue-10" outline rounded>Obligatorio *</q-badge>
            </q-item-section>
          </q-item>
        </q-card-section>
        <q-separator/>

        <q-card-section class="q-pa-xs">
          <div class="row items-center q-gutter-sm q-mb-sm">
            <q-btn dense no-caps color="primary" icon="add" label="Añadir víctima" @click="addVictima" size="10px"/>
            <!--            <q-btn dense no-caps color="negative" icon="remove" label="Quitar última" :disable="f.victimas.length<=1" @click="removeVictima"/>-->
            <div class="text-caption text-grey-7 q-ml-sm">Total: {{ f.victimas.length }}</div>
          </div>
          <template v-for="(v, idx) in f.victimas" :key="idx">
            <q-card flat bordered class="q-mb-md">
              <q-card-section class="row items-center q-pa-xs">
                <q-item class="full-width" dense>
                  <q-item-section avatar>
                    <q-icon name="person"/>
                  </q-item-section>
                  <q-item-section>
                    <div class="text-subtitle2 text-weight-medium">Víctima {{ idx + 1 }}</div>
                  </q-item-section>
                  <q-item-section side>
                    <q-btn-group flat>
                      <!--                      <q-btn dense flat color="primary" icon="content_copy"  @click="copyVictimaToDenunciante(idx)" title="Copiar datos de esta víctima al denunciante"/>-->
                      <q-btn dense flat color="negative" icon="delete" :disable="f.victimas.length<=1"
                             @click="removeVictimaAt(idx)"/>
                    </q-btn-group>
                  </q-item-section>
                </q-item>
              </q-card-section>
              <q-separator/>
              <q-card-section>
                <div class="row q-col-gutter-md">
                  <div class="col-12 col-md-6">
                    <q-input v-model="v.nombres_apellidos" dense outlined clearable label="Nombres y apellidos *"
                             :disable="disabled"
                             v-upper/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-select v-model="v.tipo_documento" dense outlined emit-value map-options clearable
                              :options="documentos" label="Documento"
                              :disable="disabled"
                              @update:model-value="val => { if (val !== 'Otro') v.tipo_documento_otro = '' }"/>
                  </div>
                  <div class="col-6 col-md-3" v-if="v.tipo_documento==='Otro'">
                    <q-input v-model="v.tipo_documento_otro" dense outlined clearable label="Especifique otro documento"
                             :disable="disabled"
                             v-upper/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input v-model="v.ci" dense outlined clearable label="Numero de documento" v-upper :disable="disabled">
<!--                      templte a la derecha btn historial -->
<!--                      <template v-slot:after>-->
<!--                        <q-btn-->
<!--                          dense-->
<!--                          color="green"-->
<!--                          icon="history"-->
<!--                          @click="openHistorialDocumento(v.ci)"-->
<!--                          :disabled="!v.ci"-->
<!--                          title="Ver historial de este documento"-->
<!--                        />-->
<!--                      </template>-->
                    </q-input>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input v-model="v.lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento" v-upper :disable="disabled"/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input v-model="v.fecha_nacimiento" type="date" dense outlined label="Fecha de nacimiento"
                             @update:model-value="(val) => onBirthChange(val, v,'victima')" :disable="disabled"/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input v-model.number="v.edad" dense outlined type="number" label="Edad" v-upper :disable="disabled"/>
                  </div>
                  <div class="col-6 col-md-3" v-if="tipo==='DNA'">
                    <q-toggle v-model.number="v.gestante" dense :true-value="1" :false-value="0" label="¿Gestante?" :disable="disabled"/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-select v-model="v.sexo" dense outlined emit-value map-options clearable
                              :options="sexos" label="Sexo" :disable="disabled"/>
                  </div>
                  <template v-if="tipo==='DNA' || tipo==='PROPREMI'">

                  </template>
                  <template v-else>
                    <div class="col-6 col-md-3">
                      <q-select v-model="v.estado_civil" dense outlined emit-value map-options clearable
                                :options="estadosCiviles" label="Estado civil" :disable="disabled"/>
                    </div>
                  </template>
                  <template v-if="tipo==='DNA' || tipo==='PROPREMI'">

                  </template>
                  <template v-else>
                    <div class="col-12 col-md-4">
                      <q-input v-model="v.ocupacion" dense outlined clearable label="Ocupación" v-upper :disable="disabled"/>
                    </div>
                  </template>
                  <div class="col-12 col-md-4" v-if="tipo==='SLAM' || tipo==='UMADIS'">
                    <q-input v-model="v.ingreso_economico" dense outlined clearable label="Ingresos Económicos" v-upper :disable="disabled"/>
                  </div>
                  <template v-if="tipo==='DNA' || tipo==='PROPREMI'">

                  </template>
                  <template v-else>
                    <div class="col-12 col-md-4">
                      <q-select v-model="v.idioma" dense outlined emit-value map-options clearable
                                :options="idiomas" label="Idioma" :disable="disabled"/>
                    </div>
                  </template>
                  <div class="col-12 col-md-8">
                    <q-input v-model="v.domicilio" dense outlined clearable label="Domicilio" v-upper :disable="disabled"/>
                  </div>
                  <div class="col-12 col-md-4">
                    <q-input v-model="v.telefono" dense outlined clearable label="Teléfono/Celular" v-upper :disable="disabled"/>
                  </div>
                  <div class="col-6 col-md-2" v-if="tipo==='DNA'">
                    <q-toggle v-model.number="v.estudia" dense :true-value="1" :false-value="0" label="¿Estudia?" :disable="disabled"/>
                  </div>
                  <div class="col-12 col-md-4" v-if="v.estudia ===1 && tipo==='DNA'">
                    <q-select v-model="v.lugar_estudio" dense outlined emit-value map-options clearable
                              :options="colegios" label="UE / Colegio" :disable="disabled"/>
                  </div>
                  <div class="col-12 col-md-4" v-if="tipo==='PROPREMI'">
                    <q-select v-model="v.lugar_estudio" dense outlined emit-value map-options clearable
                              :options="colegios" label="UE / Colegio" :disable="disabled"/>
                  </div>
                  <div class="col-12 col-md-4" v-if="tipo==='JUVENTUDES'">
                    <q-input v-model="v.lugar_estudio" dense outlined emit-value map-options clearable
                              :options="colegios" label="U.E./Colegio/Universidad" :disable="disabled" />
                  </div>
                  <div class="col-12 col-md-4" v-if="tipo==='JUVENTUDES'">
                    <q-input v-model="v.grado_curso" dense outlined emit-value map-options clearable
                             label="Curso/Grado Actual" :disable="disabled"/>
                  </div>
                  <div class="col-12 col-md-4" v-if="tipo==='PROPREMI' || tipo==='DNA' ">
                    <q-select v-model="v.grado_curso" dense outlined emit-value map-options clearable
                              :options="cursos" label="Grado o curso" :disable="disabled"/>
                  </div>
                  <div class="col-12 col-md-4" v-if="tipo==='PROPREMI' || tipo==='DNA'  || tipo==='JUVENTUDES'">
                    <q-toggle v-model="v.trabaja" dense :true-value="1" :false-value="0" label="¿Trabaja actualmente?" :disable="disabled"/>
                    <!--                    <pre>{{v}}</pre>-->
                  </div>
                  <div class="col-12 col-md-6" v-if="tipo==='UMADIS'">
                    <q-select v-model="v.tipo_discapacidad" dense outlined emit-value map-options clearable
                              :options="tiposDiscapacidad" label="Tipo de discapacidad" :disable="disabled"/>
                  </div>
                  <div class="col-12 col-md-6" v-if="tipo==='UMADIS'">
<!--                    <q-select v-model="v.grado_discapacidad" dense outlined emit-value map-options clearable-->
<!--                              :options="gradosDiscapacidad" label="Grado de discapacidad"/>-->
                    <q-input v-model="v.grado_discapacidad" dense outlined clearable label="Grado de discapacidad" v-upper :disable="disabled"/>
                  </div>

                  <div class="col-12 col-md-6" v-if="v.trabaja===1">
                    <q-input v-model="v.lugar_trabajo" dense outlined clearable label="Lugar de trabajo" v-upper :disable="disabled"/>
                  </div>
                </div>
              </q-card-section>
            </q-card>
          </template>
          <div class="col-12" v-if="tipo==='PROPREMI'">
          </div>
          <div class="col-12" v-else>
            <q-toggle v-model="copiar" @update:model-value="copyVictimaToDenunciante" dense
                      :true-value="true" :false-value="false" title="Copiar datos de esta víctima al denunciante"
                      :disable="disabled"
            >
              <div class="text-subtitle2 text-bold" style="font-size: 17px">
                Victima es igual que denunciante?
              </div>
            </q-toggle>
          </div>
        </q-card-section>
      </q-card>
      <q-card flat bordered class="section-card q-mt-xs">
        <q-card-section class="row items-center q-pa-none">
          <q-item class="full-width" dense>
            <q-item-section avatar>
              <q-icon name="assignment_ind"/>
            </q-item-section>
            <q-item-section>
              <div class="text-subtitle1 text-weight-medium">2) DATOS DEL (A) DENUNCIANTE</div>
            </q-item-section>
            <q-item-section side>
              <q-badge color="blue-2" text-color="blue-10" outline rounded>Obligatorio *</q-badge>
            </q-item-section>
          </q-item>
        </q-card-section>
        <q-separator/>

        <q-card-section class="q-pa-xs">

          <div class="row">
            <q-card-section class="q-pa-xs">
              <div class="row items-center q-gutter-sm q-mb-sm">
                <q-btn dense no-caps color="primary" icon="add" label="Añadir denunciante" @click="addDenunciante"
                       size="10px"/>
                <div class="text-caption text-grey-7 q-ml-sm">Total: {{ f.denunciantes.length }}</div>
              </div>
              <template v-for="(d, id) in f.denunciantes" :key="id">
                <q-card flat bordered class="q-mb-md">
                  <q-card-section class="row items-center q-pa-xs">
                    <q-item class="full-width" dense>
                      <q-item-section avatar>
                        <q-icon name="person"/>
                      </q-item-section>
                      <q-item-section>
                        <div class="text-subtitle2 text-weight-medium">Denunciante {{ id + 1 }}</div>
                      </q-item-section>
                      <q-item-section side>
                        <q-btn dense flat color="negative" icon="delete" :disable="f.denunciantes.length<=1"
                               @click="removeDenuncianteAt(id)"/>
                      </q-item-section>
                    </q-item>
                  </q-card-section>
                  <q-separator/>
                  <q-card-section>
                    <div class="row q-col-gutter-md">
                      <div class="col-12 col-md-6">
                        <q-input v-model="d.denunciante_nombres" dense outlined clearable label="Nombres y apellidos *"
                                 v-upper :disable="disabled"
                        />
                      </div>
                      <div class="col-6 col-md-3">
                        <q-select v-model="d.denunciante_documento" dense outlined emit-value map-options clearable
                                  :options="documentos" label="Documento" :disable="disabled"
                                  @update:model-value="val => { if (val !== 'Otro') d.denunciante_documento_otro = '' }"/>
                      </div>
                      <div class="col-6 col-md-3" v-if="d.denunciante_documento==='Otro'">
                        <q-input v-model="d.denunciante_documento_otro" dense outlined clearable
                                 :disable="disabled"
                                 label="Especifique otro documento" v-upper/>
                      </div>
                      <div class="col-6 col-md-3">
                        <q-input v-model="d.denunciante_nro" dense outlined clearable label="Numero de documento"
                                 :disable="disabled"
                                 v-upper/>
                      </div>
                      <div class="col-6 col-md-3">
                        <q-input v-model="d.denunciante_lugar_nacimiento" dense outlined clearable
                                 :disable="disabled"
                                 label="Lugar de nacimiento" v-upper/>
                      </div>
                      <div class="col-6 col-md-3">
                        <q-input v-model="d.denunciante_fecha_nacimiento" type="date" dense outlined
                                 label="Fecha de nacimiento" :disable="disabled"
                                 @update:model-value="(val) => onBirthChange(val, d, 'denunciante')"/>
                      </div>
                      <div class="col-6 col-md-3">
                        <q-input v-model.number="d.denunciante_edad" dense outlined type="number" label="Edad"/>
                      </div>
                      <div class="col-6 col-md-3">
                        <q-select v-model="d.denunciante_sexo" dense outlined emit-value map-options clearable
                                  :options="sexos" label="Sexo"/>
                      </div>
                      <div class="col-6 col-md-3">
                        <q-select v-model="d.denunciante_estado_civil" dense outlined emit-value map-options clearable
                                  :options="estadosCiviles" label="Estado civil" :disable="disabled"/>
                      </div>
                      <div class="col-12 col-md-4">
                        <!--                      <q-input v-model="d.denunciante_grado" dense outlined clearable label="Grado de Instrucción"/>-->
                        <q-select v-model="d.denunciante_grado" dense outlined emit-value map-options clearable
                                  :options="gradosInstruccion" label="Grado de Instrucción" :disable="disabled"/>
                      </div>
                      <div class="col-12 col-md-4">
                        <q-input v-model="d.denunciante_ocupacion" dense outlined clearable label="Ocupación" v-upper :disable="disabled"/>
                      </div>
                      <template v-if="tipo==='DNA' || tipo==='JUVENTUDES' || tipo==='SLAM' || tipo==='SLIM' || tipo==='UMADIS'">
                        <div class="col-12 col-md-4">
                          <q-input v-model="d.denunciante_cargo" dense outlined clearable label="Lugar de trabajo"
                                   v-upper :disable="disabled"/>
                        </div>
                      </template>
                      <template v-else>
                        <div class="col-12 col-md-4">
                          <q-input v-model="d.denunciante_cargo" dense outlined clearable label="Institucion / Cargo"
                                   v-upper :disable="disabled"/>
                        </div>
                      </template>
                      <div class="col-12 col-md-4">
                        <q-input v-model.number="d.denunciante_ingresos" dense outlined type="text"
                                 label="Ingresos Económicos" v-upper :disable="disabled"/>
                      </div>
                      <div class="col-12 col-md-4">
                        <q-select v-model="d.denunciante_idioma" dense outlined emit-value map-options clearable
                                  :options="idiomas" label="Idioma" :disable="disabled"/>
                      </div>
                      <div class="col-12 col-md-8">
                        <q-input v-model="d.denunciante_domicilio_actual" dense outlined clearable label="Domicilio"
                                 v-upper :disable="disabled"/>
                      </div>
                      <div class="col-12 col-md-4">
                        <q-input v-model="d.denunciante_telefono" dense outlined clearable label="Teléfono/Celular"
                                 v-upper :disable="disabled"/>
                      </div>
                      <div class="col-12 col-md-4">
                        <q-input v-model="d.denunciante_relacion_victima" dense outlined clearable
                                 label="Parentesco o Relación con la Víctima" v-upper :disable="disabled"/>
                      </div>
                      <div class="col-12 col-md-4">
                        <q-input v-model="d.denunciante_relacion_denunciado" dense outlined clearable
                                 label="Parentesco o Relación con el Denunciado" v-upper :disable="disabled"/>
                      </div>
                    </div>
                  </q-card-section>
                </q-card>
              </template>
            </q-card-section>
          </div>
        </q-card-section>
      </q-card>

      <q-card flat bordered class="section-card q-mt-xs">
        <q-card-section class="row items-center q-pa-none">
          <q-item class="full-width" dense>
            <q-item-section avatar>
              <q-icon name="place"/>
            </q-item-section>
            <q-item-section>
              <div class="text-subtitle1 text-weight-medium">3) Ubicacion del denunciante</div>
            </q-item-section>
            <q-item-section side>
                <q-icon :name="mostrar3Show?'expand_more':'expand_less'" @click="mostrar3Show = !mostrar3Show" class="cursor-pointer"/>
<!--              mostrar3Show-->
            </q-item-section>
          </q-item>
        </q-card-section>
        <q-separator/>
        <q-card-section class="q-pa-xs" id="denunciante-map-section" v-if="mostrar3Show">
          <div class="row q-col-gutter-md">
            <div class="col-10">
              <q-input v-model="f.denunciante_domicilio_actual" dense outlined clearable label="Direccion"/>
            </div>
            <div class="col-2">
              <q-btn label="Buscar" @click="$refs.denunMap?.geocodeAndFly(f.denunciante_domicilio_actual)"/>
            </div>
            <div class="col-12">
              <div class="text-caption text-grey-7 q-mb-xs">Ubicación (denunciante)</div>
              <MapPicker v-model="denunciantePos" :center="oruroCenter"
                         :address="f.denunciante_domicilio_actual"
                          label="Ubicación Denunciante"
                         country="bo"
                         ref="denunMap"
              />
            </div>
          </div>
        </q-card-section>
      </q-card>


      <!-- Grupo Familiar (en acordeón para no saturar) -->
      <q-card flat bordered class="section-card q-mt-xs">
        <q-expansion-item icon="diversity_3" dense-toggle expand-separator dense
                          label="4) Grupo Familiar" header-class="text-subtitle1">
          <q-card>
            <q-card-section>
              <q-btn size="10px" label="Agregar familiar" color="primary" icon="add"
                     @click="f.familiares.push({ nombre_completo: '', edad: null, parentesco: '', celular: '' })"
                     class="q-mb-md" no-caps/>
              <template v-for="(fam, index) in f.familiares" :key="index">
                <div class="row q-col-gutter-md">
                  <div class="col-12 col-md-5">
                    <q-input v-model="fam.familiar_nombre_completo" dense outlined clearable
                             :label="`Familiar ${index + 1}: Nombres y apellidos`" v-upper/>
                  </div>
                  <div class="col-6 col-md-2">
                    <!--                      <q-input v-model.number="fam.edad" dense outlined type="number" label="Edad"/>-->
                    <q-input v-model.number="fam.familiar_edad" dense outlined type="number" label="Edad"/>
                  </div>
                  <div class="col-6 col-md-2">
                    <!--                      <q-input v-model="fam.parentesco" dense outlined clearable label="Parentesco"/>-->
                    <q-input v-model="fam.familiar_parentesco" dense outlined clearable label="Parentesco"/>
                  </div>
                  <!--                  <div class="col-12 col-md-2">-->
                  <!--                    &lt;!&ndash;                      <q-input v-model="fam.celular" dense outlined clearable label="Celular"/>&ndash;&gt;-->
                  <!--                    <q-input v-model="fam.familiar_celular" dense outlined clearable label="Celular"/>-->
                  <!--                  </div>-->
                  <!--                  familiar_estado_civil familiar_ocupacion familiar_telefono-->

                  <div class="col-12 col-md-2">
<!--                    <q-input v-model="fam.familiar_estado_civil" dense outlined clearable label="Estado Civil" v-upper/>-->
                    <q-select v-model="fam.familiar_estado_civil" dense outlined emit-value map-options clearable
                              :options="estadosCiviles" label="Estado civil"/>
                  </div>
                  <div class="col-12 col-md-2">
                    <q-input v-model="fam.familiar_ocupacion" dense outlined clearable label="Ocupación" v-upper/>
                  </div>
                  <div class="col-12 col-md-2">
                    <q-input v-model="fam.familiar_telefono" dense outlined clearable label="Teléfono/Celular" v-upper/>
                  </div>
                  <div class="col-12 col-md-1">
                    <q-btn color="negative" icon="delete" dense flat @click="f.familiares.splice(index, 1)"/>
                  </div>
                </div>
              </template>
            </q-card-section>
          </q-card>
        </q-expansion-item>
      </q-card>

      <!-- Denunciado -->
      <q-card flat bordered class="section-card q-mt-xs">
        <q-card-section class="row items-center">
          <q-icon name="person_pin_circle" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">5) Datos del Denunciado</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row items-center q-gutter-sm q-mb-sm">
            <q-btn dense no-caps color="primary" icon="add" label="Añadir denunciado" @click="addDenunciado"
                   size="10px"/>
            <div class="text-caption text-grey-7 q-ml-sm">Total: {{ f.denunciados.length }}</div>
          </div>
          <template v-for="(v, idx) in f.denunciados" :key="idx">
            <q-card flat bordered class="q-mb-md">
              <q-card-section class="row items-center q-pa-xs">
                <q-item class="full-width" dense>
                  <q-item-section avatar>
                    <q-icon name="person"/>
                  </q-item-section>
                  <q-item-section>
                    <div class="text-subtitle2 text-weight-medium">Denunciados {{ idx + 1 }}</div>
                  </q-item-section>
                  <q-item-section side>
                    <q-btn dense flat color="negative" icon="delete" :disable="f.denunciados.length<=1"
                           @click="removeDenunciadoAt(idx)"/>
                  </q-item-section>
                </q-item>
              </q-card-section>
              <q-separator/>
              <q-card-section>
                <div class="row q-col-gutter-md">
                  <div class="col-12 col-md-6">
                    <q-input v-model="v.denunciado_nombres" dense outlined clearable label="Nombres y apellidos *"
                             v-upper/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-select v-model="v.denunciado_documento" dense outlined emit-value map-options clearable
                              :options="documentos" label="Documento"
                              @update:model-value="val => { if (val !== 'Otro') v.tipo_documento_otro = '' }"/>
                  </div>
                  <div class="col-6 col-md-3" v-if="v.denunciado_documento==='Otro'">
                    <q-input v-model="v.tipo_documento_otro" dense outlined clearable label="Especifique otro documento"
                             v-upper/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input v-model="v.denunciado_nro" dense outlined clearable label="Numero de documento" v-upper/>
<!--                    <pre>{{v}}</pre>-->
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input v-model="v.denunciado_lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento" v-upper/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input v-model="v.denunciado_fecha_nacimiento" type="date" dense outlined label="Fecha de nacimiento"
                             @update:model-value="(val) => onBirthChange(val, v,'denunciado')"/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input v-model.number="v.denunciado_edad" dense outlined type="number" label="Edad" v-upper/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-select v-model="v.denunciado_sexo" dense outlined emit-value map-options clearable
                              :options="sexos" label="Sexo"/>
<!--                    <pre>{{v}}</pre>-->
                  </div>
                  <div class="col-6 col-md-3">
                    <q-select v-model="v.denunciado_estado_civil" dense outlined emit-value map-options clearable
                              :options="estadosCiviles" label="Estado civil"/>
                  </div>
                  <div class="col-12 col-md-4">
                    <q-select v-model="v.denunciado_grado" dense outlined emit-value map-options clearable
                              :options="gradosInstruccion" label="Grado de Instrucción"/>
                  </div>
                  <div class="col-12 col-md-4">
                    <q-input v-model="v.denunciado_ocupacion" dense outlined clearable label="Ocupación" v-upper/>
                  </div>
<!--                  <div class="col-12 col-md-4">-->
<!--                    <q-input v-model="v.denunciado_cargo" dense outlined clearable label="Institucion / Cargo" v-upper/>-->
<!--                  </div>-->
                  <template v-if="tipo==='PROPREMI'">
                    <div class="col-12 col-md-4">
                      <q-input v-model="v.denunciado_cargo" dense outlined clearable label="Institucion / Cargo"
                               v-upper/>
                    </div>
                  </template>
                  <template v-else>
                    <div class="col-12 col-md-4">
                      <q-input v-model="v.denunciado_trabaja" dense outlined clearable label="Lugar de trabajo"
                               v-upper/>
                    </div>
                  </template>
                  <div class="col-12 col-md-4">
                    <q-input v-model="v.denunciado_ingresos" dense outlined clearable label="Ingresos Económicos"
                             v-upper/>
                  </div>
                  <div class="col-12 col-md-4">
                    <q-select v-model="v.denunciado_idioma" dense outlined emit-value map-options clearable
                              :options="idiomas" label="Idioma"/>
                  </div>
                  <div class="col-12 col-md-8">
                    <q-input v-model="v.denunciado_domicilio_actual" dense outlined clearable label="Domicilio"
                             v-upper/>
                  </div>
                  <div class="col-12 col-md-4">
                    <q-input v-model="v.denunciado_telefono" dense outlined clearable label="Teléfono/Celular" v-upper/>
                  </div>
                  <div class="col-12 col-md-4">
                    <q-input v-model="v.denunciado_relacion_victima" dense outlined clearable
                             label="Parentesco o Relación con la Víctima" v-upper/>
                  </div>
                  <div class="col-12 col-md-4">
                    <q-input v-model="v.denunciado_relacion_denunciado" dense outlined clearable
                             label="Parentesco o Relación con el Denunciante" v-upper/>
                  </div>
                </div>
              </q-card-section>
            </q-card>
          </template>
        </q-card-section>
      </q-card>

      <q-card flat bordered class="section-card q-mt-xs">
        <q-card-section class="row items-center q-pa-none">
          <q-item class="full-width" dense>
            <q-item-section avatar>
              <q-icon name="place"/>
            </q-item-section>
            <q-item-section>
              <div class="text-subtitle1 text-weight-medium">6) Ubicacion del denunciado</div>
            </q-item-section>
            <q-item-section side>
<!--              <q-badge color="blue-2" text-color="blue-10" outline rounded>Obligatorio *</q-badge>-->
              <q-icon :name="mostrar6Show?'expand_more':'expand_less'" @click="mostrar6Show = !mostrar6Show" class="cursor-pointer"/>
            </q-item-section>
          </q-item>
        </q-card-section>
        <q-separator/>
        <q-card-section class="q-pa-xs" id="denunciado-map-section" v-if="mostrar6Show">
          <div class="row q-col-gutter-md">
            <div class="col-10">
              <q-input v-model="f.denunciado_domicilio_actual" dense outlined clearable label="Direccion"/>
            </div>
            <div class="col-2">
              <q-btn label="Buscar" @click="$refs.denunciadoMap?.geocodeAndFly(f.denunciado_domicilio_actual)"/>
            </div>
            <div class="col-12">
              <div class="text-caption text-grey-7 q-mb-xs">Ubicación (denunciado)</div>
              <MapPicker v-model="denunciadoPos" :center="oruroCenter"
                         :address="f.denunciado_domicilio_actual"
                         label="Ubicación Denunciado"
                         country="bo"
                         ref="denunciadoMap"
              />
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Hechos y Tipología -->
      <q-card flat bordered class="section-card q-mt-xs">
        <q-card-section class="row items-center">
          <q-icon name="gavel" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">7) Hechos y Tipología</div>
        </q-card-section>
        <q-separator/>

        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12">
              <q-input
                v-model="f.caso_descripcion"
                type="textarea"
                outlined
                dense
                clearable
                label="Descripción del hecho"
                counter
                maxlength="3000"
                :rows="5"
              >
                <template v-slot:append>
                  <q-btn
                    flat
                    round
                    dense
                    :icon="isListening && activeField==='caso_descripcion' ? 'mic_off' : 'mic'"
                    :color="isListening && activeField==='caso_descripcion' ? 'negative' : 'primary'"
                    @click="toggleRecognition('caso_descripcion')"
                  />
                </template>
              </q-input>
            </div>
            <div class="col-12 col-md-3">
              <q-input v-model="f.caso_fecha_hecho" type="date" dense outlined label="Fecha del hecho"/>
            </div>
            <!--            <div class="col-6 col-md-3">-->
            <!--              <q-input v-model="f.denunciante_relacion" dense outlined clearable label="Relación con el denunciante"/>-->
            <!--            </div>-->
            <div class="col-12 col-md-6">
              <q-input v-model="f.caso_lugar_hecho" dense outlined clearable label="Lugar del hecho"/>
            </div>
            <div class="col-12 col-md-4">
<!--              <pre>-->
<!--                {{tipo}}-->
<!--              </pre>-->
<!--              <pre>-->
<!--                {{titulo}}-->
<!--              </pre>-->
              <q-select v-model="f.caso_tipologia" dense outlined emit-value map-options clearable
                        :options="tipologias" label="Tipología"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Violencias -->
      <template v-if="tipo==='JUVENTUDES'">

      </template>
      <template v-else>
        <q-card flat bordered class="section-card q-mt-xs">
          <q-card-section class="row items-center">
            <q-icon name="warning_amber" class="q-mr-sm"/>
            <div class="text-subtitle1 text-weight-medium">8) Tipos de violencia</div>
          </q-card-section>
          <q-separator/>

          <q-card-section>
            <div class="row q-col-gutter-md">
              <div class="col-6 col-md-3">
                <q-toggle v-model="f.violencia_fisica" label="Física"/>
              </div>
              <div class="col-6 col-md-3">
                <q-toggle v-model="f.violencia_psicologica" label="Psicológica"/>
              </div>
              <div class="col-6 col-md-3">
                <q-toggle v-model="f.violencia_sexual" label="Sexual"/>
              </div>
              <div class="col-6 col-md-3">
                <q-toggle v-model="f.violencia_economica" label="Económica/Patrimonial"/>
              </div>
              <div class="col-6 col-md-3" v-if="tipo==='DNA' || tipo==='PROPREMI' || tipo==='SLIM' || tipo==='UMADIS'">
                <q-toggle v-model="f.violencia_cibernetica" label="Cibernética"/>
              </div>
              <div class="col-6 col-md-3" v-if="tipo==='DNA'">
                <q-toggle v-model="f.violencia_abandono" label="Abandono"/>
              </div>
              <div class="col-6 col-md-3" v-if="tipo==='DNA'">
                <q-toggle v-model="f.violencia_otros" label="Otros"/>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </template>

      <!-- Seguimiento -->
      <q-card flat bordered class="section-card q-mt-xs">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">9) Seguimiento</div>
        </q-card-section>
        <q-separator/>

        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <q-select v-model="f.legal_user_id" dense outlined emit-value map-options clearable
                        :options="abogados.map(u => ({ label: u.name, value: u.id }))"
                        label="Área legal (responsable)"/>
            </div>
            <!--            <div class="col-12 col-md-4"><q-input v-model="f.seguimiento_area" dense outlined clearable label="Área psicologica (responsable)"/></div>-->
            <!--            <div class="col-12 col-md-4"><q-input v-model="f.seguimiento_area_social" dense outlined clearable label="Área social (responsable)"/></div>-->
            <!--            <div class="col-12 col-md-4"><q-input v-model="f.seguimiento_area_legal" dense outlined clearable label="Área legal (responsable)"/></div>-->
            <div class="col-12 col-md-4">
              <q-select v-model="f.psicologica_user_id" dense outlined emit-value map-options clearable
                        :options="psicologos.map(u => ({ label: u.name, value: u.id }))"
                        label="Área psicologica (responsable)"/>
<!--              <pre>{{f.psicologica_user_id}}</pre>-->
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="f.trabajo_social_user_id" dense outlined emit-value map-options clearable
                        :options="sociales.map(u => ({ label: u.name, value: u.id }))"
                        label="Área social (responsable)"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Documentos -->
      <q-card flat bordered class="section-card q-mt-xs">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">10) Check documento adjuntos</div>
        </q-card-section>
        <q-separator/>

        <q-card-section>
          <div class="row q-col-gutter-md">
<!--            UMADIS-->
            <template v-if="tipo ==='UMADIS'">
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_persona_fisica" label="Presencia física de la persona a evaluar"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_carnet_discapacidad" label="Cédula de Identidad de la persona con discapacidad"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_carnet_padres" label="Cédula de Identidad del padre, madre y/o tutor"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_certificado_medico" label="Certificado médico actualizado" true-value="1"
                            false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_croquis_direccion_denunciante" label="Croquis del domicilio actualizado"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_papeleta_luz_agua" label="Papeleta de luz y agua" true-value="1"
                            false-value="0"/>
              </div>
            </template>
            <template v-else-if="tipo ==='SLAM'">
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_fotocopia_ci_denunciante" label="Fotocopia CI denunciante"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_fotocopia_carnet_denunciado" label="Fotocopia CI denunciado"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_placas_fotograficas_domicilio_denunciante"
                            label="Placas fotográficas domicilio denunciante" true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_placas_fotograficas_domicilio_denunciado"
                            label="Placas fotográficas domicilio denunciado" true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_croquis_direccion_denunciado" label="Croquis dirección denunciado"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_croquis_direccion_denunciante"
                            label="Croquis dirección denunciante" true-value="1" false-value="0"/>
              </div>
            </template>
            <template v-else-if="tipo ==='PROPREMI'">
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_fotocopia_ci_victima" label="Fotocopia CI víctima" true-value="1"
                            false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_fotocopia_ci_denunciante" label="Fotocopia CI denunciante"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_nota_director" label="Nota director" true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_nota_distrital" label="Nota distrital" true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_nota_defensor_pueblo" label="Nota defensor del pueblo" true-value="1"
                            false-value="0"/>
              </div>
            </template>
            <template v-else-if="tipo ==='JUVENTUDES'">
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_certificado_nacimiento" label="Certificado de nacimiento del joven"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_fotocopia_ci_victima" label="Fotocopia CI del joven" true-value="1"
                            false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_libreta_notas" label="Libreta/Certificado de notas del colegio" true-value="1"
                            false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_diploma_bachiller" label="Diploma/Titulo de bachiller" true-value="1"
                            false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_comprobante_universidades" label="Comprobante/Matricula Universidad"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_fotocopia_ci_padres" label="Fotocopia CI de los padres" true-value="1"
                            false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_tres_testigos" label="Tres testigos con fotocopia de CI" true-value="1"
                            false-value="0"/>
              </div>
            </template>
            <template v-else-if="tipo ==='DNA' && titulo==='DNA Nuevo Familiar'">
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_certificado_nacimiento" label="Certificado de nacimiento hijos original"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_certificado_matrimonio" label="Certificado de matrimonio Original"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_tres_testigos" label="Tres testigos con fotocopia de CI" true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_croquis_direccion_denunciado" label="Croquis dirección denunciado"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_contrato_pago" label="Papeleta / Contrato de pago del denunciado" true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_libreta_notas" label="Prueba o libreta de notas de los hijos" true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_fotocopia_carnet_denunciante" label="Fotocopia CI denunciante"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_fotocopia_carnet_denunciado" label="Fotocopia CI denunciado"
                            true-value="1" false-value="0"/>
              </div>
            </template>

            <template v-else>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_fotocopia_carnet_denunciante" label="Fotocopia CI denunciante"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_fotocopia_carnet_denunciado" label="Fotocopia CI denunciado"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_placas_fotograficas_domicilio_denunciante"
                            label="Placas fotográficas domicilio denunciante" true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_croquis_direccion_denunciado" label="Croquis dirección denunciado"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_placas_fotograficas_domicilio_denunciado"
                            label="Placas fotográficas domicilio denunciado" true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_croquis_direccion_denunciante" label="Croquis dirección denunciante"
                            true-value="1" false-value="0"/>
              </div>
              <div class="col-12 col-md-2">
                <q-checkbox v-model="f.documento_ciudadania_digital" label="Ciudadanía digital" true-value="1"
                            false-value="0"/>
              </div>
            </template>
          </div>
          <!--          <pre>{{f}}</pre>-->
        </q-card-section>
      </q-card>

      <!-- Acciones bottom -->
      <div class="text-right q-mt-xs" v-if="accion==='nuevo'">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm" class="q-mr-sm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
      </div>
      <div class="text-right q-mt-xs" v-else-if="accion==='modificar'">
        <q-btn color="primary" icon="save" label="Actualizar" :loading="loading" @click="update"/>
      </div>
    </q-form>
<!--    dialogHistorial: false,-->
<!--    historialDocumentos: [],-->
    <q-dialog v-model="dialogHistorial" persistent max-width="600px">
      <q-card>
        <q-card-section class="row items-center q-pa-xs">
          <q-item class="full-width" dense>
            <q-item-section avatar>
              <q-icon name="history"/>
            </q-item-section>
            <q-item-section>
              <div class="text-subtitle1 text-weight-medium">Historial </div>
            </q-item-section>
          </q-item>
        </q-card-section>
        <q-card-section>
<!--          <pre>{{historialDocumentos.casos}}</pre>-->
<!--          [-->
<!--          {-->
<!--          "id": 19,-->
<!--          "caso": "SLAM 001/26",-->
<!--          "tipo": "SLAM",-->
<!--          "caso_numero": "001/26",-->
<!--          "titulo": "Proceso Penal SLAM",-->
<!--          "fecha_apertura": null,-->
<!--          "creado": "2026-01-11 05:25",-->
<!--          "participa_como": [-->
<!--          "Víctima",-->
<!--          "Denunciante"-->
<!--          ]-->
<!--          },-->
<!--          {-->
<!--          "id": 4,-->
<!--          "caso": "SLIM 001/25",-->
<!--          "tipo": "SLIM",-->
<!--          "caso_numero": "001/25",-->
<!--          "titulo": null,-->
<!--          "fecha_apertura": null,-->
<!--          "creado": "2025-11-09 17:27",-->
<!--          "participa_como": [-->
<!--          "Víctima",-->
<!--          "Denunciante"-->
<!--          ]-->
<!--          }-->
<!--          ]-->
          <div v-if="historialDocumentos.casos === 0" class="text-grey-7">
            No se encontraron documentos adjuntos en otros casos relacionados con este denunciante.
          </div>
          <q-item v-for="doc in historialDocumentos.casos" :key="doc.id" class="q-mb-sm" clickable
                  @click="goToCaso(doc.id)">
            <q-item-section>
              <div class="text-subtitle2 text-weight-medium">{{ doc.caso }} - {{ doc.titulo || 'Sin título' }}</div>
              <div class="text-caption text-grey-7">
                Creado el: {{ doc.creado }} | Participa como: {{ doc.participa_como.join(', ') }}
              </div>
            </q-item-section>
            <q-item-section side>
              <q-icon name="arrow_forward_ios" class="text-grey-5"/>
            </q-item-section>
          </q-item>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cerrar" color="primary" v-close-popup/>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import MapPicker from 'components/MapPicker.vue'
import moment from 'moment'

const vUpper = {
  _findInput(root) {
    return root.querySelector(
      'input:not([type="number"]):not([type="date"]):not([type="time"]), textarea'
    );
  },

  _attachHandlers(el, input) {
    if (!input || input.__upperBound__) return;

    el.__isComposing__ = false;

    const toUpper = (v) => (v ?? '').toLocaleUpperCase('es');

    const onCompositionStart = () => {
      el.__isComposing__ = true;
    };
    const onCompositionEnd = () => {
      el.__isComposing__ = false;
      onInput();
    };

    const onInput = () => {
      if (el.__isComposing__) return;
      const prev = input.value ?? '';
      const s = input.selectionStart, e = input.selectionEnd;
      const next = toUpper(prev); // no tocamos espacios aquí
      if (next !== prev) {
        input.value = next;
        input.dispatchEvent(new Event('input', {bubbles: true}));
        try {
          input.setSelectionRange(s, e);
        } catch {
        }
      }
    };

    const onBlur = () => {
      const prev = input.value ?? '';
      const normalized = prev
        .replace(/\u00A0/g, ' ')
        .replace(/[ \t]+/g, ' ')
        .trim();
      const next = toUpper(normalized);
      if (next !== prev) {
        input.value = next;
        input.dispatchEvent(new Event('input', {bubbles: true}));
      }
    };

    input.autocapitalize = 'characters';
    input.addEventListener('compositionstart', onCompositionStart);
    input.addEventListener('compositionend', onCompositionEnd);
    input.addEventListener('input', onInput);
    input.addEventListener('blur', onBlur);

    // Primera pasada si ya hay valor
    onInput();

    input.__upperBound__ = true;
    el.__upperCleanup__ = () => {
      input.removeEventListener('compositionstart', onCompositionStart);
      input.removeEventListener('compositionend', onCompositionEnd);
      input.removeEventListener('input', onInput);
      input.removeEventListener('blur', onBlur);
      delete input.__upperBound__;
    };
  },

  _ensure(el) {
    const input = vUpper._findInput(el);
    if (input) {
      vUpper._attachHandlers(el, input);
      return true;
    }
    return false;
  },

  mounted(el) {
    if (vUpper._ensure(el)) {
      el.__upperAttached__ = true;
      return;
    }
    // Observa hasta que Quasar pinte el input interno
    const obs = new MutationObserver(() => {
      if (vUpper._ensure(el)) {
        obs.disconnect();
        delete el.__upperObserver__;
        el.__upperAttached__ = true;
      }
    });
    obs.observe(el, {childList: true, subtree: true});
    el.__upperObserver__ = obs;
  },

  updated(el) {
    // Por si el QInput se re-renderiza y perdemos el input
    if (!el.__upperAttached__) vUpper.mounted(el);
  },

  unmounted(el) {
    if (el.__upperObserver__) {
      el.__upperObserver__.disconnect();
      delete el.__upperObserver__;
    }
    if (el.__upperCleanup__) {
      el.__upperCleanup__();
      delete el.__upperCleanup__;
    }
    delete el.__upperAttached__;
    delete el.__isComposing__;
  }
};
export default {
  name: 'CasoNuevo',
  components: {MapPicker},
  directives: {upper: vUpper},
  props: {
    showNumeroApoyoIntegral: {type: Boolean, default: false},
    // tipologias: { type: Array, default: () => [] },
    titulo: {type: String, default: 'Registrar Nuevo Caso'},
    tipo: {type: String, default: 'SLIM'},
    casoId: {type: [Number, String], default: null},
    caso: {type: Object, default: null},
    accion: {type: String, default: 'nuevo'},
  },
  data() {
    return {
      disabled: false,
      dialogHistorial: false,
      historialDocumentos: [],
      mostrar3Show: false,
      mostrar6Show: false,
      colegios: [
        'JUAN LECHÍN OQUENDO 2',
        'SOCAMANI',
        'VICHULOMA',
        'BOLIVIA DE VINTO',
        'BOLIVIA DE VINTO SECUNDARIA',
        'DONATO VASQUEZ',
        'RAQUEL GASTELU DE RIOS',
        'MARIA QUIROZ PRIMARIA',
        'MARIA QUIROZ SECUNDARIA',
        'JOSE VICTOR  ZACONETA',
        'OSCAR ALFARO',
        ' IGNACIO LEÓN 2',
        'IGNACIO LEON 1',
        'ORURO SECUNDARIA TURNO TARDE',
        'COMIBOL ORURO 1',
        'JOSE MARIA SIERRA GALVARRO',
        'MARISCAL SUCRE',
        'MARIANO BAPTISTA',
        'JUANA AZURDUY DE PADILLA',
        'NUESTRA SEÑORA DEL SOCAVON 2',
        'NUESTRA SEÑORA DEL SOCAVÒN 1',
        '"ADOLFO MIER"',
        'GENOVEVA JIMENEZ',
        'JORGE OBLITAS',
        'SIMON BOLIVAR SECUNDARIA',
        'SIMON BOLIVAR NOCTURNO',
        'RODOLFO SORIA GALVARIO',
        '12 DE ABRIL',
        'ILDEFONSO MURGUIA',
        'ADOLFO BALLIVIAN 1',
        'EDUARDO ABAROA',
        'LOLA CARDONA TORRICO',
        'JUAN MISAEL SARACHO NOCTURNO',
        '10 DE FEBRERO',
        'MARIO FLORES',
        'VIRGEN DEL MAR 1',
        'VIRGEN DEL MAR 2',
        'VIRGEN DEL MAR3',
        'ESPAÑA PRIMARIA',
        'LUIS LLOSA',
        'PANTALEON DALENCE 1',
        'JESUS MARIA 1',
        'JESUS-MARIA 2',
        'MCAL. ANDRES DE SANTA CRUZ',
        'JOSE IGNACIO DE SANJINES 1',
        'JOSE IGNACIO DE SANJINES 2',
        'ANTONIO JOSE DE SAINZ SECUNDARIO',
        'ANTONIO JOSE DE SAINZ VESPERTINO',
        'ALTO ORURO',
        'JOHN FITZGERALD KENNEDY 2',
        'JOHN FITZGERALD KENNEDY 3',
        'OSCAR UNZAGA DE LA VEGA 2',
        'U.E. OSCAR ÚNZAGA DE LA VEGA 1',
        'EJERCITO NACIONAL PRIMARIA',
        'EJERCITO MACIONAL SECUNDARIO',
        'EJÉRCITO NACIONAL NOCTURNO',
        'JUAN BELEZ DE CORDOBA',
        'FRANZ TAMAYO',
        'JACINTO RODRÍGUEZ DE HERRERA',
        'GUIDO VILLAGÓMEZ',
        'FRANCISCO FAJARDO',
        'MARIA ROSA QUINTELA',
        'EMILIO VASQUEZ',
        'MELVIN JONES',
        'LA KANTUTA 1',
        'LA KANTUTA 2',
        'LA KANTUTA 3',
        'JAIME ESCALANTE',
        'FERROVIARIA PRIMARIA',
        'SEBASTIAN PAGADOR 1',
        'SEBASTIAN PAGADOR 2',
        'INGAVI',
        'CARMEN GUZMAN DE MIER 2',
        'CARMEN GUZMAN DE MIER 1',
        'SANTA ROSA',
        'CARMEN GUZMAN DE MIER INICIAL',
        'LOS ANGELES DE NAZARIA IGNACIA',
        'ROTARIA ORURO OTTAWA PRIMARIA',
        'SAN IGNACIO DE LOYOLA 1',
        'SAN IGNACIO DE LOYOLA',
        'SAN IGNACIO DE LOYOLA  2',
        'JESUS DE NAZARETH 1',
        'JESUS DE  NAZARETH  2',
        'LUIS MARIO CAREAGA',
        'LADISLAO CABRERA',
        'EDITH ZAMORA DE PAZ',
        'NACIONAL BOLIVIA',
        'CARLOS BELTRAN MORALES',
        'CARMELA CERRUTO 1',
        'CARMELA CERRUTO 2',
        'MARCOS BELTRÁN ÁVILA SECUNDARIA',
        'BENEMERITOS DE LA PATRIA',
        'REPUBLICA DEL CANADA',
        'HIJOS DEL SOL 1',
        'JUAN PABLO',
        'JUANCITO PINTO',
        'URU URU',
        'MEJILLONES 1',
        'MEJILLONES 2',
        'CHIRIPUJIO',
        'ORURO SECUNDARIA',
        'ANTOFAGASTA',
        'NACIONAL SAN JOSE',
        'LA SALLE TARDE',
        'ANICETO ARCE NOCTURNO',
        'CASIMIRO OLAÑETA',
        'ORURO VESPERTINO',
        'AYACUCHO',
        'DONATO VASQUEZ SECUNDARIO',
        'JOHN FITZGERALD KENNEDY 1 DON BOSCO',
        'ALEMAN',
        'AMERICANO',
        'ANGLO AMERICANO',
        'BETHANIA',
        ' CENTRO DE INFORMATICA SAN MIGUEL',
        'NACIONES UNIDAS',
        'LA SALLE',
        'REEKIE',
        'CATÓLICO SAN FRANCISCO',
        'SANTA MARÍA MAGDALENA POSTEL 2',
        'ADVENTISTA ELENA G. DE WHITE',
        'EVANGELICO WILLIAM BOOTH',
        'ARRIETA',
        'UNION BOLIVIA JAPON',
        'TOMAS BARRÓN',
        'COMIBOL ORURO 2',
        'MISAEL PACHECO LOMA',
        'ALCIRA CARDONA TORRICO',
        'JOSERMO MURILLO VACARREZA',
        'FERROVIARIA  INICIAL',
        'SANTA MARÍA MAGDALENA POSTEL',
        'NIÑO QUIRQUINCHO FELIZ',
        'HUAJARA',
        'HIJOS DEL SOL',
        'MARISCAL SUCRE PRIMARIA',
        'JUAN MISAEL SARACHO SECUNDARIA',
        'ESPAÑA INICIAL',
        'ANICETO ARCE',
        'ROTARIA ORURO OTTAWA SECUNDARIA',
        'JESUS DE NAZARETH',
        'MARIA DE NAZARETH',
        'LUIS LLOSA INICIAL',
        'INSCO SECUNDARIA',
        'MARCELO QUIROGA SANTA CRUZ',
        'JUAN PABLO SECUNDARIA',
        'GUIDO VILLAGOMEZ SECUNDARIO',
        'ALCIRA CARDONA TORRICO 2',
        'SAN FELIPE DE AUSTRIA',
        'EL CARMEN',
        'M.S.T. NACIONAL ANDINO',
        'JUANA AZURDUY DE PADILLA SECUNDARIA',
        'JORGE OBLITAS SECUNDARIA',
        'FRANCISCO FAJARDO 2',
        'DIOS ES AMOR',
        'AVELINO SIÑANI',
        'SAN JUAN DE DIOS',
        'SANTA ANA 1',
        'TOMAS BARRON 2',
        'FERROVIARIA SECUNDARIA',
        'SAN LUIS',
        'NACIONAL MIXTO HIUAJARA',
        'VILLA EL PROGRESO 2 DE FEBRERO',
        'URU URU 2',
        'LUIS MARIO CAREAGA 2',
        'SANTA ROSA 2',
        'JHON FITZGERALD KENNEDY 2 DON BOSCO',
        'AVELINO SIÑANI DE SOCAMANI',
        'PICHINCHA DON BOSCO',
        '6 DE AGOSTO',
        '3 DE MAYO QAQACHACA',
        'CRISTO EL SALVADOR',
        'FRED NUÑEZ GONZALES',
        '9 DE JUNIO',
        'JUANCITO PINTO DE SOCAMANI',
        'EL PARAISO',
        'ABYA YALA',
        'GERARDINA LAFUENTE CALLEJAS',
        'SAN MIGUEL ORURO',
        'ILDEFONSO MURGUIA 2',
        'VALLE HERMOSO VINTO II',
        'SANTA CLAUDINA THEVENET',
        'FRANCISCO FAJARDO SECUNDARIA',
        'JUAN LECHIN OQUENDO SECUNDARIA',
        'TECNICO HUMANISTICO MARIA DE NAZARETH SECUNDARIO',
        'SANTA MARIA MAGDALENA POSTEL SECUNDARIA',
        'MISAEL PACHECO LOMA SECUNDARIO',
        'AVELINO SIÑANI MAGISTERIO',
        'MCAL. ANDRES DE SANTA CRUZ Y CALAHUMANA',
        'TUPAC KATARI',
        'PANTALEÓN DALENCE JIMÉNEZ',
        'BOLIVIA DE VINTO INICIAL',
        'CORAZON DE JESUS',
        'CARLOS VILLEGAS QUIROGA',
        'ERNESTO GUEVARA DE LA SERNA',
        'MARIANO BAPTISTA SECUNDARIO',
        'QUIRQUINCHO SECUNDARIO',
        '6 DE AGOSTO',
        'NACIONAL JUNIN',
        'TECNICO HUMANISTICO SAN JUAN DE DIOS SECUNDARIA',
        'LUIS MENDIZABAL SANTA CRUZ',
        'TECNICO HUMANISTICO "COLORADOS DE BOLIVIA"',
        'MARY LINERA PAREJA',
        'HUGO CHAVEZ FRIAS',
        'FIDEL ALEJANDRO CASTRO RUZ',
        'JUAN EVO MORALES AYMA',
        '27 DE JULIO HUANUNI',
        'GRAL.JUAN JOSE TORRES GONZALES',
        'JULIAN APAZA NINA',
        'AGUA MILAGRO',
        'DIONISIO MORALES CHOQUE',
        'GENOVEVA RIOS',
        'RODOLFO  ILLANES',
        'ERNESTO GUEVARA DE LA SERNA SECUNDARIA',
        'AGUA VIVA 1',
        'JOSE MARIA SIERRA GALVARRO  NIVEL SECUNDARIO',
        'LOS ANGELES DE NAZARIA IGNACIA SECUNDARIO',
        '6 DE JUNIO MAGISTERIO',
        'DIOS ES AMOR SECUNDARIO',
        'AMERICA',
        'T. H. MINEROS HUANUNI SECUNDARIA',
      ],
      tiposDiscapacidad: [
        {label: 'Física', value: 'Física'},
        {label: 'Sensorial', value: 'Sensorial'},
        {label: 'Intelectual', value: 'Intelectual'},
        {label: 'Psicosocial', value: 'Psicosocial'},
        {label: 'Múltiple', value: 'Múltiple'},
        {label: 'Otra', value: 'Otra'},
      ],
      cursos: [
        '1ro. INICIAL',
        '2do. INICIAL',
        '1RO. PRIMARIA',
        '2DO. PRIMARIA',
        '3RO. PRIMARIA',
        '4TO. PRIMARIA',
        '5TO. PRIMARIA',
        '6TO. PRIMARIA',
        '1RO. SECUNDARIA',
        '2DO. SECUNDARIA',
        '3RO. SECUNDARIA',
        '4TO. SECUNDARIA',
        '5TO. SECUNDARIA',
        '6TO. SECUNDARIA'
      ],
      gradosInstruccion: [
        {label: 'Grado Primario', value: 'Grado Primario'},
        {label: 'Grado Secundario', value: 'Grado Secundario'},
        {label: 'Bachiller', value: 'Bachiller'},
        {label: 'Grado Universitario', value: 'Grado Universitario'},
        {label: 'Profesional', value: 'Profesional'},
        {label: 'Otro', value: 'Otro'}
      ],
      copiar: false,
      tipologias: [],
      recognition: null,
      activeField: null,
      isListening: false,
      psicologos: [],
      abogados: [],
      sociales: [],
      loading: false,
      documentos: [
        {label: 'Carnet de identidad', value: 'Carnet de identidad'},
        {label: 'Pasaporte', value: 'Pasaporte'},
        // { label: 'Libreta de servicio militar', value: 'Libreta de servicio militar' },
        {label: 'Licencia de conducir', value: 'Licencia de conducir'},
        {label: 'Carnet extranjero', value: 'Carnet extranjero'},
        {label: 'Otro', value: 'Otro'}
      ],
      sexos: [
        {label: 'Masculino', value: 'Masculino'},
        {label: 'Femenino', value: 'Femenino'},
        {label: 'Otro', value: 'Otro'}
      ],
      estadosCiviles: [
        {label: 'Soltero/a', value: 'Soltero/a'},
        {label: 'Casado/a', value: 'Casado/a'},
        {label: 'Divorciado/a', value: 'Divorciado/a'},
        {label: 'Viudo/a', value: 'Viudo/a'},
        {label: 'Concubinato', value: 'Concubinato'},
      ],
      idiomas: [
        {label: 'Castellano', value: 'Castellano'},
        {label: 'Quechua', value: 'Quechua'},
        {label: 'Aymara', value: 'Aymara'},
        {label: 'Guaraní', value: 'Guaraní'},
        { label: 'Castellano / Quechua', value: 'Castellano / Quechua'},
        { label: 'Castellano / Aymara', value: 'Castellano / Aymara'},
        { label: 'Castellano / Guaraní', value: 'Castellano / Guaraní'},
        { label: 'Castellano / Quechua / Aymara', value: 'Castellano / Quechua / Aymara'},
        {label: 'Otro', value: 'Otro'}
      ],
      oruroCenter: [-17.9667, -67.1167],
      f: {
        numero_apoyo_integral: '',
        area: 'SLIM',
        zona: 'CENTRAL',
        denunciante_domicilio_actual: '',
        latitud: null,
        longitud: null,
        familiares: [],
        victimas: [
          {
            nombres_apellidos: '',
            gestante: 0,
            estudia: 0,
            ci: '',
            fecha_nacimiento: '',
            lugar_nacimiento: '',
            edad: null,
            sexo: '',
            estado_civil: '',
            ocupacion: '',
            idioma: '',
            domicilio: '',
            telefono: '',
            tipo_documento: 'Carnet de identidad',
            tipo_documento_otro: '',
            trabaja: 0
          }
        ],
        denunciantes: [
          {
            denunciante_nombres: '',
            denunciante_paterno: '',
            denunciante_materno: '',
            denunciante_documento: 'Carnet de identidad',
            denunciante_nro: '',
            denunciante_sexo: '',
            denunciante_lugar_nacimiento: '',
            denunciante_fecha_nacimiento: '',
            denunciante_edad: '',
            denunciante_telefono: '',
            denunciante_residencia: '',
            denunciante_estado_civil: '',
            denunciante_relacion: '',
            denunciante_grado: '',
            denunciante_domicilio_actual: '',
            latitud: null,
            longitud: null,
            caso_id: null,
            denunciante_idioma: '',
            denunciante_trabaja: false,
            denunciante_ocupacion: '',
            denunciante_cargo: '',
            denunciante_ingresos: null,
            denunciante_parentesco: '',
          }
        ],
        // Denunciado
        // denunciado_nombre_completo: '',
        // denunciado_documento: '',
        // denunciado_nro: '',
        // denunciado_sexo: '',
        // denunciado_residencia: '',
        denunciado_domicilio_actual: '',
        // denunciado_latitud: null,
        // denunciado_longitud: null,
        // denunciado_lugar_nacimiento: '',
        // denunciado_fecha_nacimiento: '',
        // denunciado_edad: '',
        // denunciado_telefono: '',
        // denunciado_grado: '',
        denunciados: [{
          denunciado_nombres: '',
          denunciado_paterno: '',
          denunciado_materno: '',
          denunciado_documento: 'Carnet de identidad',
          denunciado_documento_otro: '',
          denunciado_nro: '',
          denunciado_sexo: '',
          denunciado_lugar_nacimiento: '',
          denunciado_fecha_nacimiento: '',
          denunciado_edad: '',
          denunciado_telefono: '',
          denunciado_residencia: '',
          denunciado_estado_civil: '',
          denunciado_relacion: '',
          denunciado_grado: '',
          denunciado_trabaja: '',
          denunciado_prox: '',
          denunciado_ocupacion: '',
          denunciado_cargo: '',
          denunciado_ocupacion_exacto: '',
          denunciado_idioma: '',
          denunciado_fijo: '',
          denunciado_movil: '',
          denunciado_domicilio_actual: '',
          denunciado_latitud: '',
          denunciado_longitud: '',
        }],
        // Caso
        caso_numero: '',
        caso_fecha_hecho: '',
        caso_lugar_hecho: '',
        caso_tipologia: '',
        caso_modalidad: '',
        caso_descripcion: '',
        // Violencias
        violencia_fisica: false,
        violencia_psicologica: false,
        violencia_sexual: false,
        violencia_economica: false,
        violencia_cibernetica: false,
        violencia_abandono: false,
        violencia_otros: false,
        // Seguimiento
        psicologica_user_id: '',
        trabajo_social_user_id: '',
        legal_user_id: '',
        // Documentos (almacenar nombres de archivos subidos)
        documento_fotocopia_carnet_denunciante: '0',
        documento_fotocopia_carnet_denunciado: '0',
        documento_placas_fotograficas_domicilio_denunciante: '0',
        documento_certificado_nacimiento: '0',
        documento_certificado_matrimonio: '0',
        documento_tres_testigos: '0',
        documento_croquis_direccion_denunciado: '0',
        documento_croquis_direccion_denunciante: '0',
        documento_contrato_pago: '0',
        documento_libreta_notas: '0',
        documento_diploma_bachiller: '0',
        documento_comprobante_universidades: '0',
        documento_fotocopia_ci_padres: '0',
        documento_placas_fotograficas_domicilio_denunciado: '0',
        documento_ciudadania_digital: '0',
        documento_fotocopia_ci_victima: '0',
        documento_fotocopia_ci_denunciante: '0',
        documento_nota_director: '0',
        documento_nota_distrital: '0',
        documento_nota_defensor_pueblo: '0',
        documento_persona_fisica: '0',
        documento_carnet_discapacidad: '0',
        documento_carnet_padres: '0',
        documento_certificado_medico: '0',
        documento_papeleta_luz_agua: '0',
      }
    }
  },
  mounted() {
    // para crear disabled: false, y para modificar disabled: true
    this.disabled = this.accion === 'modificar'
    // orderna colegios
    this.colegios.sort()
    // if caso
    if (this.caso) {
      this.f = {...this.f, ...this.caso}
      if (this.caso.victimas && this.caso.victimas.length > 0) this.f.victimas = this.caso.victimas
      if (this.caso.denunciantes && this.caso.denunciantes.length > 0) this.f.denunciantes = this.caso.denunciantes
      if (this.caso.denunciados && this.caso.denunciados.length > 0) this.f.denunciados = this.caso.denunciados
    }
    if (this.tipo === 'UMADIS' && this.titulo==='Nuevo Caso Penal UMADIS') {
      this.tipologias = [
        'Violacion física',
        'Violacion psicológica',
        'Violacion sexual',
        'Violacion económica/patrimonial',
        'Violacion familiar (doméstica)',
        'Tipología múltiple',
      ]
    }
    if (this.tipo === 'UMADIS' && this.titulo==='Nuevo Familiar UMADIS') {
      this.tipologias = [
        'Asistencia familiar',
        'Interdictos',
        'Extinción de autoridad',
        'Entrega provisional',
        'Perdida de personas con discapacidad',
      ]
    }
    if (this.tipo === 'UMADIS' && this.titulo==='Nuevo Caso Social UMADIS') {
      this.tipologias = [
        'Bullying',
        'Ciberbullying',
      ]
    }
    if( this.tipo === 'UMADIS' && this.titulo==='Nuevo Apoyo Integral UMADIS') {
      this.tipologias = [
        'Informes psicológicos',
        'Informes sociales',
        'Informes psicosociales',
        'Otros'
      ]
    }
    if (this.tipo === 'JUVENTUDES') {
      this.tipologias = [
        'Asistencia familiar',
      ]
    }
    if (this.tipo === 'SLAM' && this.titulo==='Proceso Penal SLAM') {
      this.tipologias = [
        'Violencia Física',
        'Violencia Psicológica',
        'Violencia Sexual',
        'Violencia Económica/Patrimonial',
        'Abandono',
        'Extravío',
        'Tipología Múltiple',
        'Otra'
      ]
    }
    if (this.tipo === 'SLAM' && this.titulo==='Proceso Penal SLAM') {
      this.tipologias = [
        'Violencia Física',
        'Violencia Psicológica',
        'Violencia Sexual',
        'Violencia Económica/Patrimonial',
        'Abandono',
        'Extravío',
        'Tipología Múltiple',
        'Otra'
      ]
    }
    if (this.tipo === 'SLAM' && this.titulo==='Proceso Familiar SLAM') {
      this.tipologias = [
        'Asistencia familiar',
        'Otros'
      ]
    }

    if (this.tipo === 'SLAM' && this.titulo==='Apoyo Integral SLAM') {
      this.tipologias = [
        'Informes psicológicos',
        'Informes sociales',
        'Informes psicosociales',
        'Otros'
      ]
    }

    if (this.tipo === 'SLIM' && this.titulo==='Nuevo Proceso Familiar') {
      this.tipologias = [
        'Asistencia familiar',
        'Otros'
      ]
    }
    if (this.tipo === 'SLIM' && (this.titulo==='Registrar Nuevo Caso Penal' || this.titulo==='Registrar Nuevo Caso Hechos de Fragancia')) {
      this.tipologias = [
        'Violencia Física',
        'Violencia Psicológica',
        'Violencia Sexual',
        'Violencia Económica/Patrimonial',
        'Abandono',
        'Extravío',
        'Tipología Múltiple',
        'Otra'
      ]
    }
    // "Apoyo Integral
    if (this.tipo === 'SLIM' && this.titulo==='Apoyo Integral') {
      this.tipologias = [
        'Informes psicológicos',
        'Informes sociales',
        'Informes psicosociales',
        'Otros'
      ]
    }
    if (this.tipo === 'PROPREMI') {
      this.tipologias = [
        'Violencia entre pares',
        'Violencia entre no pares',
        'Violencia verbal',
        'Violencia en discriminación en el sistema educativo',
        'Violencia en razón de género',
        'Violencia en situación de la situación económica',
        'Violencia cibernética en el sistema educativo',
        'Violencia multiple',
        'Otra',
      ]
    }
    console.log('Tipo:', this.tipo, 'Titulo:', this.titulo)
    if (this.tipo === 'DNA' && (this.titulo==='DNA Proceso Penal'  || this.titulo==='DNA Nuevo Hechos de Fragancia')) {
      this.tipologias = [
        'Violacion',
        'Estupro',
        'Abuso sexual',
        'Acoso sexual',
        'Infanticidio',
        'Rapto',
        'Corrupcion',
        'Proxenetismo',
        'Violencia sexual comercial',
        'Pornografia',
        'Trafico de personas',
        'Trata de personas',
        'Lesiones graves y leves',
        'Lesiones gravisimas',
        'Sustracción de menor incapaz',
        'Violencia familiar o domestica (fisica, psicologica)',
        'Abandono de niños/as',
        'Abandono por causa de honor',
        'Otros'
      ];
    }
    if (this.tipo === 'DNA' && this.titulo==='DNA Nuevo Familiar') {
      console.log('entra a tipologias DNA Nuevo Familiar')
      this.tipologias = [
        'Asistencia familiar',
        'Otros'
      ]
    }

    if (this.tipo === 'DNA' && this.titulo==='DNA Nuevo Niño, Niña o Adolescente') {
      console.log('entra a tipologias DNA Nuevo Niño, Niña o Adolescente')
      this.tipologias = [
        'Acogimiento circunstancial',
        'Infracción por violencia',
        'Irresponsabilidad paterna o materna',
        'Otros'
      ]
    }
    if (this.tipo === 'DNA' && this.titulo==='DNA Nuevo Apoyo Integral') {
      console.log('entra a tipologias DNA Nuevo Apoyo Integral')
      this.tipologias = [
        'Informes psicológicos',
        'Informes sociales',
        'Informes psicosociales',
        'Otros'
      ]
    }
    // if (Registrar Nuevo Caso Hechos de Fragancia SLIM ALL)
    // DNA Nuevo Hechos de Fragancia
    if (
      (this.tipo === 'SLIM' && this.titulo==='Registrar Nuevo Caso Hechos de Fragancia') ||
      (this.tipo === 'DNA' && this.titulo==='DNA Nuevo Hechos de Fragancia')
    ) {
      this.$axios.get('/usuariosRoleAll').then(res => {
        this.psicologos = res.data.psicologos
        this.abogados = res.data.abogados
        this.sociales = res.data.sociales
      }).catch(() => {
        this.$alert.error('No se pudo cargar los usuarios por rol')
      })
    }else{
      this.$axios.get('/usuariosRole').then(res => {
        this.psicologos = res.data.psicologos
        this.abogados = res.data.abogados
        this.sociales = res.data.sociales
      }).catch(() => {
        this.$alert.error('No se pudo cargar los usuarios por rol')
      })
    }

    // ==== Inicializar Web Speech API ====
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
      const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
      this.recognition = new SpeechRecognition()
      // Puedes usar 'es-ES' o 'es-BO'; es-BO no está en todos los navegadores. Dejo es-ES por compatibilidad.
      this.recognition.lang = 'es-ES'
      this.recognition.interimResults = false
      this.recognition.continuous = false

      this.recognition.onstart = () => {
        this.isListening = true
      }
      this.recognition.onend = () => {
        this.isListening = false
        this.activeField = null
      }
      this.recognition.onresult = (event) => {
        const text = event.results[0][0].transcript
        if (this.activeField === 'caso_descripcion') {
          // agrega con espacio si ya hay contenido
          this.f.caso_descripcion = (this.f.caso_descripcion ? (this.f.caso_descripcion + ' ') : '') + text
        }
      }
      this.recognition.onerror = (event) => {
        console.error('Error de reconocimiento de voz:', event.error)
        this.$q.notify({color: 'negative', message: 'Error de micrófono: ' + event.error})
        this.isListening = false
        this.activeField = null
      }
    } else {
      console.warn('Reconocimiento de voz no soportado en este navegador.')
    }
  },
  computed: {
    denunciantePos: {
      // get () { return { latitud: this.f.latitud, longitud: this.f.longitud } },
      // set (v) { this.f.latitud = v.latitud; this.f.longitud = v.longitud }
      get() {
        return {latitud: this.f.denunciantes[0].latitud, longitud: this.f.denunciantes[0].longitud}
      },
      set(v) {
        this.f.denunciantes[0].latitud = v.latitud;
        this.f.denunciantes[0].longitud = v.longitud
      }
    },
    denunciadoPos: {
      // get () { return { latitud: this.f.denunciado_latitud, longitud: this.f.denunciado_longitud } },
      // set (v) { this.f.denunciado_latitud = v.latitud; this.f.denunciado_longitud = v.longitud }
      get() {
        return {latitud: this.f.denunciados[0].latitud, longitud: this.f.denunciados[0].longitud}
      },
      set(v) {
        this.f.denunciados[0].latitud = v.latitud;
        this.f.denunciados[0].longitud = v.longitud
      }
    }
  },
  methods: {
    async openHistorialDocumento(ci) {
      try {
        if (!ci) return;

        this.dialogHistorial = true;
        this.historialDocumentos = [];
        this.$q.loading.show({ message: 'Buscando historial...' });

        const { data } = await this.$axios.get('/casosHistorialDocumentos', {
          params: { ci }
        });

        this.historialDocumentos = data || [];
      } catch (e) {
        console.error(e);
        this.$q.notify({ type: 'negative', message: 'No se pudo cargar el historial' });
        this.dialogHistorial = false;
      } finally {
        this.$q.loading.hide();
      }
    },
    addDenunciado() {
      this.f.denunciados.push({
        denunciado_nombres: '',
        denunciado_paterno: '',
        denunciado_materno: '',
        denunciado_documento: 'Carnet de identidad',
        denunciado_nro: '',
        denunciado_sexo: '',
        denunciado_lugar_nacimiento: '',
        denunciado_fecha_nacimiento: '',
        denunciado_edad: '',
        denunciado_telefono: '',
        denunciado_residencia: '',
        denunciado_estado_civil: '',
        denunciado_relacion: '',
        denunciado_grado: '',
        denunciado_domicilio_actual: '',
        latitud: null,
        longitud: null,
        caso_id: null,
        denunciante_idioma: '',
        denunciante_trabaja: false,
        denunciante_ocupacion: '',
        denunciante_ingresos: null,
        denunciante_parentesco: '',
      })
    },
    removeDenunciadoAt(idx) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Está seguro de eliminar este denunciado?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        if (this.f.denunciados.length > 1) this.f.denunciados.splice(idx, 1)
      })
    },
    removeDenuncianteAt(idx) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Está seguro de eliminar este denunciante?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        if (this.f.denunciantes.length > 1) this.f.denunciantes.splice(idx, 1)
      })
    },
    addVictima() {
      this.f.victimas.push({
        nombres_apellidos: '', ci: '', fecha_nacimiento: '',
        lugar_nacimiento: '', edad: null, sexo: '', estado_civil: '',
        ocupacion: '', idioma: '', domicilio: '', telefono: '',
        gestante: null, estudia: null, lugar_estudio: '', grado_curso: '',
        trabaja: null, lugar_trabajo: '', es_denunciante: false
      })
    },
    addDenunciante() {
      this.f.denunciantes.push({
        denunciante_nombres: '',
        denunciante_paterno: '',
        denunciante_materno: '',
        denunciante_documento: 'Carnet de identidad',
        denunciante_nro: '',
        denunciante_sexo: '',
        denunciante_lugar_nacimiento: '',
        denunciante_fecha_nacimiento: '',
        denunciante_edad: '',
        denunciante_telefono: '',
        denunciante_residencia: '',
        denunciante_estado_civil: '',
        denunciante_relacion: '',
        denunciante_grado: '',
        denunciante_domicilio_actual: '',
        latitud: null,
        longitud: null,
        caso_id: null,
        denunciante_idioma: '',
        denunciante_trabaja: false,
        denunciante_ocupacion: '',
        denunciante_ingresos: null,
        denunciante_parentesco: '',
      })
    },
    copyVictimaToDenunciante(val) {
      // console.log(val)
      this.f.denunciantes = []
      if (val) {
        this.$alert.info('Complete el grado de instrucción e ingresos económicos del/los denunciante(s) copiado(s) de la(s) víctima(s).')
        this.f.victimas.forEach((v, index) => {
          this.f.denunciantes.push({
            denunciante_nombres: v.nombres_apellidos || '',
            denunciante_paterno: '',
            denunciante_materno: '',
            denunciante_documento: v.tipo_documento || 'Carnet de identidad',
            denunciante_nro: v.ci || '',
            denunciante_sexo: v.sexo || '',
            denunciante_lugar_nacimiento: v.lugar_nacimiento || '',
            denunciante_fecha_nacimiento: v.fecha_nacimiento || '',
            denunciante_edad: v.edad || '',
            denunciante_telefono: v.telefono || '',
            denunciante_residencia: v.domicilio || '',
            denunciante_estado_civil: v.estado_civil || '',
            denunciante_relacion: '',
            denunciante_grado: '',
            denunciante_domicilio_actual: v.domicilio || '',
            latitud: null,
            longitud: null,
            caso_id: null,
            denunciante_idioma: v.idioma || '',
            denunciante_trabaja: false,
            denunciante_ocupacion: v.ocupacion || '',
            denunciante_ingresos: v.ingreso_economico || null,
            denunciante_parentesco: '',

          })
        })
        return false
      } else {
        this.f.denunciantes.push({
          denunciante_nombres: '',
          denunciante_paterno: '',
          denunciante_materno: '',
          denunciante_documento: 'Carnet de identidad',
          denunciante_nro: '',
          denunciante_sexo: '',
          denunciante_lugar_nacimiento: '',
          denunciante_fecha_nacimiento: '',
          denunciante_edad: '',
          denunciante_telefono: '',
          denunciante_residencia: '',
          denunciante_estado_civil: '',
          denunciante_relacion: '',
          denunciante_grado: '',
          denunciante_domicilio_actual: '',
          latitud: null,
          longitud: null,
          caso_id: null,
          denunciante_idioma: '',
          denunciante_trabaja: false,
          denunciante_ocupacion: '',
          denunciante_ingresos: null,
          denunciante_parentesco: '',
        })
      }
    },
    removeVictimaAt(idx) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Está seguro de eliminar esta víctima?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        if (this.f.victimas.length > 1) this.f.victimas.splice(idx, 1)
      })
    },
    onBirthChange(val, v, tipo) {
      if (!val) {
        v.edad = null;
        return
      }
      const hoy = moment()
      if (tipo === 'victima') {
        const nacimiento = moment(val)
        v.edad = hoy.diff(nacimiento, 'years')
      } else if (tipo === 'denunciante') {
        const nacimiento = moment(val)
        v.denunciante_edad = hoy.diff(nacimiento, 'years')
      }
      // else denunciados
      else if (tipo === 'denunciado') {
        const nacimiento = moment(val)
        v.denunciado_edad = hoy.diff(nacimiento, 'years')
      }
    },
    toggleRecognition(field) {
      if (!this.recognition) {
        this.$q.notify({
          color: 'negative',
          message: 'El navegador no soporta reconocimiento de voz.',
        })
        return
      }
      // Si ya está escuchando este mismo campo, detén
      if (this.isListening && this.activeField === field) {
        try {
          this.recognition.stop()
        } catch (e) {
        }
        return
      }
      // Si está escuchando otro, detén primero y luego inicia en este
      if (this.isListening && this.activeField !== field) {
        try {
          this.recognition.stop()
        } catch (e) {
        }
      }
      this.activeField = field
      try {
        this.recognition.start()
      } catch (e) {
        // algunos navegadores lanzan si se llama start() muy seguido
        console.warn('No se pudo iniciar el reconocimiento:', e)
      }
    },
    req(v) {
      return !!v || 'Requerido'
    },
    resetForm() {
      this.$q.notify({type: 'info', message: 'Formulario reiniciado'})
    },
    async save() {
      if (!this.f.denunciantes[0].denunciante_nombres) {
        this.$alert.error('El/Los nombre(s) y apellido paterno del denunciante son obligatorios')
        return
      }
      if (!this.f.denunciados[0].denunciado_nombres) {
        this.$alert.error('El/Los nombre(s) y apellido paterno del denunciado son obligatorios')
        return
      }
      this.loading = true
      try {
        this.f.tipo = this.tipo
        this.f.titulo = this.titulo
        const res = await this.$axios.post('/casos', this.f)
        this.$alert.success('Caso creado')
        // Si quieres redirigir al show, descomenta:
        this.$router.push(`/casos/${res.data.id}`)
        // this.resetForm()
      } catch (e) {
        this.$alert.error(e?.response?.data?.message || 'No se pudo crear el caso')
      } finally {
        this.loading = false
      }
    },
    async update() {
      if (!this.f.denunciantes[0].denunciante_nombres) {
        this.$alert.error('El/Los nombre(s) y apellido paterno del denunciante son obligatorios')
        return
      }
      if (!this.f.denunciados[0].denunciado_nombres) {
        this.$alert.error('El/Los nombre(s) y apellido paterno del denunciado son obligatorios')
        return
      }
      this.loading = true
      try {
        this.f.tipo = this.tipo
        const res = await this.$axios.put(`/casos/${this.casoId}`, this.f)
        this.$alert.success('Caso actualizado')
        this.$emit('refresh')
        this.disabled = true
      } catch (e) {
        this.$alert.error(e?.response?.data?.message || 'No se pudo actualizar el caso')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.bg-grey-2 {
  min-height: 100%;
}

.toolbar {
  position: sticky;
  top: 50px;
  z-index: 500;
  border-radius: 12px;
}
</style>

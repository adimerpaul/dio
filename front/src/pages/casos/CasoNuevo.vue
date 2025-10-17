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
        <q-btn color="primary" icon="save" label="Modificar Cambios" :loading="loading" @click="update" no-caps dense/>
      </div>
    </div>
    <q-form class="q-mt-xs" @submit.prevent="save">
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center q-pa-none">
          <q-item class="full-width" dense>
            <q-item-section avatar><q-icon name="assignment_ind" /></q-item-section>
            <q-item-section>
              <div class="text-subtitle1 text-weight-medium">1) DATOS DE LA VICTIMA</div>
            </q-item-section>
            <q-item-section side>
              <q-badge color="blue-2" text-color="blue-10" outline rounded>Obligatorio *</q-badge>
            </q-item-section>
          </q-item>
        </q-card-section>
        <q-separator/>

        <q-card-section class="q-pa-xs">
          <div class="row items-center q-gutter-sm q-mb-sm">
            <q-btn dense no-caps color="primary" icon="add" label="Añadir víctima" @click="addVictima" size="10px" />
<!--            <q-btn dense no-caps color="negative" icon="remove" label="Quitar última" :disable="f.victimas.length<=1" @click="removeVictima"/>-->
            <div class="text-caption text-grey-7 q-ml-sm">Total: {{ f.victimas.length }}</div>
          </div>
          <template v-for="(v, idx) in f.victimas" :key="idx">
            <q-card flat bordered class="q-mb-md">
              <q-card-section class="row items-center q-pa-xs">
                <q-item class="full-width" dense>
                  <q-item-section avatar><q-icon name="person" /></q-item-section>
                  <q-item-section>
                    <div class="text-subtitle2 text-weight-medium">Víctima {{ idx + 1 }}</div>
                  </q-item-section>
                  <q-item-section side>
                    <q-btn-group flat>
<!--                      <q-btn dense flat color="primary" icon="content_copy"  @click="copyVictimaToDenunciante(idx)" title="Copiar datos de esta víctima al denunciante"/>-->
                      <q-btn dense flat color="negative" icon="delete" :disable="f.victimas.length<=1" @click="removeVictimaAt(idx)" />
                    </q-btn-group>
                  </q-item-section>
                </q-item>
              </q-card-section>
              <q-separator/>
              <q-card-section>
                <div class="row q-col-gutter-md">
                  <div class="col-12 col-md-6">
                    <q-input v-model="v.nombres_apellidos" dense outlined clearable label="Nombres y apellidos *" />
                  </div>
                  <div class="col-6 col-md-3">
                    <q-select v-model="v.tipo_documento" dense outlined emit-value map-options clearable
                              :options="documentos" label="Documento" @update:model-value="val => { if (val !== 'Otro') v.tipo_documento_otro = '' }"/>
                  </div>
                  <div class="col-6 col-md-3" v-if="v.tipo_documento==='Otro'">
                    <q-input v-model="v.tipo_documento_otro" dense outlined clearable label="Especifique otro documento"/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input v-model="v.ci" dense outlined clearable label="Numero de documento"/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input v-model="v.lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento"/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input v-model="v.fecha_nacimiento" type="date" dense outlined label="Fecha de nacimiento"
                             @update:model-value="(val) => onBirthChange(val, v,'victima')"/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input v-model.number="v.edad" dense outlined type="number" label="Edad"/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-select v-model="v.sexo" dense outlined emit-value map-options clearable
                              :options="sexos" label="Sexo"/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-select v-model="v.estado_civil" dense outlined emit-value map-options clearable
                              :options="estadosCiviles" label="Estado civil"/>
                  </div>
                  <div class="col-12 col-md-4">
                    <q-input v-model="v.ocupacion" dense outlined clearable label="Ocupación"/>
                  </div>
                  <div class="col-12 col-md-4">
                    <q-select v-model="v.idioma" dense outlined emit-value map-options clearable
                              :options="idiomas" label="Idioma"/>
                  </div>
                  <div class="col-12 col-md-8">
                    <q-input v-model="v.domicilio" dense outlined clearable label="Domicilio"/>
                  </div>
                  <div class="col-12 col-md-4">
                    <q-input v-model="v.telefono" dense outlined clearable label="Teléfono/Celular"/>
                  </div>
                </div>
              </q-card-section>
            </q-card>
          </template>
          <div class="col-12">
            <q-toggle v-model="copiar" @update:model-value="copyVictimaToDenunciante" dense
                      :true-value="true" :false-value="false" title="Copiar datos de esta víctima al denunciante"
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
            <q-item-section avatar><q-icon name="assignment_ind" /></q-item-section>
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
              <q-btn dense no-caps color="primary" icon="add" label="Añadir denunciante" @click="addDenunciante" size="10px" />
              <div class="text-caption text-grey-7 q-ml-sm">Total: {{ f.denunciantes.length }}</div>
            </div>
            <template v-for="(d, id) in f.denunciantes" :key="id">
              <q-card flat bordered class="q-mb-md">
                <q-card-section class="row items-center q-pa-xs">
                  <q-item class="full-width" dense>
                    <q-item-section avatar><q-icon name="person" /></q-item-section>
                    <q-item-section>
                      <div class="text-subtitle2 text-weight-medium">Denunciante {{ id + 1 }}</div>
                    </q-item-section>
                    <q-item-section side>
                      <q-btn dense flat color="negative" icon="delete" :disable="f.denunciantes.length<=1" @click="removeDenuncianteAt(id)" />
                    </q-item-section>
                  </q-item>
                </q-card-section>
                <q-separator/>
                <q-card-section>
                  <div class="row q-col-gutter-md">
                    <div class="col-12 col-md-6">
                      <q-input v-model="d.denunciante_nombres" dense outlined clearable label="Nombres y apellidos *" />
                    </div>
                    <div class="col-6 col-md-3">
                      <q-select v-model="d.denunciante_documento" dense outlined emit-value map-options clearable
                                :options="documentos" label="Documento" @update:model-value="val => { if (val !== 'Otro') d.denunciante_documento_otro = '' }"/>
                    </div>
                    <div class="col-6 col-md-3" v-if="d.denunciante_documento==='Otro'">
                      <q-input v-model="d.denunciante_documento_otro" dense outlined clearable label="Especifique otro documento"/>
                    </div>
                    <div class="col-6 col-md-3">
                      <q-input v-model="d.denunciante_nro" dense outlined clearable label="Numero de documento"/>
                    </div>
                    <div class="col-6 col-md-3">
                      <q-input v-model="d.denunciante_lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento"/>
                    </div>
                    <div class="col-6 col-md-3">
                      <q-input v-model="d.denunciante_fecha_nacimiento" type="date" dense outlined label="Fecha de nacimiento"
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
                                :options="estadosCiviles" label="Estado civil"/>
                    </div>
                    <div class="col-12 col-md-4">
<!--                      <q-input v-model="d.denunciante_grado" dense outlined clearable label="Grado de Instrucción"/>-->
                      <q-select v-model="d.denunciante_grado" dense outlined emit-value map-options clearable
                                :options="gradosInstruccion" label="Grado de Instrucción"/>
                    </div>
                    <div class="col-12 col-md-4">
                      <q-input v-model="d.denunciante_ocupacion" dense outlined clearable label="Ocupación"/>
                    </div>
                    <div class="col-12 col-md-4">
                      <q-input v-model.number="d.denunciante_ingresos" dense outlined type="text" label="Ingresos Económicos"/>
                    </div>
                    <div class="col-12 col-md-4">
                      <q-select v-model="d.denunciante_idioma" dense outlined emit-value map-options clearable
                                :options="idiomas" label="Idioma"/>
                    </div>
                    <div class="col-12 col-md-8">
                      <q-input v-model="d.denunciante_domicilio_actual" dense outlined clearable label="Domicilio"/>
                    </div>
                    <div class="col-12 col-md-4">
                      <q-input v-model="d.denunciante_telefono" dense outlined clearable label="Teléfono/Celular"/>
                    </div>
                    <div class="col-12 col-md-4">
                      <q-input v-model="d.denunciante_relacion_victima" dense outlined clearable label="Parentesco o Relación con la Víctima"/>
                    </div>
                    <div class="col-12 col-md-4">
                      <q-input v-model="d.denunciante_relacion_denunciado" dense outlined clearable label="Parentesco o Relación con el Denunciado"/>
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
            <q-item-section avatar><q-icon name="place" /></q-item-section>
            <q-item-section>
              <div class="text-subtitle1 text-weight-medium">3) Ubicacion del denunciante</div>
            </q-item-section>
            <q-item-section side>
              <q-badge color="blue-2" text-color="blue-10" outline rounded>Obligatorio *</q-badge>
            </q-item-section>
          </q-item>
        </q-card-section>
        <q-separator/>
        <q-card-section class="q-pa-xs">
          <div class="row q-col-gutter-md">
            <div class="col-10">
              <q-input v-model="f.denunciante_domicilio_actual" dense outlined clearable label="Domicilio actual"/>
            </div>
            <div class="col-2">
              <q-btn label="Buscar" @click="$refs.denunMap?.geocodeAndFly(f.denunciante_domicilio_actual)" />
            </div>
            <div class="col-12">
              <div class="text-caption text-grey-7 q-mb-xs">Ubicación (denunciante)</div>
              <MapPicker v-model="denunciantePos" :center="oruroCenter"
                          :address="f.denunciante_domicilio_actual"
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
              <q-btn size="10px" label="Agregar familiar" color="primary" icon="add" @click="f.familiares.push({ nombre_completo: '', edad: null, parentesco: '', celular: '' })" class="q-mb-md" no-caps/>
              <template v-for="(fam, index) in f.familiares" :key="index">
                <div class="row q-col-gutter-md">
                  <div class="col-12 col-md-5" >
                    <q-input v-model="fam.familiar_nombre_completo" dense outlined clearable :label="`Familiar ${index + 1}: Nombres y apellidos`"/>
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
                    <q-input v-model="fam.familiar_estado_civil" dense outlined clearable label="Estado Civil"/>
                  </div>
                  <div class="col-12 col-md-2">
                    <q-input v-model="fam.familiar_ocupacion" dense outlined clearable label="Ocupación"/>
                  </div>
                  <div class="col-12 col-md-2">
                    <q-input v-model="fam.familiar_telefono" dense outlined clearable label="Teléfono/Celular"/>
                  </div>
                  <div class="col-12 col-md-1">
                    <q-btn color="negative" icon="delete" dense flat @click="f.familiares.splice(index, 1)" />
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
            <q-btn dense no-caps color="primary" icon="add" label="Añadir denunciado" @click="addDenunciado" size="10px" />
            <div class="text-caption text-grey-7 q-ml-sm">Total: {{ f.denunciados.length }}</div>
          </div>
          <template v-for="(v, idx) in f.denunciados" :key="idx">
            <q-card flat bordered class="q-mb-md">
              <q-card-section class="row items-center q-pa-xs">
                <q-item class="full-width" dense>
                  <q-item-section avatar><q-icon name="person" /></q-item-section>
                  <q-item-section>
                    <div class="text-subtitle2 text-weight-medium">Denunciados {{ idx + 1 }}</div>
                  </q-item-section>
                  <q-item-section side>
                    <q-btn dense flat color="negative" icon="delete" :disable="f.denunciados.length<=1" @click="removeDenunciadoAt(idx)" />
                  </q-item-section>
                </q-item>
              </q-card-section>
              <q-separator/>
              <q-card-section>
                <div class="row q-col-gutter-md">
                  <div class="col-12 col-md-6">
                    <q-input v-model="v.denunciado_nombres" dense outlined clearable label="Nombres y apellidos *" />
                  </div>
                  <div class="col-6 col-md-3">
                    <q-select v-model="v.denunciado_documento" dense outlined emit-value map-options clearable
                              :options="documentos" label="Documento" @update:model-value="val => { if (val !== 'Otro') v.tipo_documento_otro = '' }"/>
                  </div>
                  <div class="col-6 col-md-3" v-if="v.denunciado_documento==='Otro'">
                    <q-input v-model="v.tipo_documento_otro" dense outlined clearable label="Especifique otro documento"/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input v-model="v.ci" dense outlined clearable label="Numero de documento"/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input v-model="v.lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento"/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input v-model="v.fecha_nacimiento" type="date" dense outlined label="Fecha de nacimiento"
                             @update:model-value="(val) => onBirthChange(val, v,'victima')"/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-input v-model.number="v.edad" dense outlined type="number" label="Edad"/>
                  </div>
                  <div class="col-6 col-md-3">
                    <q-select v-model="v.sexo" dense outlined emit-value map-options clearable
                              :options="sexos" label="Sexo"/>
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
                    <q-input v-model="v.denunciado_ocupacion" dense outlined clearable label="Ocupación"/>
                  </div>
                  <div class="col-12 col-md-4">
                    <q-select v-model="v.denunciado_idioma" dense outlined emit-value map-options clearable
                              :options="idiomas" label="Idioma"/>
                  </div>
                  <div class="col-12 col-md-8">
                    <q-input v-model="v.denunciado_domicilio_actual" dense outlined clearable label="Domicilio"/>
                  </div>
                  <div class="col-12 col-md-4">
                    <q-input v-model="v.denunciado_telefono" dense outlined clearable label="Teléfono/Celular"/>
                  </div>
<!--                  'denunciado_ingresos',-->
<!--                  'denunciado_relacion_victima',-->
<!--                  'denunciado_relacion_denunciado',-->
                  <div class="col-12 col-md-4">
                    <q-input v-model="v.denunciado_ingresos" dense outlined clearable label="Ingresos Económicos"/>
                  </div>
                  <div class="col-12 col-md-4">
                    <q-input v-model="v.denunciado_relacion_victima" dense outlined clearable label="Parentesco o Relación con la Víctima"/>
                  </div>
                  <div class="col-12 col-md-4">
                    <q-input v-model="v.denunciado_relacion_denunciante" dense outlined clearable label="Parentesco o Relación con el Denunciante"/>
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
            <q-item-section avatar><q-icon name="place" /></q-item-section>
            <q-item-section>
              <div class="text-subtitle1 text-weight-medium">6) Ubicacion del denunciado</div>
            </q-item-section>
            <q-item-section side>
              <q-badge color="blue-2" text-color="blue-10" outline rounded>Obligatorio *</q-badge>
            </q-item-section>
          </q-item>
        </q-card-section>
        <q-separator/>
        <q-card-section class="q-pa-xs">
          <div class="row q-col-gutter-md">
            <div class="col-10">
              <q-input v-model="f.denunciado_domicilio_actual" dense outlined clearable label="Domicilio actual"/>
            </div>
            <div class="col-2">
              <q-btn label="Buscar" @click="$refs.denunciadoMap?.geocodeAndFly(f.denunciado_domicilio_actual)" />
            </div>
            <div class="col-12">
              <div class="text-caption text-grey-7 q-mb-xs">Ubicación (denunciante)</div>
              <MapPicker v-model="denunciadoPos" :center="oruroCenter"
                         :address="f.denunciado_domicilio_actual"
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
<!--            <div class="col-12 col-md-3">-->
<!--              <q-input v-model="f.caso_fecha_hecho" type="date" dense outlined label="Fecha del hecho"/>-->
<!--            </div>-->
<!--            <div class="col-6 col-md-3">-->
<!--              <q-input v-model="f.denunciante_relacion" dense outlined clearable label="Relación con el denunciante"/>-->
<!--            </div>-->
<!--            <div class="col-12 col-md-6">-->
<!--              <q-input v-model="f.caso_lugar_hecho" dense outlined clearable label="Lugar del hecho"/>-->
<!--            </div>-->
            <div class="col-12 col-md-4">
              <q-select v-model="f.caso_tipologia" dense outlined emit-value map-options clearable
                        :options="tipologias" label="Tipología"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Violencias -->
      <q-card flat bordered class="section-card q-mt-xs">
        <q-card-section class="row items-center">
          <q-icon name="warning_amber" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">8) Tipos de violencia</div>
        </q-card-section>
        <q-separator/>

        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-6 col-md-3"><q-toggle v-model="f.violencia_fisica" label="Física"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="f.violencia_psicologica" label="Psicológica"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="f.violencia_sexual" label="Sexual"/></div>
            <div class="col-6 col-md-3"><q-toggle v-model="f.violencia_economica" label="Económica/Patrimonial"/></div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Seguimiento -->
      <q-card flat bordered class="section-card q-mt-xs">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">9) Seguimiento</div>
        </q-card-section>
        <q-separator/>

        <q-card-section>
          <div class="row q-col-gutter-md">
<!--            <div class="col-12 col-md-4"><q-input v-model="f.seguimiento_area" dense outlined clearable label="Área psicologica (responsable)"/></div>-->
<!--            <div class="col-12 col-md-4"><q-input v-model="f.seguimiento_area_social" dense outlined clearable label="Área social (responsable)"/></div>-->
<!--            <div class="col-12 col-md-4"><q-input v-model="f.seguimiento_area_legal" dense outlined clearable label="Área legal (responsable)"/></div>-->
            <div class="col-12 col-md-4">
              <q-select v-model="f.psicologica_user_id" dense outlined emit-value map-options clearable
                        :options="psicologos.map(u => ({ label: u.username, value: u.id }))"
                        label="Área psicologica (responsable)"/>
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="f.trabajo_social_user_id" dense outlined emit-value map-options clearable
                        :options="sociales.map(u => ({ label: u.username, value: u.id }))"
                        label="Área social (responsable)"/>
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="f.legal_user_id" dense outlined emit-value map-options clearable
                        :options="abogados.map(u => ({ label: u.username, value: u.id }))"
                        label="Área legal (responsable)"/>
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
            <div class="col-12 col-md-2">
              <q-checkbox v-model="f.documento_fotocopia_carnet_denunciante" label="Fotocopia CI denunciante" true-value="1" false-value="0"/>
            </div>
            <div class="col-12 col-md-2">
              <q-checkbox v-model="f.documento_fotocopia_carnet_denunciado" label="Fotocopia CI denunciado" true-value="1" false-value="0"/>
            </div>
            <div class="col-12 col-md-2">
              <q-checkbox v-model="f.documento_placas_fotograficas_domicilio_denunciante" label="Placas fotográficas domicilio denunciante" true-value="1" false-value="0"/>
            </div>
            <div class="col-12 col-md-2">
              <q-checkbox v-model="f.documento_croquis_direccion_denunciado" label="Croquis dirección denunciado" true-value="1" false-value="0"/>
            </div>
            <div class="col-12 col-md-2">
              <q-checkbox v-model="f.documento_placas_fotograficas_domicilio_denunciado" label="Placas fotográficas domicilio denunciado" true-value="1" false-value="0"/>
            </div>
            <div class="col-12 col-md-2">
              <q-checkbox v-model="f.documento_ciudadania_digital" label="Ciudadanía digital" true-value="1" false-value="0"/>
            </div>
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
  </q-page>
</template>

<script>
import MapPicker from 'components/MapPicker.vue'
import moment from 'moment'

export default {
  name: 'CasoNuevo',
  components: { MapPicker },
  props: {
    showNumeroApoyoIntegral: { type: Boolean, default: false },
    // tipologias: { type: Array, default: () => [] },
    titulo: { type: String, default: 'Registrar Nuevo Caso' },
    tipo: { type: String, default: 'SLIM' },
    casoId: { type: [Number, String], default: null },
    caso: { type: Object, default: null },
    accion : { type: String, default: 'nuevo' },
  },
  data () {
    return {
      gradosInstruccion: [
        { label: 'Grado Primario', value: 'Grado Primario' },
        { label: 'Grado Secundario', value: 'Grado Secundario' },
        { label: 'Bachiller', value: 'Bachiller' },
        { label: 'Grado Universitario', value: 'Grado Universitario' },
        { label: 'Profesional', value: 'Profesional' },
        { label: 'Otro', value: 'Otro' }
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
        { label: 'Carnet de identidad', value: 'Carnet de identidad' },
        { label: 'Pasaporte', value: 'Pasaporte' },
        // { label: 'Libreta de servicio militar', value: 'Libreta de servicio militar' },
        { label: 'Licencia de conducir', value: 'Licencia de conducir' },
        { label: 'Carnet extranjero', value: 'Carnet extranjero' },
        { label: 'Otro', value: 'Otro' }
      ],
      sexos: [
        { label: 'Masculino', value: 'Masculino' },
        { label: 'Femenino', value: 'Femenino' },
        { label: 'Otro', value: 'Otro' }
      ],
      estadosCiviles: [
        { label: 'Soltero/a', value: 'Soltero/a' },
        { label: 'Casado/a', value: 'Casado/a' },
        { label: 'Divorciado/a', value: 'Divorciado/a' },
        { label: 'Viudo/a', value: 'Viudo/a' },
        { label: 'Concubinato', value: 'Concubinato' }
      ],
      idiomas: [
        { label: 'Catellano', value: 'Catellano' },
        { label: 'Quechua', value: 'Quechua' },
        { label: 'Aymara', value: 'Aymara' },
        { label: 'Guaraní', value: 'Guaraní' },
        { label: 'Otro', value: 'Otro' }
      ],
      oruroCenter: [-17.9667, -67.1167],
      f: {
        numero_apoyo_integral: '',
        area: 'SLIM',
        zona: 'CENTRAL',
        denunciante_domicilio_actual: '',
        latitud: null,
        longitud: null,
        familiares : [],
        victimas: [
          { nombres_apellidos: '', ci: '', fecha_nacimiento: '', lugar_nacimiento: '', edad: null, sexo: '', estado_civil: '', ocupacion: '', idioma: '', domicilio: '', telefono: '',tipo_documento:'Carnet de identidad',tipo_documento_otro:''}
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
        // Seguimiento
        psicologica_user_id: '',
        trabajo_social_user_id: '',
        legal_user_id: '',
        // Documentos (almacenar nombres de archivos subidos)
        documento_fotocopia_carnet_denunciante: '0',
        documento_fotocopia_carnet_denunciado: '0',
        documento_placas_fotograficas_domicilio_denunciante: '0',
        documento_croquis_direccion_denunciado: '0',
        documento_placas_fotograficas_domicilio_denunciado: '0',
        documento_ciudadania_digital: '0',
      }
    }
  },
  mounted() {
    // if caso
    if (this.caso) {
      this.f = { ...this.f, ...this.caso }
      if (this.caso.victimas && this.caso.victimas.length > 0) this.f.victimas = this.caso.victimas
      if (this.caso.denunciantes && this.caso.denunciantes.length > 0) this.f.denunciantes = this.caso.denunciantes
      if (this.caso.denunciados && this.caso.denunciados.length > 0) this.f.denunciados = this.caso.denunciados
    }
    if (this.tipo === 'SLIM') {
      this.tipologias = [
        'Violencia Física',
        'Violencia Feminicida',
        'Violencia Psicológica',
        'Violencia Mediática',
        'Violencia Simbólica y/o Encubierta',
        'Violencia Contra la Dignidad, la Honra y el Nombre',
        'Violencia Sexual',
        'Violencia Contra los Derechos Reproductivos',
        'Violencia en Servicios de Salud',
        'Violencia Patrimonial y Económica',
        'Violencia Laboral',
        'Violencia en el Sistema Educativo Plurinacional',
        'Violencia en el Ejercicio Político y de Liderazgo de la Mujer',
        'Violencia Institucional',
        'Violencia en la Familia',
        'Violencia Contra los Derechos y la Libertad Sexual',
        'Tipologias Multiples',
        'Otra'
      ]
    }

    this.$axios.get('/usuariosRole').then(res => {
      this.psicologos = res.data.psicologos
      this.abogados = res.data.abogados
      this.sociales = res.data.sociales
    }).catch(() => {
      this.$alert.error('No se pudo cargar los usuarios por rol')
    })
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
        this.$q.notify({ color: 'negative', message: 'Error de micrófono: ' + event.error })
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
      get () { return { latitud: this.f.denunciantes[0].latitud, longitud: this.f.denunciantes[0].longitud } },
      set (v) { this.f.denunciantes[0].latitud = v.latitud; this.f.denunciantes[0].longitud = v.longitud }
    },
    denunciadoPos: {
      // get () { return { latitud: this.f.denunciado_latitud, longitud: this.f.denunciado_longitud } },
      // set (v) { this.f.denunciado_latitud = v.latitud; this.f.denunciado_longitud = v.longitud }
      get () { return { latitud: this.f.denunciados[0].latitud, longitud: this.f.denunciados[0].longitud } },
      set (v) { this.f.denunciados[0].latitud = v.latitud; this.f.denunciados[0].longitud = v.longitud }
    }
  },
  methods: {
    addDenunciado(){
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
    addVictima () {
      this.f.victimas.push({
        nombres_apellidos: '', ci: '', fecha_nacimiento: '',
        lugar_nacimiento: '', edad: null, sexo: '', estado_civil: '',
        ocupacion: '', idioma: '', domicilio: '', telefono: '',
        gestante: null, estudia: null, lugar_estudio: '', grado_curso: '',
        trabaja: null, lugar_trabajo: '', es_denunciante: false
      })
    },
    addDenunciante(){
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
    copyVictimaToDenunciante(val){
      console.log(val)
      this.f.denunciantes = []
      if (val){
        console.log(this.f.victimas)
        this.f.victimas.forEach((v,index)=>{
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
            denunciante_ingresos: null,
            denunciante_parentesco: '',
          })
        })
        return false
      }else {
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
    removeVictimaAt (idx) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Está seguro de eliminar esta víctima?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        if (this.f.victimas.length > 1) this.f.victimas.splice(idx, 1)
      })
    },
    onBirthChange (val, v,tipo) {
      if (!val) { v.edad = null; return }
      const hoy = moment()
      if (tipo === 'victima') {
        const nacimiento = moment(val)
        v.edad = hoy.diff(nacimiento, 'years')
      } else if (tipo === 'denunciante') {
        const nacimiento = moment(val)
        v.denunciante_edad = hoy.diff(nacimiento, 'years')
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
        try { this.recognition.stop() } catch (e) {}
        return
      }
      // Si está escuchando otro, detén primero y luego inicia en este
      if (this.isListening && this.activeField !== field) {
        try { this.recognition.stop() } catch (e) {}
      }
      this.activeField = field
      try {
        this.recognition.start()
      } catch (e) {
        // algunos navegadores lanzan si se llama start() muy seguido
        console.warn('No se pudo iniciar el reconocimiento:', e)
      }
    },
    req (v) { return !!v || 'Requerido' },
    resetForm () {
      this.$q.notify({ type: 'info', message: 'Formulario reiniciado' })
    },
    async save () {
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
    async update(){
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
.bg-grey-2 { min-height: 100%; }
.toolbar {
  position: sticky;
  top: 50px;
  z-index: 500;
  border-radius: 12px;
}
</style>

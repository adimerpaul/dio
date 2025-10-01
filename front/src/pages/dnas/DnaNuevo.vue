<!-- src/pages/dna/CasoNuevoDNA.vue -->
<template>
  <q-page class="q-pa-md bg-grey-2">
    <!-- Barra superior -->
    <div class="toolbar q-pa-sm bg-white row items-center q-gutter-sm shadow-1" v-if="!editable">
      <div class="col">
        <div class="text-h6 text-weight-bold">Registro de Atención y/o Denuncia (DNA)</div>
        <div class="text-caption text-grey-7">Nuevo caso · Formato SID</div>
      </div>
      <div class="col-auto row q-gutter-sm">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
      </div>
    </div>
    <div  v-if="editable">
      <div class="col-auto text-right">
<!--        btoton refresh-->
        <q-btn flat color="primary" icon="refresh" label="Recargar" @click="getCaso" class="q-mr-sm"/>
        <q-btn color="primary" icon="save" label="Actualizar" :loading="loading" @click="update" v-if="$store.user?.role === 'Administrador' || $store.user?.role === 'Asistente'"/>
      </div>
    </div>
    <template v-if="$store.user?.role === 'Administrador' || $store.user?.role === 'Asistente'">
      <q-form class="q-mt-lg" @submit.prevent="save">
      <!-- 1) DATOS GENERALES -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="assignment" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">1) Datos generales</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3">
              <q-input v-model="f.caso_fecha_hecho" type="date" dense outlined label="Fecha de atención"/>
            </div>
<!--            <div class="col-12 col-md-5">-->
<!--              <q-input v-model="f.principal" dense outlined clearable label="PRINCIPAL (p.ej. ASISTENCIA FAMILIAR) *" :rules="[req]"/>-->
<!--            </div>-->
<!--            <div class="col-12 col-md-4">-->
<!--              <q-select v-model="f.caso_tipologia" :options="tipologias" dense outlined emit-value map-options clearable-->
<!--                        label="Tipología (catálogo DNA)"/>-->
<!--            </div>-->

<!--            <div class="col-12 col-md-8">-->
<!--              <q-input v-model="f.domicilio" dense outlined clearable label="Domicilio"/>-->
<!--            </div>-->
            <div class="col-6 col-md-2">
              <q-input v-model="f.zona" dense outlined clearable label="Zona"/>
            </div>
<!--            <div class="col-6 col-md-2">-->
<!--              <q-input v-model="f.telefono" dense outlined clearable label="Teléfono"/>-->
<!--            </div>-->

<!--            <div class="row q-mt-xs">-->
<!--            </div>-->

<!--            <div class="col-12">-->
<!--              <q-input-->
<!--                v-model="f.caso_descripcion"-->
<!--                type="textarea"-->
<!--                autogrow-->
<!--                outlined-->
<!--                dense-->
<!--                clearable-->
<!--                label="Descripción del hecho"-->
<!--                counter-->
<!--                maxlength="3000"-->
<!--              >-->
<!--                <template v-slot:append>-->
<!--                  <q-btn-->
<!--                    flat-->
<!--                    round-->
<!--                    dense-->
<!--                    :icon="isListening && activeField==='caso_descripcion' ? 'mic_off' : 'mic'"-->
<!--                    :color="isListening && activeField==='caso_descripcion' ? 'negative' : 'primary'"-->
<!--                    @click="toggleRecognition('caso_descripcion')"-->
<!--                  />-->
<!--                </template>-->
<!--              </q-input>-->
<!--            </div>-->
          </div>
        </q-card-section>
      </q-card>

      <!-- 2) DEL MENOR (tabla) -->
      <q-card flat bordered class="section-card">
        <q-expansion-item icon="child_care" dense-toggle expand-separator
                          label="2) Datos del/los menor(es)" header-class="text-subtitle1">
          <q-card>
            <q-card-section>
              <div class="q-mb-sm">
                <q-btn dense icon="add" color="primary" label="Agregar menor" @click="addMenor"/>
              </div>

              <q-markup-table dense flat bordered class="q-mb-sm">
                <thead>
                <tr class="bg-blue-1 text-blue-10">
                  <th>Nombre y apellidos</th>
                  <th style="width:90px">Gestante</th>
                  <th style="width:90px">Edad (años)</th>
                  <th style="width:90px">Edad (meses)</th>
                  <th style="width:110px">Sexo</th>
                  <th style="width:90px">C. Nac</th>
                  <th style="width:90px">Estudia</th>
                  <th>Último curso</th>
                  <th>Tipo de trabajo</th>
                  <th style="width:60px"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(m,i) in f.menores" :key="i">
                  <td><q-input v-model="m.nombres" dense outlined/></td>
                  <td class="text-center"><q-toggle v-model="m.gestante"/></td>
                  <td><q-input v-model.number="m.edad_anios" type="number" dense outlined/></td>
                  <td><q-input v-model.number="m.edad_meses" type="number" dense outlined/></td>
                  <td><q-select v-model="m.sexo" :options="sexos" emit-value map-options dense outlined/></td>
                  <td class="text-center"><q-toggle v-model="m.cert_nac"/></td>
                  <td class="text-center"><q-toggle v-model="m.estudia"/></td>
                  <td><q-input v-model="m.ultimo_curso" dense outlined/></td>
                  <td><q-input v-model="m.tipo_trabajo" dense outlined/></td>
                  <td class="text-center">
                    <q-btn flat dense round icon="delete" color="negative" @click="f.menores.splice(i,1)"/>
                  </td>
                </tr>
                </tbody>
              </q-markup-table>
              <div v-if="!f.menores.length" class="text-grey text-caption">Sin menores registrados</div>
            </q-card-section>
          </q-card>
        </q-expansion-item>
      </q-card>

      <!-- 3) GRUPO FAMILIAR -->
      <q-card flat bordered class="section-card">
        <q-expansion-item icon="diversity_3" dense-toggle expand-separator
                          label="3) Datos del grupo familiar" header-class="text-subtitle1">
          <q-card>
            <q-card-section>
              <div class="q-mb-sm">
                <q-btn dense icon="add" color="primary" label="Agregar integrante" @click="addFam"/>
              </div>

              <q-markup-table dense flat bordered>
                <thead>
                <tr class="bg-blue-1 text-blue-10">
                  <th>Nombre y apellidos</th>
                  <th>Parentesco</th>
                  <th style="width:100px">Edad</th>
<!--                  <th style="width:120px">Sexo</th>-->
                  <th>G. Instrucción</th>
                  <th>Ocupación</th>
                  <th style="width:60px"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(g,i) in f.familiares" :key="'g'+i">
                  <td><q-input v-model="g.familiar_nombres" dense outlined/></td>
                  <td><q-input v-model="g.familiar_parentesco" dense outlined/></td>
                  <td><q-input v-model.number="g.familiar_edad" type="number" dense outlined/></td>
<!--                  <td><q-select v-model="g.familiar_sexo" :options="sexos" emit-value map-options dense outlined/></td>-->
                  <td><q-input v-model="g.familiar_ocupacion" dense outlined/></td>
                  <td><q-input v-model="g.familiar_ocupacion_exacto" dense outlined/></td>
                  <td class="text-center">
                    <q-btn flat dense round icon="delete" color="negative" @click="f.familiares.splice(i,1)"/>
                  </td>
                </tr>
                </tbody>
              </q-markup-table>
              <div v-if="!f.familiares.length" class="text-grey text-caption">Sin integrantes registrados</div>
            </q-card-section>
          </q-card>
        </q-expansion-item>
      </q-card>

      <!-- 4) DENUNCIADO -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="person_off" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">4) Datos del denunciado</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-2">
              <q-input v-model="f.denunciados[0].denunciado_nombres" dense outlined clearable label="Nombres *" :rules="[req]"/>
            </div>
            <div class="col-12 col-md-2">
              <q-input v-model="f.denunciados[0].denunciado_paterno" dense outlined clearable label="Paterno "/>
            </div>
            <div class="col-12 col-md-2">
              <q-input v-model="f.denunciados[0].denunciado_materno" dense outlined clearable label="Materno "/>
            </div>

            <div class="col-6 col-md-2">
<!--              <q-select v-model="f.denunciado_sexo" :options="sexos" emit-value map-options dense outlined label="Sexo"/>-->
              <q-select v-model="f.denunciados[0].denunciado_sexo" :options="sexos" emit-value map-options dense outlined label="Sexo"/>
            </div>
            <div class="col-6 col-md-2">
<!--              <q-input v-model.number="f.denunciado_edad" type="number" dense outlined label="Edad"/>-->
              <q-input v-model.number="f.denunciados[0].denunciado_edad" type="number" dense outlined label="Edad"/>
            </div>
            <div class="col-6 col-md-2">
<!--              <q-input v-model="f.denunciado_relacion" dense outlined label="Parentesco / Tipo de relación"/>-->
              <q-input v-model="f.denunciados[0].denunciado_relacion" dense outlined label="Parentesco / Tipo de relación"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciado_ci" dense outlined clearable label="C.I."/>-->
              <q-input v-model="f.denunciados[0].denunciado_nro" dense outlined clearable label="C.I."/>
            </div>
            <div class="col-12 col-md-6">
<!--              <q-input v-model="f.denunciado_domicilio" dense outlined clearable label="Domicilio"/>-->
              <q-input v-model="f.denunciados[0].denunciado_residencia" dense outlined clearable label="Domicilio"/>
            </div>
            <div class="col-6 col-md-2">
<!--              busncar btn-->
              <q-btn outline label="Buscar" @click="$refs.denMap?.geocodeAndFly(f.denunciados[0].denunciado_residencia)" />
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="f.denunciantes[0].denunciante_telefono" dense outlined clearable label="Teléfono"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciado_lugar_trabajo" dense outlined clearable label="Lugar de trabajo"/>-->
              <q-input v-model="f.denunciados[0].denunciado_lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciado_ocupacion" dense outlined clearable label="Ocupación"/>-->
              <q-input v-model="f.denunciados[0].denunciado_ocupacion" dense outlined clearable label="Ocupación"/>
            </div>
            <div class="col-12">
              <div class="text-caption text-grey-7 q-mb-xs">Ubicación (para hoja de ruta)</div>
              <MapPicker
                v-model="denunciadoPos"
                :center="oruroCenter"
                :address="f.denunciados[0].denunciado_residencia"
                country="bo"
                ref="denMap"
              />
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 5) DENUNCIANTE -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="person" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">5) Datos del denunciante</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-2">
<!--              <q-input v-model="f.denunciante_nombre" dense outlined clearable label="Nombres y apellidos *" :rules="[req]"/>-->
              <q-input v-model="f.denunciantes[0].denunciante_nombres" dense outlined clearable label="Nombres *" :rules="[req]"/>
            </div>
            <div class="col-6 col-md-2">
<!--              <q-select v-model="f.denunciante_sexo" :options="sexos" emit-value map-options dense outlined label="Sexo"/>-->
              <q-select v-model="f.denunciantes[0].denunciante_sexo" :options="sexos" emit-value map-options dense outlined label="Sexo"/>
            </div>
            <div class="col-6 col-md-2">
<!--              <q-input v-model.number="f.denunciante_edad" type="number" dense outlined label="Edad"/>-->
              <q-input v-model.number="f.denunciantes[0].denunciante_edad" type="number" dense outlined label="Edad"/>
            </div>
            <div class="col-6 col-md-2">
<!--              <q-input v-model="f.denunciante_ci" dense outlined clearable label="C.I."/>-->
              <q-input v-model="f.denunciantes[0].denunciante_nro" dense outlined clearable label="C.I."/>
            </div>
            <div class="col-12 col-md-6">
<!--              <q-input v-model="f.denunciante_domicilio" dense outlined clearable label="Domicilio"/>-->
              <q-input v-model="f.denunciantes[0].denunciante_residencia" dense outlined clearable label="Domicilio"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciante_telefono" dense outlined clearable label="Teléfono"/>-->
              <q-input v-model="f.denunciantes[0].denunciante_telefono" dense outlined clearable label="Teléfono"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciante_lugar_trabajo" dense outlined clearable label="Lugar de trabajo"/>-->
              <q-input v-model="f.denunciantes[0].denunciante_lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento"/>
            </div>
            <div class="col-6 col-md-2">
<!--              <q-input v-model="f.denunciante_ocupacion" dense outlined clearable label="Ocupación"/>-->
              <q-input v-model="f.denunciantes[0].denunciante_relacion" dense outlined clearable label="Parentesco / Tipo de relación"/>
            </div>
            <div class="col-12 col-md-8">
              <q-input v-model="f.caso_direccion" dense outlined clearable label="Buscar dirección en el mapa"/>
            </div>
            <div class="col-12 col-md-2">
              <q-btn outline label="Buscar" @click="$refs.denMap?.geocodeAndFly(f.caso_direccion)" />
            </div>
            <div class="col-12">
              <div class="text-caption text-grey-7 q-mb-xs">Ubicación (para hoja de ruta)</div>
              <MapPicker
                v-model="denunciantePos"
                :center="oruroCenter"
                :address="f.domicilio"
                country="bo"
                ref="denMap"
              />
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 6) DESCRIPCIÓN -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="description" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">6) Descripción de la denuncia</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3">
              <q-input v-model="f.caso_fecha_hecho" type="date" dense outlined label="Fecha del hecho"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="f.denunciante_relacion" dense outlined clearable label="Relación con el denunciante"/>
            </div>
            <div class="col-12 col-md-6">
              <q-input v-model="f.caso_lugar_hecho" dense outlined clearable label="Lugar del hecho"/>
            </div>

<!--            <div class="col-12 col-md-4">-->
<!--              <q-input v-model="f.caso_tipologia" dense outlined clearable label="Tipología"/>-->
<!--            </div>-->
            <div class="col-12 col-md-4">
              <q-select v-model="f.caso_tipologia" :options="tipologias" dense outlined emit-value map-options clearable
                        label="Tipología (catálogo DNA)"/>
            </div>
            <div class="col-12">
              <q-input
                v-model="f.caso_descripcion"
                type="textarea"
                autogrow
                outlined
                dense
                clearable
                label="Descripción del hecho"
                counter
                maxlength="3000"
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
          </div>
        </q-card-section>
      </q-card>

      <!-- Seguimiento -->
      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">7) Seguimiento</div>
        </q-card-section>
        <q-separator/>

        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <q-select v-model="f.psicologica_user_id" dense outlined emit-value map-options clearable
                        :options="psicologos.map(u => ({ label: u.name, value: u.id }))"
                        label="Área psicológica (responsable)"/>
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="f.trabajo_social_user_id" dense outlined emit-value map-options clearable
                        :options="sociales.map(u => ({ label: u.name, value: u.id }))"
                        label="Área social (responsable)"/>
            </div>
            <div class="col-12 col-md-4">
              <q-select v-model="f.legal_user_id" dense outlined emit-value map-options clearable
                        :options="abogados.map(u => ({ label: u.name, value: u.id }))"
                        label="Área legal (responsable)"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Check documentos -->
      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">8) Check documentos adjuntos</div>
        </q-card-section>
        <q-separator/>

        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-2">
              <q-checkbox v-model="f.documento_fotocopia_carnet_denunciante" label="Fotocopia CI denunciante" true-value="1" false-value="0" />
            </div>
            <div class="col-12 col-md-2">
              <q-checkbox v-model="f.documento_fotocopia_carnet_denunciado" label="Fotocopia CI denunciado" true-value="1" false-value="0" />
            </div>
            <div class="col-12 col-md-2">
              <q-checkbox v-model="f.documento_placas_fotograficas_domicilio_denunciante" label="Placas fotográficas dom. denunciante" true-value="1" false-value="0" />
            </div>
            <div class="col-12 col-md-2">
              <q-checkbox v-model="f.documento_croquis_direccion_denunciado" label="Croquis dirección denunciado" true-value="1" false-value="0" />
            </div>
            <div class="col-12 col-md-2">
              <q-checkbox v-model="f.documento_placas_fotograficas_domicilio_denunciado" label="Placas fotográficas dom. denunciado" true-value="1" false-value="0" />
            </div>
            <div class="col-12 col-md-2">
              <q-checkbox v-model="f.documento_ciudadania_digital" label="Ciudadanía digital" true-value="1" false-value="0" />
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Acciones -->
      <div class="text-right q-mb-lg">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm" class="q-mr-sm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save" v-if="!editable"/>
        <q-btn color="primary" icon="save" label="Actualizar" :loading="loading" @click="update" v-if="editable"/>
      </div>
    </q-form>
    </template>
    <div v-else class="q-pa-md">
      <!-- CABECERA -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center q-col-gutter-sm">
          <div class="col-12 col-md-8">
            <div class="text-h6 text-weight-bold">
              Detalle DNA · Caso <span v-if="f.caso_numero">#{{ f.caso_numero }}</span><span v-else>#{{ f.id }}</span>
            </div>
            <div class="text-caption text-grey-7">
              Apertura:
              <q-chip dense color="indigo-1" text-color="indigo-9">
                {{ fmtDate(f.fecha_apertura_caso) || '—' }}
              </q-chip>
              · Registrado por:
              <q-chip dense color="grey-2" text-color="grey-9">
                {{ f.user?.name || '—' }}
              </q-chip>
            </div>
          </div>
          <div class="col-12 col-md-4 flex items-center justify-end">
            <q-chip dense outline color="primary" text-color="primary">Tipo: {{ show(f.tipo || 'DNA') }}</q-chip>
            <q-chip dense outline color="teal" text-color="teal">Área: {{ show(f.area) }}</q-chip>
            <q-chip dense outline color="orange" text-color="orange">Zona: {{ show(f.zona) }}</q-chip>
          </div>
        </q-card-section>
      </q-card>

      <!-- DATOS GENERALES -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="assignment" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">1) Datos generales</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-6 col-md-3">
              <div class="text-caption text-grey-7">Fecha de atención</div>
              <div class="text-body1">{{ fmtDate(f.caso_fecha_hecho) || '—' }}</div>
            </div>
            <div class="col-6 col-md-3">
              <div class="text-caption text-grey-7">N° Apoyo Integral</div>
              <div class="text-body1">{{ show(f.numero_apoyo_integral) }}</div>
            </div>
            <div class="col-12 col-md-6">
              <div class="text-caption text-grey-7">Principal</div>
              <div class="text-body1">{{ show(f.principal) }}</div>
            </div>

            <div class="col-12 col-md-4">
              <div class="text-caption text-grey-7">Tipología</div>
              <div class="text-body1">{{ show(f.caso_tipologia) }}</div>
            </div>
            <div class="col-12 col-md-4">
              <div class="text-caption text-grey-7">N° Caso</div>
              <div class="text-body1">{{ show(f.caso_numero) }}</div>
            </div>
            <div class="col-12 col-md-4">
              <div class="text-caption text-grey-7">Dirección / Zona</div>
              <div class="text-body1">
                {{ show(f.caso_direccion) }}
                <span v-if="f.caso_direccion && f.zona">·</span>
                {{ show(f.zona) }}
              </div>
            </div>

            <div class="col-12">
              <div class="text-caption text-grey-7 q-mb-xs">Descripción</div>
              <div class="q-pa-sm bg-grey-1" style="white-space: pre-wrap; border-radius: 10px;">
                {{ show(f.descripcion || f.caso_descripcion) }}
              </div>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- RESPONSABLES -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="people_alt" class="q-mr-sm" />
          <div class="text-subtitle1 text-weight-medium">2) Responsables</div>
        </q-card-section>
        <q-separator />
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <div class="text-caption text-grey-7">Área Psicológica</div>
              <div class="text-body1">
                {{ f.psicologica_user?.name || '—' }}
                <q-chip v-if="f.psicologica_user?.celular" dense color="blue-1" text-color="blue-9" class="q-ml-xs">
                  {{ f.psicologica_user?.celular }}
                </q-chip>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="text-caption text-grey-7">Trabajo Social</div>
              <div class="text-body1">
                {{ f.trabajo_social_user?.name || '—' }}
                <q-chip v-if="f.trabajo_social_user?.celular" dense color="blue-1" text-color="blue-9" class="q-ml-xs">
                  {{ f.trabajo_social_user?.celular }}
                </q-chip>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="text-caption text-grey-7">Área Legal</div>
              <div class="text-body1">
                {{ f.legal_user?.name || '—' }}
                <q-chip v-if="f.legal_user?.celular" dense color="blue-1" text-color="blue-9" class="q-ml-xs">
                  {{ f.legal_user?.celular }}
                </q-chip>
              </div>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- FECHAS CLAVE -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="event" class="q-mr-sm" />
          <div class="text-subtitle1 text-weight-medium">3) Fechas clave</div>
        </q-card-section>
        <q-separator />
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3">
              <div class="text-caption text-grey-7">Apertura</div>
              <div class="text-body1">{{ fmtDate(f.fecha_apertura_caso) || '—' }}</div>
            </div>
            <div class="col-12 col-md-3">
              <div class="text-caption text-grey-7">Derivación Psicológica</div>
              <div class="text-body1">{{ fmtDate(f.fecha_derivacion_psicologica) || '—' }}</div>
            </div>
            <div class="col-12 col-md-3">
              <div class="text-caption text-grey-7">Informe Psicología</div>
              <div class="text-body1">{{ fmtDate(f.fecha_informe_area_psicologica) || '—' }}</div>
            </div>
            <div class="col-12 col-md-3">
              <div class="text-caption text-grey-7">Informe Social</div>
              <div class="text-body1">{{ fmtDate(f.fecha_informe_area_social) || '—' }}</div>
            </div>
            <div class="col-12 col-md-3">
              <div class="text-caption text-grey-7">Informe Trabajo Social</div>
              <div class="text-body1">{{ fmtDate(f.fecha_informe_trabajo_social) || '—' }}</div>
            </div>
            <div class="col-12 col-md-3">
              <div class="text-caption text-grey-7">Derivación Legal</div>
              <div class="text-body1">{{ fmtDate(f.fecha_derivacion_area_legal) || '—' }}</div>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- DOCUMENTOS (checks) -->
      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm" />
          <div class="text-subtitle1 text-weight-medium">4) Documentación (checks)</div>
        </q-card-section>
        <q-separator />
        <q-card-section>
          <div class="row q-col-gutter-sm">
            <div class="col-12 col-sm-6 col-md-4">
              <q-item dense>
                <q-item-section avatar>
                  <q-icon :name="f.documento_fotocopia_carnet_denunciante==='1' ? 'check_circle' : 'cancel'"
                          :color="f.documento_fotocopia_carnet_denunciante==='1' ? 'positive' : 'grey-5'"/>
                </q-item-section>
                <q-item-section>Fotocopia CI denunciante</q-item-section>
              </q-item>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
              <q-item dense>
                <q-item-section avatar>
                  <q-icon :name="f.documento_fotocopia_carnet_denunciado==='1' ? 'check_circle' : 'cancel'"
                          :color="f.documento_fotocopia_carnet_denunciado==='1' ? 'positive' : 'grey-5'"/>
                </q-item-section>
                <q-item-section>Fotocopia CI denunciado</q-item-section>
              </q-item>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
              <q-item dense>
                <q-item-section avatar>
                  <q-icon :name="f.documento_placas_fotograficas_domicilio_denunciante==='1' ? 'check_circle' : 'cancel'"
                          :color="f.documento_placas_fotograficas_domicilio_denunciante==='1' ? 'positive' : 'grey-5'"/>
                </q-item-section>
                <q-item-section>Placas dom. denunciante</q-item-section>
              </q-item>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
              <q-item dense>
                <q-item-section avatar>
                  <q-icon :name="f.documento_croquis_direccion_denunciado==='1' ? 'check_circle' : 'cancel'"
                          :color="f.documento_croquis_direccion_denunciado==='1' ? 'positive' : 'grey-5'"/>
                </q-item-section>
                <q-item-section>Croquis dirección denunciado</q-item-section>
              </q-item>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
              <q-item dense>
                <q-item-section avatar>
                  <q-icon :name="f.documento_placas_fotograficas_domicilio_denunciado==='1' ? 'check_circle' : 'cancel'"
                          :color="f.documento_placas_fotograficas_domicilio_denunciado==='1' ? 'positive' : 'grey-5'"/>
                </q-item-section>
                <q-item-section>Placas dom. denunciado</q-item-section>
              </q-item>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
              <q-item dense>
                <q-item-section avatar>
                  <q-icon :name="f.documento_ciudadania_digital==='1' ? 'check_circle' : 'cancel'"
                          :color="f.documento_ciudadania_digital==='1' ? 'positive' : 'grey-5'"/>
                </q-item-section>
                <q-item-section>Ciudadanía digital</q-item-section>
              </q-item>
            </div>
<!--            'documento_fotocopia_carnet_denunciante',-->
<!--            'documento_fotocopia_carnet_denunciado',-->
<!--            'documento_placas_fotograficas_domicilio_denunciante',-->
<!--            'documento_croquis_direccion_denunciado',-->
<!--            'documento_placas_fotograficas_domicilio_denunciado',-->
<!--            'documento_ciudadania_digital',-->
<!--            'documento_certificado_nacimiento',-->
<!--            'documento_certificado_matrimonio',-->
<!--            'documento_tres_testigos',-->
<!--            'documento_contrato_pago',-->
<!--            'documento_libreta_notas',-->
<!--            'documento_numero_cuenta',-->
<!--            'documento_otros',-->
<!--            'documento_otros_detalle',-->
            <!--            <div class="col-12 col-sm-6 col-md-4">-->
            <!--              <q-item dense>-->
            <!--                <q-item-section avatar>-->
            <!--                  <q-icon :name="f.documento_fotocopia_carnet_denunciante==='1' ? 'check_circle' : 'cancel'"-->
            <!--                          :color="f.documento_fotocopia_carnet_denunciante==='1' ? 'positive' : 'grey-5'"/>-->
            <!--                </q-item-section>-->
            <!--                <q-item-section>Fotocopia CI denunciante</q-item-section>-->
            <!--              </q-item>-->
            <!--            </div>-->
          </div>
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>

<script>
import MapPicker from 'components/MapPicker.vue'
import moment from 'moment'

export default {
  name: 'CasoNuevoDNA',
  components: { MapPicker },
  props: {
    showNumeroApoyoIntegral: { type: Boolean, default: false },
    casoId: { type: [Number, String], default: null },
    editable: { type: Boolean, default: false },
    tipoProceso: { type: String, default: 'PROCESO_FAMILIAR' } // PROCESO_PENAL, PROCESO_NNA, PROCESO_FAMILIAR, APOYO_INTEGRAL
  },
  data () {
    return {
      recognition: null,
      activeField: null,
      isListening: false,
      loading: false,
      oruroCenter: [-17.9667, -67.1167],
      abogados: [],
      psicologos: [],
      sociales: [],
      sexos: [
        { label: 'Masculino', value: 'Masculino' },
        { label: 'Femenino',  value: 'Femenino'  },
        { label: 'Otro',      value: 'Otro'      },
      ],
      tipologias: [
        'ASISTENCIA FAMILIAR', 'VIOLENCIA FAMILIAR O DOMÉSTICA', 'OTROS'
      ],
      f: {
        familiares : [
          { nombre_completo: '', edad: null, parentesco: '', celular: '' }
        ],
        numero_apoyo_integral: '',
        area: 'SLIM',
        zona: 'CENTRAL',
        denunciantes : [{
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
          denunciante_trabaja: false,
          denunciante_relacion: '',
          denunciante_grado: '',
          latitud: null,
          longitud: null,
        }],
        menores: [],
        denunciados: [
          {
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
            denunciado_trabaja: 1,
            denunciado_prox: '',
            denunciado_ocupacion: '',
            denunciado_ocupacion_exacto: '',
            denunciado_idioma: '',
            denunciado_fijo: '',
            denunciado_movil: '',
            denunciado_domicilio_actual: '',
            denunciado_latitud: null,
            denunciado_longitud: null,
          }
        ],
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
        // Documentos (checks)
        documento_fotocopia_carnet_denunciante: "0",
        documento_fotocopia_carnet_denunciado: "0",
        documento_placas_fotograficas_domicilio_denunciante: "0",
        documento_croquis_direccion_denunciado: "0",
        documento_placas_fotograficas_domicilio_denunciado: "0",
        documento_ciudadania_digital: "0",
      }
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
      get () { return { latitud: this.f.denunciados[0].denunciado_latitud, longitud: this.f.denunciados[0].denunciado_longitud } },
      set (v) { this.f.denunciados[0].denunciado_latitud = v.latitud; this.f.denunciados[0].denunciado_longitud = v.longitud }
    }
  },
  mounted () {
    // if crear
    if (!this.f.caso_fecha_hecho) {
      this.f.caso_fecha_hecho = moment().format('YYYY-MM-DD')
    }
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
      const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
      this.recognition = new SpeechRecognition()
      this.recognition.lang = 'es-ES'
      this.recognition.interimResults = false
      this.recognition.continuous = false

      this.recognition.onstart = () => { this.isListening = true }
      this.recognition.onend = () => { this.isListening = false; this.activeField = null }
      this.recognition.onresult = (event) => {
        const text = event.results[0][0].transcript
        if (this.activeField === 'caso_descripcion') {
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
    if (this.tipoProceso === 'PROCESO_PENAL') {
      this.tipologias = [
        'VIOLACION', 'ESTUPRO', 'ABUSO SEXUAL', 'ACOSO SEXUAL', 'INFANTICIDIO', 'RAPTO', 'CORRUPCION',
        'PROXENETISMO', 'VIOLENCIA SEXUAL COMERCIAL', 'PORNOGRAFIA', 'TRAFICO DE PERSONAS', 'TRATA DE PERSONAS',
        'LESIONES GRAVES Y LEVES', 'LESIONES GRAVISIMAS', 'SUSTRACCIÓN DE MENOR INCAPAZ',
        'VIOLENCIA FAMILIAR O DOMESTICA (FISICA, PSICOLOGICA)', 'ABANDONO DE NIÑOS/AS', 'ABANDONO POR CAUSA DE HONOR',
        'OTROS'
      ]
    } else if (this.tipoProceso === 'PROCESO_NNA') {
      this.tipologias = [
        'ACOGIMIENTO CIRCUNSTANCIAL', 'INFRACCION POR VIOLENCIA', 'IRRESPONSABILIDAD PATERNA O MATERNA', 'OTROS'
      ]
    } else if (this.tipoProceso === 'APOYO_INTEGRAL') {
      this.tipologias = [
        'INFORMES PSICOLOGICOS', 'INFORMES SOCIALES'
      ]
    } else {
      this.tipologias = [
        'ASISTENCIA FAMILIAR', 'VIOLENCIA FAMILIAR O DOMÉSTICA', 'OTROS'
      ]
    }

    this.$axios.get('/usuariosRole').then(res => {
      this.psicologos = res.data.psicologos
      this.abogados = res.data.abogados
      this.sociales = res.data.sociales
    }).catch(() => {
      this.$alert.error('No se pudo cargar los usuarios por rol')
    })
    // cllas get caso
    this.getCaso()
    // carga abogados (ya tienes este endpoint)
    this.$axios.get('/usuariosRole')
      .then(r => { this.abogados = r.data.abogados || [] })
      .catch(() => { this.$alert?.error?.('No se pudo cargar abogados') })
  },
  methods: {
    toggleRecognition(field) {
      if (!this.recognition) {
        this.$q.notify({ color: 'negative', message: 'El navegador no soporta reconocimiento de voz.' })
        return
      }
      if (this.isListening && this.activeField === field) {
        try { this.recognition.stop() } catch (e) {}
        return
      }
      if (this.isListening && this.activeField !== field) {
        try { this.recognition.stop() } catch (e) {}
      }
      this.activeField = field
      try { this.recognition.start() } catch (e) { console.warn('No se pudo iniciar el reconocimiento:', e) }
    },
    show (v) {
      if (v === null || v === undefined || v === '') return '—'
      return String(v)
    },
    yesNo (v) {
      const on = (v === true) || (v === 1) || (v === '1')
      return on ? 'Sí' : 'No'
    },
    fmtDate (v) {
      if (!v) return ''
      // Acepta 'YYYY-MM-DD' o ISO
      const d = new Date(v)
      if (isNaN(d.getTime())) return this.show(v)
      const y = d.getFullYear()
      const m = String(d.getMonth()+1).padStart(2,'0')
      const day = String(d.getDate()).padStart(2,'0')
      return `${y}-${m}-${day}`
    },
    getCaso () {
      if (this.casoId) {
        this.loading = true
        this.$axios.get(`/casos/${this.casoId}`).then(res => {
          this.f = res.data
          // Asegurar que denunciantes y denunciado tengan al menos un objeto
          if (!this.f.denunciantes || this.f.denunciantes.length === 0) {
            this.f.denunciantes = [{
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
              denunciante_trabaja: false,
              denunciante_relacion: '',
              denunciante_grado: '',
              latitud: null,
              longitud: null,
            }]
          }
          if (!this.f.denunciados || this.f.denunciados.length === 0) {
            this.f.denunciados = [
              {
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
                denunciado_trabaja: 1,
                denunciado_prox: '',
                denunciado_ocupacion: '',
                denunciado_ocupacion_exacto: '',
                denunciado_idioma: '',
                denunciado_fijo: '',
                denunciado_movil: '',
                denunciado_domicilio_actual: '',
                denunciado_latitud: null,
                denunciado_longitud: null,
              }
            ]
          }
        }).catch(() => {
          this.$alert.error('No se pudo cargar el caso')
        }).finally(() => { this.loading = false })
      }
    },
    req (v) { return !!v || 'Requerido' },
    addMenor () {
      this.f.menores.push({
        nombre: '', gestante: false, edad_anios: null, edad_meses: null,
        sexo: '', cert_nac: false, estudia: false, ultimo_curso: '', tipo_trabajo: ''
      })
    },
    addFam () {
      this.f.familiares.push({ nombre: '', parentesco: '', edad: null, sexo: '', instruccion: '', ocupacion: '' })
    },
    resetForm () {
      const keep = {}
      this.f = {
        ...keep,
        fecha_atencion: '', principal: '', tipologia: '',
        domicilio: '', zona: '', telefono: '', latitud: null, longitud: null,
        menores: [{ nombre: '', gestante: false, edad_anios: null, edad_meses: null, sexo: '', cert_nac: false, estudia: false, ultimo_curso: '', tipo_trabajo: '' }],
        familiares: [],
        denunciado_nombre: '', denunciado_sexo: '', denunciado_edad: null, denunciado_relacion: '', denunciado_ci: '', denunciado_domicilio: '', denunciado_telefono: '', denunciado_lugar_trabajo: '', denunciado_ocupacion: '',
        denunciante_nombre: '', denunciante_sexo: '', denunciante_edad: null, denunciante_ci: '', denunciante_domicilio: '', denunciante_telefono: '', denunciante_lugar_trabajo: '', denunciante_ocupacion: '',
        descripcion: '',
        abogado_user_id: null,
      }
      this.$q.notify({ type: 'info', message: 'Formulario reiniciado' })
    },
    async update(){
      // if (!this.f.principal) {
      //   this.$alert?.error?.('El campo PRINCIPAL es obligatorio')
      //   return
      // }
      // if (!this.f.denunciante_nombre) {
      //   this.$alert?.error?.('El nombre del denunciante es obligatorio')
      //   return
      // }
      this.loading = true
      try {
        const res = await this.$axios.put(`/casos/${this.casoId}`, this.f)
        this.$alert?.success?.('Caso DNA actualizado')
        this.$router.push(`/casos/${this.casoId}`)
      } catch (e) {
        this.$alert?.error?.(e?.response?.data?.message || 'No se pudo actualizar el caso DNA')
      } finally {
        this.loading = false
      }
    },

    async save () {
      // if (!this.f.principal) {
      //   this.$alert?.error?.('El campo PRINCIPAL es obligatorio')
      //   return
      // }
      // if (!this.f.denunciante_nombre) {
      //   this.$alert?.error?.('El nombre del denunciante es obligatorio')
      //   return
      // }
      this.loading = true
      try {
        const res = await this.$axios.post('/casos', { ...this.f, tipo: 'DNA' })
        const id = res?.data?.id || res?.data?.caso?.id
        this.$alert?.success?.('Caso DNA creado')
        if (id) this.$router.push(`/casos/${id}`)
        else     this.$router.push('/casos?tipo=DNA')
      } catch (e) {
        this.$alert?.error?.(e?.response?.data?.message || 'No se pudo crear el caso DNA')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.bg-grey-2 { min-height: 100%; }
.toolbar   { position: sticky; top: 0; z-index: 5; border-radius: 12px; }
.section-card { border-radius: 14px; overflow: hidden; margin-bottom: 16px; background: #fff; }
</style>

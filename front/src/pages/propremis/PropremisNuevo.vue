<!-- src/pages/propremi/CasoNuevoPROPREMI.vue -->
<template>
  <q-page class="q-pa-md bg-grey-2">
    <!-- Toolbar -->
    <div class="toolbar q-pa-sm bg-white row items-center q-gutter-sm shadow-1" v-if="!editable">
      <div class="col">
        <div class="text-h6 text-weight-bold">Registro de Atención y/o Denuncia — PROPREMI</div>
        <div class="text-caption text-grey-7">SID · Programa Municipal PROPREMI</div>
      </div>
      <div class="col-auto row q-gutter-sm">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
      </div>
    </div>

    <div v-else class="q-mb-sm text-right">
      <q-btn flat color="primary" icon="refresh" label="Recargar" @click="getCaso" class="q-mr-sm"/>
      <q-btn color="primary" icon="save" label="Actualizar" :loading="loading" @click="update" v-if="$store.user?.role === 'Administrador' || $store.user?.role === 'Asistente'"/>
    </div>
    <template v-if="$store.user?.role === 'Administrador' || $store.user?.role === 'Asistente'">
    <q-form class="q-mt-lg" @submit.prevent="save">
      <!-- 1) Datos generales -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="assignment" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">1) Datos generales</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3">
              <q-input v-model="f.caso_fecha_hecho" type="date" dense outlined label="Fecha de atención/registro"/>
            </div>
            <div class="col-12 col-md-4">
              <q-input v-model="f.principal" dense outlined clearable :rules="[req]"
                       label="Tipología principal (p. ej., Apoyo Integral) *"/>
            </div>
            <div class="col-6 col-md-3">
              <q-input v-model="f.caso_lugar_hecho" dense outlined clearable label="U.E. / Colegio"/>
            </div>
            <div class="col-6 col-md-2">
              <q-input v-model="f.caso_numero" dense outlined clearable label="N° de caso" readonly/>
            </div>

            <div class="col-12 col-md-8">
              <q-input v-model="f.caso_direccion" dense outlined clearable label="Domicilio / Dirección (para hoja de ruta)"/>
            </div>
            <div class="col-12 col-md-2">
              <q-btn outline label="Buscar en mapa" @click="$refs.denMap?.geocodeAndFly(f.caso_direccion)"/>
            </div>
            <div class="col-12">
              <MapPicker
                v-model="denunciantePos"
                :center="oruroCenter"
                :address="f.caso_direccion"
                country="bo"
                ref="denMap"
              />
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 2) Afectado(s) / Menor(es) -->
      <q-card flat bordered class="section-card">
        <q-expansion-item icon="child_care" dense-toggle expand-separator
                          label="2) Datos del/los afectado(s)" header-class="text-subtitle1">
          <q-card>
            <q-card-section>
              <div class="q-mb-sm">
                <q-btn dense icon="add" color="primary" label="Agregar afectado" @click="addMenor"/>
              </div>

              <q-markup-table dense flat bordered>
                <thead>
                <tr class="bg-blue-1 text-blue-10">
                  <th>Nombres y apellidos</th>
                  <th style="width:90px">Edad (años)</th>
                  <th style="width:90px">Edad (meses)</th>
                  <th style="width:110px">Sexo</th>
                  <th style="width:80px">C. Nac</th>
                  <th style="width:80px">Estudia</th>
                  <th>Último curso</th>
                  <th>Tipo de trabajo</th>
                  <th style="width:56px"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(m,i) in f.menores" :key="i">
                  <td><q-input v-model="m.nombres" dense outlined/></td>
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
              <div v-if="!f.menores.length" class="text-grey text-caption">Sin afectados registrados</div>
            </q-card-section>
          </q-card>
        </q-expansion-item>
      </q-card>

      <!-- 3) Grupo familiar -->
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
                  <th>Nombres y apellidos</th>
                  <th>Parentesco</th>
                  <th style="width:100px">Edad</th>
                  <th style="width:120px">Estado civil</th>
                  <th>Ocupación</th>
                  <th style="width:140px">Celular</th>
                  <th style="width:56px"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(g,i) in f.familiares" :key="'g'+i">
                  <td class="row q-col-gutter-xs">
                    <div class="col"><q-input v-model="g.familiar_nombres" dense outlined placeholder="Nombres"/></div>
                    <div class="col-6 col-md-3"><q-input v-model="g.familiar_paterno" dense outlined placeholder="Paterno"/></div>
                    <div class="col-6 col-md-3"><q-input v-model="g.familiar_materno" dense outlined placeholder="Materno"/></div>
                  </td>
                  <td><q-input v-model="g.familiar_parentesco" dense outlined/></td>
                  <td><q-input v-model.number="g.familiar_edad" type="number" dense outlined/></td>
                  <td><q-input v-model="g.familiar_estado_civil" dense outlined/></td>
                  <td><q-input v-model="g.familiar_ocupacion" dense outlined/></td>
                  <td><q-input v-model="g.familiar_telefono" dense outlined/></td>
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

      <!-- 4) Denunciado/a -->
      <q-card flat bordered class="section-card">
        <q-expansion-item icon="person_off" dense-toggle expand-separator
                          label="4) Datos del denunciado/a" header-class="text-subtitle1">
          <q-card>
            <q-card-section>
              <div class="row q-col-gutter-md">
                <div class="col-12 col-md-3"><q-input v-model="f.denunciados[0].denunciado_nombres" dense outlined clearable label="Nombres"/></div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_paterno" dense outlined clearable label="Ap. paterno"/></div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_materno" dense outlined clearable label="Ap. materno"/></div>

                <div class="col-6 col-md-3"><q-select v-model="f.denunciados[0].denunciado_sexo" :options="sexos" emit-value map-options dense outlined label="Sexo"/></div>

                <div class="col-6 col-md-3">
                  <q-select v-model="f.denunciados[0].denunciado_documento" :options="$documentos" emit-value map-options dense outlined label="Documento"/>
                </div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_nro" dense outlined clearable label="N° documento"/></div>

                <div class="col-12 col-md-3">
                  <q-input v-model="f.denunciados[0].denunciado_fecha_nacimiento" type="date" dense outlined label="Fecha de nacimiento"
                           @update:model-value="autoEdad('denunciado')"/>
                </div>
                <div class="col-12 col-md-3"><q-input v-model="f.denunciados[0].denunciado_lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento"/></div>

                <div class="col-6 col-md-2"><q-input v-model.number="f.denunciados[0].denunciado_edad" type="number" dense outlined label="Edad"/></div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_residencia" dense outlined clearable label="Residencia habitual"/></div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_estado_civil" dense outlined clearable label="Estado civil"/></div>

                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_idioma" dense outlined clearable label="Idioma"/></div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_grado" dense outlined clearable label="Grado de instrucción"/></div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_ocupacion" dense outlined clearable label="Ocupación"/></div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciados[0].denunciado_telefono" dense outlined clearable label="Teléfono"/></div>
                <div class="col-12 col-md-6"><q-input v-model="f.denunciados[0].denunciado_domicilio_actual" dense outlined clearable label="Dirección actual"/></div>
                <div class="col-12 col-md-6"><q-input v-model="f.denunciados[0].denunciado_relacion" dense outlined clearable label="Relación con la denunciante"/></div>
              </div>
            </q-card-section>
          </q-card>
        </q-expansion-item>
      </q-card>

      <!-- 5) Denunciante -->
      <q-card flat bordered class="section-card">
        <q-expansion-item icon="person" dense-toggle expand-separator
                          label="5) Datos del/la denunciante" header-class="text-subtitle1" default-opened>
          <q-card>
            <q-card-section>
              <div class="row q-col-gutter-md">
                <div class="col-12 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_nombres" dense outlined clearable :rules="[req]" label="Nombres *"/></div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_paterno" dense outlined clearable label="Ap. paterno"/></div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_materno" dense outlined clearable label="Ap. materno"/></div>

                <div class="col-6 col-md-3"><q-select v-model="f.denunciantes[0].denunciante_sexo" :options="sexos" emit-value map-options dense outlined label="Sexo"/></div>

                <div class="col-6 col-md-3">
                  <q-select v-model="f.denunciantes[0].denunciante_documento" :options="$documentos" emit-value map-options dense outlined label="Documento"/>
                </div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_nro" dense outlined clearable label="N° documento"/></div>

                <div class="col-12 col-md-3">
                  <q-input v-model="f.denunciantes[0].denunciante_fecha_nacimiento" type="date" dense outlined label="Fecha de nacimiento"
                           @update:model-value="autoEdad('denunciante')"/>
                </div>
                <div class="col-12 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento"/></div>

                <div class="col-6 col-md-2"><q-input v-model.number="f.denunciantes[0].denunciante_edad" type="number" dense outlined label="Edad"/></div>
                <div class="col-6 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_residencia" dense outlined clearable label="Residencia habitual"/></div>
                <div class="col-6 col-md-3">
                  <q-select v-model="f.denunciantes[0].denunciante_estado_civil" :options="estadosCiviles" dense outlined label="Estado civil"/>
                </div>

                <div class="col-12 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_relacion" dense outlined clearable label="Relación con el denunciado/a"/></div>
                <div class="col-12 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_grado" dense outlined clearable label="Grado de instrucción"/></div>

                <div class="col-12 col-md-3"><q-toggle v-model="f.denunciantes[0].denunciante_trabaja" label="Trabaja"/></div>
                <div class="col-12 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_ocupacion" dense outlined clearable label="Ocupación"/></div>

                <div class="col-12 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_idioma" dense outlined clearable label="Idioma"/></div>
                <div class="col-12 col-md-3"><q-input v-model="f.denunciantes[0].denunciante_telefono" dense outlined clearable label="Teléfono de referencia"/></div>

                <div class="col-12 col-md-6"><q-input v-model="f.denunciantes[0].denunciante_domicilio_actual" dense outlined clearable label="Domicilio actual"/></div>
              </div>
            </q-card-section>
          </q-card>
        </q-expansion-item>
      </q-card>

      <!-- 6) Descripción -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="description" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">6) Descripción de la denuncia</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <q-input v-model="f.caso_descripcion" type="textarea" autogrow outlined dense clearable
                   label="Breve circunstancia del hecho" maxlength="5000" counter>
            <template v-slot:append>
              <q-icon name="mic" class="cursor-pointer"
                      :color="isListening && activeField==='caso_descripcion' ? 'red' : 'grey-7'"
                      @click="toggleRecognition('caso_descripcion')"/>
            </template>
          </q-input>
        </q-card-section>
      </q-card>

      <!-- 7) Tipología de violencia (mapea a campos existentes) -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="warning" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">7) Tipología de violencia</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-6 col-md-3"><q-checkbox v-model="f.violencia_psicologica" label="Psicológica"/></div>
            <div class="col-6 col-md-3"><q-checkbox v-model="f.violencia_fisica" label="Física"/></div>
            <div class="col-6 col-md-3"><q-checkbox v-model="f.violencia_sexual" label="Sexual / de género"/></div>
            <div class="col-6 col-md-3"><q-checkbox v-model="f.violencia_economica" label="Económica"/></div>
            <div class="col-6 col-md-3"><q-checkbox v-model="f.violencia_patrimonial" label="Patrimonial"/></div>
            <div class="col-6 col-md-3"><q-checkbox v-model="f.violencia_simbolica" label="Simbólica / verbal / bullying"/></div>
            <div class="col-6 col-md-3"><q-checkbox v-model="f.violencia_institucional" label="Institucional"/></div>
            <div class="col-12 col-md-9">
              <q-input v-model="f.caso_tipologia" dense outlined clearable
                       label="Detalle adicional (p. ej., violencia cibernética, discriminación, etc.)"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 8) Check de documentos -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="inventory_2" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">8) Documentos adjuntos (check)</div>
        </q-card-section>
        <q-separator/>
        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3">
              <q-checkbox v-model="f.documento_fotocopia_carnet_denunciante" true-value="1" false-value="0"
                          label="Fotocopia CI denunciante"/>
            </div>
            <div class="col-12 col-md-3">
              <q-checkbox v-model="f.documento_fotocopia_carnet_denunciado" true-value="1" false-value="0"
                          label="Fotocopia CI denunciado"/>
            </div>
            <div class="col-12 col-md-3">
              <q-checkbox v-model="f.documento_placas_fotograficas_domicilio_denunciante" true-value="1" false-value="0"
                          label="Placas fotográficas dom. denunciante"/>
            </div>
            <div class="col-12 col-md-3">
              <q-checkbox v-model="f.documento_croquis_direccion_denunciado" true-value="1" false-value="0"
                          label="Croquis dirección denunciado"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- 9) Seguimiento -->
      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">9) Seguimiento del caso</div>
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

      <!-- Botones -->
      <div class="text-right q-mb-lg">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm" class="q-mr-sm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save" v-if="!editable"/>
        <q-btn color="primary" icon="save" label="Actualizar" :loading="loading" @click="update" v-if="editable"/>
      </div>
    </q-form>
    </template>
    <template v-else>
      <div class="q-pa-md">
        <!-- CABECERA -->
        <q-card flat bordered class="section-card">
          <q-card-section class="row items-center q-col-gutter-sm">
            <div class="col-12 col-md-8">
              <div class="text-h6 text-weight-bold">
                Detalle PROPREMI · Caso
                <span v-if="f.caso_numero">#{{ f.caso_numero }}</span>
                <span v-else>#{{ f.id }}</span>
              </div>
              <div class="text-caption text-grey-7">
                Registrado:
                <q-chip dense color="indigo-1" text-color="indigo-9">
                  {{ fmtDate(f.caso_fecha_hecho) || '—' }}
                </q-chip>
                · Por:
                <q-chip dense color="grey-2" text-color="grey-9">
                  {{ f.user?.name || '—' }}
                </q-chip>
              </div>
            </div>
            <div class="col-12 col-md-4 flex items-center justify-end">
              <q-chip dense outline color="primary" text-color="primary">Tipo: {{ show(f.tipo || 'PROPREMI') }}</q-chip>
              <q-chip v-if="f.zona" dense outline color="teal" text-color="teal">Zona: {{ show(f.zona) }}</q-chip>
            </div>
          </q-card-section>
        </q-card>

        <!-- DATOS DEL CASO -->
        <q-card flat bordered class="section-card">
          <q-card-section class="row items-center">
            <q-icon name="assignment" class="q-mr-sm"/>
            <div class="text-subtitle1 text-weight-medium">1) Datos del caso</div>
          </q-card-section>
          <q-separator/>
          <q-card-section>
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-6">
                <div class="text-caption text-grey-7">Tipología principal</div>
                <div class="text-body1">{{ show(f.principal) }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="text-caption text-grey-7">N° caso</div>
                <div class="text-body1">{{ show(f.caso_numero) }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="text-caption text-grey-7">Fecha de atención/registro</div>
                <div class="text-body1">{{ fmtDate(f.caso_fecha_hecho) || '—' }}</div>
              </div>

              <div class="col-12 col-md-6">
                <div class="text-caption text-grey-7">U.E. / Colegio</div>
                <div class="text-body1">{{ show(f.caso_lugar_hecho) }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div class="text-caption text-grey-7">Domicilio / Dirección</div>
                <div class="text-body1">{{ show(f.caso_direccion) }}</div>
              </div>

              <div class="col-12">
                <div class="text-caption text-grey-7 q-mb-xs">Descripción</div>
                <div class="q-pa-sm bg-grey-1" style="white-space: pre-wrap; border-radius: 10px;">
                  {{ show(f.caso_descripcion) }}
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>

        <!-- TIPOLOGÍA DE VIOLENCIA -->
        <q-card flat bordered class="section-card" v-if="
      f.violencia_fisica || f.violencia_psicologica || f.violencia_sexual || f.violencia_economica ||
      f.violencia_patrimonial || f.violencia_simbolica || f.violencia_institucional || f.caso_tipologia
    ">
          <q-card-section class="row items-center">
            <q-icon name="warning" class="q-mr-sm"/>
            <div class="text-subtitle1 text-weight-medium">2) Tipología de violencia</div>
          </q-card-section>
          <q-separator/>
          <q-card-section>
            <div class="row q-col-gutter-sm">
              <div class="col-auto" v-if="f.violencia_psicologica"><q-chip color="deep-purple-1" text-color="deep-purple-9" dense>Psicológica</q-chip></div>
              <div class="col-auto" v-if="f.violencia_fisica"><q-chip color="red-1" text-color="red-9" dense>Física</q-chip></div>
              <div class="col-auto" v-if="f.violencia_sexual"><q-chip color="pink-1" text-color="pink-9" dense>Sexual</q-chip></div>
              <div class="col-auto" v-if="f.violencia_economica"><q-chip color="orange-1" text-color="orange-9" dense>Económica</q-chip></div>
              <div class="col-auto" v-if="f.violencia_patrimonial"><q-chip color="blue-grey-1" text-color="blue-grey-9" dense>Patrimonial</q-chip></div>
              <div class="col-auto" v-if="f.violencia_simbolica"><q-chip color="cyan-1" text-color="cyan-9" dense>Simbólica</q-chip></div>
              <div class="col-auto" v-if="f.violencia_institucional"><q-chip color="brown-1" text-color="brown-9" dense>Institucional</q-chip></div>

              <div class="col-12 q-mt-sm" v-if="f.caso_tipologia">
                <div class="text-caption text-grey-7">Detalle adicional</div>
                <div class="text-body1">{{ show(f.caso_tipologia) }}</div>
              </div>
            </div>
          </q-card-section>
        </q-card>

        <!-- RESPONSABLES -->
        <q-card flat bordered class="section-card" v-if="f.psicologica_user || f.trabajo_social_user || f.legal_user">
          <q-card-section class="row items-center">
            <q-icon name="people_alt" class="q-mr-sm" />
            <div class="text-subtitle1 text-weight-medium">3) Responsables</div>
          </q-card-section>
          <q-separator />
          <q-card-section>
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-4" v-if="f.psicologica_user">
                <div class="text-caption text-grey-7">Área Psicológica</div>
                <div class="text-body1">
                  {{ f.psicologica_user?.name || '—' }}
                  <q-chip v-if="f.psicologica_user?.celular" dense color="blue-1" text-color="blue-9" class="q-ml-xs">
                    {{ f.psicologica_user?.celular }}
                  </q-chip>
                </div>
              </div>
              <div class="col-12 col-md-4" v-if="f.trabajo_social_user">
                <div class="text-caption text-grey-7">Trabajo Social</div>
                <div class="text-body1">
                  {{ f.trabajo_social_user?.name || '—' }}
                  <q-chip v-if="f.trabajo_social_user?.celular" dense color="blue-1" text-color="blue-9" class="q-ml-xs">
                    {{ f.trabajo_social_user?.celular }}
                  </q-chip>
                </div>
              </div>
              <div class="col-12 col-md-4" v-if="f.legal_user">
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

        <!-- FECHAS CLAVE (si tu backend las trae) -->
        <q-card flat bordered class="section-card" v-if="
      f.fecha_apertura_caso || f.fecha_derivacion_psicologica || f.fecha_informe_area_psicologica ||
      f.fecha_informe_area_social || f.fecha_informe_trabajo_social || f.fecha_derivacion_area_legal
    ">
          <q-card-section class="row items-center">
            <q-icon name="event" class="q-mr-sm" />
            <div class="text-subtitle1 text-weight-medium">4) Fechas clave</div>
          </q-card-section>
          <q-separator />
          <q-card-section>
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-3" v-if="f.fecha_apertura_caso">
                <div class="text-caption text-grey-7">Apertura</div>
                <div class="text-body1">{{ fmtDate(f.fecha_apertura_caso) }}</div>
              </div>
              <div class="col-12 col-md-3" v-if="f.fecha_derivacion_psicologica">
                <div class="text-caption text-grey-7">Derivación Psicológica</div>
                <div class="text-body1">{{ fmtDate(f.fecha_derivacion_psicologica) }}</div>
              </div>
              <div class="col-12 col-md-3" v-if="f.fecha_informe_area_psicologica">
                <div class="text-caption text-grey-7">Informe Psicología</div>
                <div class="text-body1">{{ fmtDate(f.fecha_informe_area_psicologica) }}</div>
              </div>
              <div class="col-12 col-md-3" v-if="f.fecha_informe_area_social">
                <div class="text-caption text-grey-7">Informe Social</div>
                <div class="text-body1">{{ fmtDate(f.fecha_informe_area_social) }}</div>
              </div>
              <div class="col-12 col-md-3" v-if="f.fecha_informe_trabajo_social">
                <div class="text-caption text-grey-7">Informe Trabajo Social</div>
                <div class="text-body1">{{ fmtDate(f.fecha_informe_trabajo_social) }}</div>
              </div>
              <div class="col-12 col-md-3" v-if="f.fecha_derivacion_area_legal">
                <div class="text-caption text-grey-7">Derivación Legal</div>
                <div class="text-body1">{{ fmtDate(f.fecha_derivacion_area_legal) }}</div>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </template>
  </q-page>
</template>

<script>
import MapPicker from 'components/MapPicker.vue'

export default {
  name: 'CasoNuevoPROPREMI',
  components: { MapPicker },
  props: {
    casoId: { type: [Number, String], default: null },
    editable: { type: Boolean, default: false },
  },
  data () {
    return {
      loading: false,
      recognition: null, activeField: null, isListening: false,
      oruroCenter: [-17.9667, -67.1167],
      psicologos: [], abogados: [], sociales: [],
      sexos: [
        { label: 'Masculino', value: 'Masculino' },
        { label: 'Femenino',  value: 'Femenino'  },
        { label: 'Otro',      value: 'Otro'      },
      ],
      estadosCiviles: ['Soltero/a','Casado/a','Divorciado/a','Separado/a','Viudo/a','Unión libre','Otro'],
      f: {
        // Caso
        tipo: 'PROPREMI',
        principal: '',
        caso_numero: '',
        caso_fecha_hecho: '',
        caso_lugar_hecho: '',
        caso_direccion: '',
        caso_tipologia: '',
        caso_descripcion: '',

        // Violencias (mapean a columnas existentes)
        violencia_fisica: false,
        violencia_psicologica: false,
        violencia_sexual: false,
        violencia_economica: false,
        violencia_patrimonial: false,
        violencia_simbolica: false,
        violencia_institucional: false,

        // Nested
        denunciantes: [{
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
          denunciante_trabaja: false,
          denunciante_ocupacion: '',
          denunciante_idioma: '',
          denunciante_domicilio_actual: '',
          latitud: null,
          longitud: null,
        }],
        denunciados: [{
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
          denunciado_ocupacion: '',
          denunciado_idioma: '',
          denunciado_domicilio_actual: '',
          denunciado_latitud: null,
          denunciado_longitud: null,
        }],
        familiares: [],
        menores: [],

        // Asignaciones
        psicologica_user_id: null,
        trabajo_social_user_id: null,
        legal_user_id: null,

        // Documentos (checks)
        documento_fotocopia_carnet_denunciante: "0",
        documento_fotocopia_carnet_denunciado: "0",
        documento_placas_fotograficas_domicilio_denunciante: "0",
        documento_croquis_direccion_denunciado: "0",
      }
    }
  },
  computed: {
    denunciantePos: {
      get () {
        const d = this.f.denunciantes?.[0] || {}
        return { latitud: d.latitud, longitud: d.longitud }
      },
      set (v) {
        if (!this.f.denunciantes?.length) return
        this.f.denunciantes[0].latitud  = v?.latitud ?? v?.lat ?? null
        this.f.denunciantes[0].longitud = v?.longitud ?? v?.lng ?? null
      }
    }
  },
  mounted () {
    // Carga responsables por rol
    this.$axios.get('/usuariosRole').then(res => {
      this.psicologos = res.data.psicologos || []
      this.abogados   = res.data.abogados   || []
      this.sociales   = res.data.sociales   || []
    }).catch(() => this.$alert?.error?.('No se pudo cargar los usuarios por rol'))

    // Mic para dictado en descripción
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
      const SR = window.SpeechRecognition || window.webkitSpeechRecognition
      this.recognition = new SR()
      this.recognition.lang = 'es-ES'
      this.recognition.interimResults = false
      this.recognition.continuous = false
      this.recognition.onstart = () => { this.isListening = true }
      this.recognition.onend   = () => { this.isListening = false; this.activeField = null }
      this.recognition.onresult = (e) => {
        const text = e.results[0][0].transcript
        if (this.activeField === 'caso_descripcion') {
          this.f.caso_descripcion = (this.f.caso_descripcion ? this.f.caso_descripcion + ' ' : '') + text
        }
      }
    }

    this.getCaso()
  },
  methods: {
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
    req (v) { return !!v || 'Requerido' },
    addMenor () {
      this.f.menores.push({
        nombres: '', edad_anios: null, edad_meses: null,
        sexo: '', cert_nac: false, estudia: false, ultimo_curso: '', tipo_trabajo: ''
      })
    },
    addFam () {
      this.f.familiares.push({
        familiar_nombres: '', familiar_paterno: '', familiar_materno: '',
        familiar_parentesco: '', familiar_edad: null, familiar_estado_civil: '',
        familiar_ocupacion: '', familiar_telefono: ''
      })
    },
    toggleRecognition (field) {
      if (!this.recognition) {
        this.$q.notify({ color: 'negative', message: 'El navegador no soporta micrófono.' })
        return
      }
      if (this.isListening && this.activeField === field) { try { this.recognition.stop() } catch(e){}; return }
      if (this.isListening) { try { this.recognition.stop() } catch(e){} }
      this.activeField = field
      try { this.recognition.start() } catch (e) {}
    },
    resetForm () {
      const fresh = this.$options.data().f
      this.f = JSON.parse(JSON.stringify(fresh))
      this.$q.notify({ type: 'info', message: 'Formulario reiniciado' })
    },
    getCaso () {
      if (!this.editable || !this.casoId) return
      this.loading = true
      this.$axios.get(`/casos/${this.casoId}`)
        .then(r => {
          const data = r.data || {}
          if (!data.denunciantes || !data.denunciantes.length) data.denunciantes = [this.$options.data().f.denunciantes[0]]
          if (!data.denunciados || !data.denunciados.length)   data.denunciados  = [this.$options.data().f.denunciados[0]]
          if (!data.familiares) data.familiares = []
          if (!data.menores)    data.menores    = []
          this.f = { ...this.f, ...data, tipo: 'PROPREMI' }
        })
        .catch(() => this.$alert?.error?.('No se pudo cargar el caso'))
        .finally(() => { this.loading = false })
    },
    async save () {
      if (!this.f.principal || !this.f.denunciantes[0].denunciante_nombres) {
        this.$alert?.error?.('Tipología principal y nombre del denunciante son obligatorios')
        return
      }
      this.loading = true
      try {
        const res = await this.$axios.post('/casos', { ...this.f, tipo: 'PROPREMI' })
        const id = res?.data?.id || res?.data?.caso?.id
        this.$alert?.success?.('Caso PROPREMI creado')
        if (id) this.$router.push(`/casos/${id}`); else this.$router.push('/casos?tipo=PROPREMI')
      } catch (e) {
        this.$alert?.error?.(e?.response?.data?.message || 'No se pudo crear el caso PROPREMI')
      } finally { this.loading = false }
    },
    async update () {
      if (!this.f.principal) { this.$alert?.error?.('La tipología principal es obligatoria'); return }
      this.loading = true
      try {
        await this.$axios.put(`/casos/${this.casoId}`, this.f)
        this.$alert?.success?.('Caso PROPREMI actualizado')
        this.$router.push(`/casos/${this.casoId}`)
      } catch (e) {
        this.$alert?.error?.(e?.response?.data?.message || 'No se pudo actualizar el caso PROPREMI')
      } finally { this.loading = false }
    },
    autoEdad (quien) {
      const key = quien === 'denunciado' ? 'denunciados' : 'denunciantes'
      const obj = this.f[key]?.[0]
      const d = obj?.[`${quien}_fecha_nacimiento`]
      if (!d) return
      const age = Math.floor((new Date() - new Date(d)) / (1000*60*60*24*365.25))
      obj[`${quien}_edad`] = Number.isFinite(age) ? age : ''
    },
  }
}
</script>

<style scoped>
.bg-grey-2 { min-height: 100%; }
.toolbar   { position: sticky; top: 0; z-index: 5; border-radius: 12px; }
.section-card { border-radius: 14px; overflow: hidden; margin-bottom: 16px; background: #fff; }
</style>

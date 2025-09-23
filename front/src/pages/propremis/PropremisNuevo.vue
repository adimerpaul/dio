<template>
  <q-page class="q-pa-md bg-grey-2">
    <!-- Barra superior -->
    <div class="toolbar q-pa-sm bg-white row items-center q-gutter-sm shadow-1" v-if="!editable">
      <div class="col">
        <div class="text-h6 text-weight-bold">Nuevo SLIM</div>
        <div class="text-caption text-grey-7">Registro de denuncia</div>
      </div>
      <div class="col-auto row q-gutter-sm">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save"/>
      </div>
    </div>
<!--    bton actulizar al aderecha-->
    <div  v-if="editable">
<!--      <div class="col">-->
<!--        <div class="text-h6 text-weight-bold">Editar SLIM</div>-->
<!--        <div class="text-caption text-grey-7">Actualización de denuncia</div>-->
<!--      </div>-->
      <div class="col-auto text-right">
<!--        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm"/>-->
        <q-btn color="primary" icon="save" label="Actualizar" :loading="loading" @click="update"/>
      </div>
    </div>

    <q-form class="q-mt-lg" @submit.prevent="save">
      <!-- Denunciante -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="assignment_ind" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">1) Datos del Denunciante</div>
          <q-space/>
          <q-badge color="blue-2" text-color="blue-10" outline>Obligatorio *</q-badge>
        </q-card-section>
        <q-separator/>

        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-2" v-if="showNumeroApoyoIntegral">
              <q-input v-model="f.numero_apoyo_integral" dense outlined clearable
                       label="Nro. Apoyo Integral *" :rules="[req]" hint="Número de apoyo integral asignado"/>
            </div>

            <div class="col-12 col-md-2">
              <q-input v-model="f.denunciantes[0].denunciante_nombres" dense outlined clearable
                       label="Nombres *" :rules="[req]"/>
            </div>
            <div class="col-12 col-md-2">
<!--              <q-input v-model="f.denunciante_paterno" dense outlined clearable label="Apellido paterno *"/>-->
              <q-input v-model="f.denunciantes[0].denunciante_paterno" dense outlined clearable label="Apellido paterno *" :rules="[req]"/>
            </div>
            <div class="col-12 col-md-2">
<!--              <q-input v-model="f.denunciante_materno" dense outlined clearable label="Apellido materno"/>-->
              <q-input v-model="f.denunciantes[0].denunciante_materno" dense outlined clearable label="Apellido materno"/>
            </div>

            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciante_lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento"/>-->
              <q-input v-model="f.denunciantes[0].denunciante_lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciante_fecha_nacimiento" type="date" dense outlined label="Fecha de nacimiento"-->
<!--                       @update:model-value="onBirthChange('denunciante')"/>-->
              <q-input v-model="f.denunciantes[0].denunciante_fecha_nacimiento" type="date" dense outlined label="Fecha de nacimiento"
                       @update:model-value="onBirthChange('denunciante')"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model.number="f.denunciante_edad" dense outlined type="number" label="Edad"/>-->
              <q-input v-model.number="f.denunciantes[0].denunciante_edad" dense outlined type="number" label="Edad"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciante_telefono" dense outlined clearable label="Teléfono/Celular"/>-->
              <q-input v-model="f.denunciantes[0].denunciante_telefono" dense outlined clearable label="Teléfono/Celular"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciante_grado" dense outlined clearable label="Grado de instrucción"/>-->
              <q-input v-model="f.denunciantes[0].denunciante_grado" dense outlined clearable label="Grado de instrucción"/>
            </div>

            <div class="col-6 col-md-3">
<!--              <q-select v-model="f.denunciante_documento" dense outlined emit-value map-options clearable-->
<!--                        :options="documentos" label="Documento"/>-->
              <q-select v-model="f.denunciantes[0].denunciante_documento" dense outlined emit-value map-options clearable
                        :options="documentos" label="Documento"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciante_nro" dense outlined clearable label="Nro documento"/>-->
              <q-input v-model="f.denunciantes[0].denunciante_nro" dense outlined clearable label="Nro documento"/>
            </div>

            <div class="col-6 col-md-3">
<!--              <q-select v-model="f.denunciante_sexo" dense outlined emit-value map-options clearable-->
<!--                        :options="sexos" label="Sexo"/>-->
              <q-select v-model="f.denunciantes[0].denunciante_sexo" dense outlined emit-value map-options clearable
                        :options="sexos" label="Sexo"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-select v-model="f.denunciante_estado_civil" dense outlined emit-value map-options clearable-->
<!--                        :options="estadosCiviles" label="Estado civil"/>-->
              <q-select v-model="f.denunciantes[0].denunciante_estado_civil" dense outlined emit-value map-options clearable
                        :options="estadosCiviles" label="Estado civil"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciante_residencia" dense outlined clearable label="Residencia"/>-->
              <q-input v-model="f.denunciantes[0].denunciante_residencia" dense outlined clearable label="Residencia"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-select v-model="f.denunciante_idioma" dense outlined emit-value map-options clearable-->
<!--                        :options="idiomas" label="Idioma"/>-->
              <q-select v-model="f.denunciantes[0].denunciante_idioma" dense outlined emit-value map-options clearable
                        :options="idiomas" label="Idioma"/>
            </div>

            <div class="col-6 col-md-3">
<!--              <q-toggle v-model="f.denunciante_trabaja" label="Trabaja"/>-->
              <q-toggle v-model="f.denunciantes[0].denunciante_trabaja" label="Trabaja"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciante_ocupacion" dense outlined clearable label="Ocupación"/>-->
              <q-input v-model="f.denunciantes[0].denunciante_ocupacion" dense outlined clearable label="Ocupación"/>
            </div>

            <div class="col-10">
<!--              <q-input v-model="f.denunciante_domicilio_actual" dense outlined clearable label="Domicilio actual"/>-->
              <q-input v-model="f.denunciantes[0].denunciante_domicilio_actual" dense outlined clearable label="Domicilio actual"/>
            </div>
            <div class="col-2">
<!--              <q-btn label="Buscar" @click="$refs.denMap?.geocodeAndFly(f.denunciante_domicilio_actual)"/>-->
              <q-btn label="Buscar" @click="$refs.denMap?.geocodeAndFly(f.denunciantes[0].denunciante_domicilio_actual)"/>
            </div>

            <div class="col-12">
              <div class="text-caption text-grey-7 q-mb-xs">Ubicación (denunciante)</div>
              <MapPicker
                v-model="denunciantePos"
                :center="oruroCenter"
                :address="f.denunciantes[0].denunciante_domicilio_actual"
                country="bo"
                ref="denMap"
              />
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Grupo Familiar -->
      <q-card flat bordered class="section-card">
        <q-expansion-item icon="diversity_3" dense-toggle expand-separator
                          label="2) Grupo Familiar" header-class="text-subtitle1">
          <q-card>
            <q-card-section>
              <q-btn label="Agregar familiar" color="primary" icon="add" @click="f.familiares.push({ nombre_completo: '', edad: null, parentesco: '', celular: '' })" class="q-mb-md"/>
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
                    <div class="col-12 col-md-2">
<!--                      <q-input v-model="fam.celular" dense outlined clearable label="Celular"/>-->
                      <q-input v-model="fam.familiar_celular" dense outlined clearable label="Celular"/>
                    </div>
<!--                    btn elimnar familiar-->
                    <div class="col-12 col-md-1">
                      <q-btn color="negative" icon="delete" dense flat @click="familiares.splice(index, 1)"/>
                    </div>
                  </div>
                </template>
            </q-card-section>
          </q-card>
        </q-expansion-item>
      </q-card>

      <!-- Denunciado -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="person_pin_circle" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">3) Datos del Denunciado</div>
        </q-card-section>
        <q-separator/>

        <q-card-section>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
<!--              $table->string('denunciado_nombres', 120)->nullable();-->
<!--              $table->string('denunciado_paterno', 80)->nullable();-->
<!--              $table->string('denunciado_materno', 80)->nullable();-->
<!--              $table->string('denunciado_documento', 40)->nullable();-->
<!--              $table->string('denunciado_nro', 30)->nullable();-->
<!--              $table->string('denunciado_sexo', 15)->nullable();-->
<!--              $table->string('denunciado_lugar_nacimiento', 120)->nullable();-->
<!--              $table->date('denunciado_fecha_nacimiento')->nullable();-->
<!--              $table->string('denunciado_edad')->nullable();-->
<!--              $table->string('denunciado_telefono')->nullable();-->
<!--              $table->string('denunciado_residencia', 120)->nullable();-->
<!--              $table->string('denunciado_estado_civil', 40)->nullable();-->
<!--              $table->string('denunciado_relacion', 60)->nullable();-->
<!--              $table->string('denunciado_grado', 60)->nullable();-->
<!--              $table->string('denunciado_trabaja', 10)->nullable();       // o boolean si sabes que es SI/NO-->
<!--              $table->string('denunciado_prox', 120)->nullable();-->
<!--              $table->string('denunciado_ocupacion', 80)->nullable();-->
<!--              $table->string('denunciado_ocupacion_exacto', 120)->nullable();-->
<!--              $table->string('denunciado_idioma', 60)->nullable();-->
<!--              $table->string('denunciado_fijo', 30)->nullable();-->
<!--              $table->string('denunciado_movil', 30)->nullable();-->
<!--              $table->text('denunciado_domicilio_actual')->nullable();    // TEXT-->
<!--              $table->decimal('denunciado_latitud', 10, 7)->nullable();-->
<!--              $table->decimal('denunciado_longitud', 10, 7)->nullable();-->
<!--              $table->unsignedBigInteger('caso_id');-->
<!--              $table->foreign('caso_id')->references('id')->on('casos');-->
<!--              <q-input v-model="f.denunciado_nombre_completo" dense outlined clearable label="Nombre completo"/>-->
              <q-input v-model="f.denunciados[0].denunciado_nombres" dense outlined clearable label="Nombres"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-select v-model="f.denunciado_documento" dense outlined emit-value map-options clearable-->
<!--                        :options="documentos" label="Documento"/>-->
              <q-select v-model="f.denunciados[0].denunciado_documento" dense outlined emit-value map-options clearable
                        :options="documentos" label="Documento"/>

            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciado_nro" dense outlined clearable label="Nro documento"/>-->
              <q-input v-model="f.denunciados[0].denunciado_nro" dense outlined clearable label="Nro documento"/>
            </div>

            <div class="col-6 col-md-3">
<!--              <q-select v-model="f.denunciado_sexo" dense outlined emit-value map-options clearable-->
<!--                        :options="sexos" label="Sexo"/>-->
              <q-select v-model="f.denunciados[0].denunciado_sexo" dense outlined emit-value map-options clearable
                        :options="sexos" label="Sexo"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciado_lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento"/>-->
              <q-input v-model="f.denunciados[0].denunciado_lugar_nacimiento" dense outlined clearable label="Lugar de nacimiento"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciado_fecha_nacimiento" type="date" dense outlined-->
<!--                       label="Fecha de nacimiento" @update:model-value="onBirthChange('denunciado')"/>-->
              <q-input v-model="f.denunciados[0].denunciado_fecha_nacimiento" type="date" dense outlined
                       label="Fecha de nacimiento" @update:model-value="onBirthChange('denunciado')"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model.number="f.denunciado_edad" dense outlined type="number" label="Edad"/>-->
              <q-input v-model.number="f.denunciados[0].denunciado_edad" dense outlined type="number" label="Edad"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciado_telefono" dense outlined clearable label="Teléfono/Celular"/>-->
              <q-input v-model="f.denunciados[0].denunciado_telefono" dense outlined clearable label="Teléfono/Celular"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciado_grado" dense outlined clearable label="Grado de instrucción"/>-->
              <q-input v-model="f.denunciados[0].denunciado_grado" dense outlined clearable label="Grado de instrucción"/>
            </div>
            <div class="col-6 col-md-3">
<!--              <q-input v-model="f.denunciado_residencia" dense outlined clearable label="Residencia"/>-->
              <q-input v-model="f.denunciados[0].denunciado_residencia" dense outlined clearable label="Residencia"/>
            </div>

            <div class="col-10">
<!--              <q-input v-model="f.denunciado_domicilio_actual" dense outlined clearable label="Domicilio actual"/>-->
              <q-input v-model="f.denunciados[0].denunciado_domicilio_actual" dense outlined clearable label="Domicilio actual"/>
            </div>
            <div class="col-2">
<!--              <q-btn label="Buscar" @click="$refs.denunMap?.geocodeAndFly(f.denunciado_domicilio_actual)"/>-->
              <q-btn label="Buscar" @click="$refs.denunMap?.geocodeAndFly(f.denunciados[0].denunciado_domicilio_actual)"/>
            </div>

            <div class="col-12">
              <div class="text-caption text-grey-7 q-mb-xs">Ubicación (denunciado)</div>
              <MapPicker v-model="denunciadoPos" :center="oruroCenter"
                         :address="f.denunciados[0].denunciado_domicilio_actual"
                         country="bo"
                         ref="denunMap"/>
            </div>
          </div>
        </q-card-section>
      </q-card>

      <!-- Hechos y Tipología -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="gavel" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">4) Hechos y Tipología</div>
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

            <div class="col-12 col-md-4">
              <q-input v-model="f.caso_tipologia" dense outlined clearable label="Tipología"/>
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

      <!-- Violencias -->
      <q-card flat bordered class="section-card">
        <q-card-section class="row items-center">
          <q-icon name="warning_amber" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">5) Tipos de violencia</div>
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
      <q-card flat bordered class="section-card q-mb-xl">
        <q-card-section class="row items-center">
          <q-icon name="task_alt" class="q-mr-sm"/>
          <div class="text-subtitle1 text-weight-medium">6) Seguimiento</div>
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
          <div class="text-subtitle1 text-weight-medium">7) Check documentos adjuntos</div>
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

      <!-- Acciones bottom -->
      <div class="text-right q-mb-lg">
        <q-btn flat color="primary" icon="history" label="Limpiar" @click="resetForm" class="q-mr-sm"/>
        <q-btn color="primary" icon="save" label="Guardar" :loading="loading" @click="save" v-if="!editable"/>
        <q-btn color="primary" icon="save" label="Actualizar" :loading="loading" @click="update" v-if="editable"/>
      </div>
    </q-form>
  </q-page>
</template>

<script>
import MapPicker from 'components/MapPicker.vue'
import moment from 'moment'
export default {
  name: 'SlimNuevo',
  components: { MapPicker },
  props: {
    showNumeroApoyoIntegral: { type: Boolean, default: false },
    casoId: { type: [Number, String], default: null },
    editable: { type: Boolean, default: false },
  },
  data () {
    return {
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
        { label: 'Libreta de servicio militar', value: 'Libreta de servicio militar' },
        { label: 'Licencia de conducir', value: 'Licencia de conducir' }
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
        { label: 'Castellano', value: 'Castellano' },
        { label: 'Quechua', value: 'Quechua' },
        { label: 'Aymara', value: 'Aymara' },
        { label: 'Guaraní', value: 'Guaraní' },
        { label: 'Otro', value: 'Otro' }
      ],
      oruroCenter: [-17.9667, -67.1167],

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
  mounted() {
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
        if (!this.f.denunciado || this.f.denunciado.length === 0) {
          this.f.denunciado = [
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
    this.$axios.get('/usuariosRole').then(res => {
      this.psicologos = res.data.psicologos
      this.abogados = res.data.abogados
      this.sociales = res.data.sociales
    }).catch(() => {
      this.$alert.error('No se pudo cargar los usuarios por rol')
    })

    // Web Speech API
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
  methods: {
    onBirthChange(kind) {
      const hoy = moment()
      const v = kind === 'denunciante' ? this.f.denunciantes[0].denunciante_fecha_nacimiento : this.f.denunciados[0].denunciado_fecha_nacimiento
      if(kind ==='denunciante'){
        // this.f.denunciante_edad = v ? hoy.diff(moment(v), 'years') : ''
        this.f.denunciantes[0].denunciante_edad = v ? hoy.diff(moment(v), 'years') : ''
      }else {
        // this.f.denunciado_edad = v ? hoy.diff(moment(v), 'years') : ''
        this.f.denunciados[0].denunciado_edad = v ? hoy.diff(moment(v), 'years') : ''
      }
    },
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
    req (v) { return !!v || 'Requerido' },
    resetForm () {
      const bools = ['denunciante_trabaja','violencia_fisica','violencia_psicologica','violencia_sexual','violencia_economica',
        'documento_fotocopia_carnet_denunciante','documento_fotocopia_carnet_denunciado',
        'documento_placas_fotograficas_domicilio_denunciante','documento_croquis_direccion_denunciado',
        'documento_placas_fotograficas_domicilio_denunciado','documento_ciudadania_digital'
      ]
      Object.keys(this.f).forEach(k => {
        if (bools.includes(k)) this.f[k] = false
        else if (k.includes('lat') || k.includes('long')) this.f[k] = null
        else this.f[k] = ''
      })
      this.f.area = 'SLIM'
      this.f.zona = 'CENTRAL'
      this.$q.notify({ type: 'info', message: 'Formulario reiniciado' })
    },
    async update() {
      if (this.showNumeroApoyoIntegral && !this.f.numero_apoyo_integral) {
        this.$alert.error('El número de apoyo integral es obligatorio')
        return
      }
      if (!this.f.denunciantes[0].denunciante_nombres) {
        this.$alert.error('El/Los nombre(s) del denunciante es obligatorio')
        return
      }
      this.loading = true
      try {
        await this.$axios.put(`/casos/${this.f.id}`, this.f)
        this.$alert.success('SLIM actualizado')
      } catch (e) {
        this.$alert.error(e?.response?.data?.message || 'No se pudo actualizar el SLIM')
      } finally {
        this.loading = false
      }
    },
    async save () {
      if (this.showNumeroApoyoIntegral && !this.f.numero_apoyo_integral) {
        this.$alert.error('El número de apoyo integral es obligatorio')
        return
      }
      if (!this.f.denunciantes[0].denunciante_nombres) {
        this.$alert.error('El/Los nombre(s) del denunciante es obligatorio')
        return
      }
      this.loading = true
      this.f.tipo = 'SLIM'
      try {
        const res = await this.$axios.post('/casos', this.f)
        this.$alert.success('SLIM creado')
        this.$router.push(`/casos/${res.data.id}`)
      } catch (e) {
        this.$alert.error(e?.response?.data?.message || 'No se pudo crear el SLIM')
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
.section-card {
  border-radius: 14px;
  overflow: hidden;
  margin-bottom: 16px;
  background: #fff;
}
</style>

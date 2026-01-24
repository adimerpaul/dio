<template>
  <q-page class="q-pa-md bg-grey-2">

    <!-- Header -->
    <div class="row items-center q-mb-md">
<!--      <a href="myscan://open">Escanear ahora</a>-->
      <div class="col-12 col-md-12">
        <div class="text-h6 text-weight-bold">
          {{caso?.tipo}} {{ caso?.caso_numero || '...' }}
<!--          ({{caso?.estado_caso}}) q-chip-->
          <q-chip
            v-if="caso?.estado_caso"
            :color="caso?.estado_caso === 'Apertura Denuncia' ? 'green' : caso?.estado_caso === 'Cerrado' ? 'red' : 'grey'"
            text-color="white"
            size="sm"
            class="q-ml-sm"
          >
            {{ caso?.estado_caso }}
          </q-chip>
<!--          <pre>{{ caso?.etapa_procesal }}</pre>-->
          <q-chip
            v-if="caso?.etapa_procesal"
            color="blue"
            text-color="white"
            size="sm"
            class="q-ml-sm"
          >
            {{ caso?.etapa_procesal }}
          </q-chip>
          <q-btn flat icon="arrow_back" @click="$router.back()" class="q-mr-sm"/>
        </div>
          <div class="text-subtitle2 text-black-7">
            <q-icon name="calendar_today" class="q-mr-sm"/>
            Abierto
            {{ formatYmdHis(caso?.fecha_apertura_caso)}}
            <span v-if="caso?.fecha_apertura_caso"> ({{cronometroDias}})</span>
            <br>
            <span class="text-black text-h4">
              {{ cronometroHorasDiasMesesAnios }}
            </span>
          </div>
<!--        <div class="text-caption text-grey-7">Detalle y gestión integral</div>-->
      </div>
      <div class="col-auto row q-gutter-sm">
        <q-btn-dropdown
          flat
          color="positive"
          icon="chat"
          label="WhatsApp"
          v-if="hasAnyWa"
        >
          <q-list>
            <q-item v-if="caso?.psicologica_user?.celular" clickable @click="sendWhatsApp('psico')">
              <q-item-section avatar><q-icon name="psychology"/></q-item-section>
              <q-item-section>Psicología ({{ caso.psicologica_user.name }})</q-item-section>
              <q-item-section side class="text-caption text-grey">{{ caso.psicologica_user.celular }}</q-item-section>
            </q-item>

            <q-item v-if="caso?.legal_user?.celular" clickable @click="sendWhatsApp('legal')">
              <q-item-section avatar><q-icon name="gavel"/></q-item-section>
              <q-item-section>Legal ({{ caso.legal_user.name }})</q-item-section>
              <q-item-section side class="text-caption text-grey">{{ caso.legal_user.celular }}</q-item-section>
            </q-item>

            <q-item v-if="caso?.trabajo_social_user?.celular" clickable @click="sendWhatsApp('social')">
              <q-item-section avatar><q-icon name="people"/></q-item-section>
              <q-item-section>Trabajo social ({{ caso.trabajo_social_user.name }})</q-item-section>
              <q-item-section side class="text-caption text-grey">{{ caso.trabajo_social_user.celular }}</q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
        <q-btn-dropdown flat color="secondary" icon="print" label="Imprimir PDF"
                        v-if="role === 'Administrador' || role === 'Auxiliar' || role === 'Asistente' || role === 'Abogado'"
        >
          <q-list>
            <q-item clickable @click="printPdf" v-close-popup >
              <q-item-section>Ficha del Caso</q-item-section>
            </q-item>
            <q-separator/>
            <q-item clickable @click="printPdfHojaRuta('denunciante')" v-close-popup >
              <q-item-section>Ubicacion (Denunciante)</q-item-section>
            </q-item>
            <q-item clickable @click="printPdfHojaRuta('denunciado')" v-close-popup >
              <q-item-section>Ubicacion (Denunciado)</q-item-section>
            </q-item>
<!--            print-registro-domiciliario-->
            <q-item clickable @click="printPdfHojaRuta('registro-domiciliario')" v-close-popup >
              <q-item-section>Registro Domiciliario</q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
        <q-space/>
        <q-btn flat color="primary" icon="refresh" @click="fetchCaso" :loading="loading"/>
        <q-btn flat color="negative" icon="delete" label="Eliminar Caso" @click="eliminarCaso()" v-if="role === 'Administrador'" no-caps/>
        <div v-if="role === 'Abogado' && caso?.legal_user_id === $store.user?.id && !caso?.fecha_aceptacion_area_legal">
          <q-btn color="red" icon="check_circle" label="Aceptar Caso Legal" @click="aceptarCasoLegal()" no-caps size="10px" :loading="loading"/>
        </div>
        <div v-if="role === 'Psicologo' && caso?.psicologica_user_id === $store.user?.id && !caso?.fecha_aceptacion_area_psicologica">
          <q-btn color="red" icon="check_circle" label="Aceptar Caso Psicológico" @click="aceptarCasoPsicolico()" no-caps size="10px" :loading="loading"/>
        </div>
        <div v-if="role === 'Social' && caso?.trabajo_social_user_id === $store.user?.id && !caso?.fecha_aceptacion_area_social">
          <q-btn color="red" icon="check_circle" label="Aceptar Caso Social" @click="aceptarCasoSocial()" no-caps size="10px" :loading="loading"/>
        </div>
<!--        'fecha_devolucion_area_psicologica',-->
<!--&lt;!&ndash;        'fecha_devolucion_area_social',&ndash;&gt; boton color verde-->
<!--        <pre>{{caso}}</pre>-->
        <div  v-if="role === 'Psicologo' && caso?.psicologica_user_id === $store.user?.id && caso?.fecha_devolucion_area_psicologica == null">
          <q-btn color="green" icon="check_circle" label="Devolucion Caso Psicológico" @click="devolverCasoPsicolico()" no-caps size="10px" :loading="loading"/>
        </div>
        <div  v-if="role === 'Social' && caso?.trabajo_social_user_id === $store.user?.id && caso?.fecha_devolucion_area_social == null">
          <q-btn color="green" icon="check_circle" label="Devolucion Caso Social" @click="devolverCasoSocial()" no-caps size="10px" :loading="loading"/>
        </div>
<!--        btn verificar antecedentes-->
        <div>
          <q-btn color="grey" icon="verified" label="Verificar Antecedentes" @click="antecendetesDenunciado" no-caps size="10px" :loading="loading"/>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <q-card flat bordered class="q-mb-md">
      <q-tabs v-model="tab" class="text-primary" dense align="left"
              active-color="primary" indicator-color="primary" outside-arrows mobile-arrows>
        <q-tab name="info"         label="1 Información General"   icon="dashboard"       no-caps/>
        <q-tab name="seguimiento"  label="2 Seguimiento"           icon="track_changes"   no-caps/>
        <template v-if="subtipo===undefined">
        <q-tab name="hoja"         label="3 Hoja de Ruta"          icon="report_problem"  no-caps />
          <q-tab name="psico"        label="4 Área Psicológico"      icon="psychology"      no-caps v-if="role === 'Administrador' || role === 'Psicologo'"/>
          <q-tab name="legal"        label="5 Área Legal"            icon="gavel"           no-caps v-if="role === 'Administrador' || role === 'Abogado'"/>
          <q-tab name="social"       label="6 Área Social"           icon="people"          no-caps v-if="role === 'Administrador' || role === 'Social'"/>
          <q-tab name="docs"         label="7 Documentos General"    icon="folder"          no-caps/>
          <q-tab name="fotos"        label="8 Fotografías"           icon="photo_library"   no-caps/>
        </template>
        <q-tab name="codigo"        label="9 Codigos"               icon="code"   no-caps/>
        <q-tab name="estados" label="10 Estado Caso"        icon="warning"         no-caps/>
      </q-tabs>
    </q-card>
<!--    <pre>{{role}}</pre>-->

    <q-tab-panels v-model="tab" animated keep-alive>
      <!-- 1) Información General -->
      <q-tab-panel name="info">
<!--        <SlimNuevo :casoId="caso.id" :showNumeroApoyoIntegral="caso?.numero_apoyo_integral" :editable="true" v-if="caso?.tipo==='SLIM'" @refresh="fetchCaso"/>-->
<!--        -->
<!--        <CasoNuevoDNA :casoId="caso.id" :showNumeroApoyoIntegral="caso?.numero_apoyo_integral" :editable="true" v-else-if="caso?.tipo==='DNA'" @refresh="fetchCaso"/>-->
<!--        <CasoNuevoSLAM :casoId="caso.id" :showNumeroApoyoIntegral="caso?.numero_apoyo_integral" :editable="true" v-else-if="caso?.tipo==='SLAM'" @refresh="fetchCaso"/>-->
<!--        <CasoNuevoUMADIS :casoId="caso.id" :showNumeroApoyoIntegral="caso?.numero_apoyo_integral" :editable="true" v-else-if="caso?.tipo==='UMADIS'" @refresh="fetchCaso"/>-->
<!--        <CasoNuevoPROPREMI :casoId="caso.id" :showNumeroApoyoIntegral="caso?.numero_apoyo_integral" :editable="true" v-else-if="caso?.tipo==='PROPREMI'" @refresh="fetchCaso"/>-->
        <CasoNuevo :casoId="caso.id" :showNumeroApoyoIntegral="caso?.numero_apoyo_integral" :accion="'modificar'" v-if="caso" @refresh="fetchCaso" :caso="caso" :tipo="caso?.tipo" :titulo="caso?.titulo"/>


<!--        <CasoNuevoDNA :casoId="caso.id" :showNumeroApoyoIntegral="caso?.numero_apoyo_integral" :editable="true" v-else-if="caso?.tipo==='DNA'" @refresh="fetchCaso"/>-->
<!--        <CasoNuevoSLAM :casoId="caso.id" :showNumeroApoyoIntegral="caso?.numero_apoyo_integral" :editable="true" v-else-if="caso?.tipo==='SLAM'" @refresh="fetchCaso"/>-->
<!--        <CasoNuevoUMADIS :casoId="caso.id" :showNumeroApoyoIntegral="caso?.numero_apoyo_integral" :editable="true" v-else-if="caso?.tipo==='UMADIS'" @refresh="fetchCaso"/>-->
<!--        <CasoNuevoPROPREMI :casoId="caso.id" :showNumeroApoyoIntegral="caso?.numero_apoyo_integral" :editable="true" v-else-if="caso?.tipo==='PROPREMI'" @refresh="fetchCaso"/>-->
      </q-tab-panel>
      <q-tab-panel name="seguimiento">
        <Seguimiento :caso="caso"/>
      </q-tab-panel>

      <!-- 2) Problemática -->
      <q-tab-panel name="problematica">
        <Problematica :case-id="caseId"/>
      </q-tab-panel>

      <q-tab-panel name="hoja">
        <HojaRuta :case-id="caseId" :caso ="caso" @refresh="fetchCaso"/>
      </q-tab-panel>

      <!-- 3) Sesiones Psicológico -->
      <q-tab-panel name="psico">
        <SesionesPsicologico :case-id="caseId" :caso="caso" @refresh="fetchCaso"/>
      </q-tab-panel>

      <!-- 4) Informes Legal -->
      <q-tab-panel name="legal">
        <InformesLegal :case-id="caseId" :caso="caso" @refresh="fetchCaso"/>
      </q-tab-panel>
<!--      <q-tab name="social"       label="6 Área Social"           icon="people"          no-caps v-if="role === 'Administrador' || role === 'Social'"/>-->
      <q-tab-panel name="social">
        <SocialInformes :caso="caso" @refresh="fetchCaso" :case-id="caseId"/>
      </q-tab-panel>

      <!-- 5) Apoyo Integral -->
      <q-tab-panel name="apoyo">
        <ApoyoIntegral :case-id="caseId"/>
      </q-tab-panel>

      <!-- 6) Documentos General -->
      <q-tab-panel name="docs">
        <DocumentosGeneral :case-id="caseId" :caso="caso" @refresh="fetchCaso"/>
      </q-tab-panel>

      <!-- 7) Fotografías -->
      <q-tab-panel name="fotos">
        <Fotografias :case-id="caseId" :caso="caso" @refresh="fetchCaso"/>
      </q-tab-panel>

      <!-- 8) Códigos -->
      <q-tab-panel name="codigo">
        <Codigo :case-id="caseId" :caso="caso" @refresh="fetchCaso"/>
      </q-tab-panel>
      <!-- 10 ) Estados del Caso -->
      <q-tab-panel name="estados">
        <EstadoCaso :case-id="caseId" :caso="caso" @refresh="fetchCaso"/>
      </q-tab-panel>
    </q-tab-panels>
    <q-dialog v-model="antecedentesDenunciadoDialog" >
      <q-card style="min-width: 70vw; max-width: 90vw;">
        <q-card-section class="row items-center bg-primary text-white">
          Antecedentes del Denunciado
<!--          btn imprmimir-->
          <q-space/>
          <q-btn icon="print" flat round dense @click="printantecedentesAll()"/>
          <q-btn icon="close" flat round dense @click="antecedentesDenunciadoDialog = false"/>
        </q-card-section>
<!--        <q-card-section>-->
<!--          <div class="text-h6">Antecedentes del Denunciado</div>-->
<!--        </q-card-section>-->
<!--        <q-separator/>-->
        <q-card-section>
<!--          <pre>{{antecedentesDenunciado}}</pre>-->
          <q-markup-table dense flat bordered>
            <thead>
              <tr>
<!--                <th>Opciones</th>-->
                <th>Caso ID</th>
                <th>Denunciado</th>
                <th>Tipologia</th>
                <th>Número de Caso</th>
                <th>Área</th>
                <th>Fecha de Hecho</th>
                <th>Descripción del Caso</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in antecedentesDenunciado" :key="item.id">
<!--                btn imprimir-->
                <td>{{item.id || '—'}}</td>
                <td>
<!--                  <pre>{{item}}</pre>-->

<!--                  {-->
<!--                  "id": 17,-->
<!--                  "area": "SLIM",-->
<!--                  "zona": "CENTRAL",-->
<!--                  "nurej": null,-->
<!--                  "numero_juzgado": null,-->
<!--                  "numero_juzgado_padre": null,-->
<!--                  "responsable_fiscalia": null,-->
<!--                  "estado_caso": null,-->
<!--                  "estado_caso_otro": null,-->
<!--                  "respaldo": null,-->
<!--                  "observaciones": null,-->
<!--                  "archivo_caso": null,-->
<!--                  "cud": null,-->
<!--                  "numero_apoyo_integral": null,-->
<!--                  "tipo": "SLIM",-->
<!--                  "principal": null,-->
<!--                  "caso_numero": "004/25",-->
<!--                  "caso_fecha_hecho": null,-->
<!--                  "caso_lugar_hecho": null,-->
<!--                  "caso_zona": null,-->
<!--                  "caso_direccion": null,-->
<!--                  "caso_descripcion": "en dia 5 de diciembre el sr. pedro abuso sexualmente de la sra monica , identificando violencia fisica",-->
<!--                  "caso_tipologia": "Tipología Múltiple",-->
<!--                  "caso_modalidad": null,-->
<!--                  "violencia_fisica": true,-->
<!--                  "violencia_psicologica": true,-->
<!--                  "violencia_sexual": true,-->
<!--                  "violencia_economica": false,-->
<!--                  "violencia_patrimonial": null,-->
<!--                  "violencia_simbolica": null,-->
<!--                  "violencia_institucional": null,-->
<!--                  "violencia_cibernetica": false,-->
<!--                  "psicologica_user_id": 7,-->
<!--                  "trabajo_social_user_id": 20,-->
<!--                  "legal_user_id": 8,-->
<!--                  "documento_fotocopia_carnet_denunciante": "0",-->
<!--                  "documento_fotocopia_carnet_denunciado": "0",-->
<!--                  "documento_placas_fotograficas_domicilio_denunciante": "0",-->
<!--                  "documento_croquis_direccion_denunciado": "0",-->
<!--                  "documento_croquis_direccion_denunciante": "0",-->
<!--                  "documento_placas_fotograficas_domicilio_denunciado": "0",-->
<!--                  "documento_ciudadania_digital": "0",-->
<!--                  "documento_otros": null,-->
<!--                  "documento_otros_detalle": null,-->
<!--                  "documento_certificado_nacimiento": "0",-->
<!--                  "documento_certificado_matrimonio": "0",-->
<!--                  "documento_tres_testigos": "0",-->
<!--                  "documento_contrato_pago": "0",-->
<!--                  "documento_libreta_notas": "0",-->
<!--                  "documento_numero_cuenta": null,-->
<!--                  "documento_fotocopia_ci_victima": "0",-->
<!--                  "documento_fotocopia_ci_denunciante": "0",-->
<!--                  "documento_nota_director": "0",-->
<!--                  "documento_nota_distrital": "0",-->
<!--                  "documento_nota_defensor_pueblo": "0",-->
<!--                  "documento_diploma_bachiller": "0",-->
<!--                  "documento_comprobante_universidades": "0",-->
<!--                  "documento_fotocopia_ci_padres": "0",-->
<!--                  "fecha_apertura_caso": "2025-12-05 07:11:17",-->
<!--                  "fecha_derivacion_psicologica": null,-->
<!--                  "fecha_informe_area_social": null,-->
<!--                  "fecha_informe_area_psicologica": null,-->
<!--                  "fecha_informe_trabajo_social": null,-->
<!--                  "fecha_derivacion_area_legal": "2025-12-05 07:11:17",-->
<!--                  "fecha_derivacion_area_psicologica": "2025-12-05 07:11:17",-->
<!--                  "fecha_aceptacion_area_psicologica": null,-->
<!--                  "fecha_derivacion_area_social": "2025-12-05 07:11:17",-->
<!--                  "fecha_aceptacion_area_social": null,-->
<!--                  "user_id": 6,-->
<!--                  "documento_persona_fisica": "0",-->
<!--                  "documento_carnet_discapacidad": "0",-->
<!--                  "documento_carnet_padres": "0",-->
<!--                  "documento_certificado_medico": "0",-->
<!--                  "documento_papeleta_luz_agua": "0",-->
<!--                  "fecha_aceptacion_area_legal": "2026-01-18 17:37:40",-->
<!--                  "ingreso_economico": null,-->
<!--                  "violencia_otros": false,-->
<!--                  "violencia_abandono": false,-->
<!--                  "codigo_file": null,-->
<!--                  "titulo": "Registrar Nuevo Caso Hechos de Fragancia",-->
<!--                  "etapa_procesal": null,-->
<!--                  "etapa": null,-->
<!--                  "fecha_devolucion_area_psicologica": null,-->
<!--                  "fecha_devolucion_area_social": null,-->
<!--                  "denunciados": [-->
<!--                  {-->
<!--                  "id": 75,-->
<!--                  "denunciado_nombres": "PEDRO VILLALOBOS",-->
<!--                  "denunciado_paterno": null,-->
<!--                  "denunciado_materno": null,-->
<!--                  "denunciado_documento": "Carnet de identidad",-->
<!--                  "denunciado_documento_otro": null,-->
<!--                  "denunciado_nro": "12345678",-->
<!--                  "denunciado_sexo": null,-->
<!--                  "denunciado_lugar_nacimiento": null,-->
<!--                  "denunciado_fecha_nacimiento": null,-->
<!--                  "denunciado_edad": null,-->
<!--                  "denunciado_telefono": null,-->
<!--                  "denunciado_residencia": null,-->
<!--                  "denunciado_estado_civil": null,-->
<!--                  "denunciado_relacion": null,-->
<!--                  "denunciado_grado": null,-->
<!--                  "denunciado_trabaja": null,-->
<!--                  "denunciado_prox": null,-->
<!--                  "denunciado_ocupacion": null,-->
<!--                  "denunciado_ocupacion_exacto": null,-->
<!--                  "denunciado_idioma": null,-->
<!--                  "denunciado_fijo": null,-->
<!--                  "denunciado_movil": null,-->
<!--                  "denunciado_domicilio_actual": null,-->
<!--                  "denunciado_ingresos": null,-->
<!--                  "denunciado_relacion_victima": null,-->
<!--                  "denunciado_relacion_denunciado": null,-->
<!--                  "denunciado_latitud": null,-->
<!--                  "denunciado_longitud": null,-->
<!--                  "denunciado_cargo": null,-->
<!--                  "caso_id": 17-->
<!--                  }-->
<!--                  ]-->
<!--                  }-->
                  <div v-for="denunciado in item.denunciados" :key="denunciado.id">
                    {{denunciado.denunciado_nombres}} - {{denunciado.denunciado_nro}}
                  </div>
                </td>
                <td>{{item.caso_tipologia || '—'}}</td>
                <td>{{item.caso_numero || '—'}}</td>
                <td>{{item.area || '—'}}</td>
                <td>{{(item.caso_fecha_hecho) || '—'}}</td>
                <td>{{item.caso_descripcion || '—'}}</td>
              </tr>
            </tbody>
          </q-markup-table>
<!--          <pre>{{antecedentesDenunciado}}</pre>-->
<!--          [-->
<!--          {-->
<!--          "id": 17,-->
<!--          "area": "SLIM",-->
<!--          "zona": "CENTRAL",-->
<!--          "nurej": null,-->
<!--          "numero_juzgado": null,-->
<!--          "numero_juzgado_padre": null,-->
<!--          "responsable_fiscalia": null,-->
<!--          "estado_caso": null,-->
<!--          "estado_caso_otro": null,-->
<!--          "respaldo": null,-->
<!--          "observaciones": null,-->
<!--          "archivo_caso": null,-->
<!--          "cud": null,-->
<!--          "numero_apoyo_integral": null,-->
<!--          "tipo": "SLIM",-->
<!--          "principal": null,-->
<!--          "caso_numero": "004/25",-->
<!--          "caso_fecha_hecho": null,-->
<!--          "caso_lugar_hecho": null,-->
<!--          "caso_zona": null,-->
<!--          "caso_direccion": null,-->
<!--          "caso_descripcion": "en dia 5 de diciembre el sr. pedro abuso sexualmente de la sra monica , identificando violencia fisica",-->
<!--          "caso_tipologia": "Tipología Múltiple",-->
<!--          "caso_modalidad": null,-->
<!--          "violencia_fisica": true,-->
<!--          "violencia_psicologica": true,-->
<!--          "violencia_sexual": true,-->
<!--          "violencia_economica": false,-->
<!--          "violencia_patrimonial": null,-->
<!--          "violencia_simbolica": null,-->
<!--          "violencia_institucional": null,-->
<!--          "violencia_cibernetica": false,-->
<!--          "psicologica_user_id": 7,-->
<!--          "trabajo_social_user_id": 20,-->
<!--          "legal_user_id": 8,-->
<!--          "documento_fotocopia_carnet_denunciante": "0",-->
<!--          "documento_fotocopia_carnet_denunciado": "0",-->
<!--          "documento_placas_fotograficas_domicilio_denunciante": "0",-->
<!--          "documento_croquis_direccion_denunciado": "0",-->
<!--          "documento_croquis_direccion_denunciante": "0",-->
<!--          "documento_placas_fotograficas_domicilio_denunciado": "0",-->
<!--          "documento_ciudadania_digital": "0",-->
<!--          "documento_otros": null,-->
<!--          "documento_otros_detalle": null,-->
<!--          "documento_certificado_nacimiento": "0",-->
<!--          "documento_certificado_matrimonio": "0",-->
<!--          "documento_tres_testigos": "0",-->
<!--          "documento_contrato_pago": "0",-->
<!--          "documento_libreta_notas": "0",-->
<!--          "documento_numero_cuenta": null,-->
<!--          "documento_fotocopia_ci_victima": "0",-->
<!--          "documento_fotocopia_ci_denunciante": "0",-->
<!--          "documento_nota_director": "0",-->
<!--          "documento_nota_distrital": "0",-->
<!--          "documento_nota_defensor_pueblo": "0",-->
<!--          "documento_diploma_bachiller": "0",-->
<!--          "documento_comprobante_universidades": "0",-->
<!--          "documento_fotocopia_ci_padres": "0",-->
<!--          "fecha_apertura_caso": "2025-12-05 07:11:17",-->
<!--          "fecha_derivacion_psicologica": null,-->
<!--          "fecha_informe_area_social": null,-->
<!--          "fecha_informe_area_psicologica": null,-->
<!--          "fecha_informe_trabajo_social": null,-->
<!--          "fecha_derivacion_area_legal": "2025-12-05 07:11:17",-->
<!--          "fecha_derivacion_area_psicologica": "2025-12-05 07:11:17",-->
<!--          "fecha_aceptacion_area_psicologica": null,-->
<!--          "fecha_derivacion_area_social": "2025-12-05 07:11:17",-->
<!--          "fecha_aceptacion_area_social": null,-->
<!--          "user_id": 6,-->
<!--          "documento_persona_fisica": "0",-->
<!--          "documento_carnet_discapacidad": "0",-->
<!--          "documento_carnet_padres": "0",-->
<!--          "documento_certificado_medico": "0",-->
<!--          "documento_papeleta_luz_agua": "0",-->
<!--          "fecha_aceptacion_area_legal": "2026-01-18 17:37:40",-->
<!--          "ingreso_economico": null,-->
<!--          "violencia_otros": false,-->
<!--          "violencia_abandono": false,-->
<!--          "codigo_file": null,-->
<!--          "titulo": "Registrar Nuevo Caso Hechos de Fragancia",-->
<!--          "etapa_procesal": null,-->
<!--          "etapa": null,-->
<!--          "fecha_devolucion_area_psicologica": null,-->
<!--          "fecha_devolucion_area_social": null,-->
<!--          "denunciados": [-->
<!--          {-->
<!--          "id": 75,-->
<!--          "denunciado_nombres": "PEDRO VILLALOBOS",-->
<!--          "denunciado_paterno": null,-->
<!--          "denunciado_materno": null,-->
<!--          "denunciado_documento": "Carnet de identidad",-->
<!--          "denunciado_documento_otro": null,-->
<!--          "denunciado_nro": "12345678",-->
<!--          "denunciado_sexo": null,-->
<!--          "denunciado_lugar_nacimiento": null,-->
<!--          "denunciado_fecha_nacimiento": null,-->
<!--          "denunciado_edad": null,-->
<!--          "denunciado_telefono": null,-->
<!--          "denunciado_residencia": null,-->
<!--          "denunciado_estado_civil": null,-->
<!--          "denunciado_relacion": null,-->
<!--          "denunciado_grado": null,-->
<!--          "denunciado_trabaja": null,-->
<!--          "denunciado_prox": null,-->
<!--          "denunciado_ocupacion": null,-->
<!--          "denunciado_ocupacion_exacto": null,-->
<!--          "denunciado_idioma": null,-->
<!--          "denunciado_fijo": null,-->
<!--          "denunciado_movil": null,-->
<!--          "denunciado_domicilio_actual": null,-->
<!--          "denunciado_ingresos": null,-->
<!--          "denunciado_relacion_victima": null,-->
<!--          "denunciado_relacion_denunciado": null,-->
<!--          "denunciado_latitud": null,-->
<!--          "denunciado_longitud": null,-->
<!--          "denunciado_cargo": null,-->
<!--          "caso_id": 17-->
<!--          }-->
<!--          ]-->
<!--          },-->
<!--          {-->
<!--          "id": 4,-->
<!--          "area": "SLIM",-->
<!--          "zona": "CENTRAL",-->
<!--          "nurej": "7373782829",-->
<!--          "numero_juzgado": "JUZGADO DE LA NIÑEZ Y ADOLESCENCIA N° 2",-->
<!--          "numero_juzgado_padre": "Juzgado Tribunal Sala",-->
<!--          "responsable_fiscalia": "Abg. Juan Perez",-->
<!--          "estado_caso": "Archivado",-->
<!--          "estado_caso_otro": null,-->
<!--          "respaldo": "/storage/caso_respaldo/user_1/1763028961_Criterios diagnósticos  TCA 2025.pptx",-->
<!--          "observaciones": "el estado se encuentra en apertura en juzgado",-->
<!--          "archivo_caso": null,-->
<!--          "cud": "7272883993",-->
<!--          "numero_apoyo_integral": null,-->
<!--          "tipo": "SLIM",-->
<!--          "principal": null,-->
<!--          "caso_numero": "001/25",-->
<!--          "caso_fecha_hecho": "2025-03-03",-->
<!--          "caso_lugar_hecho": "casa amigo",-->
<!--          "caso_zona": null,-->
<!--          "caso_direccion": null,-->
<!--          "caso_descripcion": "En sabado 3 de marzo la señorita fue a la avenida civica",-->
<!--          "caso_tipologia": "Violencia Sexual",-->
<!--          "caso_modalidad": null,-->
<!--          "violencia_fisica": false,-->
<!--          "violencia_psicologica": false,-->
<!--          "violencia_sexual": true,-->
<!--          "violencia_economica": false,-->
<!--          "violencia_patrimonial": null,-->
<!--          "violencia_simbolica": null,-->
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
// Componentes de tabs
import InfoGeneral       from './tabs/InfoGeneral.vue'
import Problematica      from './tabs/Problematica.vue'
import SesionesPsicologico from './tabs/SesionesPsicologico.vue'
import InformesLegal     from './tabs/InformesLegal.vue'
import ApoyoIntegral     from './tabs/ApoyoIntegral.vue'
import DocumentosGeneral from './tabs/DocumentosGeneral.vue'
import Fotografias       from './tabs/Fotografias.vue'
import HojaRuta from "pages/casos/tabs/HojaRuta.vue";
import Seguimiento from "pages/casos/tabs/Seguimiento.vue";
import SlimNuevo from "pages/slims/SlimNuevo.vue";
import SocialInformes from "pages/casos/tabs/Social.vue";
import CasoNuevoDNA from "pages/dnas/DnaNuevo.vue";
// import CasoNuevoSLAM from "pages/slams/SlamNuevo.vue";
// import CasoNuevoUMADIS from "pages/umadis/UmadisNuevo.vue";
import CasoNuevoPROPREMI from "pages/propremis/PropremisNuevo.vue";
import Codigo from "pages/casos/tabs/Codigo.vue";
import moment from "moment";
import CasoNuevo from "pages/casos/CasoNuevo.vue";
import EstadoCaso from "pages/casos/tabs/EstadoCaso.vue";

import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'
export default {
  name: 'CasoDetalle',
  components: {
    EstadoCaso,
    CasoNuevo,
    Codigo,
    CasoNuevoPROPREMI,
    // CasoNuevoUMADIS,
    // CasoNuevoSLAM,
    CasoNuevoDNA,
    SocialInformes,
    SlimNuevo,
    Seguimiento,
    HojaRuta,
    InfoGeneral, Problematica, SesionesPsicologico, InformesLegal, ApoyoIntegral, DocumentosGeneral, Fotografias
  },
  data () {
    return {
      loading: false,
      tab: 'info',
      caso: null,
      defaultCountryCallingCode: '591',
      now: moment(),
      cronometroTimer: null,
      antecedentesDenunciado: [],
      antecedentesDenunciadoDialog : false,
    }
  },
  computed: {
    caseId () { return this.$route.params.id },
    subtipo () { return this.$route.params.subtipo },
    role () { return this.$store.user?.role || '' },
    hasAnyWa () {
      const c = this.caso || {}
      return !!(c?.psicologica_user?.celular || c?.legal_user?.celular || c?.trabajo_social_user?.celular)
    },
    cronometroYmdHis() {
      if (!this.caso?.fecha_apertura_caso) return '—'
      const duracion = moment.duration(this.now.diff(moment(this.caso.fecha_apertura_caso)))
      const days = Math.floor(duracion.asDays())
      const hours = duracion.hours()
      const minutes = duracion.minutes()
      const seconds = duracion.seconds()
      return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
    },
    cronometroDias() {
      if (!this.caso?.fecha_apertura_caso) return '—'
      const duracion = moment.duration(this.now.diff(moment(this.caso.fecha_apertura_caso)))
      const years = duracion.years()
      const months = duracion.months()
      const days = duracion.days()
      const hours = duracion.hours()
      const minutes = duracion.minutes()
      const seconds = duracion.seconds()

      let parts = []
      if (years) parts.push(`${years} año${years !== 1 ? 's' : ''}`)
      if (months) parts.push(`${months} mes${months !== 1 ? 'es' : ''}`)
      if (days) parts.push(`${days} día${days !== 1 ? 's' : ''}`)
      // if (hours) parts.push(`${hours} hora${hours !== 1 ? 's' : ''}`)
      // if (minutes) parts.push(`${minutes} minuto${minutes !== 1 ? 's' : ''}`)
      // if (seconds) parts.push(`${seconds} segundo${seconds !== 1 ? 's' : ''}`)

      return parts.join(', ')
    },
    cronometroHorasDiasMesesAnios() {
      if (!this.caso?.fecha_apertura_caso) return '—'
      const duracion = moment.duration(this.now.diff(moment(this.caso.fecha_apertura_caso)))
      const years = duracion.years()
      const months = duracion.months()
      const days = duracion.days()
      const hours = duracion.hours()
      const minutes = duracion.minutes()
      const seconds = duracion.seconds()

      let parts = []
      // if (years) parts.push(`${years} año${years !== 1 ? 's' : ''}`)
      // if (months) parts.push(`${months} mes${months !== 1 ? 'es' : ''}`)
      // if (days) parts.push(`${days} día${days !== 1 ? 's' : ''}`)
      if (hours) parts.push(`${hours}:${hours !== 1 ? '' : ''}`)
      if (minutes) parts.push(`${minutes}:${minutes !== 1 ? '' : ''}`)
      if (seconds) parts.push(`${seconds}${seconds !== 1 ? '' : ''}`)
      return parts.join('')
    }
  },
  created () {
    this.fetchCaso()
    this.cronometroTimer = setInterval(() => {
      this.now = moment();
    }, 1000);
  },
  beforeUnmount() {
    if (this.cronometroTimer) clearInterval(this.cronometroTimer);
  },
  methods: {
    printantecedentesAll () {
      try {
        const doc = new jsPDF()
        const title = `Antecedentes del Denunciado - Caso ID ${this.caseId}`
        doc.setFontSize(16)
        doc.text(title, 14, 20)
        const columns = [
          { header: 'Caso ID', dataKey: 'id' },
          { header: 'Denunciado', dataKey: 'denunciados' },
          { header: 'Número de Caso', dataKey: 'caso_numero' },
          { header: 'Área', dataKey: 'area' },
          { header: 'Fecha de Hecho', dataKey: 'caso_fecha_hecho' },
          { header: 'Descripción del Caso', dataKey: 'caso_descripcion' },
        ]
        const rows = this.antecedentesDenunciado.map(item => ({
          id: item.id || '—',
          denunciados: item.denunciados.map(d => d.denunciado_nombres + ' - ' + d.denunciado_nro).join(', '),
          caso_numero: item.caso_numero || '—',
          area: item.area || '—',
          caso_fecha_hecho: item.caso_fecha_hecho || '—',
          caso_descripcion: item.caso_descripcion || '—',
        }))
        autoTable(doc, {
          startY: 30,
          head: [columns.map(col => col.header)],
          body: rows.map(row => columns.map(col => row[col.dataKey])),
          styles: { fontSize: 8 },
          headStyles: { fillColor: [41, 128, 185], textColor: 255 },
          margin: { top: 30 },
        })
        // Abrir para imprimir / guardar
        doc.output('dataurlnewwindow') // abre en nueva pestaña
        // doc.save(`antecedentes_${this.caseId}.pdf`) // si prefieres descargar
      } catch (e) {
        console.error(e)
        this.$q.notify({ type: 'negative', message: 'No se pudo generar el PDF de antecedentes' })
      }
    },
    formatYmdHis(ymdhis) {
      return moment(ymdhis).format('DD/MM/YYYY HH:mm:ss');
    },
    devolverCasoPsicolico(){
      this.$q.dialog({
        title: 'Confirmar devolución',
        html: true,
        message: 'Confirma que deseas <b>DEVOLVER </b> el CASO: <b>' + (this.caso?.tipo || '')+' '+(this.caso?.caso_numero || '') + '</b><br><br>'+
          '<span style="font-weight: bold">Denunciante: </span>' +(this.caso?.denunciantes[0]?.denunciante_nombres || '') + ' ' + (this.caso?.denunciantes[0]?.denunciante_paterno || '') + ' ' + (this.caso?.denunciantes[0]?.denunciante_materno || '') +'<br>'+
          '<span style="font-weight: bold">Tipologia: </span>' + (this.caso?.caso_tipologia || '') +
          '',
        persistent: true,
        ok: { label: 'Devolver Caso', color: 'positive' },
        cancel: { label: 'Cancelar', color: 'red' }
      }).onOk(async () => {
        try {
          await this.$axios.post(`/casos/${this.caseId}/devolver-psicologico`);
          this.$q.notify({ type: 'positive', message: 'Caso psicológico devuelto exitosamente.' });
          this.fetchCaso();
        } catch (e) {
          this.$alert.error(e?.response?.data?.message || 'No se pudo devolver el caso psicológico');
        }
      });
    },
    antecendetesDenunciado(){
      this.loading = true;
      this.$axios.get(`/casos/${this.caseId}/antecedentes-denunciado`).then((res)=>{
        console.log(res.data);
        this.antecedentesDenunciado = res.data;
        this.antecedentesDenunciadoDialog = true;
      }).catch((e)=>{
        this.$alert.error(e?.response?.data?.message || 'No se pudo obtener los antecedentes del denunciado');
      }).finally(()=>{
        this.loading = false;
      });
    },
    devolverCasoSocial(){
      this.$q.dialog({
        title: 'Confirmar devolución',
        html: true,
        message: 'Confirma que deseas <b>DEVOLVER </b> el CASO: <b>' + (this.caso?.tipo || '')+' '+(this.caso?.caso_numero || '') + '</b><br><br>'+
          '<span style="font-weight: bold">Denunciante: </span>' +(this.caso?.denunciantes[0]?.denunciante_nombres || '') + ' ' + (this.caso?.denunciantes[0]?.denunciante_paterno || '') + ' ' + (this.caso?.denunciantes[0]?.denunciante_materno || '') +'<br>'+
          '<span style="font-weight: bold">Tipologia: </span>' + (this.caso?.caso_tipologia || '') +
          '',
        persistent: true,
        ok: { label: 'Devolver Caso', color: 'positive' },
        cancel: { label: 'Cancelar', color: 'red' }
      }).onOk(async () => {
        try {
          await this.$axios.post(`/casos/${this.caseId}/devolver-social`);
          this.$q.notify({ type: 'positive', message: 'Caso social devuelto exitosamente.' });
          this.fetchCaso();
        } catch (e) {
          this.$alert.error(e?.response?.data?.message || 'No se pudo devolver el caso social');
        }
      });
    },
    aceptarCasoSocial(){
      this.$q.dialog({
        title: 'Confirmar aceptación',
        html: true,
        message: 'Confirma que deseas <b>ACEPTAR </b> el CASO: <b>' + (this.caso?.tipo || '')+' '+(this.caso?.caso_numero || '') + '</b><br><br>'+
          '<span style="font-weight: bold">Denunciante: </span>' +(this.caso?.denunciantes[0]?.denunciante_nombres || '') + ' ' + (this.caso?.denunciantes[0]?.denunciante_paterno || '') + ' ' + (this.caso?.denunciantes[0]?.denunciante_materno || '') +'<br>'+
          '<span style="font-weight: bold">Tipologia: </span>' + (this.caso?.caso_tipologia || '') +
          '',
        persistent: true,
        ok: { label: 'Aceptar Caso', color: 'positive' },
        cancel: { label: 'Cancelar', color: 'red' }
      }).onOk(async () => {
        try {
          await this.$axios.post(`/casos/${this.caseId}/aceptar-social`);
          this.$q.notify({ type: 'positive', message: 'Caso social aceptado exitosamente.' });
          this.fetchCaso();
          const { data } = await this.$axios.get('/casos-pendientes-resumen')
          this.$store.pending = data
        } catch (e) {
          this.$alert.error(e?.response?.data?.message || 'No se pudo aceptar el caso social');
        }
      });
    },
    aceptarCasoPsicolico(){
      this.$q.dialog({
        title: 'Confirmar aceptación',
        html: true,
        message: 'Confirma que deseas <b>ACEPTAR </b> el CASO: <b>' + (this.caso?.tipo || '')+' '+(this.caso?.caso_numero || '') + '</b><br><br>'+
          '<span style="font-weight: bold">Denunciante: </span>' +(this.caso?.denunciantes[0]?.denunciante_nombres || '') + ' ' + (this.caso?.denunciantes[0]?.denunciante_paterno || '') + ' ' + (this.caso?.denunciantes[0]?.denunciante_materno || '') +'<br>'+
          '<span style="font-weight: bold">Tipologia: </span>' + (this.caso?.caso_tipologia || '') +
          '',
        persistent: true,
        ok: { label: 'Aceptar Caso', color: 'positive' },
        cancel: { label: 'Cancelar', color: 'red' }
      }).onOk(async () => {
        try {
          await this.$axios.post(`/casos/${this.caseId}/aceptar-psicologico`);
          this.$q.notify({ type: 'positive', message: 'Caso psicológico aceptado exitosamente.' });
          this.fetchCaso();
          const { data } = await this.$axios.get('/casos-pendientes-resumen')
          this.$store.pending = data
        } catch (e) {
          this.$alert.error(e?.response?.data?.message || 'No se pudo aceptar el caso psicológico');
        }
      });
    },
    aceptarCasoLegal(){
      this.$q.dialog({
        title: 'Confirmar aceptación',
        html: true,
        message: 'Confirma que deseas <b>ACEPTAR </b> el CASO: <b>' + (this.caso?.tipo || '')+' '+(this.caso?.caso_numero || '') + '</b><br><br>'+
          '<span style="font-weight: bold">Denunciante: </span>' +(this.caso?.denunciantes[0]?.denunciante_nombres || '') + ' ' + (this.caso?.denunciantes[0]?.denunciante_paterno || '') + ' ' + (this.caso?.denunciantes[0]?.denunciante_materno || '') +'<br>'+
          '<span style="font-weight: bold">Tipologia: </span>' + (this.caso?.caso_tipologia || '') +
          '',
        persistent: true,
        ok: { label: 'Aceptar Caso', color: 'positive' },
        cancel: { label: 'Cancelar', color: 'red' }
      }).onOk(async () => {
        try {
          await this.$axios.post(`/casos/${this.caseId}/aceptar-legal`);
          this.$q.notify({ type: 'positive', message: 'Caso legal aceptado exitosamente.' });
          this.fetchCaso();
          const { data } = await this.$axios.get('/casos-pendientes-resumen')
          this.$store.pending = data
        } catch (e) {
          this.$alert.error(e?.response?.data?.message || 'No se pudo aceptar el caso legal');
        }
      });
    },
    eliminarCaso() {
      this.$q.dialog({
        title: 'Confirmar eliminación',
        message: '¿Estás seguro de que deseas eliminar este caso? Esta acción no se puede deshacer.',
        cancel: true,
        persistent: true
      }).onOk(async () => {
        try {
          await this.$axios.delete(`/casos/${this.caseId}`);
          this.$q.notify({ type: 'positive', message: 'Caso eliminado exitosamente.' });
          this.$router.back(); // Volver a la página anterior
        } catch (e) {
          this.$alert.error(e?.response?.data?.message || 'No se pudo eliminar el caso');
        }
      });
    },
    normalizePhone (raw) {
      if (!raw) return ''
      // Solo dígitos
      let digits = String(raw).replace(/\D+/g, '')
      // Si ya viene con 11-12 dígitos (ej. 5917xxxxxxx), lo respetamos
      if (/^(\d{11,13})$/.test(digits)) return digits
      // Si es un número local de 8 dígitos (BO), anteponer código país
      if (digits.length === 8) return this.defaultCountryCallingCode + digits
      // Si trae 9 dígitos (a veces 7 + prefijo), también anteponer
      if (digits.length === 9 && !digits.startsWith(this.defaultCountryCallingCode)) {
        return this.defaultCountryCallingCode + digits
      }
      return digits
    },

    waUrl (phone, text) {
      const p = this.normalizePhone(phone)
      const msg = encodeURIComponent(text || '')
      return `https://wa.me/${p}${msg ? `?text=${msg}` : ''}`
    },

    waMessage (roleKey) {
      // Mensaje base (edítalo a tu gusto)
      const c = this.caso || {}
      const num = c.caso_numero ? c.caso_numero.replace(/\\\//g, '/') : `#${this.caseId}`
      const link = (this.$axios?.defaults?.baseURL || '') + `/casos/${this.caseId}`

      const rolNombre = roleKey === 'psico'
        ? 'Psicología'
        : roleKey === 'legal'
          ? 'Legal'
          : 'Trabajo Social'

      return [
        `*SLIM - Notificación de Caso*`,
        `Nro: ${num}`,
        `Área: ${rolNombre}`,
        `Denunciante: ${c.denunciante_nombre_completo || '—'}`,
        c.caso_tipologia ? `Tipología: ${c.caso_tipologia}` : null,
        c.caso_modalidad ? `Modalidad: ${c.caso_modalidad}` : null,
        c.caso_fecha_hecho ? `Fecha del hecho: ${c.caso_fecha_hecho}` : null,
        '',
        `Ver caso: ${link}`
      ].filter(Boolean).join('\n')
    },

    sendWhatsApp (roleKey) {
      const c = this.caso || {}
      const user =
        roleKey === 'psico'  ? c.psicologica_user :
          roleKey === 'legal'  ? c.legal_user :
            roleKey === 'social' ? c.trabajo_social_user : null

      if (!user?.celular) {
        this.$q.notify({ type:'warning', message: 'No hay número de celular configurado para ese responsable' })
        return
      }

      const url = this.waUrl(user.celular, this.waMessage(roleKey))
      window.open(url, '_blank')
    },
    async fetchCaso () {
      this.loading = true
      try {
        const res = await this.$axios.get(`/casos/${this.caseId}`)
        this.caso = res.data
      } catch (e) {
        this.$alert.error(e?.response?.data?.message || 'No se pudo cargar el caso')
      } finally {
        this.loading = false
      }
    },
    printPdf () {
      const url = this.$axios.defaults.baseURL + `/casos/${this.caseId}/pdf`
      window.open(url, '_blank')
    },
    printPdfHojaRuta (tipo = 'denunciante') {
      if (tipo === 'registro-domiciliario') {
        // Route::get('/casos/{caso}/print-registro-domiciliario', [CasoController::class, 'printRegistroDomiciliario']);
        const url = this.$axios.defaults.baseURL + `/casos/${this.caseId}/print-registro-domiciliario`
        window.open(url, '_blank')
        return
      }
      const url = this.$axios.defaults.baseURL + `/casos/${this.caseId}/pdf/hoja-ruta?tipo=${tipo}`
      window.open(url, '_blank')
    },
  }
}
</script>

<style scoped>
.bg-grey-2 { min-height: 100%; }
</style>

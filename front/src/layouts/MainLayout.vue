<template>
  <q-layout view="lHh Lpr lFf">
    <!-- HEADER -->
    <q-header class="bg-white text-black" bordered>
      <q-toolbar>
        <q-btn
          flat color="primary"
          :icon="leftDrawerOpen ? 'keyboard_double_arrow_left' : 'keyboard_double_arrow_right'"
          aria-label="Menú" @click="toggleLeftDrawer" unelevated dense
        />

        <div class="row items-center q-gutter-sm">
          <div class="text-subtitle1 text-weight-medium" style="line-height:.95">
            Panel <br>
            <q-chip v-if="$store.user?.area" color="grey-3" text-color="black" size="10px" dense>
              {{ $store.user?.area }}
            </q-chip>
            <q-chip v-if="$store.user?.zona" color="grey-3" text-color="black" size="10px" dense>
              {{ $store.user?.zona }}
            </q-chip>
          </div>
        </div>

        <q-space />

        <!-- Perfil -->
        <div class="row flex-center">
          <!-- Badge de pendientes -->
<!--          <pre>{{pendingCount}}</pre>-->
          <q-btn
            flat round dense
            :icon="pendingCount > 0 ? 'notifications_active' : 'notifications_none'"
            :color="pendingCount > 0 ? 'negative' : 'grey-7'"
            :loading="pendingLoading"
            aria-label="Pendientes"
          >
            <q-badge v-if="pendingCount > 0" color="red" text-color="white" floating>
              {{ pendingCount }}
            </q-badge>
            <q-tooltip v-if="pendingCount > 0">
              Tienes {{ pendingCount }} pendiente(s)
            </q-tooltip>
            <q-menu>
              <q-list style="min-width: 250px">
                <q-item v-if="pending" v-for="(p,i) in pending" :key="i" clickable @click="irPendientes(p)" v-close-popup>
                  <q-item-section>
                    <q-item-label>
                      <div>
                        <strong>{{ p.tipo }} {{ p.caso_numero }}</strong> - {{ p.caso_fecha_hecho }}
                      </div>
                      <div class="text-caption">
                        {{ p.caso_direccion || 'Sin dirección' }}
                      </div>
                    </q-item-label>
                  </q-item-section>
                </q-item>
                <q-item v-if="!pendingCount && !pendingLoading">
                  <q-item-section>
                    <q-item-label>
                      No tienes pendientes
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </q-btn>

          <q-btn-dropdown flat unelevated no-caps dropdown-icon="expand_more">
            <template #label>
              <div class="row items-center no-wrap q-gutter-sm">
                <q-avatar rounded>
                  <q-img :src="`${$url}/../images/${$store.user?.avatar}`" width="40" height="40" v-if="$store.user?.avatar"/>
                  <q-icon name="person" v-else />
                </q-avatar>
                <div class="text-left" style="line-height:1">
                  <div class="ellipsis" style="max-width:140px;">{{ $store.user?.username }}</div>
                  <q-chip v-if="$store.user?.role" color="grey-3" text-color="black" size="10px" dense>
                    {{ $store.user?.role }}
                  </q-chip>
                </div>
              </div>
            </template>

            <q-item clickable v-close-popup>
              <q-item-section>
                <q-item-label class="text-grey-7">
                  {{ $store.user?.name || $store.user?.username || 'Usuario' }}
                </q-item-label>
                <q-item-label caption class="q-mt-xs">
                  <div class="row q-col-gutter-xs" style="min-width:160px;max-width:260px;">
                    <q-chip
                      v-for="(p,i) in ($store.permissions || [])"
                      :key="i" dense color="grey-3" text-color="black" size="12px"
                      class="q-mr-xs q-mb-xs"
                    >
                      {{ p }}
                    </q-chip>
                    <q-badge v-if="!$store.permissions?.length" color="grey-5" outline>Sin permisos</q-badge>
                  </div>
                </q-item-label>
              </q-item-section>
            </q-item>

            <q-separator />

            <q-item clickable v-ripple @click="logout" v-close-popup>
              <q-item-section avatar><q-icon name="logout"/></q-item-section>
              <q-item-section><q-item-label>Salir</q-item-label></q-item-section>
            </q-item>
          </q-btn-dropdown>
        </div>
      </q-toolbar>
    </q-header>

    <!-- DRAWER -->
    <q-drawer
      v-model="leftDrawerOpen" bordered show-if-above
      :width="220" :breakpoint="500" class="bg-primary text-white"
    >
      <q-list class="q-pb-none">
        <q-item-label header class="text-center q-pa-none q-pt-md">
          <q-avatar size="64px" class="q-mb-sm" rounded>
            <q-img src="/logo.png" width="90" />
          </q-avatar>
          <div class="text-weight-bold text-white">DIO</div>
          <div class="text-caption text-white">Dirección de Igualdad de Oportunidades (GAMO)</div>
        </q-item-label>

        <q-separator color="white" spaced />

        <q-item-label header class="q-px-md text-grey-3 q-mt-sm">
          Módulos del Sistema
        </q-item-label>
        <q-item dense to="/" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
          <q-item-section avatar>
            <q-icon name="analytics" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Dashboard</q-item-label>
          </q-item-section>
        </q-item>
        <q-item dense to="/usuarios" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPerm('Usuarios')">
          <q-item-section avatar>
            <q-icon name="people" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Usuarios</q-item-label>
          </q-item-section>
        </q-item>
        <q-item dense to="/lineas-tiempo" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPerm('Lineas de Tiempo')">
          <q-item-section avatar>
            <q-icon name="timeline" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Líneas de Tiempo</q-item-label>
          </q-item-section>
        </q-item>
        <q-item dense to="/agenda" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPerm('Agenda')">
          <q-item-section avatar>
            <q-icon name="event" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Agenda</q-item-label>
          </q-item-section>
        </q-item>
        <q-expansion-item dense expand-separator icon="add_circle" label="Nuevo SLIM" active-class="menu-active" v-if="hasAnyPerm(['Crear SLIM'])">
          <q-list>
<!--            <q-item :inset-level="0.3" dense to="/slims/nuevofisica" exact clickable class="menu-item" active-class="menu-active" v-close-popup>-->
<!--              <q-item-section avatar>-->
<!--                <q-icon name="person_add" class="text-white"/>-->
<!--              </q-item-section>-->
<!--              <q-item-section>-->
<!--                <q-item-label class="text-white">Nueva Denuncia Física</q-item-label>-->
<!--              </q-item-section>-->
<!--            </q-item>-->
<!--            <q-item :inset-level="0.3" dense to="/slims/nuevointegral" exact clickable class="menu-item" active-class="menu-active" v-close-popup>-->
<!--              <q-item-section avatar>-->
<!--                <q-icon name="diversity_1" class="text-white"/>-->
<!--              </q-item-section>-->
<!--              <q-item-section>-->
<!--                <q-item-label class="text-white">Nuevo Apoyo Integral</q-item-label>-->
<!--              </q-item-section>-->
<!--            </q-item>-->
            <q-item :inset-level="0.3" dense to="/slims/nuevo-penal" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
              <q-item-section avatar>
                <q-icon name="balance" class="text-white"/>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-white">Nuevo Proceso Penal</q-item-label>
              </q-item-section>
            </q-item>
            <q-item :inset-level="0.3" dense to="/slims/nuevo-familiar" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
              <q-item-section avatar>
                <q-icon name="family_restroom" class="text-white"/>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-white">Nuevo Proceso Familiar</q-item-label>
              </q-item-section>
            </q-item>
<!--            <q-item :inset-level="0.3" dense to="/slims/nuevo-nna" exact clickable class="menu-item" active-class="menu-active" v-close-popup>-->
<!--              <q-item-section avatar>-->
<!--                <q-icon name="child_care" class="text-white"/>-->
<!--              </q-item-section>-->
<!--              <q-item-section>-->
<!--                <q-item-label class="text-white">Nuevo Proceso Niñez y Adolescencia</q-item-label>-->
<!--              </q-item-section>-->
<!--            </q-item>-->
            <q-item :inset-level="0.3" dense to="/slims/nuevo-apoyo" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
              <q-item-section avatar>
                <q-icon name="diversity_1" class="text-white"/>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-white">Nuevo Apoyo Integral</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-expansion-item>
        <q-item dense to="/slims" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPerm('Ver SLIM')">
          <q-item-section avatar>
            <q-icon name="folder_shared" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">SLIM (Casos)</q-item-label>
          </q-item-section>
        </q-item>
        <q-expansion-item dense expand-separator icon="gavel" label="Nuevo DNA" active-class="menu-active" v-if="hasAnyPerm(['Crear DNA'])">
          <q-list>
            <q-item :inset-level="0.3" dense to="/dnas/nuevo-penal" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
              <q-item-section avatar>
                <q-icon name="balance" class="text-white"/>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-white">Nuevo Proceso Penal</q-item-label>
              </q-item-section>
            </q-item>
            <q-item :inset-level="0.3" dense to="/dnas/nuevo-familiar" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
              <q-item-section avatar>
                <q-icon name="family_restroom" class="text-white"/>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-white">Nuevo Proceso Familiar</q-item-label>
              </q-item-section>
            </q-item>
            <q-item :inset-level="0.3" dense to="/dnas/nuevo-nna" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
              <q-item-section avatar>
                <q-icon name="child_care" class="text-white"/>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-white">Nuevo Proceso Niñez y Adolescencia</q-item-label>
              </q-item-section>
            </q-item>
            <q-item :inset-level="0.3" dense to="/dnas/nuevo-apoyo" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
              <q-item-section avatar>
                <q-icon name="diversity_1" class="text-white"/>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-white">Nuevo Apoyo Integral</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-expansion-item>
        <q-item dense to="/dnas" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPerm('Ver DNA')">
          <q-item-section avatar>
            <q-icon name="folder_shared" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">DNA (Casos)</q-item-label>
          </q-item-section>
        </q-item>
        <q-expansion-item dense expand-separator icon="add_circle" label="Nuevo SLAM" active-class="menu-active" v-if="hasAnyPerm(['Crear SLAM'])">
          <q-list>
            <q-item :inset-level="0.3" dense to="/slams/nuevofisica" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
              <q-item-section avatar>
                <q-icon name="person_add" class="text-white"/>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-white">Nueva Denuncia Física</q-item-label>
              </q-item-section>
            </q-item>
            <q-item :inset-level="0.3" dense to="/slams/nuevointegral" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
              <q-item-section avatar>
                <q-icon name="diversity_1" class="text-white"/>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-white">Nuevo Apoyo Integral</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-expansion-item>
        <q-item dense to="/slams" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPerm('Ver SLAM')">
          <q-item-section avatar>
            <q-icon name="folder_shared" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">SLAM (Casos)</q-item-label>
          </q-item-section>
        </q-item>
        <q-expansion-item dense expand-separator icon="add_circle" label="Nuevo UMADIS" active-class="menu-active" v-if="hasAnyPerm(['Crear UMADIS'])">
          <q-list>
            <q-item :inset-level="0.3" dense to="/umadis/nuevofisica" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
              <q-item-section avatar>
                <q-icon name="person_add" class="text-white"/>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-white">Nueva Denuncia Física</q-item-label>
              </q-item-section>
            </q-item>
            <q-item :inset-level="0.3" dense to="/umadis/nuevointegral" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
              <q-item-section avatar>
                <q-icon name="diversity_1" class="text-white"/>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-white">Nuevo Apoyo Integral</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-expansion-item>
        <q-item dense to="/umadis" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPerm('Ver UMADIS')">
          <q-item-section avatar>
            <q-icon name="folder_shared" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">UMADIS (Casos)</q-item-label>
          </q-item-section>
        </q-item>
        <q-expansion-item dense expand-separator icon="add_circle" label="Nuevo PROPREMI" active-class="menu-active" v-if="hasAnyPerm(['Crear PROPREMI'])">
          <q-list>
<!--            <q-item :inset-level="0.3" dense to="/propremis/nuevofisica" exact clickable class="menu-item" active-class="menu-active" v-close-popup>-->
<!--              <q-item-section avatar>-->
<!--                <q-icon name="person_add" class="text-white"/>-->
<!--              </q-item-section>-->
<!--              <q-item-section>-->
<!--                <q-item-label class="text-white">Nueva Denuncia Física</q-item-label>-->
<!--              </q-item-section>-->
<!--            </q-item>-->
<!--            <q-item :inset-level="0.3" dense to="/propremis/nuevointegral" exact clickable class="menu-item" active-class="menu-active" v-close-popup>-->
<!--              <q-item-section avatar>-->
<!--                <q-icon name="diversity_1" class="text-white"/>-->
<!--              </q-item-section>-->
<!--              <q-item-section>-->
<!--                <q-item-label class="text-white">Nuevo Apoyo Integral</q-item-label>-->
<!--              </q-item-section>-->
<!--            </q-item>-->
<!--            -	INFRACCIONES SISTEMA EDUCATIVO-->
            <q-item :inset-level="0.3" dense to="/propremis/nuevo-infraccion-educativo" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
              <q-item-section avatar>
                <q-icon name="school" class="text-white"/>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-white">Nueva Infracción Sistema Educativo</q-item-label>
              </q-item-section>
            </q-item>
<!--            -	ORIENTACIONES/TALLERES-->
            <q-item :inset-level="0.3" dense to="/talleres" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
              <q-item-section avatar>
                <q-icon name="record_voice_over" class="text-white"/>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-white">Agenda Talleres de Orientación</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-expansion-item>
        <q-item dense to="/propremis" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPerm('Ver PROPREMI')">
          <q-item-section avatar>
            <q-icon name="folder_shared" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">PROPREMI (Casos)</q-item-label>
          </q-item-section>
        </q-item>
        <q-item dense to="/acogimiento" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPerm('Acogimiento')">
          <q-item-section avatar>
            <q-icon name="home" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Acogimiento</q-item-label>
          </q-item-section>
        </q-item>
        <q-expansion-item dense expand-separator icon="emoji_people" label="JUVENTUDES" active-class="menu-active" v-if="hasPerm('Crear Juventudes')">
          <q-list>
            <q-item :inset-level="0.3" dense to="/juventudes/permisos-trabajo" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
              <q-item-section avatar>
                <q-icon name="work" class="text-white"/>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-white">Permisos y Autorizaciones de Trabajo</q-item-label>
              </q-item-section>
            </q-item>
            <q-item :inset-level="0.3" dense to="/juventudes/actividades-talleres" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
              <q-item-section avatar>
                <q-icon name="record_voice_over" class="text-white"/>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-white">Actividades o Talleres</q-item-label>
              </q-item-section>
            </q-item>
            <q-item :inset-level="0.3" dense to="/juventudes/asistencias-familiares" exact clickable class="menu-item" active-class="menu-active" v-close-popup>
              <q-item-section avatar>
                <q-icon name="family_restroom" class="text-white"/>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-white">Asistencias Familiares</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-expansion-item>
<!--       Ver juventudes -->
        <q-item dense to="/juventudes" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPerm('Ver Juventudes')">
          <q-item-section avatar>
            <q-icon name="emoji_people" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Juventudes (Casos)</q-item-label>
          </q-item-section>
        </q-item>
        <q-item dense to="/reportes" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPerm('Reportes')">
          <q-item-section avatar>
            <q-icon name="bar_chart" class="text-white"/>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Reportes</q-item-label>
          </q-item-section>
        </q-item>
<!--        JUVENTUDES-->
<!--        <q-item dense to="/juventudes" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPerm('Juventudes')">-->
<!--          <q-item-section avatar>-->
<!--            <q-icon name="emoji_people" class="text-white"/>-->
<!--          </q-item-section>-->
<!--          <q-item-section>-->
<!--            <q-item-label class="text-white">Juventudes</q-item-label>-->
<!--          </q-item-section>-->
<!--        </q-item>-->
<!--        1.	PERMISOS Y AUTORIZACIONES DE TRABAJO-->
<!--        2.	ACTIVIDADES O TALLERES-->
<!--        3.	ASISTENCIAS FAMILIARES :-->


        <!--        <pre>{{$store.permissions}}</pre>-->

        <!-- Menú filtrado -->
<!--        <template v-for="link in filteredLinks" :key="link.link">-->
<!--          <q-item-->
<!--            v-if="!link.childrens || !link.childrens.length"-->
<!--            clickable :to="link.link" exact dense-->
<!--            class="menu-item" active-class="menu-active" v-close-popup-->
<!--          >-->
<!--            <q-item-section avatar>-->
<!--              <q-icon :name="link.icon" class="text-white"/>-->
<!--            </q-item-section>-->
<!--            <q-item-section>-->
<!--              <q-item-label class="text-white">{{ link.title }}</q-item-label>-->
<!--            </q-item-section>-->
<!--          </q-item>-->

<!--          <q-expansion-item-->
<!--            v-else-->
<!--            :label="link.title" :icon="link.icon"-->
<!--            expand-separator dense-->
<!--            active-class="menu-active"-->
<!--          >-->
<!--            <q-list>-->
<!--              <q-item-->
<!--                v-for="sublink in link.childrens" :key="sublink.link"-->
<!--                clickable :to="sublink.link" exact dense-->
<!--                active-class="menu-active" v-close-popup-->
<!--                :inset-level="0.3"-->
<!--              >-->
<!--                <q-item-section avatar>-->
<!--                  <q-icon :name="sublink.icon" class="text-white"/>-->
<!--                </q-item-section>-->
<!--                <q-item-section>-->
<!--                  <q-item-label class="text-white">{{ sublink.title }}</q-item-label>-->
<!--                </q-item-section>-->
<!--              </q-item>-->
<!--            </q-list>-->
<!--          </q-expansion-item>-->
<!--        </template>-->

        <q-separator color="white" class="q-mt-md" />

        <div class="q-pa-md">
          <div class="text-white-7 text-caption">DIO v{{ $version }}</div>
          <div class="text-white-7 text-caption">© {{ new Date().getFullYear() }} Gobierno Autónomo Municipal de Oruro</div>
        </div>

        <q-item clickable class="text-white" @click="logout" v-close-popup>
          <q-item-section avatar><q-icon name="logout" /></q-item-section>
          <q-item-section><q-item-label>Salir</q-item-label></q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <!-- PAGE -->
    <q-page-container class="bg-grey-2">
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { computed, getCurrentInstance, ref, onMounted, watch } from 'vue'
const { proxy } = getCurrentInstance()

/* ---------- Helpers de normalización ---------- */
const norm = (s) => (s ?? '')
  .toString()
  .normalize('NFD')
  .replace(/\p{Diacritic}/gu, '')
  .replace(/[^\p{L}\p{N}]+/gu, ' ')
  .trim()
  .toUpperCase()

/* ---------- UI header ---------- */
const leftDrawerOpen = ref(false)
const pendingCount   = ref(0)
const pending   = ref({})
const pendingLoading = ref(false)

/* ---------- Super Admin SOLO por ÁREA ---------- */
function isSuperAdmin () {
  const area = norm(proxy.$store.user?.area)
  // Solo quienes tengan área = ADMIN ven todo
  return area === 'ADMIN'
}

/* ---------- Permisos "clásicos" ---------- */
function hasPerm (perm) {
  let permisosUser = proxy.$store.permissions || []
  let include = permisosUser.includes(perm)
  return include
}
function hasAnyPerm (perms = []) {
  if (isSuperAdmin()) return true
  return (perms || []).some(p => hasPerm(p))
}

/* ---------- Menú ---------- */
const linksList = [
  { title: 'Dashboard',  icon: 'analytics', link: '/',         canPerm: 'Dashboard' },
  { title: 'Usuarios',   icon: 'people',    link: '/usuarios', canPerm: 'Usuarios' },

  // SLIM
  { title: 'Nuevo SLIM', icon: 'add_circle', link: '/slims/nuevofisica',
    onlyAreas: ['ALL'],
    childrens: [
      { title: 'Denuncia Física', icon: 'person_add',  link: '/slims/nuevofisica',   onlyAreas: ['ALL'] },
      { title: 'Apoyo Integral',  icon: 'diversity_1', link: '/slims/nuevointegral', onlyAreas: ['ALL'] },
    ]
  },
  { title: 'SLIMs', icon: 'folder_shared', link: '/slims', onlyAreas: ['ALL'] },

  // DNA
  {
    title: 'Nuevo DNA',
    icon: 'gavel',
    link: '/dnas/nuevo-penal',
    onlyAreas: ['DNA'],
    childrens: [
      { title: 'Procesos Penales',              icon: 'balance',         link: '/dnas/nuevo-penal',    onlyAreas: ['DNA'] },
      { title: 'Procesos Familiares',           icon: 'family_restroom', link: '/dnas/nuevo-familiar', onlyAreas: ['DNA'] },
      { title: 'Procesos Niñez y Adolescencia', icon: 'child_care',      link: '/dnas/nuevo-nna',      onlyAreas: ['DNA'] },
      { title: 'Apoyos Integrales',             icon: 'diversity_1',     link: '/dnas/nuevo-apoyo',    onlyAreas: ['DNA'] },
    ]
  },
  { title: 'DNA (Casos)', icon: 'folder_shared', link: '/dnas', onlyAreas: ['DNA'] },

  // SLAM
  { title: 'Nuevo SLAM', icon: 'add_circle', link: '/slams/nuevofisica',
    childrens: [
      { title: 'Denuncia Física', icon: 'person_add',  link: '/slams/nuevofisica' },
      { title: 'Apoyo Integral',  icon: 'diversity_1', link: '/slams/nuevointegral' },
    ]
  },
  { title: 'SLAMs', icon: 'folder_shared', link: '/slams' },

  // UMADIS
  { title: 'Nuevo UMADIS', icon: 'add_circle', link: '/umadis/nuevofisica',
    onlyAreas: ['UMADIS'],
    childrens: [
      { title: 'Denuncia Física', icon: 'person_add',  link: '/umadis/nuevofisica',   onlyAreas: ['UMADIS'] },
      { title: 'Apoyo Integral',  icon: 'diversity_1', link: '/umadis/nuevointegral', onlyAreas: ['UMADIS'] },
    ]
  },
  { title: 'UMADIS', icon: 'folder_shared', link: '/umadis', onlyAreas: ['UMADIS'] },

  // PROPREMI (rutas separadas para evitar duplicados)
  { title: 'Nuevo PROPREMI', icon: 'add_circle', link: '/propremis/nuevofisica',
    onlyAreas: ['PROPREMI'],
    childrens: [
      { title: 'Denuncia Física', icon: 'person_add',  link: '/propremis/nuevofisica',   onlyAreas: ['PROPREMI'] },
      { title: 'Apoyo Integral',  icon: 'diversity_1', link: '/propremis/nuevointegral', onlyAreas: ['PROPREMI'] },
    ]
  },
  { title: 'PROPREMIs', icon: 'folder_shared', link: '/propremis', onlyAreas: ['PROPREMI'] },

  // Otros
  { title: 'Agenda',           icon: 'event',       link: '/agenda',        canPerm: 'Agenda' },
  { title: 'Líneas de Tiempo', icon: 'timeline',    link: '/lineas-tiempo', canPerm: 'Lineas de Tiempo' },
  { title: 'KPIs',             icon: 'query_stats', link: '/kpis',          canPerm: 'Kpis' },
  { title: 'Auditorías',       icon: 'shield',      link: '/auditorias',    canPerm: 'Auditorias' },
]

const filteredLinks = computed(() => {
  const area  = norm(proxy.$store.user?.area)
  const admin = isSuperAdmin()

  // Chequea permisos (sin sombreamiento de hasPerm)
  const checkPerm = (item) => {
    if (admin) return true
    const p = item?.canPerm
    if (!p) return true
    return Array.isArray(p) ? hasAnyPerm(p) : hasPerm(p)
  }

  const matchArea = (item) => {
    if (admin) return true

    const includesALL = (oa) => {
      if (!oa) return false
      if (Array.isArray(oa)) return oa.map(norm).includes('ALL')
      return norm(oa) === 'ALL'
    }

    // Si declara ALL, visible
    if (includesALL(item.onlyAreas) || includesALL(item.onlyArea)) return true

    // Restringe por área específica
    if (item.onlyArea) {
      if (!area) return false
      return norm(item.onlyArea) === area
    }
    if (item.onlyAreas) {
      if (!area) return false
      const arr = Array.isArray(item.onlyAreas) ? item.onlyAreas : [item.onlyAreas]
      return arr.map(norm).includes(area)
    }
    return true
  }

  const passes = (item) => !!item && matchArea(item) && checkPerm(item)

  return linksList
    .filter(Boolean)
    .filter(passes)
    .map(link => link.childrens
      ? ({ ...link, childrens: (link.childrens || []).filter(passes) })
      : link
    )
})

/* ---------- Pendientes ---------- */
async function fetchPendientesCount () {
  pendingLoading.value = true
  try {
    const { data } = await proxy.$axios.get('/casos-pendientes-resumen')
    // {
    //   "pendientes": [
    //   {
    //     "id": 12,
    //     "area": "ADMIN",
    //     "zona": "CENTRAL",
    //     "numero_apoyo_integral": null,
    //     "tipo": "DNA",
    //     "principal": "principal",
    //     "caso_numero": "011\/25",
    //     "caso_fecha_hecho": "2025-09-04",
    //     "caso_lugar_hecho": null,
    //     "caso_zona": null,
    //     "caso_direccion": "aaaaa",
    //     "caso_descripcion": null,
    //     "caso_tipologia": "ASISTENCIA FAMILIAR",
    //     "caso_modalidad": null,
    //     "violencia_fisica": null,
    //     "violencia_psicologica": null,
    //     "violencia_sexual": null,
    //     "violencia_economica": null,
    //     "violencia_patrimonial": null,
    //     "violencia_simbolica": null,
    //     "violencia_institucional": null,
    //     "psicologica_user_id": 3,
    //     "trabajo_social_user_id": 5,
    //     "legal_user_id": 4,
    //     "documento_fotocopia_carnet_denunciante": "1",
    //     "documento_fotocopia_carnet_denunciado": "1",
    //     "documento_placas_fotograficas_domicilio_denunciante": "1",
    //     "documento_croquis_direccion_denunciado": "1",
    //     "documento_placas_fotograficas_domicilio_denunciado": "0",
    //     "documento_ciudadania_digital": "0",
    //     "documento_otros": null,
    //     "documento_otros_detalle": null,
    //     "fecha_apertura_caso": "2025-09-23",
    //     "fecha_derivacion_psicologica": null,
    //     "fecha_informe_area_social": null,
    //     "fecha_informe_area_psicologica": null,
    //     "fecha_informe_trabajo_social": null,
    //     "fecha_derivacion_area_legal": null,
    //     "user_id": 1
    //   },
    //   {
    //     "id": 17,
    //     "area": "ADMIN",
    //     "zona": "CENTRAL",
    //     "numero_apoyo_integral": null,
    //     "tipo": "PROPREMI",
    //     "principal": "asdas",
    //     "caso_numero": "003\/25",
    //     "caso_fecha_hecho": "2025-09-27",
    //     "caso_lugar_hecho": "asdsa",
    //     "caso_zona": null,
    //     "caso_direccion": null,
    //     "caso_descripcion": null,
    //     "caso_tipologia": null,
    //     "caso_modalidad": null,
    //     "violencia_fisica": false,
    //     "violencia_psicologica": false,
    //     "violencia_sexual": true,
    //     "violencia_economica": false,
    //     "violencia_patrimonial": "0",
    //     "violencia_simbolica": "0",
    //     "violencia_institucional": "1",
    //     "psicologica_user_id": 3,
    //     "trabajo_social_user_id": 5,
    //     "legal_user_id": 10,
    //     "documento_fotocopia_carnet_denunciante": "0",
    //     "documento_fotocopia_carnet_denunciado": "1",
    //     "documento_placas_fotograficas_domicilio_denunciante": "1",
    //     "documento_croquis_direccion_denunciado": "0",
    //     "documento_placas_fotograficas_domicilio_denunciado": null,
    //     "documento_ciudadania_digital": null,
    //     "documento_otros": null,
    //     "documento_otros_detalle": null,
    //     "fecha_apertura_caso": "2025-09-24",
    //     "fecha_derivacion_psicologica": null,
    //     "fecha_informe_area_social": null,
    //     "fecha_informe_area_psicologica": null,
    //     "fecha_informe_trabajo_social": null,
    //     "fecha_derivacion_area_legal": null,
    //     "user_id": 1
    //   }
    // ]
    // }
    // console.log(data)
    pending.value = data
    pendingCount.value = data.length
  } catch (e) {
    // opcional
  } finally {
    pendingLoading.value = false
  }
}

function irPendientes (p) {
  // proxy.$router.push({ path: '/slims', query: { only_pendientes: 1 } })
  proxy.$router.push(`/${p.tipo.toLowerCase()}s/`)
}

onMounted(() => { fetchPendientesCount() })
watch(() => proxy.$route.fullPath, () => { fetchPendientesCount() })

/* ---------- Misc ---------- */
function toggleLeftDrawer () { leftDrawerOpen.value = !leftDrawerOpen.value }

function logout () {
  proxy.$alert.dialog('¿Desea salir del sistema?')
    .onOk(() => {
      proxy.$axios.post('/logout')
        .then(() => {
          proxy.$store.isLogged = false
          proxy.$store.user = {}
          proxy.$store.permissions = []
          localStorage.removeItem('tokenDio')
          localStorage.removeItem('user')
          proxy.$router.push('/login')
        })
        .catch(() => proxy.$alert.error('Error al cerrar sesión. Intente nuevamente.'))
    })
}
</script>

<style scoped>
.menu-item { border-radius: 10px; margin: 4px 8px; padding: 4px 6px; transition: background .2s ease; }
.menu-item:hover { background: rgba(255,255,255,.10); }
.menu-active { background: rgba(255,255,255,.18); color: #fff !important; border-radius: 10px; }
</style>

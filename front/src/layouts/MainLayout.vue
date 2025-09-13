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
            Panel DIO · GAMO <br>
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
        <div class="row items-center q-gutter-sm">
          <!-- Badge de pendientes -->
          <q-btn
            flat round dense
            :icon="pendingCount > 0 ? 'notifications_active' : 'notifications_none'"
            :color="pendingCount > 0 ? 'negative' : 'grey-7'"
            @click="irPendientes"
            :loading="pendingLoading"
            aria-label="Pendientes"
          >
            <q-badge
              v-if="pendingCount > 0"
              color="red" text-color="white"
              floating
            >
              {{ pendingCount }}
            </q-badge>
            <q-tooltip v-if="pendingCount > 0">
              Tienes {{ pendingCount }} pendiente(s)
            </q-tooltip>
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
                <q-item-label class="text-grey-7">Permisos asignados</q-item-label>
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

        <!-- Menú por permisos -->
        <template v-for="link in filteredLinks" :key="link.title">
          <q-item
            v-if="!link.childrens || !link.childrens.length"
            clickable :to="link.link" exact dense
            class="menu-item" active-class="menu-active" v-close-popup
          >
            <q-item-section avatar>
              <q-icon :name="link.icon" class="text-white"/>
            </q-item-section>
            <q-item-section>
              <q-item-label class="text-white">{{ link.title }}</q-item-label>
            </q-item-section>
          </q-item>

          <q-expansion-item
            v-else
            :label="link.title" :icon="link.icon"
            expand-separator dense
            active-class="menu-active"
          >
            <q-list>
              <q-item
                v-for="sublink in link.childrens" :key="sublink.title"
                clickable :to="sublink.link" exact dense
                active-class="menu-active" v-close-popup
                :inset-level="0.3"
              >
              <q-item-section avatar>
                <q-icon :name="sublink.icon" class="text-white"/>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-white">{{ sublink.title }}</q-item-label>
              </q-item-section>
              </q-item>
            </q-list>
          </q-expansion-item>
        </template>

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

const leftDrawerOpen = ref(false)
const pendingCount   = ref(0)
const pendingLoading = ref(false)

function hasPerm (perm) {
  if (!perm) return true
  return (proxy.$store.permissions || []).includes(perm)
}
function hasAnyPerm (perms = []) {
  return (perms || []).some(p => hasPerm(p))
}

/** Menú actualizado a SLIMs */
const linksList = [
  { title: 'Dashboard',        icon: 'analytics',       link: '/',                  canPerm: 'Dashboard' },
  { title: 'Usuarios',         icon: 'people',          link: '/usuarios',          canPerm: 'Usuarios' },

  // ======== SLIMs
  { title: 'Nuevo SLIM', icon: 'add_circle', link: '/slims/nuevofisica', canPerm: 'Casos',
    childrens: [
      { title: 'Denuncia Física', icon: 'person_add',   link: '/slims/nuevofisica',   canPerm: 'Casos' },
      { title: 'Apoyo Integral',  icon: 'diversity_1',  link: '/slims/nuevointegral', canPerm: 'Casos' },
    ]
  },
  { title: 'SLIMs',            icon: 'folder_shared',   link: '/slims',             canPerm: 'Casos' },

  // Otros módulos
  { title: 'Agenda',           icon: 'event',           link: '/agenda',            canPerm: 'Agenda' },
  { title: 'Líneas de Tiempo', icon: 'timeline',        link: '/lineas-tiempo',     canPerm: 'Lineas de Tiempo' },
  { title: 'KPIs',             icon: 'query_stats',     link: '/kpis',              canPerm: 'Kpis' },
  { title: 'Auditorías',       icon: 'shield',          link: '/auditorias',        canPerm: 'Auditorias' },
]

const filteredLinks = computed(() =>
  linksList.filter(l => Array.isArray(l.canPerm) ? hasAnyPerm(l.canPerm) : hasPerm(l.canPerm))
)

/** Ahora consulta pendientes de SLIMs */
async function fetchPendientesCount () {
  pendingLoading.value = true
  try {
    const { data } = await proxy.$axios.get('/slims/pendientes-resumen')
    pendingCount.value = Number(data?.pendientes || 0)
  } catch (e) {
    // opcional: proxy.$q.notify({ type:'warning', message:'No se pudo cargar pendientes' })
  } finally {
    pendingLoading.value = false
  }
}

function irPendientes () {
  // abre SLIMs mostrando SOLO faltantes (si usas query en la tabla)
  proxy.$router.push({ path: '/slims', query: { only_pendientes: 1 } })
}

onMounted(() => { fetchPendientesCount() })
watch(() => proxy.$route.fullPath, () => { fetchPendientesCount() })

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

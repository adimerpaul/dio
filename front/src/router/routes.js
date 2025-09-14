const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/IndexPage.vue'), meta: { requiresAuth: true, perm: 'Dashboard' } },
      { path: '/usuarios', component: () => import('pages/usuarios/Usuarios.vue'), meta: { requiresAuth: true, perm: 'Usuarios' } },

      // ======== SLIMS (antes /casos)
      { path: '/slims', component: () => import('pages/slims/Slims.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      { path: '/slims/nuevofisica', component: () => import('pages/slims/SlimNuevoFisico.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      { path: '/slims/nuevointegral', component: () => import('pages/slims/SlimNuevoIntegral.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      { path: '/slims/:id', component: () => import('pages/slims/SlimShow.vue'), meta: { requiresAuth: true, perm: 'Casos' } },

      // { path: '/dnas',              component: () => import('pages/dnas/Dnas.vue'),              meta: { requiresAuth: true } },
      { path: '/dnas/nuevo-penal',  component: () => import('pages/dnas/DnaNuevoPenal.vue'),    meta: { requiresAuth: true } },
      { path: '/dnas/nuevo-familiar', component: () => import('pages/dnas/DnaNuevoFamiliar.vue'), meta: { requiresAuth: true } },
      { path: '/dnas/nuevo-nna',    component: () => import('pages/dnas/DnaNuevoNna.vue'),      meta: { requiresAuth: true } },
      { path: '/dnas/nuevo-apoyo',  component: () => import('pages/dnas/DnaNuevoApoyo.vue'),    meta: { requiresAuth: true } },

      { path: '/lineas-tiempo', component: () => import('pages/lineastiempo/LineasTiempo.vue'), meta: { requiresAuth: true, perm: 'Lineas de Tiempo' } },
      { path: '/kpis', component: () => import('pages/kpis/Kpis.vue'), meta: { requiresAuth: true, perm: 'Kpis' } },
      { path: '/auditorias', component: () => import('pages/auditorias/Auditorias.vue'), meta: { requiresAuth: true, perm: 'Auditorias' } },
      { path: '/agenda', component: () => import('pages/agenda/Agenda.vue'), meta: { requiresAuth: true, perm: 'Agenda' } },
    ]
  },
  { path: '/login', component: () => import('layouts/Login.vue') },
  { path: '/:catchAll(.*)*', component: () => import('pages/ErrorNotFound.vue') }
]
export default routes

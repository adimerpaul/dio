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
      { path: '/slims/nuevo-penal', component: () => import('pages/slims/SlimNuevoPenal.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      { path: '/slims/nuevo-familiar', component: () => import('pages/slims/SlimNuevoFamiliar.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      { path: '/slims/nuevo-apoyo', component: () => import('pages/slims/SlimNuevoApoyo.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      // to="/slim/nuevo-hechos-fragancia"
      { path: '/slims/nuevo-hechos-fragancia', component: () => import('pages/slims/SlimNuevoHechosFragancia.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      { path: '/propremis/nuevo-infraccion-educativo', component: () => import('pages/propremis/PropremisNuevoInfraccionEducativo.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      { path: '/talleres', component: () => import('pages/talleres/Talleres.vue'), meta: { requiresAuth: true, perm: 'Talleres' } },

      { path: '/casos/:id', component: () => import('pages/casos/CasoShow.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      { path: '/casos/:id/:subtipo', component: () => import('pages/casos/CasoShow.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      { path: '/slams', component: () => import('pages/slams/Slams.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      // http://localhost:9001/slams/nuevo-notarial
      { path: '/slams/nuevo-notarial', component: () => import('pages/slams/SlamNotarialPage.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      { path: '/slams/nuevo-penal', component: () => import('pages/slams/SlamNuevoPenal.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      { path: '/slams/nuevo-familiar', component: () => import('pages/slams/SlamNuevoFamiliar.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      { path: '/slams/nuevo-apoyo', component: () => import('pages/slams/SlamNuevoApoyo.vue'), meta: { requiresAuth: true, perm: 'Casos' } },

      { path: '/umadis', component: () => import('pages/umadis/Umadis.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      // { path: '/umadis/nuevofisica', component: () => import('pages/umadis/UmadisNuevoFisico.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      // { path: '/umadis/nuevointegral', component: () => import('pages/umadis/UmadisNuevoIntegral.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      // { path: '/umadis/:id', component: () => import('pages/umadis/UmadisShow.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      // to="/umadis/nuevo-penal"
      // to="/umadis/nuevo-familiar"
      // to="/umadis/nuevo-social"
      // to="/umadis/nuevo-apoyo"
      { path: '/umadis/nuevo-penal', component: () => import('pages/umadis/UmadisNuevoPenal.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      { path: '/umadis/nuevo-familiar', component: () => import('pages/umadis/UmadisNuevoFamiliar.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      { path: '/umadis/nuevo-social', component: () => import('pages/umadis/UmadisNuevoSocial.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      { path: '/umadis/nuevo-apoyo', component: () => import('pages/umadis/UmadisNuevoApoyo.vue'), meta: { requiresAuth: true, perm: 'Casos' } },

      { path: '/propremis', component: () => import('pages/propremis/Propremis.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      // http://localhost:9011/propremis/nuevofisica
      { path: '/propremis/nuevofisica', component: () => import('pages/propremis/PropremisNuevoFisico.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      { path: '/propremis/nuevointegral', component: () => import('pages/propremis/PropremisNuevoIntegral.vue'), meta: { requiresAuth: true, perm: 'Casos' } },


      { path: '/dnas',              component: () => import('pages/dnas/Dnas.vue'),              meta: { requiresAuth: true } },
      { path: '/dnas/nuevo-penal',  component: () => import('pages/dnas/DnaNuevoPenal.vue'),    meta: { requiresAuth: true } },
      { path: '/dnas/nuevo-familiar', component: () => import('pages/dnas/DnaNuevoFamiliar.vue'), meta: { requiresAuth: true } },
      { path: '/dnas/nuevo-nna',    component: () => import('pages/dnas/DnaNuevoNna.vue'),      meta: { requiresAuth: true } },
      { path: '/dnas/nuevo-apoyo',  component: () => import('pages/dnas/DnaNuevoApoyo.vue'),    meta: { requiresAuth: true } },
      { path: '/dnas/:id',          component: () => import('pages/dnas/DnaShow.vue'),          meta: { requiresAuth: true } },
      // to="/dnas/nuevo-hechos-fragancia"
      // to="/dnas/nuevo-hechos-denuncia"
      { path: '/dnas/nuevo-hechos-fragancia', component: () => import('pages/dnas/DnaNuevoHechosFragancia.vue'), meta: { requiresAuth: true } },
      // { path: '/dnas/nuevo-hechos-denuncia', component: () => import('pages/dnas/DnaNuevoHechosDenuncia.vue'), meta: { requiresAuth: true } },

      // juventudes
      { path: '/juventudes/asistencias-familiares', component: () => import('pages/juventudes/JuventudesAsistenciaFamiliar.vue'), meta: { requiresAuth: true, perm: 'Juventudes' } },
      // http://localhost:9000/juventudes/actividades-talleres
      { path: '/juventudes/actividades-talleres', component: () => import('pages/juventudes/JuventudesActividadesTalleres.vue'), meta: { requiresAuth: true, perm: 'Juventudes' } },
      // JUVENTUDES
      { path: '/juventudes', component: () => import('pages/juventudes/Juventudes.vue'), meta: { requiresAuth: true, perm: 'Juventudes' } },

      { path: '/lineas-tiempo', component: () => import('pages/lineastiempo/LineasTiempo.vue'), meta: { requiresAuth: true, perm: 'Lineas de Tiempo' } },
      { path: '/kpis', component: () => import('pages/kpis/Kpis.vue'), meta: { requiresAuth: true, perm: 'Kpis' } },
      { path: '/auditorias', component: () => import('pages/auditorias/Auditorias.vue'), meta: { requiresAuth: true, perm: 'Auditorias' } },
      { path: '/agenda', component: () => import('pages/agenda/Agenda.vue'), meta: { requiresAuth: true, perm: 'Agenda' } },
      { path: '/acogimiento', component: () => import('pages/acogimiento/Acogimiento.vue'), meta: { requiresAuth: true, perm: 'Acogimientos' } },
      { path: '/acogimiento/:id', component: () => import('pages/acogimiento/AcogimientoShow.vue'), meta: { requiresAuth: true, perm: 'Acogimientos' } },

      // reportes
      { path: '/reportes', component: () => import('pages/reportes/Reportes.vue'), meta: { requiresAuth: true, perm: 'Reportes' } },

      // http://localhost:9000/antecedentes-nna
      { path: '/antecedentes-nna',    component: () => import('pages/dnas/AntecedentesNna.vue'),      meta: { requiresAuth: true } },
      { path: '/remision-casos',    component: () => import('pages/RemisionCasos/RemisionCasosPage.vue'),      meta: { requiresAuth: true, perm: 'Casos' } },

    ]
  },
  { path: '/login', component: () => import('layouts/Login.vue') },
  { path: '/:catchAll(.*)*', component: () => import('pages/ErrorNotFound.vue') }
]
export default routes

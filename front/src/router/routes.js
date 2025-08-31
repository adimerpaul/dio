const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/IndexPage.vue'), meta: { requiresAuth: true, perm: 'Dashboard' } },
      { path: '/usuarios', component: () => import('pages/usuarios/Usuarios.vue'), meta: { requiresAuth: true, perm: 'Usuarios' } },
      { path: '/casos', component: () => import('pages/casos/Casos.vue'), meta: { requiresAuth: true, perm: 'Usuarios' } },
      { path: '/casos/nuevo', component: () => import('pages/casos/CasoNuevo.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      { path: '/casos/:id', component: () => import('pages/casos/CasoShow.vue'), meta: { requiresAuth: true, perm: 'Casos' } },
      // lineas de timepo { title: 'Líneas de Tiempo', icon: 'timeline',        link: '/lineas-tiempo',  canPerm: 'Lineas de Tiempo' },
      { path: '/lineas-tiempo', component: () => import('pages/lineastiempo/LineasTiempo.vue'), meta: { requiresAuth: true, perm: 'Lineas de Tiempo' } },
    ]
  },
  { path: '/login', component: () => import('layouts/Login.vue') },
  { path: '/:catchAll(.*)*', component: () => import('pages/ErrorNotFound.vue') }
]
export default routes

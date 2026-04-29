import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router';
import { useAuthStore } from '../stores/auth';

// Komponenten werden bewusst NICHT statisch importiert - Vite/Rollup splittet
// jeden () => import(...) in einen eigenen Chunk auf, sodass z.B. die Login-
// Seite nicht den Walk-Detail-Code mitlaedt. Vor diesem Refactor lag aller
// Komponenten-Code in einem ~4-MB-Bundle.

declare module 'vue-router' {
    interface RouteMeta {
        requiresAuth?: boolean;
        requiresAdmin?: boolean;
        requiresSuperAdmin?: boolean;
    }
}

export const routes: Array<RouteRecordRaw> = [
    { path: '/klienten', component: () => import('../components/Clients.vue'), name: 'Clients', meta: { requiresSuperAdmin: true } },
    { path: '/benutzer', component: () => import('../components/Users.vue'), name: 'Users', meta: { requiresAdmin: true } },
    { path: '/anmeldung', component: () => import('../components/Login.vue'), name: 'Login', meta: { requiresAuth: false } },
    { path: '/dashboard', component: () => import('../components/Dashboard.vue'), name: 'Dashboard', meta: { requiresAuth: true }, props: true },
    { path: '/was-ist-swapp-die-streetworkapp', component: () => import('../components/About.vue'), name: 'About', meta: { requiresAuth: false } },
    { path: '/changelog', component: () => import('../components/Changelog.vue'), name: 'Changelog', meta: { requiresAuth: false } },
    { path: '/faq', component: () => import('../components/Faq.vue'), name: 'Faq', meta: { requiresAuth: false } },

    { path: '/runde/:walkId/detail', component: () => import('../components/WalkDetail.vue'), name: 'WalkDetail', meta: { requiresAuth: true }, props: true },
    { path: '/runde/:teamId/beginnen', component: () => import('../components/WalkPrologue.vue'), name: 'WalkPrologue', meta: { requiresAuth: true }, props: true },
    { path: '/runde/:walkId/wegpunkt-hinzufuegen', component: () => import('../components/WalkAddWayPoint.vue'), name: 'WalkAddWayPoint', meta: { requiresAuth: true }, props: true },
    { path: '/runde/:walkId/abschliessen', component: () => import('../components/WalkEpilogue.vue'), name: 'WalkEpilogue', meta: { requiresAuth: true }, props: true },
    { path: '/runde/:walkId/wegpunkt/:wayPointId/detail', component: () => import('../components/WayPointDetail.vue'), name: 'WayPointDetail', meta: { requiresAuth: true }, props: true },

    { path: '/passwort-zuruecksetzen', component: () => import('../components/PasswordReset.vue'), name: 'PasswordReset', meta: { requiresAuth: false } },
    { path: '/abmeldung', component: () => import('../components/Logout.vue'), name: 'Logout', meta: { requiresAuth: true } },
    { path: '/passwort-aenderung-beantragen', component: () => import('../components/PasswordChangeRequest.vue'), name: 'PasswordChangeRequest', meta: { requiresAuth: true } },
    { path: '/passwort-aendern/:userId/:confirmationToken', component: () => import('../components/PasswordChange.vue'), name: 'PasswordChange', props: true, meta: { requiresAuth: false } },
    { path: '/email-bestaetigen/:userId/:confirmationToken', component: () => import('../components/UserEmailConfirm.vue'), name: 'UserEmailConfirm', props: true, meta: { requiresAuth: false } },

    { path: '/systemische-fragen', component: () => import('../components/SystemicQuestions.vue'), name: 'SystemicQuestions', meta: { requiresAdmin: true } },
    { path: '/teams', component: () => import('../components/Teams.vue'), name: 'Teams', meta: { requiresAdmin: true } },
    { path: '/tags', component: () => import('../components/Tags.vue'), name: 'Tags', meta: { requiresAdmin: true } },

    { path: '/:pathMatch(.*)*', redirect: { name: 'Dashboard' }, name: 'default', meta: { requiresAuth: true } }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    const isAuthenticated = authStore.isAuthenticated;
    const isAdmin = authStore.isAdmin || authStore.isSuperAdmin;
    const isSuperAdmin = authStore.isSuperAdmin;

    if (to.meta.requiresSuperAdmin) {
        if (isAuthenticated && isSuperAdmin) return next();
    } else if (to.meta.requiresAdmin) {
        if (isAuthenticated && isAdmin) return next();
    } else if (to.meta.requiresAuth) {
        if (isAuthenticated) return next();
    } else {
        return next();
    }

    // Redirect logic
    if (to.name === 'Login' && isAuthenticated) {
        return next({ name: 'Dashboard' });
    }
    return next({ name: 'Login' });
});

export default router;

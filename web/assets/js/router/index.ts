import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router';
import { useAuthStore } from '../stores/auth';

import Clients from '../components/Clients.vue';
import Users from '../components/Users.vue';
import Login from '../components/Login.vue';
import Logout from '../components/Logout.vue';
import PasswordChangeRequest from '../components/PasswordChangeRequest.vue';
import PasswordChange from '../components/PasswordChange.vue';
import UserEmailConfirm from '../components/UserEmailConfirm.vue';
import PasswordReset from '../components/PasswordReset.vue';
import Dashboard from '../components/Dashboard.vue';
import About from '../components/About.vue';
import Changelog from '../components/Changelog.vue';
import Faq from '../components/Faq.vue';
import WalkDetail from '../components/WalkDetail.vue';
import WayPointDetail from '../components/WayPointDetail.vue';
import SystemicQuestions from '../components/SystemicQuestions.vue';
import Teams from '../components/Teams.vue';
import Tags from '../components/Tags.vue';
import WalkPrologue from '../components/WalkPrologue.vue';
import WalkAddWayPoint from '../components/WalkAddWayPoint.vue';
import WalkEpilogue from '../components/WalkEpilogue.vue';

declare module 'vue-router' {
    interface RouteMeta {
        requiresAuth?: boolean;
        requiresAdmin?: boolean;
        requiresSuperAdmin?: boolean;
    }
}

export const routes: Array<RouteRecordRaw> = [
    { path: '/klienten', component: Clients, name: 'Clients', meta: { requiresSuperAdmin: true } },
    { path: '/benutzer', component: Users, name: 'Users', meta: { requiresAdmin: true } },
    { path: '/anmeldung', component: Login, name: 'Login', meta: { requiresAuth: false } },
    { path: '/dashboard', component: Dashboard, name: 'Dashboard', meta: { requiresAuth: true }, props: true },
    { path: '/was-ist-swapp-die-streetworkapp', component: About, name: 'About', meta: { requiresAuth: false } },
    { path: '/changelog', component: Changelog, name: 'Changelog', meta: { requiresAuth: false } },
    { path: '/faq', component: Faq, name: 'Faq', meta: { requiresAuth: false } },

    { path: '/runde/:walkId/detail', component: WalkDetail, name: 'WalkDetail', meta: { requiresAuth: true }, props: true },
    { path: '/runde/:teamId/beginnen', component: WalkPrologue, name: 'WalkPrologue', meta: { requiresAuth: true }, props: true },
    { path: '/runde/:walkId/wegpunkt-hinzufuegen', component: WalkAddWayPoint, name: 'WalkAddWayPoint', meta: { requiresAuth: true }, props: true },
    { path: '/runde/:walkId/abschliessen', component: WalkEpilogue, name: 'WalkEpilogue', meta: { requiresAuth: true }, props: true },
    { path: '/runde/:walkId/wegpunkt/:wayPointId/detail', component: WayPointDetail, name: 'WayPointDetail', meta: { requiresAuth: true }, props: true },

    { path: '/passwort-zuruecksetzen', component: PasswordReset, name: 'PasswordReset', meta: { requiresAuth: false } },
    { path: '/abmeldung', component: Logout, name: 'Logout', meta: { requiresAuth: true } },
    { path: '/passwort-aenderung-beantragen', component: PasswordChangeRequest, name: 'PasswordChangeRequest', meta: { requiresAuth: true } },
    { path: '/passwort-aendern/:userId/:confirmationToken', component: PasswordChange, name: 'PasswordChange', props: true, meta: { requiresAuth: false } },
    { path: '/email-bestaetigen/:userId/:confirmationToken', component: UserEmailConfirm, name: 'UserEmailConfirm', props: true, meta: { requiresAuth: false } },

    { path: '/systemische-fragen', component: SystemicQuestions, name: 'SystemicQuestions', meta: { requiresAdmin: true } },
    { path: '/teams', component: Teams, name: 'Teams', meta: { requiresAdmin: true } },
    { path: '/tags', component: Tags, name: 'Tags', meta: { requiresAdmin: true } },

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

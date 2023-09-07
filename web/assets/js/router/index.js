"use strict";

import VueRouter from 'vue-router';

import Clients from '../components/Clients';
import Users from '../components/Users';
import Login from '../components/Login';
import Logout from '../components/Logout';
import PasswordChangeRequest from '../components/PasswordChangeRequest';
import PasswordChange from '../components/PasswordChange';
import UserEmailConfirm from '../components/UserEmailConfirm';
import PasswordReset from '../components/PasswordReset';
import Dashboard from '../components/Dashboard';
import About from '../components/About';
import Changelog from '../components/Changelog';
import Faq from '../components/Faq';
import WalkDetail from '../components/WalkDetail';
import WayPointDetail from '../components/WayPointDetail';
import SystemicQuestions from '../components/SystemicQuestions';
import Teams from '../components/Teams';
import Tags from '../components/Tags';
import WalkPrologue from '../components/WalkPrologue';
import WalkAddWayPoint from '../components/WalkAddWayPoint';
import WalkEpilogue from '../components/WalkEpilogue';
import { useAuthStore } from '../stores/auth';

let routes = [
    { id: 1, path: '/klienten', component: Clients, name: 'Clients', meta: { requiresSuperAdmin: true } },
    {id: 2, path: "/benutzer", component: Users, name: "Users", meta: {requiresAdmin: true}},
    {id: 3, path: "/anmeldung", component: Login, name: "Login", meta: {requiresAuth: false}},
    {id: 4, path: "/dashboard", component: Dashboard, name: "Dashboard", meta: {requiresAuth: true}, props: true},
    {id: 5, path: "/was-ist-swapp-die-streetworkapp", component: About, name: "About", meta: {requiresAuth: false}, props: false},
    {id: 6, path: "/changelog", component: Changelog, name: "Changelog", meta: {requiresAuth: false}, props: false},
    {id: 6, path: "/faq", component: Faq, name: "Faq", meta: {requiresAuth: false}, props: false},
    {id: 50, path: "/runde/:walkId/detail", component: WalkDetail, name: "WalkDetail", meta: {requiresAuth: true}, props: true},
    {id: 51, path: "/runde/:teamId/beginnen", component: WalkPrologue, name: "WalkPrologue", meta: {requiresAuth: true}, props: true},
    {id: 51, path: "/runde/:walkId/wegpunkt-hinzufuegen", component: WalkAddWayPoint, name: "WalkAddWayPoint", meta: {requiresAuth: true}, props: true},
    {id: 51, path: "/runde/:walkId/abschliessen", component: WalkEpilogue, name: "WalkEpilogue", meta: {requiresAuth: true}, props: true},
    {id: 6, path: "/runde/:walkId/wegpunkt/:wayPointId/detail", component: WayPointDetail, name: "WayPointDetail", meta: {requiresAuth: true}, props: true},
    {id: 8, path: "/passwort-zuruecksetzen", component: PasswordReset, name: "PasswordReset", meta: {requiresAuth: false}},
    {id: 9, path: "/abmeldung", component: Logout, name: "Logout", meta: {requiresAuth: true}},
    {id: 10, path: "/passwort-aenderung-beantragen", component: PasswordChangeRequest, name: "PasswordChangeRequest", meta: {requiresAuth: true}},
    {id: 11, path: "/passwort-aendern/:userId/:confirmationToken", component: PasswordChange, name: "PasswordChange", props: true, meta: {requiresAuth: false}},
    {id: 11, path: "/email-bestaetigen/:userId/:confirmationToken", component: UserEmailConfirm, name: "UserEmailConfirm", props: true, meta: {requiresAuth: false}},
    {id: 20, path: "/systemische-fragen", component: SystemicQuestions, name: "SystemicQuestions", meta: {requiresAdmin: true}},
    {id: 20, path: "/teams", component: Teams, name: "Teams", meta: {requiresAdmin: true}},
    {id: 21, path: "/tags", component: Tags, name: "Tags", meta: {requiresAdmin: true}},
    {id: 0, path: "*", redirect: { name: "Dashboard" }, name: "default", meta: {requiresAuth: true}}
];

let router = new VueRouter({
    mode: "history",
    routes: routes
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    let isAuthenticated = authStore.isAuthenticated;
    let isAdmin = authStore.isAdmin || authStore.isSuperAdmin;
    let isSuperAdmin = authStore.isSuperAdmin;
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (isAuthenticated) {
            next();
        } else if (to.name === "Login" && isAuthenticated) {
            next({name: 'Dashboard'});
        } else {
            next({name: 'Login'});
        }
    } else if (to.matched.some(record => record.meta.requiresAdmin)) {
        if (isAuthenticated && isAdmin) {
            next();
        } else if (to.name === "Login" && isAuthenticated) {
            next({name: 'Dashboard'});
        } else {
            next({name: 'Login'});
        }
    } else if (to.matched.some(record => record.meta.requiresSuperAdmin)) {
        if (isAuthenticated && isSuperAdmin) {
            next();
        } else if (to.name === "Login" && isAuthenticated) {
            next({name: 'Dashboard'});
        } else {
            next({name: 'Login'});
        }
    } else {
        next();
    }
});

export default router;
export {routes};

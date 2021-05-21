"use strict";

import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '../store';

import Users from '../components/Users';
import Login from '../components/Login';
import Logout from '../components/Logout';
import PasswordChangeRequest from '../components/PasswordChangeRequest';
import PasswordChange from '../components/PasswordChange';
import PasswordReset from '../components/PasswordReset';
import Dashboard from '../components/Dashboard';
import About from '../components/About';
import WalkDetail from '../components/WalkDetail';
import WayPointDetail from '../components/WayPointDetail';
import Teams from '../components/Teams';
import Tags from '../components/Tags';
import WalkPrologue from '../components/WalkPrologue';
import WalkAddWayPoint from '../components/WalkAddWayPoint';
import WalkEpilogue from '../components/WalkEpilogue';

Vue.use(VueRouter);

let routes = [
    {id: 2, path: "/benutzer", component: Users, name: "Users", meta: {requiresSuperAdmin: true}},
    {id: 3, path: "/anmeldung", component: Login, name: "Login"},
    {id: 4, path: "/dashboard", component: Dashboard, name: "Dashboard", meta: {requiresAuth: true}, props: true},
    {id: 5, path: "/was-ist-swapp?", component: About, name: "About", meta: {requiresAuth: false}, props: false},
    {id: 50, path: "/runde/:walkId/detail", component: WalkDetail, name: "WalkDetail", props: true},
    {id: 51, path: "/runde/:walkId/beginnen", component: WalkPrologue, name: "WalkPrologue", props: true},
    {id: 51, path: "/runde/:walkId/wegpunkt-hinzufuegen", component: WalkAddWayPoint, name: "WalkAddWayPoint", props: true},
    {id: 51, path: "/runde/:walkId/abschliessen", component: WalkEpilogue, name: "WalkEpilogue", props: true},
    {id: 6, path: "/runde/:walkId/wegpunkt/:wayPointId/detail", component: WayPointDetail, name: "WayPointDetail", props: true},
    {id: 8, path: "/passwort-zuruecksetzen", component: PasswordReset, name: "PasswordReset"},
    {id: 9, path: "/abmeldung", component: Logout, name: "Logout", meta: {requiresAuth: true}},
    {id: 10, path: "/passwort-aenderung-beantragen", component: PasswordChangeRequest, name: "PasswordChangeRequest", meta: {requiresAuth: true}},
    {id: 11, path: "/passwort-aendern/:userId/:confirmationToken", component: PasswordChange, name: "PasswordChange", props: true},
    {id: 20, path: "/teams", component: Teams, name: "Teams", meta: {requiresAdmin: true}},
    {id: 21, path: "/tags", component: Tags, name: "Tags", meta: {requiresSuperAdmin: true}},
    {id: 0, path: "*", redirect: { name: "Dashboard" }, name: "default"}
];

let router = new VueRouter({
    mode: "history",
    routes: routes
});

router.beforeEach((to, from, next) => {
    let isAuthenticated = store.getters["security/isAuthenticated"];
    let isAdmin = store.getters["security/isAdmin"] || store.getters["security/isSuperAdmin"];
    let isSuperAdmin = store.getters["security/isSuperAdmin"];
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

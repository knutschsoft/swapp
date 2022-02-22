"use strict";
import Vue from 'vue';
import Vuex from 'vuex';
import VuexReset from '@ianwalter/vuex-reset';
import Changelog from './modules/changelog';
import Client from './modules/client';
import Security from './modules/security';
import User from './modules/user';
import Walk from './modules/walk';
import SystemicQuestion from './modules/systemic-question';
import Tag from './modules/tag';
import Team from './modules/team';
import WayPoint from './modules/way-point';

Vue.use(Vuex);

const debug = process.env.NODE_ENV !== 'production';

export default new Vuex.Store({
    modules: {
        changelog: Changelog,
        client: Client,
        user: User,
        security: Security,
        'systemicQuestion': SystemicQuestion,
        tag: Tag,
        team: Team,
        'walk': Walk,
        'wayPoint': WayPoint,
    },
    strict: debug,
    plugins: [VuexReset()],
    state: {
        isLoading: false,
        error: null,
    },
    getters: {
        isLoading(state) {
            return state.isLoading;
        },
        error(state) {
            return state.error;
        }
    },
    mutations: {
        // A no-op mutation must be added to serve as a trigger for a reset. The
        // name of the trigger mutation defaults to 'reset' but can be specified
        // in options, e.g. VuexReset({ trigger: 'data' }).
        reset: () => {},
    },
    actions: {
    }
})

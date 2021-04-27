"use strict";
import TeamAPI from '../../api/team';

const
    FETCHING_TEAMS = "FETCHING_TEAMS",
    FETCHING_TEAMS_SUCCESS = "FETCHING_TEAMS_SUCCESS",
    FETCHING_TEAMS_ERROR = "FETCHING_TEAMS_ERROR"
;

const state = {
    teams: [],
    error: null,
    isLoading: false,
};

const getters = {
    teams(state) {
        return state.teams;
    },
    hasTeams(state) {
        return state.teams.length > 0;
    },
    error(state) {
        return state.error;
    },
    isLoading(state) {
        return state.isLoading;
    }
};

const mutations = {
    [FETCHING_TEAMS](state) {
        state.isLoading = true;
        state.error = null;
    },
    [FETCHING_TEAMS_SUCCESS](state, teams) {
        state.error = null;
        state.isLoading = false;
        state.teams = teams;
    },
    [FETCHING_TEAMS_ERROR](state, error) {
        state.error = error;
        state.isLoading = false;
    },
};

// actions
const actions = {
    async findAll({commit}) {
        commit(FETCHING_TEAMS);
        try {
            let response = await TeamAPI.findAll();
            commit(FETCHING_TEAMS_SUCCESS, response.data['hydra:member']);
        } catch (error) {
            commit(FETCHING_TEAMS_ERROR, error);
        }
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}

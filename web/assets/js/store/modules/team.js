"use strict";
import TeamAPI from '../../api/team';

const
    FETCHING_TEAMS = "FETCHING_TEAMS",
    FETCHING_TEAMS_SUCCESS = "FETCHING_TEAMS_SUCCESS",
    FETCHING_TEAMS_ERROR = "FETCHING_TEAMS_ERROR",
    CHANGING_TEAMS = "CHANGING_TEAMS",
    CHANGING_TEAMS_SUCCESS = "CHANGING_TEAMS_SUCCESS",
    CHANGING_TEAMS_ERROR = "CHANGING_TEAMS_ERROR"
;

const state = {
    teams: [],
    error: null,
    changeTeamError: null,
    isLoading: false,
    changeTeamIsLoading: false,
};

const getters = {
    teams(state) {
        return state.teams;
    },
    getTeamById(state) {
        return teamId => {
            let foundTeam = false;
            state.teams.forEach(
                (team) => {
                    if (String(team.id) === String(teamId)) {
                        foundTeam = team;
                    }
                },
            );

            return foundTeam;
        };
    },
    getTeamByTeamName(state) {
        return teamName => {
            let foundTeam = false;
            state.teams.forEach(
                (team) => {
                    if (String(team.name) === String(teamName)) {
                        foundTeam = team;
                    }
                },
            );

            return foundTeam;
        };
    },
    getTeamByIri(state, getters) {
        return iri => getters.getTeamById(iri.replace('/api/teams/', ''));
    },
    hasTeams(state) {
        return state.teams.length > 0;
    },
    error(state) {
        return state.error;
    },
    changeTeamError(state) {
        return state.changeTeamError;
    },
    isLoading(state) {
        return state.isLoading;
    },
    changeTeamIsLoading(state) {
        return state.changeTeamIsLoading;
    },
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
    [CHANGING_TEAMS](state) {
        state.changeTeamIsLoading = true;
        state.changeTeamError = null;
    },
    [CHANGING_TEAMS_SUCCESS](state, changedTeam) {
        state.changeTeamError = null;
        state.changeTeamIsLoading = false;
        state.teams.forEach((team, index) => {
            if (String(changedTeam.id) === String(team.id)) {
                state.teams.splice(index, 1, changedTeam);
            }
        });
    },
    [CHANGING_TEAMS_ERROR](state, error) {
        state.changeTeamError = error;
        state.changeTeamIsLoading = false;
    },
};

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
    async change({commit}, payload) {
        commit(CHANGING_TEAMS);
        try {
            let response = await TeamAPI.change(payload);
            commit(CHANGING_TEAMS_SUCCESS, response.data);

            return response.data;
        } catch (error) {
            commit(CHANGING_TEAMS_ERROR, error);
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

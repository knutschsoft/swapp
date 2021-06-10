"use strict";
import UserAPI from '../../api/user';

const
    FETCHING_USERS = "FETCHING_USERS",
    FETCHING_USERS_SUCCESS = "FETCHING_USERS_SUCCESS",
    FETCHING_USERS_ERROR = "FETCHING_USERS_ERROR",
    ENABLE = "ENABLE",
    ENABLE_SUCCESS = "ENABLE_SUCCESS",
    ENABLE_ERROR = "ENABLE_ERROR",
    DISABLE = "DISABLE",
    DISABLE_SUCCESS = "DISABLE_SUCCESS",
    DISABLE_ERROR = "DISABLE_ERROR"
;

const state = {
    users: [],
    error: null,
    isLoading: false,
};

const getters = {
    users(state) {
        return state.users;
    },
    getUserById(state) {
        return userId => {
            let foundUser = false;
            state.users.forEach(
                (user) => {
                    if (String(user.id) === String(userId)) {
                        foundUser = user;
                    }
                }
            );

            return foundUser;
        }
    },
    error(state) {
        return state.error;
    },
    isLoading(state) {
        return state.isLoading;
    }
};

function replaceObjectInState(state, object) {
    state.users.forEach(function (oldObject, key) {
        if (oldObject.userId === object.userId) {
            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
            state.users.splice(key, 1, object);
        }

        state.users = [ ...state.users, object ];
    });
}

const mutations = {
    [FETCHING_USERS](state) {
        state.isLoading = true;
        state.error = null;
    },
    [FETCHING_USERS_SUCCESS](state, users) {
        state.error = null;
        state.isLoading = false;
        state.users = users;
    },
    [FETCHING_USERS_ERROR](state, error) {
        state.error = error;
        state.isLoading = false;
    },
    [ENABLE](state) {
        state.isLoading = true;
        state.error = null;
    },
    [ENABLE_SUCCESS](state, user) {
        state.error = null;
        state.isLoading = false;
        replaceObjectInState(state, user);
    },
    [ENABLE_ERROR](state, error) {
        state.error = error;
        state.isLoading = false;
    },
    [DISABLE](state) {
        state.isLoading = true;
        state.error = null;
    },
    [DISABLE_SUCCESS](state, user) {
        state.error = null;
        state.isLoading = false;
        replaceObjectInState(state, user);
    },
    [DISABLE_ERROR](state, error) {
        state.error = error;
        state.isLoading = false;
    },
};

const actions = {
    async findAll({commit}, params) {
        commit(FETCHING_USERS);
        try {
            let response = await UserAPI.findAll(params);
            commit(FETCHING_USERS_SUCCESS, response.data['hydra:member']);
            return response.data['hydra:member'];
        } catch (error) {
            commit(FETCHING_USERS_ERROR, error);
        }
    },
    async enable({commit}, userId) {
        commit(ENABLE);
        try {
            let response = await UserAPI.enable(userId);
            commit(ENABLE_SUCCESS, response.data);
            return response.data;
        } catch (error) {
            commit(ENABLE_ERROR, error);
            return null;
        }
    },
    async disable({commit}, userId) {
        commit(DISABLE);
        try {
            let response = await UserAPI.disable(userId);
            commit(DISABLE_SUCCESS, response.data);
            return response.data;
        } catch (error) {
            commit(DISABLE_ERROR, error);
            return null;
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

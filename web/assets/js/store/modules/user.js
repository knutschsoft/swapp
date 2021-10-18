"use strict";
import UserAPI from '../../api/user';

const
    FETCHING_USER = "FETCHING_USER",
    FETCHING_USER_SUCCESS = "FETCHING_USER_SUCCESS",
    FETCHING_USER_ERROR = "FETCHING_USER_ERROR",
    FETCHING_USERS = "FETCHING_USERS",
    FETCHING_USERS_SUCCESS = "FETCHING_USERS_SUCCESS",
    FETCHING_USERS_ERROR = "FETCHING_USERS_ERROR",
    CREATE = "CREATE",
    CREATE_SUCCESS = "CREATE_SUCCESS",
    CREATE_ERROR = "CREATE_ERROR",
    CHANGE = "CHANGE",
    CHANGE_SUCCESS = "CHANGE_SUCCESS",
    CHANGE_ERROR = "CHANGE_ERROR",
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
    changeUserError: null,
    isLoadingToggleUserState: [],
    isLoading: false,
    isLoadingChange: false,
    toggleUserStateError: false,
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
    getUserByIri(state, getters) {
        return iri => getters.getUserById(iri.replace('/api/users/', ''));
    },
    error(state) {
        return state.error;
    },
    changeUserError(state) {
        return state.changeUserError;
    },
    isLoading(state) {
        return state.isLoading;
    },
    isLoadingChange(state) {
        return state.isLoadingChange;
    },
    isLoadingToggleUserState(state) {
        return iri => state.isLoadingToggleUserState.includes(iri);
    },
};

function replaceObjectInState(state, object) {
    let isReplaced = false;
    state.users.forEach(function (oldObject, key) {
        if (oldObject['@id'] === object['@id']) {
            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
            state.users.splice(key, 1, object);
            isReplaced = true;
        }
    });
    if (!isReplaced) {
        state.users = [ ...state.users, object ];
    }
}

function removeStringValueFromStateProperty(stateProperty, stringValue) {
    stateProperty.forEach(function (oldStringValue, key) {
        if (oldStringValue === stringValue) {
            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
            stateProperty.splice(key, 1);
        }
    });
}

const mutations = {
    [FETCHING_USER](state) {
        state.isLoading = true;
        state.error = null;
    },
    [FETCHING_USER_SUCCESS](state, user) {
        state.error = null;
        state.isLoading = false;
        replaceObjectInState(state, user);
    },
    [FETCHING_USER_ERROR](state, error) {
        state.error = error;
        state.isLoading = false;
    },
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
    [CREATE](state) {
        state.isLoadingChange = true;
        state.changeUserError = null;
    },
    [CREATE_SUCCESS](state, user) {
        state.changeUserError = null;
        state.isLoadingChange = false;
        replaceObjectInState(state, user);
    },
    [CREATE_ERROR](state, error) {
        state.changeUserError = error;
        state.isLoadingChange = false;
    },
    [CHANGE](state) {
        state.isLoadingChange = true;
        state.changeUserError = null;
    },
    [CHANGE_SUCCESS](state, user) {
        state.changeUserError = null;
        state.isLoadingChange = false;
        replaceObjectInState(state, user);
    },
    [CHANGE_ERROR](state, error) {
        state.changeUserError = error;
        state.isLoadingChange = false;
    },
    [ENABLE](state, userIri) {
        state.isLoadingToggleUserState = [ ...state.isLoadingToggleUserState, userIri ];
        state.toggleUserStateError = null;
    },
    [ENABLE_SUCCESS](state, user) {
        state.toggleUserStateError = null;
        removeStringValueFromStateProperty(state.isLoadingToggleUserState, user['@id']);
        replaceObjectInState(state, user);
    },
    [ENABLE_ERROR](state, error, userIri) {
        state.toggleUserStateError = error;
        removeStringValueFromStateProperty(state.isLoadingToggleUserState, userIri);
    },
    [DISABLE](state, userIri) {
        state.isLoadingToggleUserState = [ ...state.isLoadingToggleUserState, userIri ];
        state.toggleUserStateError = null;
    },
    [DISABLE_SUCCESS](state, user) {
        state.toggleUserStateError = null;
        removeStringValueFromStateProperty(state.isLoadingToggleUserState, user['@id']);
        replaceObjectInState(state, user);
    },
    [DISABLE_ERROR](state, error, userIri) {
        state.toggleUserStateError = error;
        removeStringValueFromStateProperty(state.isLoadingToggleUserState, userIri);
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
    async findByIri({commit}, userIri) {
        commit(FETCHING_USER);
        try {
            let response = await UserAPI.find(userIri.replace('/api/users/', ''));
            commit(FETCHING_USER_SUCCESS, response.data);

            return response.data;
        } catch (error) {
            commit(FETCHING_USER_ERROR, error);
        }
    },
    async create({commit}, userId) {
        commit(CREATE);
        try {
            let response = await UserAPI.create(userId);
            commit(CREATE_SUCCESS, response.data);
            return response.data;
        } catch (error) {
            commit(CREATE_ERROR, error);
            return null;
        }
    },
    async change({commit}, userId) {
        commit(CHANGE);
        try {
            let response = await UserAPI.change(userId);
            commit(CHANGE_SUCCESS, response.data);
            return response.data;
        } catch (error) {
            commit(CHANGE_ERROR, error);
            return null;
        }
    },
    async enable({commit}, userIri) {
        commit(ENABLE, userIri);
        try {
            let response = await UserAPI.enable(userIri);
            commit(ENABLE_SUCCESS, response.data);
            return response.data;
        } catch (error) {
            commit(ENABLE_ERROR, error, userIri);
            return null;
        }
    },
    async disable({commit}, userIri) {
        commit(DISABLE, userIri);
        try {
            let response = await UserAPI.disable(userIri);
            commit(DISABLE_SUCCESS, response.data);
            return response.data;
        } catch (error) {
            commit(DISABLE_ERROR, error, userIri);
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

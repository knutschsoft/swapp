import SecurityAPI from '../../api/security';
import UserAPI from '../../api/user';
import axios from 'axios';

let token = localStorage.getItem('swapp-store-token');
if (token && token.length > 10) {
    axios.defaults.headers.common = {'Authorization': 'Bearer ' + token};
} else {
    token = false;
}
let user = localStorage['swapp-store-user'];
try {
    user = JSON.parse(user);
} catch (e) {
    user = false;
}

let isAuthenticated = localStorage['swapp-store-isAuthenticated'];
try {
    isAuthenticated = JSON.parse(isAuthenticated);
} catch (e) {
    isAuthenticated = false;
}
if (!user || !token) {
    isAuthenticated = false;
}

const AUTHENTICATING = "AUTHENTICATING",
    AUTHENTICATING_SUCCESS = "AUTHENTICATING_SUCCESS",
    AUTHENTICATING_ERROR = "AUTHENTICATING_ERROR",
    IS_CONFIRMATION_TOKEN_VALID = "IS_CONFIRMATION_TOKEN_VALID",
    IS_CONFIRMATION_TOKEN_VALID_SUCCESS = "IS_CONFIRMATION_TOKEN_VALID_SUCCESS",
    IS_CONFIRMATION_TOKEN_VALID_ERROR = "IS_CONFIRMATION_TOKEN_VALID_ERROR",
    REQUEST_PASSWORD_RESET = "REQUEST_PASSWORD_RESET",
    REQUEST_PASSWORD_RESET_SUCCESS = "REQUEST_PASSWORD_RESET_SUCCESS",
    REQUEST_PASSWORD_RESET_ERROR = "REQUEST_PASSWORD_RESET_ERROR",
    CHANGE_PASSWORD = "CHANGE_PASSWORD",
    CHANGE_PASSWORD_SUCCESS = "CHANGE_PASSWORD_SUCCESS",
    CHANGE_PASSWORD_ERROR = "CHANGE_PASSWORD_ERROR",
    PROVIDING_DATA_ON_REFRESH_SUCCESS = "PROVIDING_DATA_ON_REFRESH_SUCCESS";

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        isAuthenticated: isAuthenticated,
        user: user
    },
    getters: {
        isLoading(state) {
            return state.isLoading;
        },
        hasError(state) {
            return state.error !== null;
        },
        error(state) {
            return state.error;
        },
        isAuthenticated(state) {
            return state.isAuthenticated;
        },
        isAdmin(state, getters) {
            return getters.hasRole('ROLE_ADMIN');
        },
        isSuperAdmin(state, getters) {
            return getters.hasRole('ROLE_SUPER_ADMIN');
        },
        currentUser(state) {
            return state.user;
        },
        hasRole(state) {
            return role => {
                if (!state.user) {
                    return false;
                }

                return -1 !== state.user.roles.indexOf(role);
            }
        }
    },
    mutations: {
        [AUTHENTICATING](state) {
            state.isLoading = true;
            state.error = null;
            state.isAuthenticated = false;
            state.user = null;
        },
        [AUTHENTICATING_SUCCESS](state, payload) {
            state.isLoading = false;
            state.error = null;
            state.isAuthenticated = true;
            state.user = payload.user;
            localStorage.setItem('swapp-store-token', payload.token);
            localStorage.setItem('swapp-store-user', JSON.stringify(payload.user));
            localStorage.setItem('swapp-store-isAuthenticated', JSON.stringify(true));
            axios.defaults.headers.common = {'Authorization': 'Bearer ' + payload.token};
        },
        [AUTHENTICATING_ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
            state.isAuthenticated = false;
            state.user = null;
            axios.defaults.headers.common = {};
        },
        [CHANGE_PASSWORD](state) {
            state.isLoading = true;
            state.error = null;
        },
        [CHANGE_PASSWORD_SUCCESS](state) {
            state.isLoading = false;
            state.error = null;
        },
        [CHANGE_PASSWORD_ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
        },
        [IS_CONFIRMATION_TOKEN_VALID](state) {
            state.isLoading = true;
            state.error = null;
        },
        [IS_CONFIRMATION_TOKEN_VALID_SUCCESS](state) {
            state.isLoading = false;
            state.error = null;
        },
        [IS_CONFIRMATION_TOKEN_VALID_ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
        },
        [REQUEST_PASSWORD_RESET](state) {
            state.isLoading = true;
            state.error = null;
        },
        [REQUEST_PASSWORD_RESET_SUCCESS](state) {
            state.isLoading = false;
            state.error = null;
        },
        [REQUEST_PASSWORD_RESET_ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
        },
        [PROVIDING_DATA_ON_REFRESH_SUCCESS](state, payload) {
            state.isLoading = false;
            state.error = null;
            state.isAuthenticated = payload.isAuthenticated;
            state.user = payload.user;
            localStorage.setItem('swapp-store-token', payload.token);
            localStorage.setItem('swapp-store-user', JSON.stringify(payload.user));
            localStorage.setItem('swapp-store-isAuthenticated', JSON.stringify(payload.isAuthenticated));
            if (payload.token) {
                axios.defaults.headers.common = {'Authorization': 'Bearer ' + payload.token};
            } else {
                axios.defaults.headers.common = {};
            }
        },
    },
    actions: {
        async login({commit}, payload) {
            commit(AUTHENTICATING);
            try {
                let response = await SecurityAPI.login(payload.username, payload.password);
         console.log(response);
         console.log(response.data);
                const userResponse = await UserAPI.find(response.data['@id']);
                response.data.user = userResponse.data;
                commit(AUTHENTICATING_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(AUTHENTICATING_ERROR, error);
                return null;
            }
        },
        onRefresh({commit}, payload) {
            commit(PROVIDING_DATA_ON_REFRESH_SUCCESS, payload);
        },
        async isConfirmationTokenValid({commit}, {userId, confirmationToken}) {
            commit(IS_CONFIRMATION_TOKEN_VALID);
            try {
                let response = await SecurityAPI.isConfirmationTokenValid(userId, confirmationToken);
                commit(IS_CONFIRMATION_TOKEN_VALID_SUCCESS);
                return response.data;
            } catch (error) {
                commit(IS_CONFIRMATION_TOKEN_VALID_ERROR, error);
                return null;
            }
        },
        async changePassword({commit}, {userId, password, confirmationToken}) {
            commit(CHANGE_PASSWORD);
            try {
                let response = await SecurityAPI.changePassword(userId, password, confirmationToken);
                commit(CHANGE_PASSWORD_SUCCESS);
                return response.data;
            } catch (error) {
                commit(CHANGE_PASSWORD_ERROR, error);
                return null;
            }
        },
        async requestPasswordReset({commit}, {email, honeypotEmail}) {
            commit(REQUEST_PASSWORD_RESET);
            try {
                let response = await SecurityAPI.requestPasswordReset(email, honeypotEmail);
                commit(REQUEST_PASSWORD_RESET_SUCCESS);
                return response.data;
            } catch (error) {
                commit(REQUEST_PASSWORD_RESET_ERROR, error);
                return null;
            }
        },
    }
}

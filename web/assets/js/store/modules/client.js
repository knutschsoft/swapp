'use strict';
import ClientAPI from '../../api/client';

const
    FETCHING_CLIENTS = 'FETCHING_CLIENTS',
    FETCHING_CLIENTS_SUCCESS = 'FETCHING_CLIENTS_SUCCESS',
    FETCHING_CLIENTS_ERROR = 'FETCHING_CLIENTS_ERROR',
    FETCHING_CLIENT = 'FETCHING_CLIENT',
    FETCHING_CLIENT_SUCCESS = 'FETCHING_CLIENT_SUCCESS',
    FETCHING_CLIENT_ERROR = 'FETCHING_CLIENT_ERROR',
    ADDING_CLIENT = 'ADDING_CLIENT',
    ADDING_CLIENT_SUCCESS = 'ADDING_CLIENT_SUCCESS',
    ADDING_CLIENT_ERROR = 'ADDING_CLIENT_ERROR',
    CHANGING_CLIENT = 'CHANGING_CLIENT',
    CHANGING_CLIENT_SUCCESS = 'CHANGING_CLIENT_SUCCESS',
    CHANGING_CLIENT_ERROR = 'CHANGING_CLIENT_ERROR'
;

const state = {
    clients: [],
    totalClients: 0,
    error: null,
    changeClientError: null,
    isLoading: false,
    isLoadingChange: false,
};

function replaceObjectInState(state, object) {
    let isReplaced = false;
    state.clients.forEach(function (oldObject, key) {
        if (oldObject['@id'] === object['@id']) {
            // see: https://vuejs.org/v2/guide/reactivity.html#For-Arrays
            state.clients.splice(key, 1, object);
            isReplaced = true;
        }
    });
    if (!isReplaced) {
        state.clients = [ ...state.clients, object ];
    }
}

const getters = {
    clients(state) {
        return state.clients;
    },
    getClientById(state) {
        return clientId => {
            let foundClient = false;
            state.clients.forEach(
                (client) => {
                    if (String(client.id) === String(clientId)) {
                        foundClient = client;
                    }
                },
            );

            return foundClient;
        };
    },
    getClientByIri(state) {
        return clientIri => {
            let foundClient = false;
            state.clients.forEach(
                (client) => {
                    if (String(client['@id']) === String(clientIri)) {
                        foundClient = client;
                    }
                },
            );

            return foundClient;
        };
    },
    hasClients(state) {
        return state.clients.length > 0;
    },
    totalClients(state) {
        return state.totalClients;
    },
    error(state) {
        return state.error;
    },
    changeClientError(state) {
        return state.changeClientError;
    },
    isLoading(state) {
        return state.isLoading;
    },
};

const mutations = {
    [FETCHING_CLIENTS](state) {
        state.isLoading = true;
        state.error = null;
    },
    [FETCHING_CLIENTS_SUCCESS](state, clients) {
        state.error = null;
        state.isLoading = false;
        clients['hydra:member'].forEach(client => replaceObjectInState(state, client));
        state.totalClients = clients['hydra:totalItems'];
    },
    [FETCHING_CLIENTS_ERROR](state, error) {
        state.error = error;
        state.isLoading = false;
    },
    [FETCHING_CLIENT](state) {
        state.isLoading = true;
        state.error = null;
    },
    [FETCHING_CLIENT_SUCCESS](state, client) {
        state.error = null;
        state.isLoading = false;
        replaceObjectInState(state, client);
    },
    [FETCHING_CLIENT_ERROR](state, error) {
        state.error = error;
        state.isLoading = false;
    },
    [ADDING_CLIENT](state) {
        state.isLoadingChange = true;
        state.changeClientError = null;
    },
    [ADDING_CLIENT_SUCCESS](state, client) {
        state.changeClientError = null;
        state.isLoadingChange = false;
        replaceObjectInState(state, client);
    },
    [ADDING_CLIENT_ERROR](state, error) {
        state.changeClientError = error;
        state.isLoadingChange = false;
    },
    [CHANGING_CLIENT](state) {
        state.isLoadingChange = true;
        state.changeClientError = null;
    },
    [CHANGING_CLIENT_SUCCESS](state, client) {
        state.changeClientError = null;
        state.isLoadingChange = false;
        replaceObjectInState(state, client);
    },
    [CHANGING_CLIENT_ERROR](state, error) {
        state.changeClientError = error;
        state.isLoadingChange = false;
    },
};

const actions = {
    async findAll({ commit }, payload) {
        commit(FETCHING_CLIENTS);
        try {
            let response = await ClientAPI.find(payload);
            commit(FETCHING_CLIENTS_SUCCESS, response.data);
            return response.data['hydra:member'];
        } catch (error) {
            commit(FETCHING_CLIENTS_ERROR, error);
            return [];
        }
    },
    async findById({ commit }, clientId) {
        commit(FETCHING_CLIENT);
        try {
            let response = await ClientAPI.findOneById(clientId);
            commit(FETCHING_CLIENT_SUCCESS, response.data);
        } catch (error) {
            commit(FETCHING_CLIENT_ERROR, error);
        }
    },
    async findByIri({ commit }, clientIri) {
        commit(FETCHING_CLIENT);
        try {
            let response = await ClientAPI.findOneByIri(clientIri);
            commit(FETCHING_CLIENT_SUCCESS, response.data);
        } catch (error) {
            commit(FETCHING_CLIENT_ERROR, error);
        }
    },
    async create({ commit }, payload) {
        commit(ADDING_CLIENT);
        try {
            let response = await ClientAPI.create(payload);
            commit(ADDING_CLIENT_SUCCESS, response.data);
            return response.data;
        } catch (error) {
            commit(ADDING_CLIENT_ERROR, error);
        }
    },
    async change({ commit }, payload) {
        commit(CHANGING_CLIENT);
        try {
            let response = await ClientAPI.change(payload);
            commit(CHANGING_CLIENT_SUCCESS, response.data);
            return response.data;
        } catch (error) {
            commit(CHANGING_CLIENT_ERROR, error);
        }
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};

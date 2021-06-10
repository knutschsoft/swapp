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
    addClientError: null,
    changeClientError: null,
    isLoading: false,
};

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
    hasClients(state) {
        return state.clients.length > 0;
    },
    totalClients(state) {
        return state.totalClients;
    },
    error(state) {
        return state.error;
    },
    addClientError(state) {
        return state.addClientError;
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
        state.clients = clients['hydra:member'];
        state.totalClients = clients['hydra:totalItems'];
    },
    [FETCHING_CLIENTS_ERROR](state, error) {
        state.error = error;
        console.log(error);
        state.isLoading = false;
    },
    [FETCHING_CLIENT](state) {
        state.isLoading = true;
        state.error = null;
    },
    [FETCHING_CLIENT_SUCCESS](state, client) {
        state.error = null;
        state.isLoading = false;
        let fetchedClient = client;
        state.clients.forEach((client, index) => {
            if (String(fetchedClient.id) === String(client.id)) {
                state.clients.splice(index, 1);
            }
        });

        state.clients = [...state.clients, fetchedClient];
    },
    [FETCHING_CLIENT_ERROR](state, error) {
        state.error = error;
        state.isLoading = false;
    },
    [ADDING_CLIENT](state) {
        state.isLoading = true;
        state.addClientError = null;
    },
    [ADDING_CLIENT_SUCCESS](state, client) {
        state.addClientError = null;
        state.isLoading = false;
        let fetchedClient = client;
        state.clients.forEach((client, index) => {
            if (String(fetchedClient.id) === String(client.id)) {
                state.clients.splice(index, 1);
            }
        });

        state.clients = [...state.clients, fetchedClient];
    },
    [ADDING_CLIENT_ERROR](state, error) {
        state.addClientError = error;
        state.isLoading = false;
    },
    [CHANGING_CLIENT](state) {
        state.isLoading = true;
        state.changeClientError = null;
    },
    [CHANGING_CLIENT_SUCCESS](state, client) {
        state.changeClientError = null;
        state.isLoading = false;
        let fetchedClient = client;
        state.clients.forEach((client, index) => {
            if (String(fetchedClient.id) === String(client.id)) {
                state.clients.splice(index, 1);
            }
        });

        state.clients = [...state.clients, fetchedClient];
    },
    [CHANGING_CLIENT_ERROR](state, error) {
        state.changeClientError = error;
        state.isLoading = false;
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
    async addClient({ commit }, payload) {
        commit(ADDING_CLIENT);
        try {
            let response = await ClientAPI.addClient(payload);
            commit(ADDING_CLIENT_SUCCESS, response.data);
            return response.data;
        } catch (error) {
            commit(ADDING_CLIENT_ERROR, error);
        }
    },
    async changeClient({ commit }, payload) {
        commit(CHANGING_CLIENT);
        try {
            let response = await ClientAPI.changeClient(payload);
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
